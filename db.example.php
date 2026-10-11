<?php
$host = '127.0.0.1';
$user = '';
$pass = '';
$dbname = ''; // Ganti dengan nama database kamu

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
