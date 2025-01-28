<?php
session_start(); 
session_destroy(); 
header("Location: kelola.php"); // Arahkan pengguna kembali ke halaman utama atau halaman login
exit();
?>