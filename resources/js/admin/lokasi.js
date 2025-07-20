document.addEventListener("DOMContentLoaded", function () {
    // =======================
    // 🔹 Alamat Select & Input Sinkron
    // =======================
    const alamatSelect = document.getElementById('alamatSelect');
    const alamatInput = document.getElementById('alamatInput');

    if (alamatSelect && alamatInput) {
        alamatSelect.addEventListener('change', function () {
            alamatInput.value = this.value;
        });

        new TomSelect('#alamatSelect', {
            placeholder: "Cari atau pilih alamat...",
            allowEmptyOption: true,
        });
    }

    // =======================
    // 🔹 TomSelect untuk Pilih Alamat
    // =======================
    const selectForAlamat = document.querySelector("#alamatSelect");
    if (selectForAlamat && !selectForAlamat.tomselect) {
        new TomSelect("#alamatSelect", {
            placeholder: "--- Pilih alamat ---",
            allowEmptyOption: true,
        });
    }

    // =======================
    // 🔹 Modal Tambah Lokasi
    // =======================
    window.closeModalTambahLokasi = function () {
        const modal = document.getElementById('modalTambahLokasi');
        if (modal) {
            modal.classList.add('hidden');
            const form = modal.querySelector('form');
            if (form) form.reset();
        }
    };

    // =======================
    // 🔹 Modal Tambah Estimasi
    // =======================
    window.closeModalTambahEstimasi = function () {
        const modal = document.getElementById('modalTambahEstimasi');
        if (modal) {
            modal.classList.add('hidden');
            const form = modal.querySelector('form');
            if (form) form.reset();
        }
    };

    // =======================
    // 🔹 Filter Lokasi Estimasi (from → to)
    // =======================
    const fromSelect = document.getElementById('from_location');
    const toSelect = document.getElementById('to_location');

    if (fromSelect && toSelect) {
        function filterToLocationOptions() {
            const selectedFrom = fromSelect.value;

            // Tampilkan semua opsi di lokasi kedua
            Array.from(toSelect.options).forEach(option => {
                option.hidden = false;
            });

            // Sembunyikan lokasi yang sama dengan lokasi pertama
            const optionToHide = toSelect.querySelector(`option[value="${selectedFrom}"]`);
            if (optionToHide) {
                optionToHide.hidden = true;

                if (toSelect.value === selectedFrom) {
                    toSelect.value = '';
                }
            }
        }

        fromSelect.addEventListener('change', filterToLocationOptions);
        filterToLocationOptions(); // Panggil saat pertama kali load
    }
});
