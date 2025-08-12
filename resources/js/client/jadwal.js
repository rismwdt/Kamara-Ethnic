console.log("jadwal.js loaded");

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btnPesanSekarang').forEach(button => {
        button.addEventListener('click', () => {
            const eventId = button.dataset.eventId;

            const form = document.getElementById('formJadwal');
            form?.reset();

            const inputJadwal = form?.querySelector('input[name="event_id"]');
            const inputPesanan = document.querySelector('#modalPesanan input[name="event_id"]');

            if (inputJadwal) inputJadwal.value = eventId;
            if (inputPesanan) inputPesanan.value = eventId;

            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-jadwal' }));
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('cek-jadwal-button').addEventListener('click', function (e) {
        e.preventDefault();
        const form = document.getElementById('formJadwal');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);

        console.log("Data dikirim:");
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

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
            if (data.price) {
                    const dpElement = document.getElementById('dp_amount');
                    if (dpElement) {
                        dpElement.textContent = data.dp.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
                    }
                }
            if (data.available) {
                const eventId = form.querySelector('input[name="event_id"]').value;
                const tanggal = form.querySelector('input[name="date"]').value;
                const mulai   = form.querySelector('input[name="start_time"]').value;
                const selesai = form.querySelector('input[name="end_time"]').value;
                const alamat  = form.querySelector('#alamat')?.value ?? '';

                const formPesanan = document.getElementById('formPesanan');
                if (formPesanan) {
                    const locationInput = formPesanan.querySelector('textarea[name="location_detail"]');
                    console.log("Field lokasi ditemukan:", locationInput);

                    formPesanan.querySelector('input[name="event_id"]').value = eventId;
                    formPesanan.querySelector('input[name="date"]').value = tanggal;
                    formPesanan.querySelector('input[name="start_time"]').value = mulai;
                    formPesanan.querySelector('input[name="end_time"]').value = selesai;
                    if (locationInput) {
                        locationInput.value = alamat;
                    }
                }

                window._dataPesanan = { eventId, tanggal, mulai, selesai, alamat };
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

function tutupModalBentrokDanBukaJadwal() {
    window.dispatchEvent(new CustomEvent('close-modal', { detail: 'modal-jadwal-bentrok' }));
    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-jadwal' }));
}
