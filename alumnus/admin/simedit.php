<?php

include "../koneksi.php";
$id = intval($_POST['id']);
$jenis_penataran=$_POST['jenis_penataran'];
$tahun = intval($_POST['tahun']);
$nama = $_POST['nama'];
$pangkat=$_POST['pangkat'];
$nrp=$_POST['nrp'];
$nilai=$_POST['nilai'];
$ttl=$_POST['ttl'];
$kesatuan=$_POST['kesatuan'];
$alamat=$_POST['alamat'];

$stmt = $kon->prepare("UPDATE alumnus SET jenis_penataran=?, tahun=?, nama=?, pangkat=?, nrp=?, nilai=?, ttl=?, kesatuan=?, alamat=? WHERE id=?");
$stmt->bind_param("sssssssssi", $jenis_penataran, $tahun, $nama, $pangkat, $nrp, $nilai, $ttl, $kesatuan, $alamat, $id);

if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
                header("Location: kelola.php?status=success");
            } else {
                header("Location: kelola.php?status=no_change");
            }
            exit();
} else {
    echo "Gagal: " . $stmt->error;
}