<?php
session_start();
include 'koneksi.php';

$users_id = $_SESSION['user_id'] ?? null;
$orders   = [];

if ($users_id && $conn) {
    $stmt = $conn->prepare("
        SELECT o.*, COUNT(oi.id) AS jumlah_item
        FROM orders o
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        WHERE o.users_id = ?
        GROUP BY o.order_id
        ORDER BY o.order_date DESC
        LIMIT 50
    ");
    $stmt->bind_param("i", $users_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $orders[] = $row;
    $stmt->close();
}

// Kalau JOIN gagal karena kolom id berbeda, fallback
if (empty($orders) && $users_id && $conn) {
    $res2 = $conn->query("SELECT * FROM orders WHERE users_id = $users_id ORDER BY order_date DESC LIMIT 50");
    if ($res2) {
        while ($row = $res2->fetch_assoc()) {
            $row['jumlah_item'] = '-';
            $orders[] = $row;
        }
    }
}

$statusConfig = [
    'Pending'    => ['color' => '#F297A0', 'label' => 'Pending'],
    'pending'    => ['color' => '#F297A0', 'label' => 'Pending'],
    'Dibayar'    => ['color' => '#B6BB79', 'label' => '✓ Dibayar'],
    'dibayar'    => ['color' => '#B6BB79', 'label' => '✓ Dibayar'],
    'Diproses'   => ['color' => '#F4A261', 'label' => 'Diproses'],
    'Dikirim'    => ['color' => '#4ECDC4', 'label' => 'Dikirim'],
    'Selesai'    => ['color' => '#B6BB79', 'label' => 'Selesai'],
    'Dibatalkan' => ['color' => '#999999', 'label' => 'Dibatalkan'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan – Beautify</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Fraunces:ital,wght@0,600;1,300&display=swap" rel="stylesheet">
    <style>
        :root { --pink:#F297A0; --bg:#F3EBD8; --text:#3B2A2B; --muted:#8A7070; --border:#EDD9CC; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--bg); color:var(--text); }

        header { background:var(--pink); padding:14px 0; box-shadow:0 2px 8px rgba(0,0,0,.15); }
        .header-inner { max-width:900px; margin:auto; padding:0 20px; display:flex; align-items:center; justify-content:space-between; }
        .logo { font-family:'Fraunces',serif; font-size:24px; font-weight:600; color:white; text-decoration:none; }
        .logo span { font-style:italic; font-weight:300; }
        .back-btn { color:white; text-decoration:none; font-size:13px; font-weight:600; }
        .back-btn:hover { opacity:.8; }

        .container { max-width:900px; margin:30px auto 60px; padding:0 20px; }
        h1 { font-size:22px; font-weight:700; margin-bottom:20px; }

        .order-card { background:white; border-radius:12px; padding:20px 24px; margin-bottom:14px; box-shadow:0 1px 4px rgba(0,0,0,.06); transition:box-shadow .2s; }
        .order-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.1); }

        .order-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px; flex-wrap:wrap; gap:8px; }
        .order-kode { font-size:17px; font-weight:700; color:var(--pink); letter-spacing:1px; }
        .order-tanggal { font-size:12px; color:var(--muted); margin-top:3px; }

        .status-badge { display:inline-block; padding:5px 14px; border-radius:20px; font-size:12px; font-weight:700; color:white; white-space:nowrap; }

        .order-meta { display:flex; gap:20px; font-size:13px; color:var(--muted); flex-wrap:wrap; margin-top:4px; }
        .order-meta strong { color:var(--text); }
        .order-total { font-size:16px; font-weight:700; color:var(--pink); margin-top:10px; padding-top:10px; border-top:1px solid var(--border); }

        .empty-state { text-align:center; padding:70px 0; color:var(--muted); }
        .empty-state .icon { font-size:56px; display:block; margin-bottom:14px; }
        .empty-state p { font-size:15px; font-weight:600; }
        .empty-state small { font-size:13px; display:block; margin-top:6px; }
        .btn-belanja { display:inline-block; margin-top:20px; background:var(--pink); color:white; padding:11px 28px; border-radius:8px; text-decoration:none; font-weight:700; font-size:14px; }
        .btn-belanja:hover { background:#e07880; }

        .login-box { background:white; border-radius:12px; padding:50px; text-align:center; box-shadow:0 1px 4px rgba(0,0,0,.06); }
        .login-box a { color:var(--pink); font-weight:700; text-decoration:none; }
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
    <h1>📦 Riwayat Pesanan</h1>

    <?php if (!$users_id): ?>
        <div class="login-box">
            <div style="font-size:52px;margin-bottom:14px;">🔐</div>
            <p style="font-size:15px;font-weight:600;margin-bottom:8px;">Kamu belum login</p>
            <p style="font-size:13px;color:var(--muted);">Silakan <a href="login.php">masuk</a> untuk melihat riwayat pesanan.</p>
        </div>

    <?php elseif (empty($orders)): ?>
        <div class="empty-state">
            <span class="icon">📭</span>
            <p>Belum ada pesanan</p>
            <small>Yuk mulai belanja produk beauty favoritmu!</small>
            <a href="index.php" class="btn-belanja">Belanja Sekarang</a>
        </div>

    <?php else: ?>
        <?php foreach ($orders as $order):
            $status  = $order['status'] ?? 'Pending';
            $cfg     = $statusConfig[$status] ?? ['color' => '#999', 'label' => $status];
            $tanggal = isset($order['order_date'])
                ? date('d M Y, H:i', strtotime($order['order_date']))
                : '-';
            $totalFmt  = 'Rp ' . number_format($order['total_price'] ?? 0, 0, ',', '.');
            $ongkirFmt = ($order['shipping_cost'] ?? 0) == 0
                ? 'GRATIS'
                : 'Rp ' . number_format($order['shipping_cost'], 0, ',', '.');
        ?>
        <div class="order-card">
            <div class="order-header">
                <div>
                    <div class="order-kode"><?= htmlspecialchars($order['kode_order'] ?? '-') ?></div>
                    <div class="order-tanggal">📅 <?= $tanggal ?> WIB</div>
                </div>
                <span class="status-badge" style="background:<?= $cfg['color'] ?>;">
                    <?= $cfg['label'] ?>
                </span>
            </div>
            <div class="order-meta">
                <?php if ($order['jumlah_item'] !== '-'): ?>
                <div>🛍 <strong><?= $order['jumlah_item'] ?> item</strong></div>
                <?php endif; ?>
                <div>🚚 Ongkir: <strong><?= $ongkirFmt ?></strong></div>
            </div>
            <div class="order-total">Total: <?= $totalFmt ?></div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>