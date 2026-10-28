/*
 * Checkpoint form: grabs the device position when the photo is chosen / the form is sent,
 * so the server can verify the tourist is really at the factory.
 */
export function initCheckpointForm(form) {
    const status = form.querySelector('[data-gps-status]');
    const lat = form.querySelector('input[name="lat"]');
    const lng = form.querySelector('input[name="lng"]');
    let pending = null;

    const locate = () => {
        if (pending) return pending;
        pending = new Promise((resolve) => {
            if (!('geolocation' in navigator)) return resolve(false);
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    lat.value = pos.coords.latitude;
                    lng.value = pos.coords.longitude;
                    if (status) status.textContent = `📍 GPS ±${Math.round(pos.coords.accuracy)} m`;
                    resolve(true);
                },
                () => {
                    if (status) status.textContent = '⚠️ GPS no disponible / GPS unavailable';
                    resolve(false);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 15000 },
            );
        });
        return pending;
    };

    form.querySelector('input[type="file"]')?.addEventListener('change', locate);
    form.addEventListener('submit', async (e) => {
        if (lat.value) return;
        e.preventDefault();
        await locate();
        form.submit();
    });
}
