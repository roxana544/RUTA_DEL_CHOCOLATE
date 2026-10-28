import L from 'leaflet';

export const SUCRE = [-19.0476, -65.2596];

const ESRI = 'https://server.arcgisonline.com/ArcGIS/rest/services';
const ATTRIBUTION = 'Imágenes &copy; <a href="https://www.esri.com">Esri</a>, Maxar, Earthstar Geographics &mdash; Rutas &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';

/**
 * Satellite base map (Esri World Imagery, free, no API key) with place and street labels on top.
 * Pass { layerSwitcher: true } to let the visitor toggle between satellite and street map.
 */
export function baseMap(el, options = {}) {
    const { layerSwitcher = false, ...leafletOptions } = options;
    const map = L.map(el, { zoomControl: false, attributionControl: true, ...leafletOptions });

    const tiles = { maxNativeZoom: 17, maxZoom: 19 };
    const satellite = L.layerGroup([
        L.tileLayer(`${ESRI}/World_Imagery/MapServer/tile/{z}/{y}/{x}`, { ...tiles, attribution: ATTRIBUTION }),
        L.tileLayer(`${ESRI}/Reference/World_Transportation/MapServer/tile/{z}/{y}/{x}`, { ...tiles, opacity: 0.85 }),
        L.tileLayer(`${ESRI}/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}`, tiles),
    ]).addTo(map);

    if (layerSwitcher) {
        const streets = L.tileLayer(`${ESRI}/World_Street_Map/MapServer/tile/{z}/{y}/{x}`, { ...tiles, attribution: ATTRIBUTION });
        L.control.layers({ 'Satélite': satellite, 'Mapa': streets }, {}, { position: 'topright', collapsed: true }).addTo(map);
    }

    return map;
}

/**
 * Street-following legs between consecutive stops (OSRM public router).
 * Falls back to a straight segment if the service is unreachable, and caches results per browser.
 */
export async function fetchLegs(points) {
    const straight = points.slice(0, -1).map((p, i) => [p, points[i + 1]]);
    const legs = await Promise.all(straight.map(async ([a, b], i) => {
        const key = `leg:${a.lat.toFixed(5)},${a.lng.toFixed(5)}>${b.lat.toFixed(5)},${b.lng.toFixed(5)}`;
        try {
            const cached = sessionStorage.getItem(key);
            if (cached) return JSON.parse(cached).map(([lat, lng]) => L.latLng(lat, lng));
        } catch { /* storage unavailable */ }
        try {
            const url = `https://router.project-osrm.org/route/v1/driving/${a.lng},${a.lat};${b.lng},${b.lat}?overview=full&geometries=geojson`;
            const res = await fetch(url, { signal: AbortSignal.timeout(6000) });
            const json = await res.json();
            const coords = json.routes?.[0]?.geometry?.coordinates;
            if (!coords?.length) throw new Error('no route');
            const line = coords.map(([lng, lat]) => [lat, lng]);
            try { sessionStorage.setItem(key, JSON.stringify(line)); } catch { /* ignore */ }
            return line.map(([lat, lng]) => L.latLng(lat, lng));
        } catch {
            return straight[i];
        }
    }));

    return legs;
}

export function factoryIcon(color, label) {
    return L.divIcon({
        className: '',
        html: `<div class="factory-pin" style="--c:${color}">${label}</div>`,
        iconSize: [38, 38],
        iconAnchor: [19, 19],
    });
}

export function storeIcon(selected = false) {
    return L.divIcon({
        className: '',
        html: `<div class="store-pin${selected ? ' is-selected' : ''}"></div>`,
        iconSize: [14, 14],
        iconAnchor: [7, 7],
    });
}

export function escapeHtml(text) {
    return String(text).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]);
}

/** Static-ish route map used on the tour detail page. */
export function initRouteMap(el) {
    const stops = JSON.parse(el.dataset.stops);
    const map = baseMap(el, { scrollWheelZoom: false, layerSwitcher: true });

    const points = stops.map((s) => [s.lat, s.lng]);
    const route = L.polyline(points, { color: '#ffd166', weight: 5, opacity: 0.95, dashArray: '2 10', lineCap: 'round' }).addTo(map);
    stops.forEach((s, i) => {
        L.marker([s.lat, s.lng], { icon: factoryIcon(s.color, i + 1) })
            .bindPopup(`<strong>${escapeHtml(s.name)}</strong>`)
            .addTo(map);
    });
    if (points.length > 1) {
        fetchLegs(points.map(([lat, lng]) => L.latLng(lat, lng))).then((legs) => {
            route.setLatLngs(legs).setStyle({ dashArray: null });
        });
    }

    L.control.zoom({ position: 'bottomright' }).addTo(map);
    if (points.length > 1) {
        map.fitBounds(points, { padding: [50, 50] });
    } else {
        map.setView(points[0] ?? SUCRE, 15);
    }
}

/** Map of pickup points synced with the <select name="store_id"> in checkout. */
export function initPickupMap(el) {
    const stores = JSON.parse(el.dataset.stores);
    const select = document.getElementById(el.dataset.select);
    const map = baseMap(el, { scrollWheelZoom: false, layerSwitcher: true });
    L.control.zoom({ position: 'bottomright' }).addTo(map);

    const markers = new Map();
    stores.forEach((s) => {
        const marker = L.marker([s.lat, s.lng], { icon: storeIcon() })
            .bindTooltip(escapeHtml(s.name))
            .on('click', () => {
                select.value = String(s.id);
                select.dispatchEvent(new Event('change'));
            })
            .addTo(map);
        markers.set(String(s.id), marker);
    });

    const highlight = () => {
        markers.forEach((m, id) => m.setIcon(storeIcon(id === select.value)));
        const selected = markers.get(select.value);
        if (selected) map.flyTo(selected.getLatLng(), 16, { duration: 0.8 });
    };
    select.addEventListener('change', highlight);

    map.fitBounds(stores.map((s) => [s.lat, s.lng]), { padding: [40, 40] });
    if (select.value) highlight();
}
