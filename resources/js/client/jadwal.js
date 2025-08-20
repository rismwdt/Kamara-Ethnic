console.log("jadwal.js loaded");

document.addEventListener('DOMContentLoaded', function () {
    let map, marker;
    // input di modal-jadwal
    const locationInput = document.getElementById('location_detail');
    const suggestions   = document.getElementById('suggestions');
    const latEl         = document.getElementById('latitude');
    const lonEl         = document.getElementById('longitude');

    function toNumber(val) {
        if (typeof val === 'number') return val;
        return Number(String(val || '').replace(/[^\d]/g, '')) || 0;
    }

    function setMarker(lat, lon) {
        lat = parseFloat(lat); lon = parseFloat(lon);
        if (!map) return;
        if (marker) marker.setLatLng([lat, lon]);
        else marker = L.marker([lat, lon]).addTo(map);
        if (latEl) latEl.value = lat;
        if (lonEl) lonEl.value = lon;
    }

    function clearSuggestions() {
        if (!suggestions) return;
        suggestions.innerHTML = '';
        suggestions.style.display = 'none';
    }

    function renderSuggestions(items) {
        if (!suggestions) return;
        if (!items || !items.length) { clearSuggestions(); return; }
        suggestions.innerHTML = '';
        items.forEach(function (place) {
            const li = document.createElement('li');
            li.textContent = place.display_name || (place.lat + ', ' + place.lon);
            li.style.cursor = 'pointer';
            li.style.padding = '6px 8px';

            li.addEventListener('mousedown', function (e) {
                e.preventDefault();
                if (locationInput) locationInput.value = place.display_name;
                setMarker(place.lat, place.lon);
                if (map) map.setView([parseFloat(place.lat), parseFloat(place.lon)], 15);
                clearSuggestions();
            });
            li.addEventListener('mouseenter', function () { li.style.background = '#f1f5f9'; });
            li.addEventListener('mouseleave', function () { li.style.background = 'transparent'; });

            suggestions.appendChild(li);
        });
        suggestions.style.display = 'block';
    }

    let debounceTimer = null;
    let currentController = null;
    let lastQuery = '';

    async function searchAddress(q) {
        if (currentController) currentController.abort();
        currentController = new AbortController();
        try {
            const res = await fetch('/api/nominatim?q=' + encodeURIComponent(q), {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                signal: currentController.signal
            });
            if (!res.ok) { console.warn('Nominatim proxy error:', res.status); clearSuggestions(); return; }
            const data = await res.json();
            if (q === lastQuery) renderSuggestions(data);
        } catch (err) {
            if (!err || err.name !== 'AbortError') console.warn('Gagal ambil lokasi:', err);
            clearSuggestions();
        }
    }

    if (locationInput) {
        locationInput.addEventListener('input', function () {
            const q = this.value.trim(); lastQuery = q;
            if (latEl) latEl.value = ''; if (lonEl) lonEl.value = '';
            if (debounceTimer) clearTimeout(debounceTimer);
            if (q.length < 3) { clearSuggestions(); return; }
            debounceTimer = setTimeout(function () { searchAddress(q); }, 300);
        });
    }

    document.addEventListener('click', function (e) {
        if (!suggestions || !locationInput) return;
        if (e.target === locationInput || suggestions.contains(e.target)) return;
        clearSuggestions();
    });

    // === TOMBOL PESAN ===
    const btnsPesan = document.querySelectorAll('.btnPesanSekarang'); // pastikan class ini ada di tombol
    if (btnsPesan && btnsPesan.length) {
        btnsPesan.forEach(function (button) {
            button.addEventListener('click', function () {
                window._lastPesanBtn = button;

                const eventId  = button.getAttribute('data-event-id') || '';
                const price    = toNumber(button.getAttribute('data-price') || 0);
                const date     = button.getAttribute('data-date')  || '';
                const start    = button.getAttribute('data-start') || '';
                const end      = button.getAttribute('data-end')   || '';

                // set event_id di modal Jadwal (by name dan by id)
                const formJ = document.getElementById('formJadwal');
                if (formJ) {
                    const byName = formJ.querySelector('input[name="event_id"]');
                    if (byName) byName.value = eventId;
                }
                const byId = document.getElementById('event_id_input');
                if (byId) byId.value = eventId;

                // prefill waktu kalau disediakan di tombol
                if (date)  (document.getElementById('date')       || {}).value = date;
                if (start) (document.getElementById('start_time') || {}).value = start;
                if (end)   (document.getElementById('end_time')   || {}).value = end;

                // init peta sekali
                setTimeout(function () {
                    if (!map) {
                        map = L.map('map').setView([-6.200000, 106.816666], 12);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(map);
                        map.on('click', function (e) {
                            setMarker(e.latlng.lat, e.latlng.lng);
                        });
                    }
                }, 300);

                // simpan harga default untuk DP (dipakai pesanan.js)
                window._dataPesanan = Object.assign({}, window._dataPesanan, { price });

                // buka modal jadwal
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-jadwal' }));
            });
        });
    }

    // === CEK JADWAL ===
    const cekBtn = document.getElementById('cek-jadwal-button');
    if (cekBtn) {
        cekBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const form = document.getElementById('formJadwal');
            if (!form) return;

            if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                if (typeof form.reportValidity === 'function') form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const tokenEl  = form.querySelector('input[name="_token"]');

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': tokenEl ? tokenEl.value : '',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function (response) {
                if (!response.ok) {
                    return response.json().catch(function () { throw new Error('HTTP ' + response.status); });
                }
                return response.json();
            })
            .then(function (data) {
                console.log('Respons cek jadwal:', data);

                if (data && data.available) {
                    const priceFromBtn   = window._lastPesanBtn ? toNumber(window._lastPesanBtn.getAttribute('data-price')) : 0;
                    const priceFromInput = toNumber((document.getElementById('event_price') || {}).value || 0);
                    const price          = priceFromBtn > 0 ? priceFromBtn : priceFromInput;

                    // simpan untuk pesanan.js hitung DP
                    window._dataPesanan = {
                        eventId:   formData.get('event_id'),
                        tanggal:   formData.get('date'),
                        mulai:     formData.get('start_time'),
                        selesai:   formData.get('end_time'),
                        alamat:    formData.get('location_detail'),
                        latitude:  formData.get('latitude'),
                        longitude: formData.get('longitude'),
                        price:     price
                    };

                    // isi form pesanan
                    const formPesanan = document.getElementById('formPesanan');
                    if (formPesanan) {
                        const setVal = (sel, val) => { const el = formPesanan.querySelector(sel); if (el) el.value = val; };
                        setVal('input[name="event_id"]',   window._dataPesanan.eventId);
                        setVal('input[name="date"]',       window._dataPesanan.tanggal);
                        setVal('input[name="start_time"]', window._dataPesanan.mulai);
                        setVal('input[name="end_time"]',   window._dataPesanan.selesai);
                        const locTextarea = formPesanan.querySelector('textarea[name="location_detail"]');
                        if (locTextarea) locTextarea.value = window._dataPesanan.alamat;
                        setVal('input[name="latitude"]',   window._dataPesanan.latitude);
                        setVal('input[name="longitude"]',  window._dataPesanan.longitude);
                        setVal('input[name="price"]',      price);
                    }
                    console.log('[pesanan prefill]', {
  eventId: window._dataPesanan.eventId,
  date:    window._dataPesanan.tanggal,
  start:   window._dataPesanan.mulai,
  end:     window._dataPesanan.selesai
});


                    // buka modal pesanan (pesanan.js akan hitung DP)
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-pesanan' }));

                    // tutup modal jadwal
                    window.dispatchEvent(new CustomEvent('close-modal', { detail: 'modal-jadwal' }));
                } else {
                    const p = document.getElementById('jadwal-bentrok-reason');
                    if (p && data && data.message) p.textContent = data.message;
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-jadwal-bentrok' }));
                }
            })
            .catch(function (error) {
                console.error('Gagal cek jadwal:', error);
                const p = document.getElementById('jadwal-bentrok-reason');
                if (p && error && error.message) p.textContent = error.message;
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-jadwal-bentrok' }));
            });
        });
    }
});
