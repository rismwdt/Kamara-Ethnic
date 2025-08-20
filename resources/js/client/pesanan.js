console.log("pesanan.js loaded");

// ——— util ———
function rupiah(n) {
  n = Number(n) || 0;
  try { return new Intl.NumberFormat('id-ID').format(n); }
  catch { return (n).toLocaleString('id-ID'); }
}

function toNumber(val) {
  if (typeof val === 'number') return val;
  return Number(String(val || '').replace(/[^\d]/g, '')) || 0;
}

function ensureHidden(form, name) {
  var el = form.querySelector('input[name="' + name + '"]');
  if (!el) {
    el = document.createElement('input');
    el.type = 'hidden';
    el.name = name;
    form.appendChild(el);
  }
  return el;
}

// ——— core ———
function setDpAmount() {
  var form = document.getElementById('formPesanan');
  if (!form) {
    console.warn('[pesanan] #formPesanan tidak ditemukan');
    return;
  }

  // ambil harga dari hidden input atau dari _dataPesanan
  var priceEl = form.querySelector('input[name="price"]') || document.getElementById('event_price');
  var price = 0;

  if (priceEl && priceEl.value) {
    price = toNumber(priceEl.value);
  } else if (window._dataPesanan && window._dataPesanan.price) {
    price = toNumber(window._dataPesanan.price);
  }

  var dp = Math.round(price * 0.5);

  var dpEl = document.getElementById('dp_amount');
  if (dpEl) dpEl.textContent = 'Rp ' + rupiah(dp);

  ensureHidden(form, 'price').value = price;
  ensureHidden(form, 'dp').value = dp;

  console.log('[pesanan] hitung DP → price:', price, 'dp:', dp);
}

// ——— hooks ———

// 1) Hitung DP tiap kali modal pesanan dibuka.
//   Beberapa komponen modal pakai event di window, ada juga yang bubbling di document.
//   Kita dengerin dua-duanya supaya aman.
function handleOpenModalEvent(e) {
  // detail bisa 'modal-pesanan' (string) atau { detail: 'modal-pesanan' }
  var detail = e && e.detail;
  if (detail === 'modal-pesanan') {
    // kasih sedikit delay supaya input sudah terisi dari jadwal.js
    setTimeout(setDpAmount, 50);
  }
}
window.addEventListener('open-modal', handleOpenModalEvent);
document.addEventListener('open-modal', handleOpenModalEvent);

// 2) Kalau field price diisi/diupdate belakangan (misal dari script lain), tetap update DP.
document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('formPesanan');
  if (!form) return;

  var priceEl = form.querySelector('input[name="price"]') || document.getElementById('event_price');
  if (priceEl) {
    ['change','input'].forEach(evt => {
      priceEl.addEventListener(evt, function () {
        setDpAmount();
      });
    });
  }
});
