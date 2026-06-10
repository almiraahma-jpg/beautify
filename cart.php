<?php
// ============================================================
//  cart.php  –  Beautify Shopping Cart
// ============================================================
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja – Beautify</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Fraunces:ital,wght@0,300;0,600;1,300&display=swap" rel="stylesheet">
    <style>
        :root {
            --pink: #F297A0;
            --pink-light: #F9D0CE;
            --bg: #F3EBD8;
            --text: #3B2A2B;
            --text-muted: #8A7070;
            --border: #EDD9CC;
            --secondary: #DCDFBA;
            --secondary-text: #5A5E3A;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); }

        header { background: var(--pink); padding: 14px 0; box-shadow: 0 2px 8px rgba(0,0,0,.15); }
        .header-inner { max-width: 1100px; margin: auto; padding: 0 20px; display: flex; align-items: center; justify-content: space-between; }
        .logo { font-family: 'Fraunces', serif; font-size: 26px; font-weight: 600; color: white; text-decoration: none; }
        .logo span { font-style: italic; font-weight: 300; }
        .back-btn { color: white; text-decoration: none; font-size: 13px; font-weight: 600; }
        .back-btn:hover { opacity: .8; }

        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 340px; gap: 20px; }
        .card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
        .card-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border); }

        .cart-item { display: flex; gap: 16px; padding: 16px 0; border-bottom: 1px solid var(--border); align-items: center; }
        .cart-item:last-child { border-bottom: none; }
        .item-img { width: 80px; height: 80px; border-radius: 8px; object-fit: cover; flex-shrink: 0; background: #fafafa; }
        .item-info { flex: 1; }
        .item-brand { font-size: 11px; color: var(--text-muted); margin-bottom: 4px; }
        .item-name { font-size: 14px; font-weight: 600; margin-bottom: 6px; }
        .item-price { font-size: 16px; font-weight: 700; color: var(--pink); }
        .item-subtotal { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        .qty-control { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
        .qty-btn { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--border); background: #f9f9f9; cursor: pointer; font-size: 16px; font-weight: 700; display: flex; align-items: center; justify-content: center; color: var(--text); transition: background .2s; }
        .qty-btn:hover { background: var(--pink-light); border-color: var(--pink); }
        .qty-num { font-size: 14px; font-weight: 700; min-width: 24px; text-align: center; }
        .btn-hapus { color: var(--pink); border: none; background: none; font-size: 12px; font-weight: 600; margin-top: 6px; cursor: pointer; padding: 0; }
        .btn-hapus:hover { text-decoration: underline; }

        .empty-cart { text-align: center; padding: 60px 0; color: var(--text-muted); }
        .empty-cart .icon { font-size: 60px; display: block; margin-bottom: 16px; }
        .empty-cart p { font-size: 16px; font-weight: 600; margin-bottom: 8px; }
        .btn-belanja { display: inline-block; margin-top: 20px; background: var(--pink); color: white; padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px; }

        .summary-row { display: flex; justify-content: space-between; font-size: 13px; color: var(--text-muted); margin-bottom: 10px; }
        .summary-total { display: flex; justify-content: space-between; font-size: 18px; font-weight: 700; color: var(--text); margin: 14px 0; padding-top: 14px; border-top: 2px solid var(--border); }
        .btn-checkout { display: block; width: 100%; background: var(--pink); color: white; text-align: center; padding: 14px; border-radius: 10px; font-size: 15px; font-weight: 700; text-decoration: none; border: none; cursor: pointer; transition: background .2s; }
        .btn-checkout:hover { background: #e07880; }
        .btn-checkout:disabled { opacity: .5; cursor: not-allowed; }
        .note-ongkir { font-size: 12px; color: var(--text-muted); text-align: center; margin-top: 10px; }

        @media (max-width: 768px) { .container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<header>
    <div class="header-inner">
        <a href="index.php" class="logo">Beauti<span>fy</span></a>
        <a href="index.php" class="back-btn">← Lanjut Belanja</a>
    </div>
</header>

<div class="container">
    <!-- KIRI: LIST PRODUK -->
    <div class="card">
        <div class="card-title">🛒 Keranjang Belanja
            <span style="font-size:13px;font-weight:400;color:var(--text-muted);margin-left:8px;" id="jumlahProduk"></span>
        </div>
        <div id="cartList"></div>
    </div>

    <!-- KANAN: RINGKASAN -->
    <div>
        <div class="card">
            <div class="card-title">📋 Ringkasan Belanja</div>
            <div class="summary-row"><span>Total Produk</span><span id="sumItem">0 item</span></div>
            <div class="summary-row"><span>Subtotal</span><span id="sumSubtotal">Rp 0</span></div>
            <div class="summary-row">
                <span>Ongkos Kirim</span>
                <span id="sumOngkir" style="color:#B6BB79;font-weight:600;">Rp 15.000</span>
            </div>
            <div class="summary-total">
                <span>Total</span>
                <span style="color:var(--pink);" id="sumTotal">Rp 0</span>
            </div>
            <button class="btn-checkout" id="btnCheckout" onclick="goCheckout()">Lanjut ke Checkout →</button>
            <p class="note-ongkir">🚚 Gratis ongkir untuk pembelian ≥ Rp 50.000</p>
        </div>
    </div>
</div>

<script>
let cart = JSON.parse(sessionStorage.getItem('beautify_cart') || '[]');

function formatRp(n) { return 'Rp ' + parseInt(n).toLocaleString('id-ID'); }
function saveCart()  { sessionStorage.setItem('beautify_cart', JSON.stringify(cart)); }

function renderCart() {
    const list = document.getElementById('cartList');

    if (cart.length === 0) {
        list.innerHTML = `
            <div class="empty-cart">
                <span class="icon">🛒</span>
                <p>Keranjang kamu kosong</p>
                <a href="index.php" class="btn-belanja">Mulai Belanja</a>
            </div>`;
        document.getElementById('jumlahProduk').textContent = '';
        document.getElementById('sumItem').textContent      = '0 item';
        document.getElementById('sumSubtotal').textContent  = 'Rp 0';
        document.getElementById('sumOngkir').textContent    = formatRp(15000);
        document.getElementById('sumTotal').textContent     = 'Rp 0';
        document.getElementById('btnCheckout').disabled     = true;
        return;
    }

    const subtotal = cart.reduce((s, c) => s + c.price * c.qty, 0);
    const ongkir   = subtotal >= 50000 ? 0 : 15000;
    const total    = subtotal + ongkir;
    const totalQty = cart.reduce((s, c) => s + c.qty, 0);

    document.getElementById('jumlahProduk').textContent    = `(${totalQty} produk)`;
    document.getElementById('sumItem').textContent         = totalQty + ' item';
    document.getElementById('sumSubtotal').textContent     = formatRp(subtotal);
    document.getElementById('sumOngkir').textContent       = ongkir === 0 ? 'GRATIS' : formatRp(ongkir);
    document.getElementById('sumOngkir').style.color       = ongkir === 0 ? '#B6BB79' : 'var(--pink)';
    document.getElementById('sumTotal').textContent        = formatRp(total);
    document.getElementById('btnCheckout').disabled        = false;

    list.innerHTML = cart.map(item => `
        <div class="cart-item">
            <img class="item-img"
                 src="${item.img || 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=400&q=80'}"
                 alt="${item.name}"
                 onerror="this.src='https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=400&q=80'">
            <div class="item-info">
                <div class="item-brand">${item.brand}</div>
                <div class="item-name">${item.name}</div>
                <div class="item-price">${formatRp(item.price)}</div>
                <div class="item-subtotal">Subtotal: ${formatRp(item.price * item.qty)}</div>
                <div class="qty-control">
                    <button class="qty-btn" onclick="changeQty(${item.id}, -1)">−</button>
                    <span class="qty-num">${item.qty}</span>
                    <button class="qty-btn" onclick="changeQty(${item.id}, +1)">+</button>
                </div>
                <button class="btn-hapus" onclick="removeItem(${item.id})">🗑 Hapus</button>
            </div>
        </div>
    `).join('');
}

function changeQty(id, delta) {
    const item = cart.find(c => c.id == id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) cart = cart.filter(c => c.id != id);
    saveCart(); renderCart();
}

function removeItem(id) {
    cart = cart.filter(c => c.id != id);
    saveCart(); renderCart();
}

function goCheckout() {
    if (cart.length > 0) window.location.href = 'checkout.php';
}

renderCart();
</script>
</body>
</html>