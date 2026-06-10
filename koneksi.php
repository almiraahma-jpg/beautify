<?php
$conn = new mysqli("localhost", "root", "", "Beautify");
if ($conn->connect_error) {
    $conn = null;
} else {
    $conn->set_charset("utf8mb4");
}
?>