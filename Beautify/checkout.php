<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout – Beautify</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Fraunces:ital,wght@0,300;0,600;1,300&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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

        header { background: var(--pink); padding: 14px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        .header-inner { max-width: 1100px; margin: auto; padding: 0 20px; display: flex; align-items: center; justify-content: space-between; }
        .logo { font-family: 'Fraunces', serif; font-size: 26px; font-weight: 600; color: white; text-decoration: none; }
        .logo span { font-style: italic; font-weight: 300; }
        .back-btn { color: white; text-decoration: none; font-size: 13px; font-weight: 600; }
        .back-btn:hover { opacity: 0.8; }

        /* STEP INDICATOR */
        .steps {
            max-width: 1100px;
            margin: 24px auto 0;
            padding: 0 20px;
            display: flex;
            align-items: center;
            gap: 0;
        }
        .step {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
        }
        .step.active { color: var(--pink); }
        .step.done { color: #B6BB79; }
        .step-num {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: #EDD9CC;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }
        .step.active .step-num { background: var(--pink); color: white; }
        .step.done .step-num { background: #B6BB79; color: white; }
        .step-line { flex: 1; height: 2px; background: #EDD9CC; margin: 0 12px; }
        .step-line.done { background: #B6BB79; }

        .container { max-width: 1100px; margin: 20px auto 40px; padding: 0 20px; display: grid; grid-template-columns: 1fr 340px; gap: 20px; }
        .card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); margin-bottom: 16px; }
        .card-title { font-size: 16px; font-weight: 700; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 8px; }

        /* FORM */
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%; padding: 10px 14px; border: 1px solid var(--border);
            border-radius: 8px; font-size: 13px; font-family: inherit;
            color: var(--text); background: white; outline: none; transition: border 0.2s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus { border-color: var(--pink); box-shadow: 0 0 0 3px rgba(242,151,160,0.15); }
        .form-group input.error { border-color: #e07880; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .error-msg { font-size: 11px; color: #e07880; margin-top: 4px; display: none; }
        .error-msg.show { display: block; }

        /* PAYMENT OPTIONS */
        .payment-options { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .payment-option {
            border: 2px solid var(--border); border-radius: 10px; padding: 14px 12px;
            cursor: pointer; transition: border 0.2s, background 0.2s;
            display: flex; align-items: center; gap: 10px;
            font-size: 13px; font-weight: 600;
        }
        .payment-option input[type="radio"] { accent-color: var(--pink); width: 16px; height: 16px; }
        .payment-option:has(input:checked) { border-color: var(--pink); background: #FFF5F6; }
        .pay-icon { font-size: 22px; }

        /* EWALLET INPUT */
        .ewallet-input { display: none; margin-top: 12px; }
        .ewallet-input.show { display: block; }

        /* ORDER SUMMARY */
        .order-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--border); font-size: 13px; }
        .order-item:last-child { border-bottom: none; }
        .order-item-name { font-weight: 500; }
        .order-item-qty { color: var(--text-muted); font-size: 11px; }
        .order-item-price { font-weight: 700; color: var(--pink); }
        .summary-row { display: flex; justify-content: space-between; font-size: 13px; color: var(--text-muted); margin-bottom: 8px; }
        .summary-total { display: flex; justify-content: space-between; font-size: 18px; font-weight: 700; color: var(--text); margin: 14px 0; padding-top: 14px; border-top: 2px solid var(--border); }

        .btn-bayar {
            display: block; width: 100%; background: var(--pink); color: white;
            text-align: center; padding: 14px; border-radius: 10px;
            font-size: 15px; font-weight: 700; border: none; cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-bayar:hover { background: #e07880; transform: translateY(-1px); }
        .btn-bayar:active { transform: translateY(0); }

        /* MODAL SUKSES */
        .modal-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 500;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: white;
            border-radius: 20px;
            padding: 48px 36px;
            text-align: center;
            max-width: 420px;
            width: 90%;
            animation: popIn 0.3s ease;
        }
        @keyframes popIn {
            from { opacity:0; transform: scale(0.85); }
            to   { opacity:1; transform: scale(1); }
        }
        .modal-icon { font-size: 72px; display: block; margin-bottom: 16px; }
        .modal-box h2 { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
        .modal-box p { font-size: 14px; color: var(--text-muted); line-height: 1.6; margin-bottom: 8px; }
        .order-code { 
            background: #FFF5F6; border: 1px dashed var(--pink);
            border-radius: 8px; padding: 10px 16px;
            font-size: 18px; font-weight: 700; color: var(--pink);
            letter-spacing: 2px; margin: 16px 0;
        }
        .modal-actions { display: flex; gap: 10px; margin-top: 20px; }
        .btn-modal-main {
            flex: 1; background: var(--pink); color: white;
            padding: 12px; border-radius: 8px; text-decoration: none;
            font-weight: 700; font-size: 14px; text-align: center;
            border: none; cursor: pointer;
        }
        .btn-modal-main:hover { background: #e07880; }
        .btn-modal-sec {
            flex: 1; background: var(--secondary); color: var(--secondary-text);
            padding: 12px; border-radius: 8px; text-decoration: none;
            font-weight: 700; font-size: 14px; text-align: center;
        }

        /* LOADING SPINNER */
        .spinner {
            display: none;
            width: 20px; height: 20px;
            border: 3px solid rgba(255,255,255,0.4);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 768px) {
            .container { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; }
            .payment-options { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<header>
    <div class="header-inner">
        <a href="index.php" class="logo">Beauti<span>fy</span></a>
        <a href="cart.php" class="back-btn">← Kembali ke Keranjang</a>
    </div>
</header>

<!-- STEP INDICATOR -->
<div class="steps">
    <div class="step done">
        <div class="step-num">✓</div>
        <span>Keranjang</span>
    </div>
    <div class="step-line done"></div>
    <div class="step active">
        <div class="step-num">2</div>
        <span>Checkout</span>
    </div>
    <div class="step-line"></div>
    <div class="step">
        <div class="step-num">3</div>
        <span>Selesai</span>
    </div>
</div>

<div class="container">

    <!-- KIRI: FORM -->
    <div>
        <!-- Alamat -->
        <div class="card">
            <div class="card-title">📍 Alamat Pengiriman</div>
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Lengkap *</label>
                    <input type="text" id="nama" placeholder="Nama penerima">
                    <div class="error-msg" id="err-nama">Nama wajib diisi</div>
                </div>
                <div class="form-group">
                    <label>No. Telepon *</label>
                    <input type="text" id="telepon" placeholder="08xxxxxxxxxx">
                    <div class="error-msg" id="err-telepon">Nomor telepon wajib diisi</div>
                </div>
            </div>
            <div class="form-group">
                <label>Alamat Lengkap *</label>
                <textarea id="alamat" rows="3" placeholder="Jl. nama jalan, No. rumah, RT/RW..."></textarea>
                <div class="error-msg" id="err-alamat">Alamat wajib diisi</div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Kota *</label>
                    <input type="text" id="kota" placeholder="Surabaya">
                    <div class="error-msg" id="err-kota">Kota wajib diisi</div>
                </div>
                <div class="form-group">
                    <label>Kode Pos *</label>
                    <input type="text" id="kodepos" placeholder="60111" maxlength="5">
                    <div class="error-msg" id="err-kodepos">Kode pos wajib diisi</div>
                </div>
            </div>
            <div class="form-group">
                <label>Catatan Pengiriman (opsional)</label>
                <input type="text" id="catatan" placeholder="Taruh di depan pintu, dll...">
            </div>
        </div>

        <!-- Metode Pengiriman -->
        <div class="card">
            <div class="card-title">🚚 Metode Pengiriman</div>
            <div class="payment-options">
                <label class="payment-option">
                    <input type="radio" name="pengiriman" value="reguler" checked>
                    <span class="pay-icon">📦</span>
                    <div>
                        <div>Reguler</div>
                        <div style="font-size:11px;color:var(--text-muted);font-weight:400;">3-5 hari kerja</div>
                    </div>
                </label>
                <label class="payment-option">
                    <input type="radio" name="pengiriman" value="express">
                    <span class="pay-icon">⚡</span>
                    <div>
                        <div>Express</div>
                        <div style="font-size:11px;color:var(--text-muted);font-weight:400;">1-2 hari kerja</div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Metode Pembayaran -->
        <div class="card">
            <div class="card-title">💳 Metode Pembayaran</div>
            <div class="payment-options">
                <label class="payment-option">
                    <input type="radio" name="pembayaran" value="gopay" checked onchange="showEwallet('gopay')">
                    <span class="pay-icon">💚</span> GoPay
                </label>
                <label class="payment-option">
                    <input type="radio" name="pembayaran" value="ovo" onchange="showEwallet('ovo')">
                    <span class="pay-icon">💜</span> OVO
                </label>
                <label class="payment-option">
                    <input type="radio" name="pembayaran" value="dana" onchange="showEwallet('dana')">
                    <span class="pay-icon">💙</span> Dana
                </label>
                <label class="payment-option">
                    <input type="radio" name="pembayaran" value="transfer" onchange="showEwallet('transfer')">
                    <span class="pay-icon">🏦</span> Transfer Bank
                </label>
            </div>

            <!-- Input nomor e-wallet -->
            <div class="ewallet-input show" id="input-gopay">
                <div class="form-group" style="margin-top:12px;margin-bottom:0;">
                    <label>Nomor GoPay *</label>
                    <input type="text" id="no-gopay" placeholder="08xxxxxxxxxx">
                </div>
            </div>
            <div class="ewallet-input" id="input-ovo">
                <div class="form-group" style="margin-top:12px;margin-bottom:0;">
                    <label>Nomor OVO *</label>
                    <input type="text" id="no-ovo" placeholder="08xxxxxxxxxx">
                </div>
            </div>
            <div class="ewallet-input" id="input-dana">
                <div class="form-group" style="margin-top:12px;margin-bottom:0;">
                    <label>Nomor Dana *</label>
                    <input type="text" id="no-dana" placeholder="08xxxxxxxxxx">
                </div>
            </div>
            <div class="ewallet-input" id="input-transfer">
                <div style="margin-top:12px;background:#FFF5F6;border-radius:8px;padding:14px;font-size:13px;">
                    <div style="font-weight:700;margin-bottom:8px;">Rekening Tujuan:</div>
                    <div>🏦 <b>BCA</b> – 1234567890 a.n. <b>Beautify Indonesia</b></div>
                    <div style="margin-top:4px;">🏦 <b>Mandiri</b> – 0987654321 a.n. <b>Beautify Indonesia</b></div>
                    <div style="margin-top:8px;font-size:11px;color:var(--text-muted);">Upload bukti transfer setelah pembayaran di halaman pesanan.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- KANAN: RINGKASAN -->
    <div>
        <div class="card" style="position:sticky;top:80px;">
            <div class="card-title">🧾 Ringkasan Pesanan</div>
            <div id="orderItems"></div>
            <div style="margin-top:14px;">
                <div class="summary-row"><span>Subtotal</span><span id="coSubtotal">-</span></div>
                <div class="summary-row"><span>Ongkos Kirim</span><span id="coOngkir" style="color:#B6BB79;font-weight:600;">-</span></div>
                <div class="summary-total">
                    <span>Total Bayar</span>
                    <span style="color:var(--pink);" id="coTotal">-</span>
                </div>
            </div>
            <button class="btn-bayar" onclick="prosesBayar()">
                <span id="btnText">Bayar Sekarang →</span>
                <div class="spinner" id="spinner"></div>
            </button>
            <div style="text-align:center;margin-top:10px;font-size:11px;color:var(--text-muted);">
                🔒 Transaksi aman & terenkripsi
            </div>
        </div>
    </div>

</div>

<!-- MODAL SUKSES -->
<div class="modal-overlay" id="modalSukses">
    <div class="modal-box">
        <span class="modal-icon">🎉</span>
        <h2>Pesanan Berhasil!</h2>
        <p>Terima kasih sudah belanja di <strong>Beautify</strong>!<br>Pesananmu sedang diproses.</p>
        <div class="order-code" id="orderCode">-</div>
        <p style="font-size:12px;">Simpan kode pesanan di atas untuk melacak pengirimanmu.</p>
        <div class="modal-actions">
            <a href="index.php" class="btn-modal-main">🛍 Belanja Lagi</a>
            <a href="riwayat_pesanan.php" class="btn-modal-sec">📦 Lihat Pesanan</a>
        </div>
    </div>
</div>

<script>
    // ─── LOAD CART ───
    let cart = JSON.parse(sessionStorage.getItem('beautify_cart') || '[]');
    if (cart.length === 0) window.location.href = 'cart.php';

    function formatRp(n) { return 'Rp ' + parseInt(n).toLocaleString('id-ID'); }

    // ─── RENDER RINGKASAN ───
    const subtotal = cart.reduce((s, c) => s + c.price * c.qty, 0);
    const ongkir   = subtotal >= 50000 ? 0 : 15000;
    const total    = subtotal + ongkir;

    document.getElementById('coSubtotal').textContent = formatRp(subtotal);
    document.getElementById('coOngkir').textContent   = ongkir === 0 ? 'GRATIS' : formatRp(ongkir);
    document.getElementById('coTotal').textContent    = formatRp(total);
    document.getElementById('btnText').textContent    = `Bayar Sekarang ${formatRp(total)} →`;

    document.getElementById('orderItems').innerHTML = cart.map(item => `
        <div class="order-item">
            <div>
                <div class="order-item-name">${item.name}</div>
                <div class="order-item-qty">${item.qty}x · ${item.brand}</div>
            </div>
            <div class="order-item-price">${formatRp(item.price * item.qty)}</div>
        </div>
    `).join('');

    // ─── TOGGLE E-WALLET INPUT ───
    function showEwallet(type) {
        ['gopay','ovo','dana','transfer'].forEach(t => {
            document.getElementById('input-' + t).classList.remove('show');
        });
        document.getElementById('input-' + type).classList.add('show');
    }

    // ─── VALIDASI ───
    function validate() {
        let valid = true;
        const fields = ['nama', 'telepon', 'alamat', 'kota', 'kodepos'];
        fields.forEach(f => {
            const el  = document.getElementById(f);
            const err = document.getElementById('err-' + f);
            if (!el.value.trim()) {
                el.classList.add('error');
                err.classList.add('show');
                valid = false;
            } else {
                el.classList.remove('error');
                err.classList.remove('show');
            }
        });
        return valid;
    }

    // ─── PROSES BAYAR ───
        function prosesBayar() {
    const subtotal = cart.reduce((s, c) => s + c.price * c.qty, 0);  // ← tambah ini
    const ongkir   = subtotal >= 50000 ? 0 : 15000;                   // ← tambah ini
    const total    = subtotal + ongkir;                                // ← tambah ini

    if (!validate()) {
            // scroll ke error pertama
            document.querySelector('.error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // Simulasi loading
        document.getElementById('btnText').style.display = 'none';
        document.getElementById('spinner').style.display = 'block';
        document.querySelector('.btn-bayar').disabled = true;

        setTimeout(() => {
            // Generate kode order
            const kode = 'BTF-' + Date.now().toString().slice(-6).toUpperCase();
            document.getElementById('orderCode').textContent = kode;

          // Simpan ke riwayat
const pesananLama = JSON.parse(sessionStorage.getItem('beautify_pesanan') || '[]');
pesananLama.unshift({
    kode     : kode,
    tanggal  : new Date().toISOString(),
    items    : [...cart],
    subtotal : subtotal,
    ongkir   : ongkir,
    total    : total,
    nama     : document.getElementById('nama').value,
    pembayaran: document.querySelector('input[name="pembayaran"]:checked').value
});
sessionStorage.setItem('beautify_pesanan', JSON.stringify(pesananLama));

// Kosongkan cart
sessionStorage.removeItem('beautify_cart');

            // Tampilkan modal sukses
            document.getElementById('modalSukses').classList.add('open');
        }, 1800); // simulasi proses 1.8 detik
    }

    // ─── REAL-TIME VALIDASI ───
    ['nama','telepon','alamat','kota','kodepos'].forEach(f => {
        document.getElementById(f).addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('error');
                document.getElementById('err-' + f).classList.remove('show');
            }
        });
    });
</script>
</body>
</html>