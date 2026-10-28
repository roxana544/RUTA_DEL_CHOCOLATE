import L from 'leaflet';
import { baseMap, factoryIcon, storeIcon, escapeHtml, fetchLegs } from './maps';

/*
 * Apple-style scroll-driven satellite map.
 *
 * Every element with [data-map-step] is a keyframe (center + zoom). As the
 * page scrolls, the camera is interpolated between the keyframe above and the
 * one below the middle of the viewport, and the route line "draws itself"
 * from one factory to the next along the streets, with a traveller marker
 * walking the route. The map tilts as it zooms in for a 3D feel.
 */

const clamp = (v, a = 0, b = 1) => Math.min(b, Math.max(a, v));
const lerp = (a, b, t) => a + (b - a) * t;
const easeInOut = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);

export function initScrollMap(el) {
    const factories = JSON.parse(el.dataset.factories);
    const story = document.querySelector('.story');
    const steps = [...document.querySelectorAll('[data-map-step]')];
    const rail = document.querySelector('.progress-rail');
    const railDots = rail ? [...rail.querySelectorAll('a')] : [];
    const hero = document.querySelector('[data-hero]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const map = baseMap(el, {
        dragging: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        boxZoom: false,
        keyboard: false,
        touchZoom: false,
        zoomSnap: 0,
        zoomDelta: 0.25,
        inertia: false,
        zoomAnimation: false,
    });

    // Route: faint full path + a golden path that grows with the scroll (follows the streets once loaded).
    const points = factories.map((f) => L.latLng(f.lat, f.lng));
    let legs = points.slice(0, -1).map((p, i) => [p, points[i + 1]]);
    const ghost = L.polyline(points, { color: '#ffffff', weight: 3, opacity: 0.5, dashArray: '2 9', lineCap: 'round' }).addTo(map);
    const drawn = L.polyline([], { color: '#ffd166', weight: 6, opacity: 1, lineCap: 'round', lineJoin: 'round' }).addTo(map);
    const traveler = L.marker(points[0], {
        icon: L.divIcon({ className: '', html: '<div class="traveler"><span>🍫</span></div>', iconSize: [34, 34], iconAnchor: [17, 17] }),
        interactive: false, keyboard: false, zIndexOffset: 1000, opacity: 0,
    }).addTo(map);
    let legLengths = [];
    const measureLegs = () => {
        legLengths = legs.map((leg) => leg.reduce((sum, p, i) => (i ? sum + leg[i - 1].distanceTo(p) : 0), 0) || 1);
    };
    measureLegs();
    fetchLegs(points).then((loaded) => {
        legs = loaded;
        measureLegs();
        ghost.setLatLngs(legs);
        drawRoute(current.route);
    });

    const markers = new Map();
    factories.forEach((f, i) => {
        const marker = L.marker([f.lat, f.lng], { icon: factoryIcon(f.color, i + 1), keyboard: false })
            .bindTooltip(escapeHtml(f.name), { direction: 'top', offset: [0, -22], className: 'factory-label' })
            .on('click', () => document.getElementById(`fabrica-${f.slug}`)?.scrollIntoView({ behavior: 'smooth', block: 'center' }))
            .addTo(map);
        markers.set(f.slug, marker);
        f.stores.forEach((s) => L.marker([s.lat, s.lng], { icon: storeIcon(), keyboard: false }).bindTooltip(escapeHtml(s.name)).addTo(map));
    });

    // Build keyframes from the DOM.
    const allBounds = L.latLngBounds(points);
    const keyframes = steps.map((step) => {
        const d = step.dataset;
        if (d.mapStep === 'all') {
            return { el: step, fit: true, focus: null, route: points.length - 1 };
        }
        return {
            el: step,
            lat: parseFloat(d.lat),
            lng: parseFloat(d.lng),
            zoom: parseFloat(d.zoom),
            focus: d.focus || null,
            route: d.route !== undefined ? parseFloat(d.route) : -1,
        };
    });

    const resolveFits = () => {
        keyframes.forEach((k) => {
            if (!k.fit) return;
            // Integer zooms at rest avoid seams between scaled tiles.
            const zoom = Math.floor(map.getBoundsZoom(allBounds.pad(0.25)));
            const c = allBounds.getCenter();
            Object.assign(k, { lat: c.lat, lng: c.lng, zoom });
        });
    };

    // Shift the focus point away from the text card (right on desktop, up on mobile).
    const cameraFor = (lat, lng, zoom) => {
        const size = map.getSize();
        const offset = size.x >= 768 ? L.point(-size.x * 0.18, 0) : L.point(0, size.y * 0.24);
        const projected = map.project([lat, lng], zoom).add(offset);
        return map.unproject(projected, zoom);
    };

    const target = { lat: 0, lng: 0, zoom: 5, route: -1 };
    const current = { lat: 0, lng: 0, zoom: 5, route: -1 };
    let activeIndex = -1;
    let running = false;
    let stepCenters = [];

    const measure = () => {
        stepCenters = keyframes.map((k) => {
            const r = k.el.getBoundingClientRect();
            return r.top + window.scrollY + r.height / 2;
        });
        resolveFits();
    };

    const computeTarget = () => {
        const anchor = window.scrollY + window.innerHeight / 2;
        let i = 0;
        while (i < stepCenters.length - 1 && anchor > stepCenters[i + 1]) i++;

        const a = keyframes[i];
        const b = keyframes[Math.min(i + 1, keyframes.length - 1)];
        const span = stepCenters[i + 1] - stepCenters[i] || 1;
        const raw = anchor <= stepCenters[0] ? 0 : clamp((anchor - stepCenters[i]) / span);
        // Hold still near each chapter, then glide to the next one.
        const t = easeInOut(clamp((raw - 0.2) / 0.6));

        // Zooming in: move the center early; zooming out: move it late.
        const tc = b.zoom > a.zoom ? 1 - Math.pow(1 - t, 3) : Math.pow(t, 3);

        target.lat = lerp(a.lat, b.lat, tc);
        target.lng = lerp(a.lng, b.lng, tc);
        target.zoom = lerp(a.zoom, b.zoom, t);
        target.route = lerp(a.route, b.route, t);

        const nearest = t < 0.5 ? i : Math.min(i + 1, keyframes.length - 1);
        if (nearest !== activeIndex) {
            activeIndex = nearest;
            markers.forEach((m, slug) => m.getElement()?.firstElementChild?.classList.toggle('is-active', slug === keyframes[nearest].focus));
            railDots.forEach((dot, n) => dot.classList.toggle('is-active', n === nearest));
        }

        // Fade the hero title as the camera dives into Sucre.
        if (hero) {
            const p = clamp(window.scrollY / (window.innerHeight * 0.8));
            hero.style.opacity = String(1 - p);
            hero.style.transform = `translateY(${p * -60}px) scale(${1 - p * 0.08})`;
        }

        // Hide the map and rail once the story is over.
        const storyEnd = story.getBoundingClientRect().bottom;
        const over = storyEnd < window.innerHeight * 0.3;
        el.classList.toggle('is-hidden', over);
        document.querySelector('.map-vignette')?.classList.toggle('opacity-0', over);
        if (rail) rail.style.opacity = over || window.scrollY < window.innerHeight * 0.5 ? '0' : '1';
    };

    /** Point at distance `d` (meters) along a leg. */
    const pointAlong = (leg, total, d) => {
        let left = d;
        for (let i = 1; i < leg.length; i++) {
            const seg = leg[i - 1].distanceTo(leg[i]);
            if (left <= seg && seg > 0) {
                const t = left / seg;
                return { latlng: L.latLng(lerp(leg[i - 1].lat, leg[i].lat, t), lerp(leg[i - 1].lng, leg[i].lng, t)), index: i };
            }
            left -= seg;
        }
        return { latlng: leg[leg.length - 1], index: leg.length };
    };

    function drawRoute(progress) {
        if (progress < 0) {
            traveler.setOpacity(0);
            return drawn.setLatLngs([]);
        }
        const whole = Math.min(Math.floor(progress), legs.length);
        const frac = progress - whole;
        const path = [];
        for (let i = 0; i < whole; i++) path.push(...legs[i]);

        let head = points[Math.min(whole, points.length - 1)];
        if (frac > 0 && legs[whole]) {
            const { latlng, index } = pointAlong(legs[whole], legLengths[whole], legLengths[whole] * frac);
            path.push(...legs[whole].slice(0, index), latlng);
            head = latlng;
        } else if (!path.length) {
            path.push(head);
        }
        drawn.setLatLngs(path);
        traveler.setLatLng(head).setOpacity(1);
    }

    const render = () => {
        const k = reduceMotion ? 1 : 0.14;
        let moving = false;
        for (const key of ['lat', 'lng', 'zoom', 'route']) {
            const diff = target[key] - current[key];
            if (Math.abs(diff) > 1e-6) {
                current[key] += diff * k;
                moving = true;
            } else {
                current[key] = target[key];
            }
        }

        map.setView(cameraFor(current.lat, current.lng, current.zoom), current.zoom, { animate: false });
        el.classList.toggle('is-far', current.zoom < 10);
        // Tilt the camera as we dive into the city (CSS 3D on the map container).
        const tilt = reduceMotion ? 0 : 42 * clamp((current.zoom - 12.5) / 3.5);
        el.style.setProperty('--tilt', `${tilt.toFixed(2)}deg`);
        el.style.setProperty('--tilt-scale', (1 + tilt / 42 * 0.55).toFixed(3));
        drawRoute(current.route);

        if (moving) {
            requestAnimationFrame(render);
        } else {
            running = false;
        }
    };

    const tick = () => {
        computeTarget();
        if (!running) {
            running = true;
            requestAnimationFrame(render);
        }
    };

    // Initial state: jump straight to the current scroll position.
    map.setView(L.latLng(keyframes[0].lat, keyframes[0].lng), keyframes[0].zoom, { animate: false });
    measure();
    computeTarget();
    Object.assign(current, target);
    render();

    window.addEventListener('scroll', tick, { passive: true });
    window.addEventListener('resize', () => {
        map.invalidateSize();
        measure();
        tick();
    });
    window.addEventListener('load', () => {
        measure();
        tick();
    });
}
