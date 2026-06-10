<?php
error_reporting(0);
ini_set('display_errors', 0);
include 'koneksi.php';
header('Content-Type: application/json');

$keyword = trim($_GET['q'] ?? '');
$keyword = substr($keyword, 0, 100);

if ($keyword === '') {
    $stmt = $conn->prepare("
        SELECT p.*, c.category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id_category
        ORDER BY RAND()
        LIMIT 20
    ");
    $stmt->execute();
} else {
    $like = '%' . $keyword . '%';
    $stmt = $conn->prepare("
        SELECT p.*, c.category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id_category
        WHERE p.product_name LIKE ?
           OR p.brand         LIKE ?
           OR c.category_name LIKE ?
        ORDER BY p.sold_count DESC
        LIMIT 20
    ");
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
}

$result   = $stmt->get_result();
$products = [];

while ($row = $result->fetch_assoc()) {
    $isStarSeller = $row['stock'] > 15;
    $disc         = 15;
    $hargaCoret   = round($row['price'] * 1.15);

    $products[] = [
        'id_product'   => $row['id_product'],
        'product_name' => $row['product_name'],
        'brand'        => $row['brand'],
        'category_name'=> $row['category_name'],
        'price'        => $row['price'],
        'hargaCoret'   => $hargaCoret,
        'disc'         => $disc,
        'stock'        => $row['stock'],
        'sold'         => $row['sold_count'] ?? 0,
        'rating'       => number_format(rand(40, 50) / 10, 1),
        'isStarSeller' => $isStarSeller,
        'description'  => $row['description'] ?? '',   // ✅ deskripsi
        'img'          => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=400&q=80',
    ];
}

echo json_encode(['products' => $products]);
?>