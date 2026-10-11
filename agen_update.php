<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $nama_lengkap = $_POST['nama_lengkap'];
    $tempat_tanggal_lahir = $_POST['tempat_tanggal_lahir'];
    $nik = $_POST['nik'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $agama = $_POST['agama'];
    $status_pernikahan = $_POST['status_pernikahan'];
    $kewarganegaraan = $_POST['kewarganegaraan'];
    $alamat_ktp = $_POST['alamat_ktp'];
    $alamat_domisili = $_POST['alamat_domisili'];
    $no_hp = $_POST['no_hp'];
    $email = $_POST['email'];
    $tipe_agen = $_POST['tipe_agen'];
    $tanggal_bergabung = $_POST['tanggal_bergabung'] ?? null;
    if (empty($tanggal_bergabung)) {
    $result = $conn->query("SELECT tanggal_bergabung FROM agen WHERE id = $id");
    $tanggal_bergabung = $result->fetch_assoc()['tanggal_bergabung'];
    }
    $status = $_POST['status'];
    $nama_kontak_darurat = $_POST['nama_kontak_darurat'];
    $no_kontak_darurat = $_POST['no_kontak_darurat'];
    $hubungan_dengan_pembeli = $_POST['hubungan_dengan_pembeli'];

    $fotoUpdate = '';
    $fileName = null;

    

    if (!empty($_FILES['foto_agen']['name'])) {

        $stmtOld = $conn->prepare("SELECT foto_agen FROM agen WHERE id = ?");
        $stmtOld->bind_param("i", $id);
        $stmtOld->execute();
        $stmtOld->bind_result($oldFoto);
        $stmtOld->fetch();
        $stmtOld->close();

        $defaultFiles = ['profile1.jpg','default.png'];  // sesuaikan nama file default-mu
        $oldPath = __DIR__ . '/assets/img/uploads/' . $oldFoto;
        if (
            $oldFoto
            && file_exists($oldPath)
            && ! in_array($oldFoto, $defaultFiles, true)
        ) {
            unlink($oldPath);
        }

        $fileName = time() . '_' . basename($_FILES['foto_agen']['name']);
        $targetPath = 'assets/img/uploads/' . $fileName;
        if (move_uploaded_file($_FILES['foto_agen']['tmp_name'], $targetPath)) {
            $fotoUpdate = ", foto_agen = ?";
        }
    }

    // Bangun query update
    $query = "
        UPDATE agen SET 
            nama_lengkap = ?, 
            tempat_tanggal_lahir = ?, 
            nik = ?, 
            jenis_kelamin = ?, 
            agama = ?, 
            status_pernikahan = ?, 
            kewarganegaraan = ?, 
            alamat_ktp = ?, 
            alamat_domisili = ?, 
            no_hp = ?, 
            email = ?, 
            tipe_agen = ?, 
            tanggal_bergabung = ?, 
            status = ?, 
            nama_kontak_darurat = ?, 
            no_kontak_darurat = ?, 
            hubungan_dengan_pembeli = ?
            $fotoUpdate
        WHERE id = ?
    ";

    $stmt = $conn->prepare($query);

    if (!empty($fotoUpdate)) {
        // Bind param termasuk foto
        $stmt->bind_param(
            "ssssssssssssssssssi",
            $nama_lengkap,
            $tempat_tanggal_lahir,
            $nik,
            $jenis_kelamin,
            $agama,
            $status_pernikahan,
            $kewarganegaraan,
            $alamat_ktp,
            $alamat_domisili,
            $no_hp,
            $email,
            $tipe_agen,
            $tanggal_bergabung,
            $status,
            $nama_kontak_darurat,
            $no_kontak_darurat,
            $hubungan_dengan_pembeli,
            $fileName,
            $id
        );
    } else {
        // Bind param tanpa foto
        $stmt->bind_param(
            "sssssssssssssssssi",
            $nama_lengkap,
            $tempat_tanggal_lahir,
            $nik,
            $jenis_kelamin,
            $agama,
            $status_pernikahan,
            $kewarganegaraan,
            $alamat_ktp,
            $alamat_domisili,
            $no_hp,
            $email,
            $tipe_agen,
            $tanggal_bergabung,
            $status,
            $nama_kontak_darurat,
            $no_kontak_darurat,
            $hubungan_dengan_pembeli,
            $id
        );
    }

    if ($stmt->execute()) {
        header("Location: agen_detail?id=" . $id);
        exit;
    } else {
        echo "Gagal mengupdate data: " . $stmt->error;
    }
    
}
?>
