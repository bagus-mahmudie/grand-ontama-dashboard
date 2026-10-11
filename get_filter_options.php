<?php
include 'db.php';

$unit = $_GET['unit'] ?? '';
$data = [
    'nama_type' => [],
    'type' => [],
    'nomor_unit' => [],
    'lb_lt' => [],
];

if ($unit) {
    foreach (array_keys($data) as $field) {
        $stmt = $conn->prepare("SELECT DISTINCT $field FROM properti WHERE unit = ? ORDER BY $field");
        $stmt->bind_param("s", $unit);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $data[$field][] = $row[$field];
        }
    }
}

echo json_encode($data);
