console.log('[pesanan] loaded');

// ---------- util ----------
function pad2(x) {
    return String(x).padStart(2, '0');
}

function datePlus(days) {
    const d = new Date();
    d.setDate(d.getDate() + days);
    return `${d.getFullYear()}-${pad2(d.getMonth()+1)}-${pad2(d.getDate())}`;
}

function defaultDate() {
    return datePlus(3);
} // H+3 biar lolos rule server
function defaultStart() {
    const d = new Date();
    d.setHours(d.getHours() + 2, 0, 0, 0); // +2 jam dari sekarang
    return `${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
}

function defaultEnd(startHHmm) {
    const [hh, mm] = (startHHmm || '00:00').split(':').map(Number);
    return `${pad2((hh+2)%24)}:${pad2(mm||0)}`; // durasi default 2 jam
}

function formatIDR(n) {
    try {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(n).replace(',00', '');
    } catch {
        return 'Rp ' + (n || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
}

function rupiah(n) {
    n = Number(n) || 0;
    try {
        return new Intl.NumberFormat('id-ID').format(n);
    } catch {
        return (n).toLocaleString('id-ID');
    }
}

function toNumber(val) {
    if (typeof val === 'number') return val;
    return Number(String(val || '').replace(/[^\d]/g, '')) || 0;
}

function ensureHidden(form, name) {
    let el = form.querySelector(`input[name="${name}"]`);
    if (!el) {
        el = document.createElement('input');
        el.type = 'hidden';
        el.name = name;
        form.appendChild(el);
    }
    return el;
}

// ---------- DP calc ----------
function setDpAmount() {
    const form = document.getElementById('formPesanan');
    if (!form) return;

    const priceEl = form.querySelector('input[name="price"]') || document.getElementById('event_price');
    let price = 0;
    if (priceEl && priceEl.value) {
        price = toNumber(priceEl.value);
    } else if (window._dataPesanan && window._dataPesanan.price) {
        price = toNumber(window._dataPesanan.price);
    }

    const dp = Math.round(price * 0.5); // 50%
    const dpEl = document.getElementById('dp_amount');
    if (dpEl) dpEl.textContent = 'Rp ' + rupiah(dp);

    ensureHidden(form, 'price').value = price;
    ensureHidden(form, 'dp').value = dp;

    console.log('[pesanan] DP dihitung → price:', price, 'dp:', dp);
}

// Hitung DP setiap modal dibuka
function handleOpenModalEvent(e) {
    const detail = e && e.detail;
    if (detail === 'modal-pesanan') {
        setTimeout(setDpAmount, 50);
        // set min date H+3 bila belum
        const dateInp = document.getElementById('date');
        if (dateInp) {
            const min = defaultDate();
            dateInp.min = min;
            if (!dateInp.value || dateInp.value < min) dateInp.value = min;
        }
    }
}
window.addEventListener('open-modal', handleOpenModalEvent);
document.addEventListener('open-modal', handleOpenModalEvent);

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formPesanan');
    if (form) {
        const priceEl = form.querySelector('input[name="price"]') || document.getElementById('event_price');
        if (priceEl) {
            ['change', 'input'].forEach(evt => priceEl.addEventListener(evt, setDpAmount));
        }
        const dateInp = document.getElementById('date');
        if (dateInp) {
            const min = defaultDate();
            dateInp.min = min;
            if (!dateInp.value || dateInp.value < min) dateInp.value = min;
        }
    }
});

// ---------- Klik "Pesan Sekarang" ----------
document.addEventListener('click', (e) => {
    const btn = e.target.closest('.btnPesanSekarang');
    if (!btn) return;
    e.preventDefault();

    const $ = sel => document.querySelector(sel);

    const eventId = btn.dataset.eventId || '';
    const price = Number(btn.dataset.price || 0);
    const dateVal = (btn.dataset.date || '').trim() || defaultDate();
    const startVal = (btn.dataset.start || '').trim() || defaultStart();
    const endVal = (btn.dataset.end || '').trim() || defaultEnd(startVal);

    $('#event_id_pesanan') && ($('#event_id_pesanan').value = eventId);
    $('#event_price') && ($('#event_price').value = price);

    $('#date') && ($('#date').value = dateVal);
    $('#start_time') && ($('#start_time').value = startVal);
    $('#end_time') && ($('#end_time').value = endVal);

    // reset alamat
    $('#location_detail') && ($('#location_detail').value = '');

    // tampilkan DP
    const dpNominal = Math.round(price * 0.5);
    const dpEl = document.getElementById('dp_amount');
    if (dpEl) dpEl.textContent = formatIDR(dpNominal);
    ensureHidden(document.getElementById('formPesanan'), 'dp').value = dpNominal;

    // buka modal
    window.dispatchEvent(new CustomEvent('open-modal', {
        detail: 'modal-pesanan'
    }));

    // fokus ke input pertama
    setTimeout(() => {
        const first = document.querySelector('#formPesanan input[required], #formPesanan textarea[required]');
        first && first.focus();
    }, 50);
});

// ---------- Autocomplete alamat ----------
(function () {
    const ta = document.getElementById('location_detail');
    const list = document.getElementById('suggestions');
    if (!ta || !list) return;

    let timer = null;
    ta.addEventListener('input', () => {
        const q = ta.value.trim();
        list.innerHTML = '';
        if (timer) clearTimeout(timer);
        if (q.length < 4) return;

        timer = setTimeout(async () => {
            try {
                const url = `https://nominatim.openstreetmap.org/search?format=json&limit=8&countrycodes=id&addressdetails=1&q=${encodeURIComponent(q)}`;
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();

                list.innerHTML = '';
                data.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = item.display_name;
                    li.style.padding = '6px 8px';
                    li.style.cursor = 'pointer';
                    li.addEventListener('mouseenter', () => li.style.background = '#f5f5f5');
                    li.addEventListener('mouseleave', () => li.style.background = 'white');
                    li.addEventListener('click', () => {
                        ta.value = item.display_name;
                        list.innerHTML = '';
                    });
                    list.appendChild(li);
                });
            } catch (e) {
                console.warn('[pesanan] autocomplete error:', e);
                list.innerHTML = '';
            }
        }, 350);
    });

    document.addEventListener('click', (e) => {
        if (!list.contains(e.target) && e.target !== ta) list.innerHTML = '';
    });
})();
