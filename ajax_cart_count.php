<?php
session_start();
include 'koneksi.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
$total = $conn->query("SELECT SUM(quantity) as t FROM cart WHERE users_id=$user_id")->fetch_assoc();
echo json_encode(['total_qty' => (int)$total['t']]);