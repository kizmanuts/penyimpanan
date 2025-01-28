
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit</title>
</head>
<body>
<?php
include "../koneksi.php";

$id = intval($_GET['id']); // Sanitasi input

$stmt = $kon->prepare("SELECT * FROM alumnus WHERE id = ?");
$stmt->bind_param("i", $id); // 'i' untuk integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Data tidak ditemukan.";
    exit();
}
?>
<form action="simedit.php" method="post" autocomplete="off" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <label for="jenis_penataran">Jenis Penataran : </label><br>
        <select name="jenis_penataran" id="jenis_penataran" >
            <option value="<?php echo $row['jenis_penataran']; ?>">~<?php echo $row['jenis_penataran']; ?>~</option>
            <option value="Pertahanan Siber">Pertahanan Siber</option>
            <option value="Pengamanan Sistem Informasi">Pengamanan Sistem Informasi</option>
            <option value="Pengamanan Jaringan">Pengamanan Jaringan</option>
            <option value="Teknisi Jaringan Dan Komputer">Teknisi Jaringan Dan Komputer</option>
            <option value="Administrator Jaringan">Administrator Jaringan</option>
            <option value="Literasi Komputer">Literasi Komputer</option>
            <option value="Pemograman Website">Pemograman Website</option>
            <option value="Pemograman Aplikasi Mobile">Pemograman Aplikasi Mobile</option>
            <option value="Multimedia, Desain & Grafis">Multimedia, Desain & Grafis</option>
        </select><br>
        <br>
        <label for="tahun">Tahun</label><br>
        <input type="number" name="tahun" id="tahun" maxlength="4" value="<?php echo $row['tahun']; ?>"><br>
        <br>
        <label for="nama">Nama : </label><br>
        <input type="text" name="nama" id="nama" required value="<?php echo $row['nama']; ?>"><br>
        <br>
        <label for="pangkat">Pangkat : </label><br>
        <input type="text" name="pangkat" id="pangkat" required value="<?php echo $row['pangkat']; ?>"><br>
        <br>
        <label for="nrp">NRP : </label><br>
        <input type="number" name="nrp" id="nrp" required maxlength="20" value="<?php echo $row['nrp']; ?>"><br>
        <br>
        <label for="nilai">Nilai Akhir : </label><br>
        <input type="text" name="nilai" id="nilai" required value="<?php echo $row['nilai']; ?>"><br>
        <br>
        <label for="ttl">Tempat Tanggal Lahir : </label><br>
        <input type="date" name="ttl" id="ttl" value="<?php echo $row['ttl']; ?>"><br>
        <br>
        <label for="kesatuan">Asal Kesatuan : </label><br>
        <input type="text" name="kesatuan" id="kesatuan" required value="<?php echo $row['kesatuan']; ?>"><br>
        <br>
        <label for="alamat">Alamat : </label><br>
        <textarea name="alamat" id="alamat" cols="30" rows="5"><?php echo $row['alamat']; ?></textarea>
        <br>
        <br>
        <button type="submit" name="submit">Submit</button>
        
        </form>
</body>
</html>