console.log("pesanan.js loaded");

window.addEventListener('open-modal', function (e) {
    if (e.detail === 'modal-pesanan' && window._dataPesanan) {
        setTimeout(() => {
            const data = window._dataPesanan;

            document.querySelector('#modal-pesanan input[name="event_id"]').value = data.eventId;
            document.querySelector('#modal-pesanan input[name="date"]').value = data.tanggal;
            document.querySelector('#modal-pesanan input[name="start_time"]').value = data.mulai;
            document.querySelector('#modal-pesanan input[name="end_time"]').value = data.selesai;
            document.querySelector('#modal-pesanan textarea[name="location_detail"]').value = data.alamat;
            document.querySelector('#modal-pesanan input[name="latitude"]').value = data.latitude;
            document.querySelector('#modal-pesanan input[name="longitude"]').value = data.longitude;

            // Kalau ada DP
            if (document.getElementById('dp_amount')) {
                document.getElementById('dp_amount').textContent = 'Rp ' + Number(data.dp).toLocaleString();
            }
        }, 100);
    }
});

// Efek popup berhasil
document.addEventListener('DOMContentLoaded', function () {
    const modalBerhasil = document.getElementById('modalBerhasil');
    if (modalBerhasil) {
        modalBerhasil.classList.remove('hidden');

        setTimeout(() => {
            modalBerhasil.classList.add('hidden');
        }, 100);
    }
});
