<?php
session_start();
include 'koneksi.php';

$user_id    = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
$product_id = intval($_POST['product_id']);
$qty        = intval($_POST['qty'] ?? 1);

// Cek apakah sudah ada di keranjang
$cek = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE users_id=? AND product_id=?");
$cek->bind_param("ii", $user_id, $product_id);
$cek->execute();
$row = $cek->get_result()->fetch_assoc();

if ($row) {
    $new_qty = $row['quantity'] + $qty;
    $upd = $conn->prepare("UPDATE cart SET quantity=? WHERE cart_id=?");
    $upd->bind_param("ii", $new_qty, $row['cart_id']);
    $upd->execute();
} else {
    $ins = $conn->prepare("INSERT INTO cart (users_id, product_id, quantity) VALUES (?,?,?)");
    $ins->bind_param("iii", $user_id, $product_id, $qty);
    $ins->execute();
}

// Hitung total qty untuk badge
$total = $conn->query("SELECT SUM(quantity) as t FROM cart WHERE users_id=$user_id")->fetch_assoc();
echo json_encode(['status' => 'ok', 'total_qty' => (int)$total['t']]);