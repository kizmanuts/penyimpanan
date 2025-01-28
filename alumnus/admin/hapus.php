<?php
include "../koneksi.php";

if(isset($_GET['id'])){
    $id=$_GET['id'];
    $del=mysqli_query($kon,"DELETE FROM alumnus WHERE id=$id");
    header('location:kelola.php');
}
else{
    echo "Invalid";
}