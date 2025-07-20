// TOMBOL "PESAN SEKARANG" – Buka modal pemilihan jadwal
document.querySelectorAll('.btnPesanSekarang').forEach(button => {
    button.addEventListener('click', () => {
        const eventId = button.dataset.eventId;
        const modal = document.getElementById('modalJadwal');
        const form = document.getElementById('formJadwal');

        if (modal && form) {
            modal.classList.remove('hidden');
            form.reset(); // reset input agar tidak ada data lama
            form.querySelector('input[name="event_id"]').value = eventId;
        }

        // Juga isi form pemesanan dengan event_id agar konsisten
        const pesananInput = document.querySelector('#modalPesanan input[name="event_id"]');
        if (pesananInput) {
            pesananInput.value = eventId;
        }
    });
});

// TUTUP MODAL BENTROK LALU KEMBALI KE MODAL JADWAL
function tutupModalBentrokDanBukaJadwal() {
    const modalBentrok = document.getElementById('modalJadwalBentrok');
    const modalJadwal = document.getElementById('modalJadwal');

    if (modalBentrok && modalJadwal) {
        modalBentrok.classList.add('hidden');
        modalJadwal.classList.remove('hidden');
    }
}

// SAAT FORM JADWAL DIKIRIM UNTUK CEK JADWAL
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formJadwal');

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.available) {
                // Jadwal tersedia → buka modal pesanan
                document.getElementById('modalJadwal')?.classList.add('hidden');
                document.getElementById('modalPesanan')?.classList.remove('hidden');

                // Isi otomatis data ke form pemesanan
                document.querySelector('#modalPesanan input[name="tanggal"]').value = formData.get('date');
                document.querySelector('#modalPesanan input[name="start_time"]').value = formData.get('start_time');
                document.querySelector('#modalPesanan input[name="end_time"]').value = formData.get('end_time');
                document.querySelector('#modalPesanan textarea[name="alamat"]').value = formData.get('location');
            } else {
                // Jadwal bentrok → tampilkan modal gagal
                document.getElementById('modalJadwal')?.classList.add('hidden');
                document.getElementById('modalJadwalBentrok')?.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Jadwal check error:', error);
            alert("Terjadi kesalahan saat mengecek jadwal. Silakan coba lagi.");
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btnPesanSekarang').forEach(button => {
        button.addEventListener('click', function () {
            const eventId = this.getAttribute('data-event-id');
            const eventInput = document.getElementById('event_id_input');
            if (eventInput) {
                eventInput.value = eventId;
            }

            const modal = document.getElementById('modal-jadwal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        });
    });
});
