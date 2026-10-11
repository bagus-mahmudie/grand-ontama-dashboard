<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Cek apakah data agen ada
    $cek = mysqli_query($conn, "SELECT * FROM agen WHERE id = $id");
    if (mysqli_num_rows($cek) > 0) {
        // ======= MULAI: hapus file foto agen =======
        // 1) Ambil nama file foto_agen
        $row = mysqli_fetch_assoc($cek);
        $fotoLama = $row['foto_agen'];

        // 2) Hapus file fisik jika ada, tapi kecuali foto default
        $filePath = __DIR__ . '/assets/img/uploads/' . $fotoLama;
        $defaultFiles = ['profile1.jpg','default.png'];  // sesuaikan dengan nama default-mu
        if (
            $fotoLama
            && file_exists($filePath)
            && ! in_array($fotoLama, $defaultFiles, true)
        ) {
            unlink($filePath);
        }

        // ======= SELESAI: hapus file foto agen =======

        // 3) Hapus record agen
        $delete = mysqli_query($conn, "DELETE FROM agen WHERE id = $id");
        if ($delete) {
            header("Location: agen?delete=success");
            exit();
        } else {
            echo "Gagal menghapus data agen.";
        }
    } else {
        echo "Agen tidak ditemukan.";
    }
} else {
    echo "ID tidak valid.";
}
?>
