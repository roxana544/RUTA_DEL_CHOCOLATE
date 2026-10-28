import { initScrollMap } from './scroll-map';
import { initRouteMap, initPickupMap } from './maps';
import { initBookingForm } from './booking';
import { initCheckpointForm } from './checkpoint';

document.addEventListener('DOMContentLoaded', () => {
    const scrollMap = document.getElementById('scroll-map');
    if (scrollMap) initScrollMap(scrollMap);

    document.querySelectorAll('[data-route-map]').forEach(initRouteMap);
    document.querySelectorAll('[data-pickup-map]').forEach(initPickupMap);
    document.querySelectorAll('[data-booking-form]').forEach(initBookingForm);
    document.querySelectorAll('[data-checkpoint-form]').forEach(initCheckpointForm);

    // Reveal-on-scroll animations
    const io = new IntersectionObserver(
        (entries) => entries.forEach((e) => {
            if (e.isIntersecting) {
                e.target.classList.add('is-visible');
                io.unobserve(e.target);
            }
        }),
        { threshold: 0.15 },
    );
    document.querySelectorAll('.reveal').forEach((el) => io.observe(el));

    // Mobile menu
    document.querySelector('[data-menu-toggle]')?.addEventListener('click', () => {
        document.querySelector('[data-menu]')?.classList.toggle('hidden');
    });
});
