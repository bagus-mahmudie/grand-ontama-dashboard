<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login");
    exit();
}
include 'db.php';

if (isset($_POST['doc_id'], $_POST['agen_id'])) {
    $doc_id  = intval($_POST['doc_id']);
    $agen_id = intval($_POST['agen_id']);

    // Ambil path file untuk dihapus dari filesystem
    $res = $conn->query("SELECT path_file FROM dokumen_agen WHERE id = $doc_id");
    if ($res && $res->num_rows) {
        $row = $res->fetch_assoc();
        $file = $row['path_file'];
        if (file_exists($file)) {
            unlink($file);
        }
        // Hapus record di database
        $conn->query("DELETE FROM dokumen_agen WHERE id = $doc_id");
    }
}

// Redirect kembali ke detail agen
header("Location: agen_detail?id={$agen_id}");
exit();
?>
