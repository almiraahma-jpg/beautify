<?php
session_start();
include 'koneksi.php';

$isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';

$stmt = $conn->prepare("
    SELECT p.*, c.category_name, p.sold_count
    FROM products p
    JOIN categories c ON p.category_id = c.id_category
    ORDER BY RAND()
");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beautify – Premium Beauty Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Fraunces:ital,wght@0,300;0,600;1,300&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --pink:#F297A0; --pink-light:#F9D0CE; --orange:#F297A0;
            --bg:#F3EBD8; --text:#3B2A2B; --text-muted:#8A7070;
            --border:#EDD9CC; --card-radius:10px;
            --secondary:#DCDFBA; --secondary-text:#5A5E3A;
        }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--bg); color:var(--text); font-size:14px; }

        /* TOPBAR */
        .topbar { background:var(--pink); color:white; font-size:12px; padding:6px 0; }
        .topbar-inner { max-width:1280px; margin:auto; padding:0 16px; display:flex; justify-content:space-between; align-items:center; }
        .topbar a { color:rgba(255,255,255,0.85); text-decoration:none; }
        .topbar a:hover { color:white; }
        .topbar-links { display:flex; gap:16px; align-items:center; }
        .topbar-links span { opacity:0.5; }
        .notif-link { display:inline-flex; align-items:center; gap:4px; }
        .notif-badge-dot { display:none; background:#DCDFBA; color:#5A5E3A; font-size:10px; font-weight:800; min-width:17px; height:17px; border-radius:50%; align-items:center; justify-content:center; padding:0 3px; }

        /* HEADER */
        header { background:var(--pink); position:sticky; top:0; z-index:100; box-shadow:0 2px 8px rgba(0,0,0,0.15); }
        .header-inner { max-width:1280px; margin:auto; padding:12px 16px; display:flex; align-items:center; gap:16px; }
        .logo { font-family:'Fraunces',serif; font-size:28px; font-weight:600; color:white; white-space:nowrap; letter-spacing:-0.5px; }
        .logo span { font-style:italic; font-weight:300; }
        .search-bar { flex:1; display:flex; background:white; border-radius:4px; overflow:hidden; height:40px; }
        .search-bar input { flex:1; border:none; outline:none; padding:0 14px; font-size:14px; font-family:inherit; }
        .search-bar button { background:var(--orange); border:none; color:white; padding:0 20px; cursor:pointer; display:flex; align-items:center; transition:background 0.2s; }
        .search-bar button:hover { background:#e07880; }
        .header-actions { display:flex; align-items:center; gap:20px; color:white; }
        .header-action-btn { display:flex; flex-direction:column; align-items:center; gap:3px; cursor:pointer; color:white; text-decoration:none; font-size:11px; position:relative; }
        .header-action-btn svg { width:22px; height:22px; }
        .cart-badge { position:absolute; top:-6px; right:-8px; background:#DCDFBA; color:#5A5E3A; font-size:10px; font-weight:700; width:18px; height:18px; border-radius:50%; display:flex; align-items:center; justify-content:center; }

        /* PROFILE DROPDOWN */
        .profile-wrapper { position:relative; }
        .profile-trigger { display:flex; flex-direction:column; align-items:center; gap:3px; cursor:pointer; color:white; font-size:11px; user-select:none; }
        .profile-trigger svg { width:22px; height:22px; }
        .profile-dropdown { display:none; position:absolute; top:calc(100% + 14px); right:-10px; background:white; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.18); min-width:190px; padding:8px 0; z-index:300; color:var(--text); animation:dropFade 0.18s ease; }
        @keyframes dropFade { from{opacity:0;transform:translateY(-6px);}to{opacity:1;transform:translateY(0);} }
        .profile-dropdown.open { display:block; }
        .profile-dropdown::before { content:''; position:absolute; top:-6px; right:22px; width:12px; height:12px; background:white; transform:rotate(45deg); box-shadow:-2px -2px 5px rgba(0,0,0,0.06); z-index:-1; }
        .profile-dropdown a { display:flex; align-items:center; gap:10px; padding:10px 18px; font-size:13px; color:var(--text); text-decoration:none; font-weight:500; transition:background 0.15s,color 0.15s; }
        .profile-dropdown a:hover { background:#FFF0F1; color:var(--pink); }
        .profile-dropdown .dropdown-divider { margin:6px 0; border:none; border-top:1px solid #F3EBD8; }
        .profile-dropdown .logout-link { color:var(--pink); }

        /* NAV */
        nav.category-nav { background:white; border-bottom:1px solid var(--border); }
        .nav-inner { max-width:1280px; margin:auto; padding:0 16px; display:flex; }
        .nav-inner a { display:block; padding:12px 16px; text-decoration:none; color:var(--text); font-size:13px; font-weight:500; border-bottom:2px solid transparent; white-space:nowrap; transition:all 0.2s; }
        .nav-inner a:hover,.nav-inner a.active { color:var(--pink); border-bottom-color:var(--pink); }

        .container { max-width:1280px; margin:auto; padding:16px; }

        /* HERO */
        .hero-section { display:grid; grid-template-columns:1fr 200px; gap:12px; margin-bottom:16px; }
        .hero-main { background:linear-gradient(135deg,#F297A0 0%,#F9D0CE 60%,#F3EBD8 100%); border-radius:var(--card-radius); overflow:hidden; position:relative; height:280px; display:flex; align-items:center; }
        .hero-content { padding:36px 40px; color:#3B2A2B; flex:1; position:relative; z-index:2; }
        .hero-content .eyebrow { font-size:11px; font-weight:600; letter-spacing:3px; text-transform:uppercase; color:#F297A0; margin-bottom:10px; }
        .hero-content h2 { font-family:'Fraunces',serif; font-size:42px; font-weight:300; line-height:1.15; margin-bottom:8px; }
        .hero-content h2 em { font-style:italic; font-weight:600; }
        .hero-content p { font-size:14px; color:#7A5A5C; margin-bottom:24px; }
        .btn-hero { display:inline-block; background:#F297A0; color:white; padding:10px 24px; border-radius:20px; font-weight:700; font-size:13px; text-decoration:none; transition:transform 0.2s,box-shadow 0.2s; box-shadow:0 4px 12px rgba(242,151,160,0.35); }
        .btn-hero:hover { transform:translateY(-2px); }
        .hero-img { position:absolute; right:-20px; bottom:0; height:95%; object-fit:cover; z-index:1; opacity:0.25; }
        .hero-side { display:flex; flex-direction:column; gap:12px; }
        .mini-banner { flex:1; border-radius:var(--card-radius); padding:18px; display:flex; flex-direction:column; justify-content:flex-end; color:#3B2A2B; font-weight:700; font-size:13px; cursor:pointer; transition:filter 0.2s; }
        .mini-banner:hover { filter:brightness(1.05); }
        .mini-banner.a { background:linear-gradient(135deg,#F297A0,#F9D0CE); }
        .mini-banner.b { background:linear-gradient(135deg,#DCDFBA,#c8cba0); }
        .mini-banner .mini-tag { font-size:10px; font-weight:600; letter-spacing:1px; opacity:0.75; margin-bottom:4px; }

        /* FLASH SALE */
        .section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; flex-wrap:wrap; gap:10px; }
        .section-title { display:flex; align-items:center; gap:12px; font-weight:700; font-size:18px; }
        .flash-timer { display:flex; align-items:center; gap:4px; font-size:12px; }
        .timer-block { background:#F297A0; color:white; padding:2px 8px; border-radius:4px; font-weight:700; font-size:13px; min-width:28px; text-align:center; }
        .flash-timer .sep { color:#F297A0; font-weight:700; }
        .see-all { color:var(--pink); text-decoration:none; font-size:13px; font-weight:600; }
        .see-all:hover { text-decoration:underline; }

        /* CATEGORY PILLS */
        .category-pills { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; background:white; padding:30px; border-radius:16px; }
        .cat-pill { display:flex; flex-direction:column; align-items:center; gap:8px; cursor:pointer; text-decoration:none; transition:transform 0.2s; }
        .cat-pill:hover { transform:translateY(-3px); }
        .cat-icon { width:60px; height:60px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:26px; }
        .cat-icon.pink { background:#FFECE8; } .cat-icon.purple { background:#F3E8FF; }
        .cat-icon.blue { background:#E8F4FF; } .cat-icon.yellow { background:#FFFCE8; }
        .cat-pill span { font-size:12px; font-weight:600; color:var(--text); text-align:center; }

        /* PRODUCT CARD */
        .product-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; }
        .product-card { background:white; border-radius:var(--card-radius); overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,0.06); transition:box-shadow 0.25s,transform 0.25s; cursor:pointer; position:relative; }
        .product-card:hover { box-shadow:0 4px 20px rgba(0,0,0,0.12); transform:translateY(-3px); }
        .product-img-wrap { position:relative; background:#FAFAFA; aspect-ratio:1; overflow:hidden; }
        .product-img-wrap img { width:100%; height:100%; object-fit:cover; transition:transform 0.35s; }
        .product-card:hover .product-img-wrap img { transform:scale(1.06); }
        .cart-quick-btn { position:absolute; top:8px; right:8px; background:rgba(255,255,255,0.92); border:none; border-radius:50%; width:34px; height:34px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:16px; opacity:0; transition:opacity 0.2s,background 0.2s,transform 0.15s; box-shadow:0 2px 6px rgba(0,0,0,0.12); }
        .product-card:hover .cart-quick-btn { opacity:1; }
        .cart-quick-btn:hover { background:var(--pink); transform:scale(1.1); }
        .badge-label { position:absolute; top:8px; left:8px; padding:3px 8px; border-radius:3px; font-size:11px; font-weight:700; }
        .badge-label.sale { background:var(--pink); color:white; }
        .badge-label.star { background:var(--secondary); color:var(--secondary-text); }
        .product-info { padding:10px 12px 12px; }
        .product-brand { font-size:11px; color:var(--text-muted); margin-bottom:4px; }
        .product-name { font-size:13px; color:var(--text); line-height:1.45; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; min-height:38px; margin-bottom:6px; }
        .price-row { margin-top:4px; }
        .price-original { font-size:11px; color:#AAAAAA; text-decoration:line-through; }
        .price-main { font-size:16px; font-weight:700; color:var(--pink); line-height:1.2; }
        .discount-tag { display:inline-block; background:#F9D0CE; color:#b5606b; font-size:11px; font-weight:700; padding:2px 5px; border-radius:3px; margin-left:4px; }
        .product-meta { display:flex; align-items:center; justify-content:space-between; margin-top:6px; }
        .rating { font-size:11px; color:#FAAF00; display:flex; align-items:center; gap:2px; }
        .rating span { color:var(--text-muted); font-size:11px; }
        .location-tag { font-size:11px; color:var(--text-muted); }
        .admin-actions { display:flex; gap:6px; padding:8px 12px 10px; border-top:1px solid var(--border); opacity:0; transition:opacity 0.2s; }
        .product-card:hover .admin-actions { opacity:1; }
        .btn-edit,.btn-delete { flex:1; text-align:center; padding:7px 4px; border-radius:5px; font-size:12px; font-weight:600; text-decoration:none; }
        .btn-edit { background:#DCDFBA; color:#5A5E3A; border:1px solid #c8cba0; }
        .btn-edit:hover { background:#c8cba0; }
        .btn-delete { background:var(--pink); color:white; border:none; cursor:pointer; }
        .btn-delete:hover { background:#e07880; }

        .section-panel { background:white; border-radius:var(--card-radius); padding:18px 16px; margin-bottom:16px; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
        .btn-add-product { display:inline-flex; align-items:center; gap:6px; background:var(--pink); color:white; padding:8px 18px; border-radius:5px; font-size:13px; font-weight:600; text-decoration:none; }
        .btn-add-product:hover { background:#e07880; }

        .search-status { display:none; align-items:center; gap:10px; margin-bottom:12px; padding:10px 14px; background:#FFF0F1; border-radius:8px; font-size:13px; color:#b5606b; font-weight:500; }
        .search-status.show { display:flex; }
        .btn-clear-search { background:#F297A0; color:white; border:none; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:700; cursor:pointer; }
        .search-loading { display:none; text-align:center; padding:40px 0; color:#8A7070; font-size:13px; }
        .search-loading.show { display:block; }

        .promo-strip { background:white; border-radius:var(--card-radius); padding:14px 20px; margin-bottom:16px; display:flex; align-items:center; justify-content:space-between; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
        .promo-item { display:flex; align-items:center; gap:10px; font-size:13px; font-weight:600; }
        .promo-icon { font-size:22px; }
        .promo-sub { font-size:11px; color:var(--text-muted); font-weight:400; }

        /* CART SIDEBAR */
        .cart-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:200; }
        .cart-overlay.open { display:block; }
        .cart-sidebar { position:fixed; top:0; right:-400px; width:360px; height:100vh; background:white; z-index:201; display:flex; flex-direction:column; box-shadow:-4px 0 24px rgba(0,0,0,0.12); transition:right 0.3s ease; }
        .cart-sidebar.open { right:0; }
        .cart-header-side { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
        .cart-header-side h3 { font-size:16px; font-weight:700; }
        .btn-close-cart { background:none; border:none; font-size:22px; cursor:pointer; color:var(--text-muted); }
        .cart-items-list { flex:1; overflow-y:auto; padding:12px 16px; }
        .cart-item-row { display:flex; gap:12px; padding:10px 0; border-bottom:1px solid var(--border); align-items:center; }
        .cart-item-row img { width:60px; height:60px; object-fit:cover; border-radius:8px; flex-shrink:0; }
        .cart-item-details { flex:1; min-width:0; }
        .cart-item-details .item-name { font-size:12px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:2px; }
        .cart-item-details .item-brand { font-size:10px; color:var(--text-muted); }
        .cart-item-details .item-price { font-size:13px; font-weight:700; color:var(--pink); margin-top:2px; }
        .qty-control { display:flex; align-items:center; gap:6px; margin-top:5px; }
        .qty-control button { width:22px; height:22px; border-radius:4px; border:1px solid var(--border); background:#f9f9f9; cursor:pointer; font-size:14px; font-weight:700; display:flex; align-items:center; justify-content:center; }
        .qty-control button:hover { background:#F9D0CE; border-color:var(--pink); }
        .qty-control .qty-num { font-size:13px; font-weight:700; min-width:20px; text-align:center; }
        .btn-remove-item { background:none; border:none; color:#ccc; cursor:pointer; font-size:18px; padding:4px; }
        .btn-remove-item:hover { color:var(--pink); }
        .cart-footer-side { padding:14px 20px; border-top:1px solid var(--border); }
        .cart-subtotal { display:flex; justify-content:space-between; font-size:12px; color:var(--text-muted); margin-bottom:4px; }
        .cart-total-row { display:flex; justify-content:space-between; font-size:16px; font-weight:700; margin-bottom:14px; }
        .btn-checkout-main { display:block; width:100%; background:var(--pink); color:white; text-align:center; padding:12px; border-radius:8px; font-size:14px; font-weight:700; text-decoration:none; border:none; cursor:pointer; }
        .btn-checkout-main:hover { background:#e07880; }
        .empty-cart-msg { text-align:center; padding:50px 0; color:var(--text-muted); }
        .empty-cart-msg .ec-icon { font-size:44px; display:block; margin-bottom:10px; }
        .empty-cart-msg p { font-size:13px; }

        /* FOOTER */
        footer { background:white; border-top:1px solid var(--border); margin-top:32px; }
        .footer-main { max-width:1280px; margin:auto; padding:32px 16px; display:grid; grid-template-columns:1.5fr 1fr 1fr; gap:32px; }
        .footer-brand p { font-size:12px; color:var(--text-muted); line-height:1.7; }
        .footer-col h4 { font-size:13px; font-weight:700; margin-bottom:14px; }
        .footer-col a { display:block; font-size:12px; color:var(--text-muted); text-decoration:none; margin-bottom:8px; }
        .footer-col a:hover { color:var(--pink); }
        .footer-bottom { border-top:1px solid var(--border); padding:14px 16px; text-align:center; font-size:12px; color:var(--text-muted); max-width:1280px; margin:auto; }
        .payment-icons { display:flex; gap:8px; flex-wrap:wrap; margin-top:12px; }
        .pay-tag { background:#F9D0CE; border:1px solid #f0b8bc; padding:4px 10px; border-radius:4px; font-size:11px; font-weight:600; color:#b5606b; }

        /* ══════════════════════════
           PRODUCT DETAIL MODAL
        ══════════════════════════ */
        .pm-overlay {
            display:none; position:fixed; inset:0;
            background:rgba(0,0,0,0.6); z-index:500;
            align-items:center; justify-content:center; padding:16px;
        }
        .pm-overlay.open { display:flex; }
        .pm-box {
            background:white; border-radius:20px;
            width:100%; max-width:820px; max-height:90vh;
            overflow:hidden; display:grid; grid-template-columns:1fr 1fr;
            position:relative;
            animation:pmPop 0.28s cubic-bezier(.34,1.56,.64,1);
        }
        @keyframes pmPop { from{opacity:0;transform:scale(0.88);}to{opacity:1;transform:scale(1);} }
        .pm-close { position:absolute; top:12px; right:12px; z-index:10; width:34px; height:34px; border-radius:50%; background:rgba(0,0,0,0.2); border:none; color:white; font-size:18px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s; }
        .pm-close:hover { background:rgba(0,0,0,0.45); }
        .pm-img { width:100%; height:100%; object-fit:cover; border-radius:20px 0 0 20px; display:block; min-height:380px; }
        .pm-body { padding:28px 24px; display:flex; flex-direction:column; gap:12px; overflow-y:auto; }
        .pm-brand { font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:1px; }
        .pm-name  { font-size:20px; font-weight:800; line-height:1.3; color:var(--text); }
        .pm-rating { display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-muted); }
        .pm-stars  { color:#FAAF00; font-size:14px; }
        .pm-price-row { display:flex; align-items:baseline; gap:10px; flex-wrap:wrap; }
        .pm-price-ori  { font-size:13px; color:#bbb; text-decoration:line-through; }
        .pm-price-main { font-size:26px; font-weight:800; color:var(--pink); }
        .pm-disc       { background:#F9D0CE; color:#b5606b; font-size:12px; font-weight:700; padding:3px 9px; border-radius:5px; }
        .pm-tags { display:flex; gap:6px; flex-wrap:wrap; }
        .pm-tag  { font-size:11px; font-weight:700; padding:4px 10px; border-radius:5px; }
        .pm-tag.pink  { background:#F9D0CE; color:#b5606b; }
        .pm-tag.green { background:var(--secondary); color:var(--secondary-text); }

        /* DESKRIPSI — INI YANG PENTING */
        .pm-desc-box {
            background: #FAFAFA;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px;
        }
        .pm-desc-label {
            font-size: 11px; font-weight: 700;
            color: var(--text-muted); text-transform: uppercase;
            letter-spacing: 0.5px; margin-bottom: 6px;
        }
        .pm-desc-text {
            font-size: 13px; color: var(--text); line-height: 1.7;
        }
        .pm-desc-text.empty { color: #bbb; font-style: italic; }

        .pm-divider { border:none; border-top:1px solid var(--border); }
        .pm-stock-row { display:flex; align-items:center; gap:8px; font-size:13px; }
        .pm-stock-label { color:var(--text-muted); }
        .pm-stock-num { font-size:15px; font-weight:800; color:var(--text); }
        .pm-stock-num.low { color:#e07880; }
        .pm-loading { font-size:12px; color:var(--text-muted); display:none; }
        .pm-loading.show { display:block; }

        /* ADMIN EDIT */
        .pm-admin-box { background:#FFF5F6; border:1px solid var(--pink-light); border-radius:10px; padding:14px; }
        .pm-admin-title { font-size:12px; font-weight:700; color:var(--pink); margin-bottom:10px; }
        .pm-admin-grid { display:grid; grid-template-columns:100px 1fr; gap:8px; }
        .pm-admin-field label { font-size:11px; font-weight:600; color:var(--text-muted); display:block; margin-bottom:4px; }
        .pm-admin-field input,
        .pm-admin-field textarea {
            width:100%; padding:7px 10px; border:1.5px solid var(--border); border-radius:7px;
            font-family:inherit; font-size:13px; color:var(--text); background:white; outline:none;
        }
        .pm-admin-field input:focus,
        .pm-admin-field textarea:focus { border-color:var(--pink); }
        .pm-admin-field textarea { resize:vertical; min-height:56px; }
        .pm-save-btn { margin-top:8px; background:var(--pink); color:white; border:none; padding:8px 20px; border-radius:7px; font-size:12px; font-weight:700; cursor:pointer; transition:background 0.2s; }
        .pm-save-btn:hover { background:#e07880; }
        .pm-save-btn:disabled { background:#ccc; cursor:not-allowed; }
        .pm-save-msg { font-size:11px; font-weight:600; margin-top:6px; display:none; }
        .pm-save-msg.ok  { display:block; color:#4CAF50; }
        .pm-save-msg.err { display:block; color:#e07880; }

        /* QTY */
        .pm-qty-label { font-size:13px; font-weight:700; color:var(--text-muted); }
        .pm-qty { display:flex; align-items:center; gap:12px; }
        .pm-qty-btn { width:34px; height:34px; border-radius:9px; border:1.5px solid var(--border); background:#f9f9f9; font-size:20px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.15s; line-height:1; }
        .pm-qty-btn:hover { background:#F9D0CE; border-color:var(--pink); }
        .pm-qty-num { font-size:18px; font-weight:800; min-width:32px; text-align:center; }
        .pm-btn-cart { width:100%; padding:14px; background:var(--pink); color:white; border:none; border-radius:12px; font-size:16px; font-weight:700; cursor:pointer; transition:background 0.2s,transform 0.15s; margin-top:auto; }
        .pm-btn-cart:hover { background:#e07880; transform:translateY(-1px); }
        .pm-btn-cart:disabled { background:#ccc; cursor:not-allowed; transform:none; }

        @media (max-width:620px) { .pm-box { grid-template-columns:1fr; max-height:95vh; overflow-y:auto; } .pm-img { min-height:220px; max-height:240px; border-radius:20px 20px 0 0; } .pm-body { padding:18px; } }
        @media (max-width:1024px) { .product-grid { grid-template-columns:repeat(4,1fr); } .hero-section { grid-template-columns:1fr; } .hero-side { flex-direction:row; } }
        @media (max-width:768px) { .product-grid { grid-template-columns:repeat(2,1fr); } .category-pills { grid-template-columns:repeat(2,1fr); } .footer-main { grid-template-columns:1fr 1fr; } .promo-strip { flex-wrap:wrap; gap:12px; } .cart-sidebar { width:100%; right:-100%; } }
    </style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
    <div class="topbar-inner">
        <div class="topbar-links">
            <a href="#">Bantuan</a><span>|</span>
            <a href="notifikasi.php" class="notif-link">
                🔔 Notifikasi
                <span id="notifBadge" class="notif-badge-dot">0</span>
            </a>
            <span>|</span>
            <?php if(isset($_SESSION['user_id'])): ?>
                <span style="color:rgba(255,255,255,0.85);">Halo, <?= htmlspecialchars($_SESSION['user_nama'] ?? 'User') ?>!</span>
                <span>|</span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Masuk / Daftar</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- HEADER -->
<header>
    <div class="header-inner">
        <div class="logo">Beauti<span>fy</span></div>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Cari produk, merek, kategori..." autocomplete="off">
            <button onclick="doSearch()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
        </div>
        <div class="header-actions">
            <a href="cart.php" class="header-action-btn">
                <div style="position:relative;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    <span class="cart-badge" id="cartBadge">0</span>
                </div>
                <span>Keranjang</span>
            </a>
            <div class="profile-wrapper" id="profileWrapper">
                <div class="profile-trigger" onclick="toggleProfile()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span><?= isset($_SESSION['user_id']) ? htmlspecialchars($_SESSION['user_nama']) : 'Akun' ?></span>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div style="padding:12px 18px 8px;border-bottom:1px solid #F3EBD8;margin-bottom:4px;">
                            <div style="font-size:13px;font-weight:700;"><?= htmlspecialchars($_SESSION['user_nama']) ?></div>
                            <div style="font-size:11px;color:#aaa;margin-top:2px;"><?= htmlspecialchars($_SESSION['user_email']) ?></div>
                        </div>
                        <a href="/beautify/profil.php">👤 &nbsp;Profil Saya</a>
                        <a href="riwayat_pesanan.php">📦 &nbsp;Pesanan Saya</a>
                        <?php if($isAdmin): ?>
                        <a href="/beautify/pages/admin/dashboard.php">⚙️ &nbsp;Panel Admin</a>
                        <?php endif; ?>
                        <hr class="dropdown-divider">
                        <a href="/beautify/logout.php" class="logout-link">🚪 &nbsp;Keluar</a>
                    <?php else: ?>
                        <a href="/beautify/login.php">🚪 &nbsp;Masuk</a>
                        <a href="/beautify/register.php">📝 &nbsp;Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- CATEGORY NAV -->
<nav class="category-nav">
    <div class="nav-inner">
        <a href="index.php" class="active">Home</a>
        <a href="kategori.php?cat=flash-sale">Flash Sale</a>
        <a href="kategori.php?cat=best-seller">Best Seller</a>
        <a href="kategori.php?cat=complexion">Complexion</a>
        <a href="kategori.php?cat=lip-products">Lip Products</a>
        <a href="kategori.php?cat=eye-makeup">Eye Makeup</a>
        <a href="kategori.php?cat=eyebrow">Eyebrow</a>
    </div>
</nav>

<div class="container">

    <!-- HERO -->
    <div style="margin-bottom:16px;">
        <div class="hero-section">
            <div class="hero-main">
                <div class="hero-content">
                    <div class="eyebrow">✨ New Arrival 2026</div>
                    <h2>Glow <em>Naturally</em>.<br>Shine Confidently.</h2>
                    <p>Produk premium beauty yang menonjolkan kecantikan alami Anda</p>
                    <a href="#produk" class="btn-hero">Belanja Sekarang →</a>
                </div>
                <img src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=600&q=80" class="hero-img" alt="">
            </div>
            <div class="hero-side">
                <div class="mini-banner a"><div class="mini-tag">EXCLUSIVE</div><div>Lip Collection</div><div style="font-weight:400;font-size:11px;opacity:0.8;">Up to 30% OFF</div></div>
                <div class="mini-banner b"><div class="mini-tag">GRATIS ONGKIR</div><div>Min. Belanja 50rb</div><div style="font-weight:400;font-size:11px;opacity:0.8;">Seluruh Indonesia</div></div>
            </div>
        </div>
    </div>

    <!-- PROMO STRIP -->
    <div class="promo-strip">
        <div class="promo-item"><span class="promo-icon">🚚</span><div><div>Gratis Ongkir</div><div class="promo-sub">Min. pembelian Rp 50.000</div></div></div>
        <div class="promo-item"><span class="promo-icon">🔄</span><div><div>Retur Mudah</div><div class="promo-sub">7 hari retur gratis</div></div></div>
        <div class="promo-item"><span class="promo-icon">🔒</span><div><div>Belanja Aman</div><div class="promo-sub">Uang kembali 100%</div></div></div>
        <div class="promo-item"><span class="promo-icon">🎁</span><div><div>Member Rewards</div><div class="promo-sub">Poin setiap pembelian</div></div></div>
    </div>

    <!-- CATEGORIES -->
    <div class="category-pills" style="margin-bottom:16px;">
        <a href="kategori.php?cat=lip-products" class="cat-pill"><div class="cat-icon pink">💄</div><span>Lip Products</span></a>
        <a href="kategori.php?cat=eye-makeup"   class="cat-pill"><div class="cat-icon purple">👁</div><span>Eye Makeup</span></a>
        <a href="kategori.php?cat=complexion"   class="cat-pill"><div class="cat-icon blue">✨</div><span>Complexion</span></a>
        <a href="kategori.php?cat=eyebrow"      class="cat-pill"><div class="cat-icon yellow">🤎</div><span>Eyebrow</span></a>
    </div>

    <!-- FLASH SALE + PRODUCTS -->
    <div id="produk" class="section-panel">
        <div class="section-header">
            <div class="section-title">
                <span>⚡</span><span>Flash Sale</span>
                <div class="flash-timer">
                    <span class="timer-block" id="t-h">02</span><span class="sep">:</span>
                    <span class="timer-block" id="t-m">45</span><span class="sep">:</span>
                    <span class="timer-block" id="t-s">30</span>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:14px;">
                <?php if($isAdmin): ?>
                <a href="tambah_produk.php" class="btn-add-product">+ Tambah Produk</a>
                <?php endif; ?>
                <a href="kategori.php?cat=flash-sale" class="see-all">Lihat Semua →</a>
            </div>
        </div>

        <div class="search-status" id="searchStatus">
            <span id="searchStatusText"></span>
            <button class="btn-clear-search" onclick="clearSearch()">✕ Hapus Pencarian</button>
        </div>
        <div class="search-loading" id="searchLoading">
            <div style="font-size:28px;margin-bottom:8px;">⏳</div>
            <div>Mencari produk...</div>
        </div>

        <div class="product-grid" id="productGrid">
            <?php while($row = $result->fetch_assoc()):
                $hargaCoret   = round($row['price'] * 1.15);
                $disc         = 15;
                $isStarSeller = $row['stock'] > 15;
                $sold         = intval($row['sold_count'] ?? 0);
                $rating       = number_format(rand(40,50)/10,1);
                $badgeClass   = $isStarSeller ? 'star' : 'sale';
                $badgeText    = $isStarSeller ? '⭐ Star Seller' : '-'.$disc.'%';
                $imgUrl       = 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=600&q=80';
                $descSafe     = htmlspecialchars($row['description'] ?? '', ENT_QUOTES);
            ?>
            <div class="product-card"
                 data-id="<?= $row['id_product'] ?>"
                 data-name="<?= htmlspecialchars($row['product_name'], ENT_QUOTES) ?>"
                 data-brand="<?= htmlspecialchars($row['brand'], ENT_QUOTES) ?>"
                 data-price="<?= $row['price'] ?>"
                 data-price-ori="<?= $hargaCoret ?>"
                 data-disc="<?= $disc ?>"
                 data-star="<?= $isStarSeller ? '1':'0' ?>"
                 data-cat="<?= htmlspecialchars($row['category_name'], ENT_QUOTES) ?>"
                 data-stock="<?= $row['stock'] ?>"
                 data-sold="<?= $sold ?>"
                 data-rating="<?= $rating ?>"
                 data-desc="<?= $descSafe ?>"
                 data-img="<?= $imgUrl ?>"
                 onclick="openModal(this)">
                <div class="product-img-wrap">
                    <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" loading="lazy">
                    <span class="badge-label <?= $badgeClass ?>"><?= $badgeText ?></span>
                    <button class="cart-quick-btn" title="Tambah ke Keranjang"
                            onclick="event.stopPropagation();addToCart(<?= $row['id_product'] ?>,'<?= addslashes(htmlspecialchars($row['product_name'])) ?>','<?= addslashes(htmlspecialchars($row['brand'])) ?>',<?= $row['price'] ?>,'<?= $imgUrl ?>')">🛒</button>
                </div>
                <div class="product-info">
                    <div class="product-brand"><?= htmlspecialchars($row['brand']) ?></div>
                    <div class="product-name"><?= htmlspecialchars($row['product_name']) ?></div>
                    <div class="price-row">
                        <?php if(!$isStarSeller): ?>
                        <div class="price-original">Rp <?= number_format($hargaCoret,0,',','.') ?></div>
                        <?php endif; ?>
                        <div>
                            <span class="price-main">Rp <?= number_format($row['price'],0,',','.') ?></span>
                            <?php if(!$isStarSeller): ?><span class="discount-tag">-<?= $disc ?>%</span><?php endif; ?>
                        </div>
                    </div>
                    <div class="product-meta">
                        <div class="rating">★ <?= $rating ?><span>| <?= number_format($sold,0,',','.') ?> terjual</span></div>
                        <div class="location-tag">Surabaya</div>
                    </div>
                    <div style="margin-top:6px;">
                        <span style="background:#F9D0CE;color:#b5606b;font-size:10px;font-weight:700;padding:2px 6px;border-radius:3px;"><?= htmlspecialchars($row['category_name']) ?></span>
                        <span style="background:#DCDFBA;color:#5A5E3A;font-size:10px;font-weight:700;padding:2px 6px;border-radius:3px;margin-left:4px;">Official</span>
                    </div>
                </div>
                <?php if($isAdmin): ?>
                <div class="admin-actions">
                    <a href="edit_produk.php?id=<?= $row['id_product'] ?>" class="btn-edit" onclick="event.stopPropagation()">✏ Edit</a>
                    <a href="hapus_produk.php?id=<?= $row['id_product'] ?>" onclick="event.stopPropagation();return confirm('Hapus produk ini?')" class="btn-delete">🗑 Hapus</a>
                </div>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

</div>

<!-- ══ PRODUCT DETAIL MODAL ══ -->
<div class="pm-overlay" id="pmOverlay">
    <div class="pm-box">
        <button class="pm-close" id="pmClose">✕</button>
        <img id="pmImg" class="pm-img" src="" alt="">
        <div class="pm-body">
            <div id="pmBrand" class="pm-brand"></div>
            <div id="pmName"  class="pm-name"></div>
            <div class="pm-rating">
                <span id="pmStars"  class="pm-stars"></span>
                <span id="pmRating"></span>
                <span>·</span>
                <span id="pmSold"></span>
            </div>
            <hr class="pm-divider">
            <div class="pm-price-row">
                <span id="pmPriceOri"  class="pm-price-ori"></span>
                <span id="pmPriceMain" class="pm-price-main"></span>
                <span id="pmDisc"      class="pm-disc" style="display:none"></span>
            </div>
            <div id="pmTags" class="pm-tags"></div>

            <!-- ✅ DESKRIPSI PRODUK -->
            <div class="pm-desc-box">
                <div class="pm-desc-label">📝 Deskripsi Produk</div>
                <div id="pmDescText" class="pm-desc-text"></div>
            </div>

            <hr class="pm-divider">

            <!-- STOK -->
            <div>
                <div class="pm-loading" id="pmLoading">⏳ Mengecek stok terbaru...</div>
                <div class="pm-stock-row">
                    <span class="pm-stock-label">Stok tersedia:</span>
                    <span id="pmStockNum" class="pm-stock-num">-</span>
                </div>
            </div>

            <!-- ADMIN: EDIT -->
            <?php if($isAdmin): ?>
            <div class="pm-admin-box" id="pmAdminBox">
                <div class="pm-admin-title">⚙️ Edit Produk (Admin)</div>
                <div class="pm-admin-grid">
                    <div class="pm-admin-field">
                        <label>Stok baru</label>
                        <input type="number" id="pmStockInput" min="0" placeholder="0">
                    </div>
                    <div class="pm-admin-field">
                        <label>Deskripsi produk</label>
                        <textarea id="pmDescInput" rows="3" placeholder="Tulis deskripsi singkat..."></textarea>
                    </div>
                </div>
                <button class="pm-save-btn" id="pmSaveBtn">💾 Simpan Perubahan</button>
                <div class="pm-save-msg" id="pmSaveMsg"></div>
            </div>
            <?php endif; ?>

            <hr class="pm-divider">

            <!-- QTY -->
            <div class="pm-qty-label">Jumlah</div>
            <div class="pm-qty">
                <button class="pm-qty-btn" id="pmMinus">−</button>
                <span   class="pm-qty-num" id="pmQty">1</span>
                <button class="pm-qty-btn" id="pmPlus">+</button>
            </div>
            <button class="pm-btn-cart" id="pmBtnCart">🛒 Tambah ke Keranjang</button>
        </div>
    </div>
</div>

<!-- CART OVERLAY & SIDEBAR -->
<div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>
<div class="cart-sidebar" id="cartSidebar">
    <div class="cart-header-side">
        <h3>🛒 Keranjang Belanja</h3>
        <button class="btn-close-cart" onclick="closeCart()">✕</button>
    </div>
    <div class="cart-items-list" id="cartItemsList">
        <div class="empty-cart-msg"><span class="ec-icon">🛒</span><p>Keranjang masih kosong</p><small>Yuk tambahkan produk!</small></div>
    </div>
    <div class="cart-footer-side" id="cartFooter" style="display:none;">
        <div class="cart-subtotal"><span id="cartItemCount">0 produk</span><span>Subtotal</span></div>
        <div class="cart-total-row"><span>Total</span><span id="cartTotalPrice">Rp 0</span></div>
        <a href="checkout.php" class="btn-checkout-main">Lanjut ke Checkout →</a>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-main">
        <div class="footer-brand">
            <span class="logo" style="color:#ef929b;font-size:24px;display:block;margin-bottom:10px;">Beauti<span style="font-style:italic;font-weight:300;">fy</span></span>
            <p>Platform marketplace kecantikan terpercaya di Indonesia.</p>
            <div class="payment-icons"><span class="pay-tag">GoPay</span><span class="pay-tag">OVO</span><span class="pay-tag">Dana</span><span class="pay-tag">BCA</span></div>
        </div>
        <div class="footer-col"><h4>Layanan Pelanggan</h4><a href="#">Lacak Pesanan</a><a href="hubungi_kami.php">Hubungi Kami</a></div>
        <div class="footer-col"><h4>Tentang Beautify</h4><a href="tentang_kami.php">Tentang Kami</a><a href="blog_kecantikan.php">Blog Kecantikan</a></div>
    </div>
    <div class="footer-bottom">©️ 2026 Beautify Marketplace. Hak Cipta Dilindungi. | 🇮🇩 Indonesia</div>
</footer>

<script>
const IS_ADMIN = <?= $isAdmin ? 'true' : 'false' ?>;

// ── CART ─────────────────────────────────────────────────────
let cart = JSON.parse(sessionStorage.getItem('beautify_cart') || '[]');
function saveCart() { sessionStorage.setItem('beautify_cart', JSON.stringify(cart)); }
function formatRp(n) { return 'Rp ' + parseInt(n).toLocaleString('id-ID'); }
function openCart()  { document.getElementById('cartSidebar').classList.add('open');    document.getElementById('cartOverlay').classList.add('open');    renderCart(); }
function closeCart() { document.getElementById('cartSidebar').classList.remove('open'); document.getElementById('cartOverlay').classList.remove('open'); }

function addToCart(id, name, brand, price, img) {
    const ex = cart.find(c => c.id == id);
    if (ex) { ex.qty++; } else { cart.push({id, name, brand, price: parseInt(price), img, qty: 1}); }
    saveCart(); updateCartBadge(); renderCart(); openCart();
}
function changeQty(id, delta) {
    const item = cart.find(c => c.id == id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) cart = cart.filter(c => c.id != id);
    saveCart(); updateCartBadge(); renderCart();
}
function removeItem(id) { cart = cart.filter(c => c.id != id); saveCart(); updateCartBadge(); renderCart(); }
function updateCartBadge() { document.getElementById('cartBadge').textContent = cart.reduce((s,c)=>s+c.qty,0); }
function renderCart() {
    const list = document.getElementById('cartItemsList'), footer = document.getElementById('cartFooter');
    if (!cart.length) { list.innerHTML='<div class="empty-cart-msg"><span class="ec-icon">🛒</span><p>Keranjang masih kosong</p></div>'; footer.style.display='none'; return; }
    document.getElementById('cartItemCount').textContent  = cart.reduce((s,c)=>s+c.qty,0)+' produk';
    document.getElementById('cartTotalPrice').textContent = formatRp(cart.reduce((s,c)=>s+c.price*c.qty,0));
    footer.style.display='block';
    list.innerHTML = cart.map(item=>`
        <div class="cart-item-row">
            <img src="${item.img}" alt="${item.name}">
            <div class="cart-item-details">
                <div class="item-name">${item.name}</div>
                <div class="item-brand">${item.brand}</div>
                <div class="item-price">${formatRp(item.price)}</div>
                <div class="qty-control">
                    <button onclick="changeQty(${item.id},-1)">−</button>
                    <span class="qty-num">${item.qty}</span>
                    <button onclick="changeQty(${item.id},+1)">+</button>
                </div>
            </div>
            <button class="btn-remove-item" onclick="removeItem(${item.id})">✕</button>
        </div>`).join('');
}

// ── NOTIF BADGE ───────────────────────────────────────────────
function updateNotifBadge() {
    const notifs = JSON.parse(sessionStorage.getItem('beautify_notif') || '[]');
    const unread = notifs.filter(n => !n.read).length;
    const badge  = document.getElementById('notifBadge');
    if (!badge) return;
    badge.textContent   = unread > 99 ? '99+' : unread;
    badge.style.display = unread > 0 ? 'inline-flex' : 'none';
}

// ── MODAL ────────────────────────────────────────────────────
let pmProduct = null, pmQtyVal = 1, currentCard = null;

function setStockUI(stock) {
    const numEl = document.getElementById('pmStockNum');
    const btn   = document.getElementById('pmBtnCart');
    numEl.textContent = stock;
    numEl.className   = 'pm-stock-num' + (stock <= 5 ? ' low' : '');
    if (stock <= 0) {
        btn.disabled     = true;
        btn.textContent  = '❌ Stok Habis';
        numEl.textContent = '0 (Habis)';
        numEl.className   = 'pm-stock-num low';
    } else {
        btn.disabled    = false;
        btn.textContent = '🛒 Tambah ke Keranjang';
    }
    if (IS_ADMIN) document.getElementById('pmStockInput').value = stock;
}

function openModal(card) {
    currentCard = card;
    const d = card.dataset;
    pmProduct = { id: d.id, name: d.name, brand: d.brand, price: parseInt(d.price), img: d.img };
    pmQtyVal  = 1;

    const isStar = d.star === '1';
    const stars  = Math.round(parseFloat(d.rating));

    // Isi semua field modal
    document.getElementById('pmImg').src            = d.img;
    document.getElementById('pmImg').alt            = d.name;
    document.getElementById('pmBrand').textContent  = d.brand;
    document.getElementById('pmName').textContent   = d.name;
    document.getElementById('pmStars').textContent  = '★'.repeat(stars) + '☆'.repeat(5-stars);
    document.getElementById('pmRating').textContent = '★ ' + d.rating;
    document.getElementById('pmSold').textContent   = parseInt(d.sold).toLocaleString('id-ID') + ' terjual';
    document.getElementById('pmPriceMain').textContent = formatRp(d.price);
    document.getElementById('pmQty').textContent    = 1;

    // ✅ DESKRIPSI — ini bagian yang paling penting
    const descEl   = document.getElementById('pmDescText');
    const descVal  = (d.desc || '').trim();
    if (descVal) {
        descEl.textContent = descVal;
        descEl.className   = 'pm-desc-text';
    } else {
        descEl.textContent = 'Belum ada deskripsi untuk produk ini.';
        descEl.className   = 'pm-desc-text empty';
    }

    // Harga & diskon
    if (isStar) {
        document.getElementById('pmPriceOri').textContent = '';
        document.getElementById('pmDisc').style.display   = 'none';
    } else {
        document.getElementById('pmPriceOri').textContent = formatRp(d.priceOri);
        document.getElementById('pmDisc').textContent     = '-' + d.disc + '%';
        document.getElementById('pmDisc').style.display   = 'inline-block';
    }

    // Tags
    document.getElementById('pmTags').innerHTML =
        `<span class="pm-tag pink">${d.cat}</span>` +
        (isStar ? '<span class="pm-tag green">⭐ Star Seller</span>' : '') +
        '<span class="pm-tag green">Official</span>';

    // Stok dari cache dulu, lalu fetch realtime
    setStockUI(parseInt(d.stock));
    if (IS_ADMIN) document.getElementById('pmDescInput').value = descVal;

    // Buka modal
    document.getElementById('pmOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';

    // Fetch stok + deskripsi realtime dari DB
    document.getElementById('pmLoading').classList.add('show');
    fetch(`get_product_detail.php?id=${d.id}`)
        .then(r => r.json())
        .then(res => {
            document.getElementById('pmLoading').classList.remove('show');
            if (res.success) {
                setStockUI(res.stock);
                card.dataset.stock = res.stock;

                // Update deskripsi dari DB
                const freshDesc = (res.description || '').trim();
                card.dataset.desc = freshDesc;
                if (freshDesc) {
                    descEl.textContent = freshDesc;
                    descEl.className   = 'pm-desc-text';
                    if (IS_ADMIN) document.getElementById('pmDescInput').value = freshDesc;
                }
            }
        })
        .catch(() => document.getElementById('pmLoading').classList.remove('show'));
}

function closeModal() {
    document.getElementById('pmOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

document.getElementById('pmClose').addEventListener('click', closeModal);
document.getElementById('pmOverlay').addEventListener('click', e => { if (e.target === document.getElementById('pmOverlay')) closeModal(); });
document.getElementById('pmMinus').addEventListener('click', () => { pmQtyVal = Math.max(1, pmQtyVal-1); document.getElementById('pmQty').textContent = pmQtyVal; });
document.getElementById('pmPlus').addEventListener('click',  () => { pmQtyVal++; document.getElementById('pmQty').textContent = pmQtyVal; });
document.getElementById('pmBtnCart').addEventListener('click', () => {
    if (!pmProduct) return;
    const ex = cart.find(c => c.id == pmProduct.id);
    if (ex) { ex.qty += pmQtyVal; } else { cart.push({...pmProduct, qty: pmQtyVal}); }
    saveCart(); updateCartBadge(); closeModal(); renderCart(); openCart();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

// ── ADMIN SAVE ───────────────────────────────────────────────
<?php if($isAdmin): ?>
document.getElementById('pmSaveBtn').addEventListener('click', function() {
    if (!pmProduct) return;
    const stock   = parseInt(document.getElementById('pmStockInput').value);
    const desc    = document.getElementById('pmDescInput').value.trim();
    const msgEl   = document.getElementById('pmSaveMsg');

    if (isNaN(stock) || stock < 0) {
        msgEl.textContent = 'Stok tidak valid!';
        msgEl.className   = 'pm-save-msg err';
        return;
    }

    this.textContent = '⏳'; this.disabled = true;

    fetch('update_stock.php', {
        method : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body   : JSON.stringify({ id: pmProduct.id, stock, description: desc })
    })
    .then(r => r.json())
    .then(res => {
        this.textContent = '💾 Simpan Perubahan'; this.disabled = false;
        if (res.success) {
            msgEl.textContent = '✅ Berhasil disimpan!';
            msgEl.className   = 'pm-save-msg ok';
            setStockUI(res.stock);

            // Update deskripsi di modal
            const descEl = document.getElementById('pmDescText');
            const freshDesc = (res.description || '').trim();
            if (freshDesc) {
                descEl.textContent = freshDesc;
                descEl.className   = 'pm-desc-text';
            } else {
                descEl.textContent = 'Belum ada deskripsi untuk produk ini.';
                descEl.className   = 'pm-desc-text empty';
            }

            // Sync ke card
            if (currentCard) {
                currentCard.dataset.stock = res.stock;
                currentCard.dataset.desc  = res.description || '';
            }
            setTimeout(() => msgEl.className = 'pm-save-msg', 3000);
        } else {
            msgEl.textContent = '❌ ' + (res.message || 'Gagal');
            msgEl.className   = 'pm-save-msg err';
        }
    })
    .catch(() => {
        this.textContent = '💾 Simpan Perubahan'; this.disabled = false;
        msgEl.textContent = '❌ Koneksi error'; msgEl.className = 'pm-save-msg err';
    });
});
<?php endif; ?>

// ── COUNTDOWN ────────────────────────────────────────────────
(function(){
    let t=2*3600+45*60+30;
    const h=document.getElementById('t-h'),m=document.getElementById('t-m'),s=document.getElementById('t-s');
    function pad(n){return String(n).padStart(2,'0');}
    setInterval(()=>{if(t<=0)return;t--;h.textContent=pad(Math.floor(t/3600));m.textContent=pad(Math.floor((t%3600)/60));s.textContent=pad(t%60);},1000);
})();

// ── PROFILE DROPDOWN ─────────────────────────────────────────
function toggleProfile() { document.getElementById('profileDropdown').classList.toggle('open'); }
document.addEventListener('click', function(e) {
    const w = document.getElementById('profileWrapper');
    if (w && !w.contains(e.target)) document.getElementById('profileDropdown').classList.remove('open');
});

// ── SEARCH ───────────────────────────────────────────────────
let searchTimer = null;
document.getElementById('searchInput').addEventListener('input', ()=>{ clearTimeout(searchTimer); searchTimer=setTimeout(doSearch,400); });
document.getElementById('searchInput').addEventListener('keydown', e=>{ if(e.key==='Enter'){clearTimeout(searchTimer);doSearch();} });
function doSearch() { fetchProducts(document.getElementById('searchInput').value.trim()); }
function clearSearch() { document.getElementById('searchInput').value=''; fetchProducts(''); }
function fetchProducts(keyword) {
    const grid=document.getElementById('productGrid'),loading=document.getElementById('searchLoading'),status=document.getElementById('searchStatus'),statusText=document.getElementById('searchStatusText');
    grid.style.display='none'; loading.classList.add('show'); status.classList.remove('show');
    if(keyword) document.getElementById('produk').scrollIntoView({behavior:'smooth',block:'start'});
    fetch(`search_ajax.php?q=${encodeURIComponent(keyword)}`)
        .then(r=>r.json())
        .then(data=>{
            loading.classList.remove('show'); grid.style.display='grid';
            if(keyword){statusText.textContent=`${data.products.length} produk untuk "${keyword}"`;status.classList.add('show');}
            renderProducts(data.products);
        })
        .catch(()=>{loading.classList.remove('show');grid.style.display='grid';});
}
function renderProducts(products) {
    const grid = document.getElementById('productGrid');
    if (!products.length) { grid.innerHTML=`<div style="grid-column:1/-1;text-align:center;padding:60px 0;color:#8A7070;"><div style="font-size:48px;margin-bottom:12px;">🔍</div><div style="font-size:16px;font-weight:600;">Produk tidak ditemukan</div></div>`; return; }
    grid.innerHTML = products.map(p => {
        const nS = p.product_name.replace(/'/g,"\\'").replace(/"/g,'&quot;');
        const bS = p.brand.replace(/'/g,"\\'").replace(/"/g,'&quot;');
        const dS = (p.description||'').replace(/"/g,'&quot;');
        return `<div class="product-card"
            data-id="${p.id_product}" data-name="${p.product_name.replace(/"/g,'&quot;')}"
            data-brand="${p.brand.replace(/"/g,'&quot;')}" data-price="${p.price}"
            data-price-ori="${p.hargaCoret}" data-disc="${p.disc}"
            data-star="${p.isStarSeller?'1':'0'}" data-cat="${p.category_name}"
            data-stock="${p.stock}" data-sold="${p.sold}" data-rating="${p.rating}"
            data-desc="${dS}" data-img="${p.img}"
            onclick="openModal(this)">
            <div class="product-img-wrap">
                <img src="${p.img}" alt="${p.product_name}" loading="lazy">
                <span class="badge-label ${p.isStarSeller?'star':'sale'}">${p.isStarSeller?'⭐ Star Seller':'-'+p.disc+'%'}</span>
                <button class="cart-quick-btn" onclick="event.stopPropagation();addToCart(${p.id_product},'${nS}','${bS}',${p.price},'${p.img}')">🛒</button>
            </div>
            <div class="product-info">
                <div class="product-brand">${p.brand}</div>
                <div class="product-name">${p.product_name}</div>
                <div class="price-row">
                    ${!p.isStarSeller?`<div class="price-original">Rp ${parseInt(p.hargaCoret).toLocaleString('id-ID')}</div>`:''}
                    <div><span class="price-main">Rp ${parseInt(p.price).toLocaleString('id-ID')}</span>${!p.isStarSeller?`<span class="discount-tag">-${p.disc}%</span>`:''}</div>
                </div>
                <div class="product-meta">
                    <div class="rating">★ ${p.rating} <span>| ${parseInt(p.sold).toLocaleString('id-ID')} terjual</span></div>
                    <div class="location-tag">Surabaya</div>
                </div>
                <div style="margin-top:6px;">
                    <span style="background:#F9D0CE;color:#b5606b;font-size:10px;font-weight:700;padding:2px 6px;border-radius:3px;">${p.category_name}</span>
                    <span style="background:#DCDFBA;color:#5A5E3A;font-size:10px;font-weight:700;padding:2px 6px;border-radius:3px;margin-left:4px;">Official</span>
                </div>
            </div>
        </div>`;
    }).join('');
}

// ── INIT ─────────────────────────────────────────────────────
updateCartBadge();
updateNotifBadge();
setInterval(updateNotifBadge, 5000);
</script>
</body>
</html>