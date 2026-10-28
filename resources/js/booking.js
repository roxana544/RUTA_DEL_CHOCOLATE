/*
 * Tour booking form: loads departures with free seats for the chosen date
 * and keeps the price summary up to date.
 */
export function initBookingForm(form) {
    const dateInput = form.querySelector('[name="date"]');
    const peopleInput = form.querySelector('[name="people"]');
    const timesBox = form.querySelector('[data-times]');
    const totalEl = form.querySelector('[data-total]');
    const submit = form.querySelector('[type="submit"]');
    const price = parseFloat(form.dataset.price);
    const t = JSON.parse(form.dataset.i18n);
    const oldTime = form.dataset.oldTime;

    const updateTotal = () => {
        const people = Math.max(1, parseInt(peopleInput.value || '1', 10));
        totalEl.textContent = `Bs ${(price * people).toFixed(2)}`;
    };

    const renderTimes = (times) => {
        timesBox.innerHTML = '';
        if (!times.length) {
            timesBox.innerHTML = `<p class="text-sm text-cream-100/60">${t.noDepartures}</p>`;
            submit.disabled = true;
            return;
        }
        submit.disabled = false;
        times.forEach(({ time, seats }, i) => {
            const id = `time-${i}`;
            const full = seats < 1;
            timesBox.insertAdjacentHTML(
                'beforeend',
                `<label for="${id}" class="flex cursor-pointer items-center justify-between rounded-2xl border border-cream-100/15 px-4 py-3 transition has-[:checked]:border-gold has-[:checked]:bg-gold/10 ${full ? 'opacity-40 pointer-events-none' : ''}">
                    <span class="flex items-center gap-3">
                        <input type="radio" id="${id}" name="time" value="${time}" class="accent-[#d9a441]" ${full ? 'disabled' : ''} ${time === oldTime && !full ? 'checked' : ''} required>
                        <span class="font-semibold">${time}</span>
                    </span>
                    <span class="text-sm text-cream-100/60">${full ? t.full : t.seats.replace(':n', seats)}</span>
                </label>`,
            );
        });
    };

    const loadTimes = async () => {
        if (!dateInput.value) return;
        timesBox.innerHTML = `<p class="text-sm text-cream-100/60">${t.loading}</p>`;
        try {
            const url = `${form.dataset.availability}?date=${encodeURIComponent(dateInput.value)}`;
            const res = await fetch(url, { headers: { Accept: 'application/json' } });
            const data = await res.json();
            renderTimes(res.ok ? data.times : []);
        } catch {
            timesBox.innerHTML = `<p class="text-sm text-red-300">${t.error}</p>`;
        }
    };

    dateInput.addEventListener('change', loadTimes);
    peopleInput.addEventListener('input', updateTotal);
    updateTotal();
    loadTimes();
}
