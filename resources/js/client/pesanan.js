// TAMPILKAN MODAL BERHASIL (dari session Laravel)
document.addEventListener('DOMContentLoaded', function () {
    const modalBerhasil = document.getElementById('modalBerhasil');
    if (modalBerhasil) {
        modalBerhasil.classList.remove('hidden');

        // Otomatis sembunyikan modal setelah 5 detik
        setTimeout(() => {
            modalBerhasil.classList.add('hidden');
        }, 5000);
    }
});
