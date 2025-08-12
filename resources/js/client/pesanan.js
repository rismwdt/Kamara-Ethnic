window.addEventListener('open-modal', function (e) {
    if (e.detail === 'modal-pesanan' && window._dataPesanan) {
        setTimeout(() => {
            const data = window._dataPesanan;
            document.getElementById('event_id').value = data.eventId;
            document.getElementById('date').value = data.tanggal;
            document.getElementById('start_time').value = data.mulai;
            document.getElementById('end_time').value = data.selesai;
            document.getElementById('dp_amount').textContent = 'Rp ' + response.dp.toLocaleString();

            const lokasiTextarea = document.querySelector('#formPesanan textarea[name="location_detail"]');
            if (lokasiTextarea) {
                lokasiTextarea.value = data.alamat;
            } else {
                console.warn("Field lokasi di modal pesanan tidak ditemukan.");
            }
        }, 100);
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const modalBerhasil = document.getElementById('modalBerhasil');
    if (modalBerhasil) {
        modalBerhasil.classList.remove('hidden');

        setTimeout(() => {
            modalBerhasil.classList.add('hidden');
        }, 100);
    }
});
