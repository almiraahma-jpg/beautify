<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan – Beautify</title>
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

        /* HEADER */
        header { background: var(--pink); padding: 14px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        .header-inner {
            max-width: 900px; margin: auto; padding: 0 20px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .logo { font-family: 'Fraunces', serif; font-size: 26px; font-weight: 600; color: white; text-decoration: none; }
        .logo span { font-style: italic; font-weight: 300; }
        .back-btn { color: white; text-decoration: none; font-size: 13px; font-weight: 600; }
        .back-btn:hover { opacity: 0.8; }

        /* LAYOUT */
        .container { max-width: 900px; margin: 30px auto 40px; padding: 0 20px; }
        .page-title { font-size: 22px; font-weight: 700; margin-bottom: 20px; }

        /* CARD PESANAN */
        .card {
            background: white; border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            margin-bottom: 14px; overflow: hidden;
        }
        .card-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 16px 20px; border-bottom: 1px solid var(--border);
            background: #FAFAFA;
        }
        .pesanan-id { font-size: 14px; font-weight: 700; }
        .pesanan-tanggal { font-size: 12px; color: var(--text-muted); margin-top: 3px; }
        .badge-status {
            background: var(--secondary); color: var(--secondary-text);
            font-size: 11px; font-weight: 700;
            padding: 4px 12px; border-radius: 20px;
        }

        /* ITEMS */
        .card-items { padding: 14px 20px; }
        .order-item {
            display: flex; align-items: center; gap: 14px;
            padding: 10px 0; border-bottom: 1px solid var(--border);
        }
        .order-item:last-child { border-bottom: none; }
        .order-item img {
            width: 56px; height: 56px; border-radius: 8px;
            object-fit: cover; background: #fafafa; flex-shrink: 0;
        }
        .order-item-info { flex: 1; }
        .order-item-name  { font-size: 13px; font-weight: 600; margin-bottom: 2px; }
        .order-item-brand { font-size: 11px; color: var(--text-muted); }
        .order-item-qty   { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
        .order-item-price { font-size: 14px; font-weight: 700; color: var(--pink); white-space: nowrap; }

        /* FOOTER CARD */
        .card-footer {
            padding: 14px 20px; background: #FAFAFA;
            border-top: 1px solid var(--border);
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 10px;
        }
        .payment-info { font-size: 12px; color: var(--text-muted); }
        .payment-info span { font-weight: 600; color: var(--text); }
        .total-section { text-align: right; }
        .ongkir-info  { font-size: 11px; color: var(--text-muted); margin-bottom: 2px; }
        .total-amount { font-size: 18px; font-weight: 700; color: var(--pink); }

        /* DETAIL */
        .order-detail {
            display: none; padding: 14px 20px;
            border-top: 1px solid var(--border);
            background: #FFFDF9; font-size: 13px;
        }
        .order-detail.open { display: block; }
        .detail-row {
            display: flex; justify-content: space-between;
            padding: 5px 0; color: var(--text-muted);
        }
        .detail-row span:last-child { font-weight: 600; color: var(--text); }
        .detail-divider { border: none; border-top: 1px dashed var(--border); margin: 8px 0; }

        /* ACTIONS */
        .card-actions {
            padding: 12px 20px; display: flex; gap: 10px;
            border-top: 1px solid var(--border);
        }
        .btn-beli-lagi {
            background: var(--pink); color: white;
            padding: 8px 18px; border-radius: 8px;
            font-size: 13px; font-weight: 700;
            text-decoration: none; border: none; cursor: pointer;
            transition: background 0.2s;
        }
        .btn-beli-lagi:hover { background: #e07880; }
        .btn-detail-toggle {
            background: var(--secondary); color: var(--secondary-text);
            padding: 8px 18px; border-radius: 8px;
            font-size: 13px; font-weight: 700;
            border: none; cursor: pointer;
            transition: background 0.2s;
        }
        .btn-detail-toggle:hover { background: #c8cba0; }

        /* EMPTY */
        .empty-box {
            text-align: center; padding: 70px 20px;
            background: white; border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .empty-box .icon { font-size: 60px; display: block; margin-bottom: 16px; }
        .empty-box p { font-size: 16px; font-weight: 600; margin-bottom: 8px; }
        .empty-box small { font-size: 13px; color: var(--text-muted); }
        .btn-mulai {
            display: inline-block; margin-top: 20px;
            background: var(--pink); color: white;
            padding: 10px 24px; border-radius: 8px;
            text-decoration: none; font-weight: 700; font-size: 14px;
        }
        .btn-mulai:hover { background: #e07880; }
    </style>
</head>
<body>

<header>
    <div class="header-inner">
        <a href="index.php" class="logo">Beauti<span>fy</span></a>
        <a href="index.php" class="back-btn">← Kembali Belanja</a>
    </div>
</header>

<div class="container">
    <div class="page-title">📦 Riwayat Pesanan</div>
    <div id="pesananList"></div>
</div>

<script>
    const pesanan = JSON.parse(sessionStorage.getItem('beautify_pesanan') || '[]');

    function formatRp(n) {
        return 'Rp ' + parseInt(n).toLocaleString('id-ID');
    }

    function formatTanggal(iso) {
        const d = new Date(iso);
        const bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        return `${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}, ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')} WIB`;
    }

    function toggleDetail(i) {
        const el  = document.getElementById('detail-' + i);
        const btn = document.getElementById('btn-detail-' + i);
        const isOpen = el.classList.toggle('open');
        btn.textContent = isOpen ? '▲ Sembunyikan' : '▼ Lihat Detail';
    }

    function render() {
        const container = document.getElementById('pesananList');

        if (pesanan.length === 0) {
            container.innerHTML = `
                <div class="empty-box">
                    <span class="icon">📦</span>
                    <p>Belum ada pesanan</p>
                    <small>Yuk mulai belanja produk beauty favoritmu!</small><br>
                    <a href="index.php" class="btn-mulai">Mulai Belanja</a>
                </div>`;
            return;
        }

        container.innerHTML = pesanan.map((p, i) => {
            const payLabel = { gopay: 'GoPay', ovo: 'OVO', dana: 'Dana', transfer: 'Transfer Bank' }[p.pembayaran] || p.pembayaran;

            const itemsHTML = p.items.map(item => `
                <div class="order-item">
                    <img src="${item.img}" alt="${item.name}">
                    <div class="order-item-info">
                        <div class="order-item-name">${item.name}</div>
                        <div class="order-item-brand">${item.brand}</div>
                        <div class="order-item-qty">${item.qty} pcs</div>
                    </div>
                    <div class="order-item-price">${formatRp(item.price * item.qty)}</div>
                </div>
            `).join('');

            return `
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="pesanan-id">🧾 ${p.kode}</div>
                        <div class="pesanan-tanggal">📅 ${formatTanggal(p.tanggal)}</div>
                    </div>
                    <span class="badge-status">✅ Diproses</span>
                </div>

                <div class="card-items">${itemsHTML}</div>

                <div class="card-footer">
                    <div class="payment-info">
                        Pembayaran via <span>${payLabel}</span>
                        &nbsp;·&nbsp; ${p.items.reduce((s,c) => s + c.qty, 0)} produk
                    </div>
                    <div class="total-section">
                        <div class="ongkir-info">Ongkir: ${p.ongkir === 0 ? 'GRATIS' : formatRp(p.ongkir)}</div>
                        <div class="total-amount">${formatRp(p.total)}</div>
                    </div>
                </div>

                <div class="order-detail" id="detail-${i}">
                    <div class="detail-row"><span>Nama Penerima</span><span>${p.nama}</span></div>
                    <hr class="detail-divider">
                    <div class="detail-row"><span>Subtotal Produk</span><span>${formatRp(p.subtotal)}</span></div>
                    <div class="detail-row"><span>Ongkos Kirim</span><span>${p.ongkir === 0 ? 'GRATIS' : formatRp(p.ongkir)}</span></div>
                    <div class="detail-row" style="font-weight:700;color:var(--text);">
                        <span>Total Bayar</span><span style="color:var(--pink);">${formatRp(p.total)}</span>
                    </div>
                </div>

                <div class="card-actions">
                    <a href="index.php" class="btn-beli-lagi">🛍 Beli Lagi</a>
                    <button class="btn-detail-toggle" id="btn-detail-${i}" onclick="toggleDetail(${i})">▼ Lihat Detail</button>
                </div>
            </div>`;
        }).join('');
    }

    render();
</script>

</body>
</html>