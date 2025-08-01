document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('DOMContentLoaded', function () {
    const alamatSelect = document.getElementById("alamatSelect");
    if (alamatSelect && !alamatSelect.tomselect) {
        new TomSelect("#alamatSelect", {
            placeholder: "Cari atau pilih alamat...",
            allowEmptyOption: true,
        });
    }
});

    window.closeModalTambahLokasi = function () {
        const modal = document.getElementById('modalTambahLokasi');
        if (modal) {
            modal.classList.add('hidden');
            const form = modal.querySelector('form');
            if (form) form.reset();
        }
    };

    window.closeModalTambahEstimasi = function () {
        const modal = document.getElementById('modalTambahEstimasi');
        if (modal) {
            modal.classList.add('hidden');
            const form = modal.querySelector('form');
            if (form) form.reset();
        }
    };

    const fromSelect = document.getElementById('from_location');
    const toSelect = document.getElementById('to_location');

    if (fromSelect && toSelect) {
        function filterToLocationOptions() {
            const selectedFrom = fromSelect.value;

            Array.from(toSelect.options).forEach(option => {
                option.hidden = false;
            });

            const optionToHide = toSelect.querySelector(`option[value="${selectedFrom}"]`);
            if (optionToHide) {
                optionToHide.hidden = true;

                if (toSelect.value === selectedFrom) {
                    toSelect.value = '';
                }
            }
        }

        fromSelect.addEventListener('change', filterToLocationOptions);
        filterToLocationOptions();
    }
});
