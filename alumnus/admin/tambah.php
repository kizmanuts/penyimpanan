<?php
require '../koneksi.php';
if (isset($_POST['submit'])) {
    $jenis_penataran=$_POST['jenis_penataran'];
    $tahun=$_POST['tahun'];
    $nama = $_POST['nama'];
    $pangkat=$_POST['pangkat'];
    $nrp=$_POST['nrp'];
    $nilai=$_POST['nilai'];
    $ttl=$_POST['ttl'];
    $kesatuan=$_POST['kesatuan'];
    $alamat=$_POST['alamat'];
    if($_FILES['foto']['error'] === 4){
        echo"<script> alert('Gambar tidak ada'); </script>";
    }
    else{
        $fileName=$_FILES['foto']['name'];
        $fileSize=$_FILES['foto']['size'];
        $tmpName=$_FILES['foto']['tmp_name'];

        $validImageExtension = ['.jpg', '.jpeg', '.png'];
        $imageExtension = explode('.', $fileName);
        $imageExtension = strtolower(end($imageExtension));
        if(in_array($imageExtension, $validImageExtension)){
            echo
            "<script>
            alert('Gambar tidak ada');
            </script>";
    }
    elseif($fileSize > 1000000){
     echo"<script> alert('Terlalu besar'); </script>";
    }
    else{
		$pass=uniqid();
        $newImageName = uniqid();
        $newImageName .= '.' . $imageExtension;
        move_uploaded_file($tmpName, '../img-profil/' . $newImageName);
        $query = "INSERT INTO alumnus VALUES('','$jenis_penataran','$tahun','$nama','$pangkat','$nrp','$nilai','$ttl','$kesatuan','$alamat','$newImageName')";
        mysqli_query($kon, $query);
        echo 
        "<script> 
        alert('Berhasil') 
        document.location.href='kelola.php'
        </script>";
    }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah</title>
</head>
<body>
    <h1>Penambahan Data Alumnus</h1>
<div class="container1">
    <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
        <label for="jenis_penataran">Jenis Penataran : </label><br>
        <select name="jenis_penataran" id="jenis_penataran">
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
        <input type="number" name="tahun" id="tahun" maxlength="4"><br>
        <br>
        <label for="nama">Nama : </label><br>
        <input type="text" name="nama" id="nama" required><br>
        <br>
        <label for="pangkat">Pangkat : </label><br>
        <input type="text" name="pangkat" id="pangkat" required><br>
        <br>
        <label for="nrp">NRP : </label><br>
        <input type="number" name="nrp" id="nrp" required maxlength="20"><br>
        <br>
        <label for="nilai">Nilai Akhir : </label><br>
        <input type="text" name="nilai" id="nilai" required><br>
        <br>
        <label for="ttl">Tempat Tanggal Lahir : </label><br>
        <input type="date" name="ttl" id="ttl"><br>
        <br>
        <label for="kesatuan">Asal Kesatuan : </label><br>
        <input type="text" name="kesatuan" id="kesatuan" required><br>
        <br>
        <label for="alamat">Alamat : </label><br>
        <textarea name="alamat" id="alamat" cols="30" rows="5"></textarea>
        <br>
        <label for="foto">Foto : </label><br>
        <input type="file" name="foto" id="foto" accept=".jpg, .jpeg, .png" value="" required><br><br>

        <button type="submit" name="submit">Submit</button>
    </form>
</div>
</body>
</html>