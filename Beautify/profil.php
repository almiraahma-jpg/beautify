<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya – Beautify</title>
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
        header { background: var(--pink); position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        .header-inner { max-width: 1280px; margin: auto; padding: 12px 16px; display: flex; align-items: center; gap: 16px; }
        .logo { font-family: 'Fraunces', serif; font-size: 28px; font-weight: 600; color: white; white-space: nowrap; letter-spacing: -0.5px; text-decoration: none; }
        .logo span { font-style: italic; font-weight: 300; }
        .header-actions { margin-left: auto; display: flex; align-items: center; gap: 20px; color: white; }
        .header-action-btn { display: flex; flex-direction: column; align-items: center; gap: 3px; cursor: pointer; color: white; text-decoration: none; font-size: 11px; }
        .back-link { color: white; font-size: 13px; font-weight: 600; text-decoration: none; opacity: 0.9; }
        .back-link:hover { opacity: 1; }

        /* LAYOUT */
        .container { max-width: 1000px; margin: 28px auto 48px; padding: 0 20px; display: grid; grid-template-columns: 240px 1fr; gap: 24px; align-items: start; }

        /* HERO CARD */
        .hero-card {
            background: linear-gradient(135deg, var(--pink-light) 0%, var(--bg) 100%);
            border-radius: 16px; padding: 28px 20px; text-align: center;
            box-shadow: 0 2px 12px rgba(242,151,160,0.15);
            margin-bottom: 16px; position: relative; overflow: hidden;
        }
        .hero-card::after { content: '💄'; position: absolute; right: 12px; bottom: 8px; font-size: 56px; opacity: 0.1; pointer-events: none; }
        .avatar-wrap { position: relative; display: inline-block; margin-bottom: 14px; }
        .avatar-circle {
            width: 90px; height: 90px; border-radius: 50%;
            background: linear-gradient(135deg, var(--pink), #e8848e);
            display: flex; align-items: center; justify-content: center;
            font-size: 38px; color: white;
            border: 4px solid white; box-shadow: 0 4px 16px rgba(242,151,160,0.35);
            margin: 0 auto;
            overflow: hidden;
        }
        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-edit-btn {
            position: absolute; bottom: 2px; right: 2px;
            width: 28px; height: 28px; border-radius: 50%;
            background: var(--pink); color: white; border: 2px solid white;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; cursor: pointer; box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .hero-name { font-family: 'Fraunces', serif; font-size: 18px; font-weight: 600; color: var(--text); margin-bottom: 4px; }
        .hero-email { font-size: 12px; color: var(--text-muted); }

        /* STATS */
        .stats-row { display: flex; gap: 8px; margin-top: 16px; }
        .stat-box { flex: 1; background: rgba(255,255,255,0.7); border-radius: 10px; padding: 10px 6px; text-align: center; backdrop-filter: blur(4px); }
        .stat-val { font-size: 18px; font-weight: 800; color: var(--text); }
        .stat-label { font-size: 10px; color: var(--text-muted); font-weight: 600; margin-top: 2px; }

        /* SIDEBAR MENU */
        .side-menu { background: white; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        .side-menu a {
            display: flex; align-items: center; gap: 10px;
            padding: 13px 18px; font-size: 13px; font-weight: 600;
            color: var(--text-muted); text-decoration: none;
            border-left: 3px solid transparent; transition: all 0.2s;
        }
        .side-menu a:hover { color: var(--pink); background: #fff5f5; border-left-color: var(--pink); }
        .side-menu a.active { color: var(--pink); background: #fff5f5; border-left-color: var(--pink); }
        .side-menu a.danger { color: #e07880; }
        .side-menu a.danger:hover { background: #fff0f1; border-left-color: #e07880; }
        .side-menu-divider { border: none; border-top: 1px solid var(--border); margin: 4px 0; }

        /* MAIN CARD */
        .main-card { background: white; border-radius: 14px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); overflow: hidden; }
        .main-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 10px; }
        .main-card-header h2 { font-size: 16px; font-weight: 700; }
        .main-card-body { padding: 24px; }

        /* TAB CONTENT */
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }

        /* FORM */
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
        .form-row.one { grid-template-columns: 1fr; }
        .fg { display: flex; flex-direction: column; gap: 6px; }
        .fg label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .fg input, .fg select, .fg textarea {
            padding: 10px 14px; border: 1.5px solid var(--border);
            border-radius: 8px; font-size: 13px; font-family: inherit;
            color: var(--text); background: #fafaf8; outline: none; transition: border 0.2s;
        }
        .fg input:focus, .fg select:focus, .fg textarea:focus { border-color: var(--pink); background: white; }
        .fg textarea { resize: vertical; min-height: 80px; }
        .btn-save {
            background: var(--pink); color: white; border: none;
            padding: 11px 28px; border-radius: 8px; font-size: 13px; font-weight: 700;
            cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
            transition: background 0.2s; font-family: inherit;
        }
        .btn-save:hover { background: #e07880; }

        /* SUCCESS TOAST */
        .toast {
            display: none; position: fixed; top: 20px; right: 20px; z-index: 999;
            background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9;
            padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 700;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1); animation: slideIn 0.3s ease;
        }
        .toast.show { display: flex; align-items: center; gap: 8px; }
        @keyframes slideIn { from { opacity:0; transform: translateX(20px); } to { opacity:1; transform: translateX(0); } }

        /* RIWAYAT TABLE */
        .riwayat-card { background: white; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); margin-bottom: 12px; overflow: hidden; }
        .riwayat-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; background: #FAFAFA; border-bottom: 1px solid var(--border); }
        .riwayat-id { font-size: 13px; font-weight: 700; }
        .riwayat-tgl { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
        .badge-status { background: var(--secondary); color: var(--secondary-text); font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
        .riwayat-items { padding: 12px 18px; }
        .riwayat-item { display: flex; align-items: center; gap: 12px; padding: 8px 0; border-bottom: 1px solid var(--border); }
        .riwayat-item:last-child { border-bottom: none; }
        .riwayat-item img { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; background: #fafafa; }
        .ri-name { font-size: 12px; font-weight: 600; }
        .ri-sub { font-size: 11px; color: var(--text-muted); }
        .ri-price { font-size: 13px; font-weight: 700; color: var(--pink); margin-left: auto; white-space: nowrap; }
        .riwayat-footer { display: flex; justify-content: space-between; align-items: center; padding: 12px 18px; background: #FAFAFA; border-top: 1px solid var(--border); }
        .riwayat-total { font-size: 16px; font-weight: 700; color: var(--pink); }
        .riwayat-meta { font-size: 11px; color: var(--text-muted); }

        /* EMPTY */
        .empty-box { text-align: center; padding: 50px 20px; color: var(--text-muted); }
        .empty-box .icon { font-size: 48px; display: block; margin-bottom: 12px; }
        .empty-box p { font-size: 14px; font-weight: 600; margin-bottom: 6px; }
        .btn-belanja { display: inline-block; margin-top: 16px; background: var(--pink); color: white; padding: 9px 22px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 13px; }
        .btn-belanja:hover { background: #e07880; }

        @media (max-width: 768px) {
            .container { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- HEADER -->
<header>
    <div class="header-inner">
        <a href="index.php" class="logo">Beauti<span>fy</span></a>
        <div class="header-actions">
            <a href="index.php" class="back-link">← Kembali Belanja</a>
        </div>
    </div>
</header>

<!-- TOAST -->
<div class="toast" id="toast">✅ Perubahan berhasil disimpan!</div>

<div class="container">

    <!-- SIDEBAR -->
    <div>
        <!-- HERO -->
        <div class="hero-card">
            <div class="avatar-wrap">
                <div class="avatar-circle" id="avatarCircle">
                    <span id="avatarEmoji">👤</span>
                    <img id="avatarImg" src="" alt="" style="display:none;">
                </div>
                <label for="fotoInput" class="avatar-edit-btn" title="Ganti foto">📷</label>
                <input type="file" id="fotoInput" accept="image/*" style="display:none" onchange="previewFoto(this)">
            </div>
            <div class="hero-name" id="heroName">—</div>
            <div class="hero-email" id="heroEmail">—</div>
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-val" id="statPesanan">0</div>
                    <div class="stat-label">Pesanan</div>
                </div>
                <div class="stat-box">
                    <div class="stat-val" id="statProduk">0</div>
                    <div class="stat-label">Produk</div>
                </div>
                <div class="stat-box">
                    <div class="stat-val" style="font-size:12px;" id="statSpend">Rp 0</div>
                    <div class="stat-label">Belanja</div>
                </div>
            </div>
        </div>

        <!-- MENU -->
        <div class="side-menu">
            <a href="#" class="active" onclick="switchTab('profil', this); return false;">✏️ &nbsp;Edit Profil</a>
            <a href="#" onclick="switchTab('riwayat', this); return false;">📦 &nbsp;Riwayat Pesanan</a>
            <hr class="side-menu-divider">
            <a href="index.php">🛍 &nbsp;Kembali Belanja</a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div>

        <!-- TAB: EDIT PROFIL -->
        <div class="tab-panel active" id="tab-profil">
            <div class="main-card">
                <div class="main-card-header">
                    <span style="font-size:20px;">✏️</span>
                    <h2>Edit Biodata Diri</h2>
                </div>
                <div class="main-card-body">
                    <div class="form-row">
                        <div class="fg">
                            <label>Nama Lengkap</label>
                            <input type="text" id="inputNama" placeholder="Nama lengkap kamu">
                        </div>
                        <div class="fg">
                            <label>Email</label>
                            <input type="email" id="inputEmail" placeholder="email@contoh.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="fg">
                            <label>No. Telepon</label>
                            <input type="text" id="inputTelepon" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="fg">
                            <label>Jenis Kelamin</label>
                            <select id="inputJK">
                                <option value="">-- Pilih --</option>
                                <option value="Perempuan">Perempuan</option>
                                <option value="Laki-laki">Laki-laki</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="fg">
                            <label>Tanggal Lahir</label>
                            <input type="date" id="inputTglLahir">
                        </div>
                        <div class="fg">
                            <label>Kota</label>
                            <input type="text" id="inputKota" placeholder="Surabaya">
                        </div>
                    </div>
                    <div class="form-row one">
                        <div class="fg">
                            <label>Alamat Lengkap</label>
                            <textarea id="inputAlamat" placeholder="Jalan, kelurahan, kecamatan..."></textarea>
                        </div>
                    </div>
                    <button class="btn-save" onclick="simpanProfil()">💾 Simpan Perubahan</button>
                </div>
            </div>
        </div>

        <!-- TAB: RIWAYAT PESANAN -->
        <div class="tab-panel" id="tab-riwayat">
            <div id="riwayatContent"></div>
        </div>

    </div>
</div>

<script>
    // ─── LOAD PROFIL ───
    let profil = JSON.parse(sessionStorage.getItem('beautify_profil') || '{}');

    function loadProfil() {
        document.getElementById('inputNama').value     = profil.nama     || '';
        document.getElementById('inputEmail').value    = profil.email    || '';
        document.getElementById('inputTelepon').value  = profil.telepon  || '';
        document.getElementById('inputJK').value       = profil.jk       || '';
        document.getElementById('inputTglLahir').value = profil.tglLahir || '';
        document.getElementById('inputKota').value     = profil.kota     || '';
        document.getElementById('inputAlamat').value   = profil.alamat   || '';

        document.getElementById('heroName').textContent  = profil.nama  || 'Pengguna Beautify';
        document.getElementById('heroEmail').textContent = profil.email || 'Belum diisi';

        if (profil.foto) {
            document.getElementById('avatarEmoji').style.display = 'none';
            const img = document.getElementById('avatarImg');
            img.src = profil.foto;
            img.style.display = 'block';
        }
    }

    function simpanProfil() {
        profil.nama     = document.getElementById('inputNama').value.trim();
        profil.email    = document.getElementById('inputEmail').value.trim();
        profil.telepon  = document.getElementById('inputTelepon').value.trim();
        profil.jk       = document.getElementById('inputJK').value;
        profil.tglLahir = document.getElementById('inputTglLahir').value;
        profil.kota     = document.getElementById('inputKota').value.trim();
        profil.alamat   = document.getElementById('inputAlamat').value.trim();

        sessionStorage.setItem('beautify_profil', JSON.stringify(profil));
        loadProfil();
        showToast();
    }

    // ─── FOTO ───
    function previewFoto(input) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            profil.foto = e.target.result;
            sessionStorage.setItem('beautify_profil', JSON.stringify(profil));
            document.getElementById('avatarEmoji').style.display = 'none';
            const img = document.getElementById('avatarImg');
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }

    // ─── STATS ───
    function loadStats() {
        const pesanan = JSON.parse(sessionStorage.getItem('beautify_pesanan') || '[]');
        const totalProduk = pesanan.reduce((s, p) => s + p.items.reduce((ss, i) => ss + i.qty, 0), 0);
        const totalSpend  = pesanan.reduce((s, p) => s + (p.total || 0), 0);

        document.getElementById('statPesanan').textContent = pesanan.length;
        document.getElementById('statProduk').textContent  = totalProduk;
        document.getElementById('statSpend').textContent   = 'Rp ' + parseInt(totalSpend).toLocaleString('id-ID');
    }

    // ─── RIWAYAT ───
    function loadRiwayat() {
        const pesanan   = JSON.parse(sessionStorage.getItem('beautify_pesanan') || '[]');
        const container = document.getElementById('riwayatContent');

        if (pesanan.length === 0) {
            container.innerHTML = `
                <div class="main-card">
                    <div class="main-card-header"><span style="font-size:20px">📦</span><h2>Riwayat Pesanan</h2></div>
                    <div class="main-card-body">
                        <div class="empty-box">
                            <span class="icon">📦</span>
                            <p>Belum ada pesanan</p>
                            <small>Yuk mulai belanja produk beauty favoritmu!</small><br>
                            <a href="index.php" class="btn-belanja">Mulai Belanja</a>
                        </div>
                    </div>
                </div>`;
            return;
        }

        function formatRp(n) { return 'Rp ' + parseInt(n).toLocaleString('id-ID'); }
        function formatTgl(iso) {
            const d = new Date(iso);
            const bl = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            return `${d.getDate()} ${bl[d.getMonth()]} ${d.getFullYear()}, ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')} WIB`;
        }
        const payLabel = { gopay:'GoPay', ovo:'OVO', dana:'Dana', transfer:'Transfer Bank' };

        container.innerHTML = `
            <div class="main-card" style="margin-bottom:0;">
                <div class="main-card-header"><span style="font-size:20px">📦</span><h2>Riwayat Pesanan</h2></div>
            </div>
            <div style="margin-top:12px;">
            ${pesanan.map(p => `
                <div class="riwayat-card">
                    <div class="riwayat-header">
                        <div>
                            <div class="riwayat-id">🧾 ${p.kode}</div>
                            <div class="riwayat-tgl">📅 ${formatTgl(p.tanggal)}</div>
                        </div>
                        <span class="badge-status">✅ Diproses</span>
                    </div>
                    <div class="riwayat-items">
                        ${p.items.map(item => `
                            <div class="riwayat-item">
                                <img src="${item.img}" alt="${item.name}">
                                <div>
                                    <div class="ri-name">${item.name}</div>
                                    <div class="ri-sub">${item.brand} · ${item.qty} pcs</div>
                                </div>
                                <div class="ri-price">${formatRp(item.price * item.qty)}</div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="riwayat-footer">
                        <div class="riwayat-meta">
                            via ${payLabel[p.pembayaran] || p.pembayaran}
                            · Ongkir: ${p.ongkir === 0 ? 'GRATIS' : formatRp(p.ongkir)}
                        </div>
                        <div class="riwayat-total">${formatRp(p.total)}</div>
                    </div>
                </div>
            `).join('')}
            </div>`;
    }

    // ─── TAB SWITCH ───
    function switchTab(name, el) {
        document.querySelectorAll('.tab-panel').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.side-menu a').forEach(a => a.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        el.classList.add('active');
        if (name === 'riwayat') loadRiwayat();
    }

    // ─── TOAST ───
    function showToast() {
        const t = document.getElementById('toast');
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 2800);
    }

    // ─── INIT ───
    loadProfil();
    loadStats();
</script>

</body>
</html>