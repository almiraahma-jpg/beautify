<?php
// ── Ambil produk yang pernah dipesan user langsung di sini ────
session_start();
include 'koneksi.php';

$orderedProducts = [];
$users_id = $_SESSION['user_id'] ?? null;

if ($users_id && $conn) {
    $stmt = $conn->prepare("
        SELECT DISTINCT p.id_product AS id, p.product_name AS name, p.brand
        FROM order_items oi
        JOIN orders o   ON oi.order_id   = o.order_id
        JOIN products p ON oi.product_id = p.id_product
        WHERE o.users_id = ?
        ORDER BY p.product_name ASC
    ");
    if ($stmt) {
        $stmt->bind_param("i", $users_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $orderedProducts[] = $row;
        }
        $stmt->close();
    }
}

// Encode ke JSON untuk dipakai JS
$productsJson = json_encode($orderedProducts);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi – Beautify</title>
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }

        header { background: var(--pink); padding: 14px 0; box-shadow: 0 2px 8px rgba(0,0,0,0.15); position: sticky; top: 0; z-index: 100; }
        .header-inner { max-width: 860px; margin: auto; padding: 0 20px; display: flex; align-items: center; justify-content: space-between; }
        .logo { font-family: 'Fraunces', serif; font-size: 26px; font-weight: 600; color: white; text-decoration: none; }
        .logo span { font-style: italic; font-weight: 300; }
        .back-btn { color: rgba(255,255,255,0.9); text-decoration: none; font-size: 13px; font-weight: 600; }
        .back-btn:hover { color: white; }

        .container { max-width: 860px; margin: 28px auto 48px; padding: 0 20px; }
        .page-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
        .page-head h1 { font-size: 22px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .notif-counter { background: var(--pink); color: white; font-size: 11px; font-weight: 700; padding: 2px 9px; border-radius: 20px; }
        .notif-counter.hidden { display: none; }

        .head-actions { display: flex; gap: 10px; }
        .btn-mark-all { background: white; color: var(--pink); border: 1.5px solid var(--pink-light); padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; transition: background 0.2s, color 0.2s; }
        .btn-mark-all:hover { background: var(--pink); color: white; }
        .btn-clear-all { background: white; color: var(--text-muted); border: 1.5px solid var(--border); padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; }
        .btn-clear-all:hover { background: #f9f0ef; color: var(--pink); border-color: var(--pink-light); }

        .filter-tabs { display: flex; margin-bottom: 18px; background: white; border-radius: 10px; padding: 5px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); overflow-x: auto; }
        .tab-btn { flex: 1; min-width: max-content; padding: 8px 16px; background: none; border: none; cursor: pointer; font-size: 13px; font-weight: 600; color: var(--text-muted); border-radius: 7px; transition: all 0.2s; white-space: nowrap; display: flex; align-items: center; gap: 6px; justify-content: center; }
        .tab-btn.active { background: var(--pink); color: white; }
        .tab-btn:not(.active):hover { background: var(--pink-light); color: var(--text); }
        .tab-dot { display: inline-flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.35); color: white; width: 18px; height: 18px; border-radius: 50%; font-size: 10px; font-weight: 800; }
        .tab-btn:not(.active) .tab-dot { background: var(--pink-light); color: var(--pink); }
        .tab-dot.hidden { display: none; }

        .notif-list { display: flex; flex-direction: column; gap: 10px; }
        .notif-card { background: white; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.07); display: flex; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; position: relative; cursor: pointer; }
        .notif-card:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,0.10); }
        .notif-card.unread { border-left: 4px solid var(--pink); }
        .notif-card.read   { border-left: 4px solid transparent; opacity: 0.85; }
        .notif-icon-wrap { width: 64px; min-width: 64px; display: flex; align-items: flex-start; justify-content: center; padding: 18px 0 18px 14px; }
        .notif-icon { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .notif-icon.checkout { background: #FFF5F6; } .notif-icon.proses { background: #FFF9E8; }
        .notif-icon.kirim    { background: #F0F9FF; } .notif-icon.diterima { background: #F0FFF4; }
        .notif-icon.promo    { background: #F9F0FF; }
        .notif-body { flex: 1; padding: 14px 14px 14px 10px; }
        .notif-title { font-size: 14px; font-weight: 700; margin-bottom: 4px; }
        .notif-msg { font-size: 13px; color: var(--text-muted); line-height: 1.55; }
        .notif-msg .highlight { color: var(--pink); font-weight: 600; }
        .notif-meta { display: flex; align-items: center; gap: 10px; margin-top: 8px; flex-wrap: wrap; }
        .notif-time { font-size: 11px; color: #B0A0A0; }
        .notif-badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 10px; }
        .notif-badge.checkout { background: #F9D0CE; color: #b5606b; } .notif-badge.proses { background: #FFF0C0; color: #8A6800; }
        .notif-badge.kirim    { background: #D0EEFF; color: #1a6a9a; } .notif-badge.diterima { background: #D4F7E0; color: #1a7a40; }
        .notif-badge.promo    { background: #EDD9FF; color: #6b3aa0; }
        .notif-unread-dot { position: absolute; top: 14px; right: 14px; width: 8px; height: 8px; border-radius: 50%; background: var(--pink); }
        .notif-card.read .notif-unread-dot { display: none; }
        .order-code-chip { display: inline-flex; align-items: center; gap: 4px; background: #FFF5F6; border: 1px dashed var(--pink-light); padding: 2px 8px; border-radius: 5px; font-size: 11px; font-weight: 700; color: var(--pink); margin-top: 5px; }

        .empty-state { text-align: center; padding: 70px 20px; background: white; border-radius: 14px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        .empty-state .es-icon { font-size: 56px; display: block; margin-bottom: 14px; }
        .empty-state p { font-size: 16px; font-weight: 600; margin-bottom: 6px; }
        .empty-state small { font-size: 13px; color: var(--text-muted); }

        #toastContainer { position: fixed; top: 80px; right: 20px; display: flex; flex-direction: column; gap: 10px; z-index: 999; pointer-events: none; }
        .toast { background: white; border-radius: 12px; box-shadow: 0 6px 24px rgba(0,0,0,0.14); padding: 14px 18px; min-width: 280px; max-width: 340px; display: flex; align-items: flex-start; gap: 12px; border-left: 4px solid var(--pink); animation: slideInRight 0.35s ease, fadeOutRight 0.4s ease 3.6s forwards; pointer-events: auto; }
        .toast.success { border-left-color: #4CAF50; } .toast.info { border-left-color: #2196F3; }
        .toast-icon { font-size: 22px; flex-shrink: 0; }
        .toast-body { flex: 1; }
        .toast-title { font-size: 13px; font-weight: 700; margin-bottom: 2px; }
        .toast-msg { font-size: 12px; color: var(--text-muted); line-height: 1.45; }
        .toast-close { background: none; border: none; font-size: 16px; cursor: pointer; color: #ccc; padding: 0; margin-left: 4px; }
        @keyframes slideInRight { from { opacity:0; transform:translateX(60px); } to { opacity:1; transform:translateX(0); } }
        @keyframes fadeOutRight { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(60px); } }

        .review-box { background: white; border-radius: 14px; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.07); }
        .review-box label { font-size: 13px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px; }
        .review-select, .review-textarea {
            width: 100%; padding: 9px 12px; border: 1.5px solid var(--border);
            border-radius: 8px; font-family: inherit; font-size: 13px; color: var(--text);
            background: white; outline: none;
        }
        .review-select:focus, .review-textarea:focus { border-color: var(--pink); }
    </style>
</head>
<body>

<header>
    <div class="header-inner">
        <a href="index.php" class="logo">Beauti<span>fy</span></a>
        <a href="index.php" class="back-btn">← Kembali Belanja</a>
    </div>
</header>

<div id="toastContainer"></div>

<div class="container">

    <div class="page-head">
        <h1>🔔 Notifikasi <span class="notif-counter hidden" id="unreadCounter">0</span></h1>
        <div class="head-actions">
            <button class="btn-mark-all" onclick="markAllRead()">✓ Tandai semua dibaca</button>
            <button class="btn-clear-all" onclick="clearAll()">🗑 Hapus semua</button>
        </div>
    </div>

    <div class="filter-tabs">
        <button class="tab-btn active" onclick="setFilter('semua',this)">Semua <span class="tab-dot hidden" id="tab-count-semua">0</span></button>
        <button class="tab-btn" onclick="setFilter('transaksi',this)">🧾 Transaksi <span class="tab-dot hidden" id="tab-count-transaksi">0</span></button>
        <button class="tab-btn" onclick="setFilter('pengiriman',this)">🚚 Pengiriman <span class="tab-dot hidden" id="tab-count-pengiriman">0</span></button>
        <button class="tab-btn" onclick="setFilter('promo',this)">🎁 Promo <span class="tab-dot hidden" id="tab-count-promo">0</span></button>
    </div>

    <div class="notif-list" id="notifList"></div>

    <!-- ── REVIEW SECTION ── -->
    <div style="margin-top:32px;">
        <div class="page-head" style="margin-bottom:16px;">
            <h1 style="font-size:20px;">⭐ Tulis Ulasan Produk</h1>
        </div>
        <div class="review-box">
            <div style="margin-bottom:16px;">
                <label>Pilih Produk dari Pesananmu</label>
                <?php if (empty($orderedProducts)): ?>
                    <div style="font-size:13px;color:var(--text-muted);padding:8px 0;">
                        <?= $users_id
                            ? 'Belum ada produk yang dipesan. Checkout dulu yuk! 🛒'
                            : 'Silakan <a href="login.php" style="color:var(--pink);font-weight:700;">login</a> untuk menulis ulasan.' ?>
                    </div>
                <?php else: ?>
                    <select id="reviewProductSelect" class="review-select">
                        <option value="">-- Pilih produk --</option>
                        <?php foreach ($orderedProducts as $p): ?>
                        <option value="<?= $p['id'] ?>">
                            <?= htmlspecialchars($p['name']) ?><?= $p['brand'] ? ' – ' . htmlspecialchars($p['brand']) : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>
            <div style="margin-bottom:16px;">
                <label>Rating</label>
                <div id="starSelector" style="display:flex;gap:6px;">
                    <?php for($i=1;$i<=5;$i++): ?>
                    <span class="star-opt" data-val="<?= $i ?>" style="font-size:28px;cursor:pointer;opacity:0.3;transition:opacity 0.15s,transform 0.15s;">★</span>
                    <?php endfor; ?>
                </div>
                <input type="hidden" id="reviewRating" value="0">
            </div>
            <div style="margin-bottom:16px;">
                <label>Ulasan</label>
                <textarea id="reviewText" class="review-textarea" rows="3" placeholder="Bagikan pengalamanmu dengan produk ini..."></textarea>
            </div>
            <?php if (!empty($orderedProducts)): ?>
            <button onclick="submitReview()" style="background:var(--pink);color:white;border:none;padding:10px 24px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">⭐ Kirim Ulasan</button>
            <?php endif; ?>
        </div>
        <div id="reviewList" style="margin-top:16px;display:flex;flex-direction:column;gap:10px;"></div>
    </div>

</div>

<script>
// ── STORAGE & HELPERS ─────────────────────────────────────────
function getNotifs() { return JSON.parse(sessionStorage.getItem('beautify_notif') || '[]'); }
function saveNotifs(arr) { sessionStorage.setItem('beautify_notif', JSON.stringify(arr)); }
function genId() { return '_' + Math.random().toString(36).slice(2, 10); }

// ── SEED NOTIF ────────────────────────────────────────────────
function seedFromOrders() {
    const orders = JSON.parse(sessionStorage.getItem('beautify_pesanan') || '[]');
    const notifs = getNotifs();
    const existing = new Set(notifs.filter(n => n.orderCode).map(n => n.orderCode+'_'+n.type));
    let added = false;

    orders.forEach(order => {
        const base = order.kode, ts = new Date(order.tanggal).getTime();
        const pay = {gopay:'GoPay',ovo:'OVO',dana:'Dana',transfer:'Transfer Bank'}[order.pembayaran]||order.pembayaran;
        const total = 'Rp '+parseInt(order.total).toLocaleString('id-ID');
        const qty = order.items?.reduce((s,c)=>s+c.qty,0)||0;

        const seeds = [
            {k:'checkout',type:'checkout',cat:'transaksi',icon:'🧾',cls:'checkout',title:'Pesanan Berhasil Dibuat',msg:`Pesanan <span class="highlight">${total}</span> untuk ${qty} produk berhasil masuk.`,dt:0},
            {k:'proses',  type:'proses',  cat:'transaksi',icon:'✅',cls:'proses',  title:'Pembayaran Dikonfirmasi',msg:`Pembayaran <strong>${base}</strong> via ${pay} dikonfirmasi.`,dt:5*60000},
            {k:'kirim',   type:'kirim',   cat:'pengiriman',icon:'🚚',cls:'kirim',  title:'Pesanan Dalam Perjalanan',msg:`Paketmu sudah dikirim! Estimasi tiba 3–5 hari kerja.`,dt:2*3600000},
            {k:'diterima',type:'diterima',cat:'pengiriman',icon:'🎉',cls:'diterima',title:'Pesanan Telah Diterima!',msg:`Paket <strong>${base}</strong> berhasil diterima. Kasih ulasan ya! ⭐`,dt:3*24*3600000},
        ];
        seeds.forEach(s => {
            if (!existing.has(base+'_'+s.k)) {
                notifs.unshift({id:genId(),type:s.type,category:s.cat,orderCode:base,icon:s.icon,iconClass:s.cls,title:s.title,msg:s.msg,time:ts+s.dt,read:false});
                existing.add(base+'_'+s.k); added=true;
            }
        });
    });

    if (!notifs.some(n=>n.category==='promo')) {
        notifs.push({id:genId(),type:'promo',category:'promo',icon:'🎁',iconClass:'promo',title:'Flash Sale Hari Ini!',msg:'Diskon hingga <span class="highlight">30% OFF</span> untuk lip products & eye makeup.',time:Date.now()-3600000,read:false});
        notifs.push({id:genId(),type:'promo',category:'promo',icon:'🚚',iconClass:'promo',title:'Gratis Ongkir Seharian',msg:'Belanja min. Rp 50.000 gratis ongkos kirim ke seluruh Indonesia!',time:Date.now()-7200000,read:false});
        added=true;
    }
    if (added) { notifs.sort((a,b)=>b.time-a.time); saveNotifs(notifs); }
}

// ── RENDER NOTIF ──────────────────────────────────────────────
let currentFilter = 'semua';

function setFilter(f, btn) {
    currentFilter = f;
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    renderList();
}
function setDot(id, n) {
    const el = document.getElementById(id);
    el.textContent = n;
    el.classList.toggle('hidden', n===0);
}
function renderList() {
    const notifs = getNotifs();
    const uAll  = notifs.filter(n=>!n.read).length;
    const uTr   = notifs.filter(n=>n.category==='transaksi'  && !n.read).length;
    const uPg   = notifs.filter(n=>n.category==='pengiriman' && !n.read).length;
    const uPr   = notifs.filter(n=>n.category==='promo'      && !n.read).length;

    const cEl = document.getElementById('unreadCounter');
    cEl.textContent = uAll; cEl.classList.toggle('hidden', uAll===0);
    setDot('tab-count-semua', uAll); setDot('tab-count-transaksi', uTr);
    setDot('tab-count-pengiriman', uPg); setDot('tab-count-promo', uPr);

    const filtered = currentFilter==='semua' ? notifs : notifs.filter(n=>n.category===currentFilter);
    const el = document.getElementById('notifList');

    if (!filtered.length) {
        el.innerHTML='<div class="empty-state"><span class="es-icon">🔕</span><p>Tidak ada notifikasi</p><small>Notifikasi kamu akan muncul di sini</small></div>';
        return;
    }
    const bmap = {checkout:{l:'🧾 Pesanan',c:'checkout'},proses:{l:'✅ Dikonfirmasi',c:'proses'},kirim:{l:'🚚 Dikirim',c:'kirim'},diterima:{l:'🎉 Diterima',c:'diterima'},promo:{l:'🎁 Promo',c:'promo'}};
    el.innerHTML = filtered.map(n=>{
        const b = bmap[n.type]||{l:'ℹ Info',c:'info'};
        return `<div class="notif-card ${n.read?'read':'unread'}" onclick="markRead('${n.id}')">
            <div class="notif-icon-wrap"><div class="notif-icon ${n.iconClass}">${n.icon}</div></div>
            <div class="notif-body">
                <div class="notif-title">${n.title}</div>
                <div class="notif-msg">${n.msg}</div>
                ${n.orderCode?`<div class="order-code-chip">🧾 ${n.orderCode}</div>`:''}
                <div class="notif-meta"><span class="notif-time">🕐 ${relTime(n.time)}</span><span class="notif-badge ${b.c}">${b.l}</span></div>
            </div>
            ${!n.read?'<div class="notif-unread-dot"></div>':''}
        </div>`;
    }).join('');
}
function relTime(ts) {
    const d=Math.floor((Date.now()-ts)/1000);
    if(d<60)return'Baru saja';if(d<3600)return Math.floor(d/60)+' menit lalu';
    if(d<86400)return Math.floor(d/3600)+' jam lalu';
    const dt=new Date(ts),b=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return `${dt.getDate()} ${b[dt.getMonth()]} ${dt.getFullYear()}`;
}
function markRead(id) { const n=getNotifs(),x=n.find(a=>a.id===id); if(x){x.read=true;saveNotifs(n);renderList();} }
function markAllRead() { saveNotifs(getNotifs().map(n=>({...n,read:true}))); renderList(); showToast('✓','Semua ditandai dibaca','','success'); }
function clearAll() { if(!confirm('Hapus semua notifikasi?'))return; saveNotifs([]); renderList(); showToast('🗑','Notifikasi dihapus','','info'); }

// ── TOAST ─────────────────────────────────────────────────────
function showToast(icon,title,msg,type='') {
    const id=genId(),el=document.createElement('div');
    el.className=`toast ${type}`;el.id=id;
    el.innerHTML=`<div class="toast-icon">${icon}</div><div class="toast-body"><div class="toast-title">${title}</div>${msg?`<div class="toast-msg">${msg}</div>`:''}</div><button class="toast-close" onclick="dismissToast('${id}')">✕</button>`;
    document.getElementById('toastContainer').appendChild(el);
    setTimeout(()=>dismissToast(id),4000);
}
function dismissToast(id){const el=document.getElementById(id);if(el)el.remove();}

// ── REVIEW ────────────────────────────────────────────────────
let selectedRating = 0;

// Star selector
document.querySelectorAll('.star-opt').forEach(star => {
    star.addEventListener('mouseover', ()=>hl(star.dataset.val));
    star.addEventListener('mouseout',  ()=>hl(selectedRating));
    star.addEventListener('click', ()=>{ selectedRating=parseInt(star.dataset.val); document.getElementById('reviewRating').value=selectedRating; hl(selectedRating); });
});
function hl(val) {
    document.querySelectorAll('.star-opt').forEach(s=>{
        s.style.opacity=s.dataset.val<=val?'1':'0.3';
        s.style.transform=s.dataset.val<=val?'scale(1.15)':'scale(1)';
        s.style.color=s.dataset.val<=val?'#FAAF00':'#ccc';
    });
}

function submitReview() {
    const sel = document.getElementById('reviewProductSelect');
    if (!sel) return showToast('⚠️','Pilih produk dulu','','');
    const productId=sel.value, productName=sel.selectedOptions[0]?.text;
    const rating=parseInt(document.getElementById('reviewRating').value);
    const text=document.getElementById('reviewText').value.trim();
    if (!productId) return showToast('⚠️','Pilih produk dulu','','');
    if (!rating)    return showToast('⚠️','Pilih rating bintang','','');
    if (!text)      return showToast('⚠️','Tulis ulasanmu dulu','','');

    const reviews=JSON.parse(sessionStorage.getItem('beautify_reviews')||'[]');
    reviews.unshift({id:genId(),productId,productName,rating,text,time:Date.now()});
    sessionStorage.setItem('beautify_reviews',JSON.stringify(reviews));

    sel.value=''; document.getElementById('reviewText').value='';
    selectedRating=0; hl(0); document.getElementById('reviewRating').value=0;
    showToast('⭐','Ulasan terkirim!',`Rating ${rating}★ untuk ${productName}`,'success');
    renderReviews();
}

function renderReviews() {
    const reviews=JSON.parse(sessionStorage.getItem('beautify_reviews')||'[]');
    const c=document.getElementById('reviewList');
    if(!reviews.length){c.innerHTML='<div style="text-align:center;padding:30px;color:var(--text-muted);font-size:13px;">Belum ada ulasan. Jadilah yang pertama! ⭐</div>';return;}
    const b=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    c.innerHTML=reviews.map(r=>{
        const d=new Date(r.time);
        return `<div style="background:white;border-radius:12px;padding:16px 18px;box-shadow:0 1px 4px rgba(0,0,0,0.07);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                <div><span style="font-size:13px;font-weight:700;">${r.productName}</span><span style="font-size:16px;color:#FAAF00;margin-left:8px;">${'★'.repeat(r.rating)}${'☆'.repeat(5-r.rating)}</span></div>
                <span style="font-size:11px;color:var(--text-muted);">${d.getDate()} ${b[d.getMonth()]} ${d.getFullYear()}</span>
            </div>
            <p style="font-size:13px;color:var(--text-muted);line-height:1.55;margin:0;">${r.text}</p>
        </div>`;
    }).join('');
}

// ── INIT ──────────────────────────────────────────────────────
seedFromOrders();
renderList();
renderReviews();

const jco = sessionStorage.getItem('beautify_just_checkout');
if (jco) { const d=JSON.parse(jco); sessionStorage.removeItem('beautify_just_checkout'); setTimeout(()=>showToast('🎉','Pesanan Berhasil!',`Kode: <strong>${d.kode}</strong>`),400); }
</script>
</body>
</html>