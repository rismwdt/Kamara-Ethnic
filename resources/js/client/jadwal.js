console.log("jadwal.js loaded");

document.addEventListener('DOMContentLoaded', function () {
    let map, marker;
    let userLat = null;
    let userLon = null;

    const locationInput = document.getElementById('location_detail');
    const suggestions = document.getElementById('suggestions');

    // Ambil lokasi pengguna (jika diizinkan)
    // if (navigator.geolocation) {
    //     navigator.geolocation.getCurrentPosition(pos => {
    //         userLat = pos.coords.latitude;
    //         userLon = pos.coords.longitude;
    //         console.log("Lokasi pengguna:", userLat, userLon);
    //     }, err => {
    //         console.warn("Gagal ambil lokasi:", err);
    //     });
    // }

    // Fungsi pasang marker + isi lat/lon di form
    function setMarker(lat, lon) {
        if (marker) {
            marker.setLatLng([lat, lon]);
        } else {
            marker = L.marker([lat, lon]).addTo(map);
        }
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lon;
    }

    // Autocomplete alamat dari Nominatim (Indonesia saja & dekat lokasi user)
    locationInput.addEventListener('input', function () {
        let query = this.value;
        if (query.length < 3) {
            suggestions.innerHTML = '';
            return;
        }

        let url = `/api/nominatim?q=${encodeURIComponent(query)}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                suggestions.innerHTML = '';
                data.forEach(place => {
                    const li = document.createElement('li');
                    li.textContent = place.display_name;
                    li.style.cursor = 'pointer';
                    li.onclick = () => {
                        locationInput.value = place.display_name;
                        setMarker(place.lat, place.lon);
                        map.setView([place.lat, place.lon], 15);
                        suggestions.innerHTML = '';
                    };
                    suggestions.appendChild(li);
                });
            })
            .catch(err => console.warn("Gagal ambil lokasi:", err));
    });

    // Buka modal jadwal
    document.querySelectorAll('.btnPesanSekarang').forEach(button => {
        button.addEventListener('click', () => {
            const eventId = button.dataset.eventId;

            const form = document.getElementById('formJadwal');
            form?.reset();

            const inputJadwal = form?.querySelector('input[name="event_id"]');
            const inputPesanan = document.querySelector('#modalPesanan input[name="event_id"]');

            if (inputJadwal) inputJadwal.value = eventId;
            if (inputPesanan) inputPesanan.value = eventId;

            // Tampilkan peta saat modal dibuka
            setTimeout(() => {
                if (!map) {
                    map = L.map('map').setView([-6.200000, 106.816666], 12);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    // Klik peta → set marker
                    map.on('click', function (e) {
                        setMarker(e.latlng.lat, e.latlng.lng);
                    });
                }
            }, 300);

            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-jadwal' }));
        });
    });

    // Tombol cek jadwal
    document.getElementById('cek-jadwal-button').addEventListener('click', function (e) {
        e.preventDefault();
        const form = document.getElementById('formJadwal');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respons:', data);
            if (data.available) {
                const formPesanan = document.getElementById('formPesanan');
                if (formPesanan) {
                    formPesanan.querySelector('input[name="event_id"]').value = formData.get('event_id');
                    formPesanan.querySelector('input[name="date"]').value = formData.get('date');
                    formPesanan.querySelector('input[name="start_time"]').value = formData.get('start_time');
                    formPesanan.querySelector('input[name="end_time"]').value = formData.get('end_time');
                    formPesanan.querySelector('textarea[name="location_detail"]').value = formData.get('location_detail');
                    formPesanan.querySelector('input[name="latitude"]').value = formData.get('latitude');
                    formPesanan.querySelector('input[name="longitude"]').value = formData.get('longitude');
                }
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-pesanan' }));
            } else {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-jadwal-bentrok' }));
            }
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'modal-jadwal' }));
        })
        .catch(error => {
            console.error('Gagal:', error);
        });
    });
});
