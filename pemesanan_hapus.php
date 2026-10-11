<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Ambil properti_id dan customer_id dari pemesanan
    $getData = $conn->prepare("SELECT properti_id, customer_id FROM pemesanan WHERE id = ?");
    $getData->bind_param("i", $id);
    $getData->execute();
    $result = $getData->get_result();

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $properti_id = $data['properti_id'];
        $customer_id = $data['customer_id'];

        // Hapus pemesanan
        $delete = $conn->prepare("DELETE FROM pemesanan WHERE id = ?");
        $delete->bind_param("i", $id);
        if ($delete->execute()) {
            // Update status properti menjadi 'Tersedia'
            $updateProperti = $conn->prepare("UPDATE properti SET status = 'Tersedia' WHERE id = ?");
            $updateProperti->bind_param("i", $properti_id);
            $updateProperti->execute();

            // Cek apakah customer digunakan di pemesanan lain
            $check = $conn->prepare("SELECT COUNT(*) AS total FROM pemesanan WHERE customer_id = ?");
            $check->bind_param("i", $customer_id);
            $check->execute();
            $resultCheck = $check->get_result();
            $count = $resultCheck->fetch_assoc()['total'];

            if ($count == 0) {

                // ambil nama file foto customer
                $getFoto = $conn->prepare("SELECT foto_profil FROM customer WHERE id = ?");
                $getFoto->bind_param("i", $customer_id);
                $getFoto->execute();
                $getFoto->bind_result($fotoProfil);
                $getFoto->fetch();
                $getFoto->close();

                // hapus file fisiknya jika ada
                $path = __DIR__ . '/assets/img/uploads/' . $fotoProfil;
                if ($fotoProfil && file_exists($path)) {
                    unlink($path);
                }
                // Hapus customer jika tidak ada pemesanan lain
                $deleteCustomer = $conn->prepare("DELETE FROM customer WHERE id = ?");
                $deleteCustomer->bind_param("i", $customer_id);
                $deleteCustomer->execute();
            }

            header("Location: pemesanan?delete=success");
            exit;
        } else {
            echo "Gagal menghapus data pemesanan.";
        }
    } else {
        echo "Data pemesanan tidak ditemukan.";
    }
} else {
    echo "ID tidak valid.";
}
?>
