<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}
include 'db.php';

if (isset($_POST['doc_id'], $_POST['customer_id'])) {
    $doc_id      = intval($_POST['doc_id']);
    $customer_id = intval($_POST['customer_id']);

    // Ambil path file
    $res = $conn->query("SELECT path_file FROM dokumen_customer WHERE id = $doc_id");
    if ($res && $res->num_rows) {
        $row = $res->fetch_assoc();
        $file = $row['path_file'];
        if (file_exists($file)) {
            unlink($file);
        }
        // Hapus record
        $conn->query("DELETE FROM dokumen_customer WHERE id = $doc_id");
    }
}

// Redirect kembali ke halaman detail customer
header("Location: customer_detail?id={$customer_id}");
exit();
?>
