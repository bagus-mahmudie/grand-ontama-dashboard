<?php
include 'db.php';

if (isset($_GET['unit'])) {
    $unit = $conn->real_escape_string($_GET['unit']);
    // $result = $conn->query("SELECT id, nomor_unit FROM properti WHERE unit = '$unit' AND status = 'Tersedia' ORDER BY nomor_unit");
    $result = $conn->query("SELECT id, nomor_unit FROM properti WHERE unit = '$unit' AND status IN ('Tersedia', 'Booking') ORDER BY nomor_unit");

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($data);
}