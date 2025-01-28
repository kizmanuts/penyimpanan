<?php
session_start();
include "koneksi.php"; 

// Handle the form submission to set session variables
if (isset($_POST['submit'])) {
    $tahun = intval($_POST['tahun']); // Validasi input
    $jenis_penataran = $_POST['jenis_penataran'];

    // Debugging: Cek nilai
    error_log("Tahun: $tahun, Jenis Penataran: $jenis_penataran");

    // Prepare statement to check if the selected year and field exist
    $stmt = $kon->prepare("SELECT DISTINCT tahun, jenis_penataran FROM alumnus WHERE tahun=? AND jenis_penataran=?");
    $stmt->bind_param("is", $tahun, $jenis_penataran);
    
    if (!$stmt->execute()) {
        echo "<script>alert('Query gagal: " . $stmt->error . "')</script>";
    }

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['tahun'] = $tahun;
        $_SESSION['jenis_penataran'] = $jenis_penataran;
        header("location: kelola.php");
        exit();
    } else {
        echo "<script>alert('Data yang anda input tidak ada. Silahkan coba lagi')</script>";
    }
    if (empty($tahun) || empty($jenis_penataran)) {
        echo "<script>alert('Tahun atau jenis penataran tidak boleh kosong.')</script>";
        return; // Hentikan eksekusi jika ada yang kosong
    }
}
// Check if session variables are set for displaying alumni data
if (isset($_SESSION['tahun']) && isset($_SESSION['jenis_penataran'])) {
    $tahun = $_SESSION['tahun'];
    $jenis_penataran = $_SESSION['jenis_penataran'];

    // Default sorting option
    $sortOption = isset($_GET['sort']) ? $_GET['sort'] : 'default';
    // Initialize search variable
    $search = isset($_POST['search']) ? $_POST['search'] : '';

    // Determine the SQL ORDER BY clause based on the selected sort option
    switch ($sortOption) {
        case 'largest':
            $orderBy = 'nilai DESC';
            break;
        case 'smallest':
            $orderBy = 'nilai ASC';
            break;
        case 'az':
            $orderBy = 'nama ASC';
            break;
        case 'za':
            $orderBy = 'nama DESC';
            break;
        default:
            $orderBy = 'id';
    }

    // Prepare search query
    $searchQuery = $search ? "AND (nama LIKE ? OR nrp LIKE ? OR pangkat LIKE ?)" : '';
    $searchTerm = "%$search%";

    // Prepare final query
    $stmt = $kon->prepare("SELECT * FROM alumnus WHERE tahun=? AND jenis_penataran=? $searchQuery ORDER BY $orderBy");
    if ($search) {
        $stmt->bind_param("sss", $tahun, $jenis_penataran, $searchTerm);
    } else {
        $stmt->bind_param("ss", $tahun, $jenis_penataran);
    }
    $stmt->execute();
    $alumnus = $stmt->get_result();

    // Fetch distinct tahun and jenis_penataran for display
    $rows = $kon->prepare("SELECT DISTINCT tahun, jenis_penataran FROM alumnus WHERE tahun=? AND jenis_penataran=?");
    $rows->bind_param("si", $tahun, $jenis_penataran);
    $rows->execute();
    $resultRows = $rows->get_result();
    $row = $resultRows->fetch_assoc();
    ?>
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Daftar Alumnus</title>
      
    </head>
    <body>
        <h2>Daftar Alumnus Penataran <?php echo htmlspecialchars($row['jenis_penataran']); ?> Tahun <?php echo htmlspecialchars($row['tahun']); ?></h2>

        <a href="kembali.php">Kembali</a>
        <form method="GET" action="">
            <label for="sort">Urutkan:</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="default">Default</option>
                <option value="largest" <?php if ($sortOption == 'largest') echo 'selected'; ?>>Nilai Terbesar</option>
                <option value="smallest" <?php if ($sortOption == 'smallest') echo 'selected'; ?>>Nilai Terkecil</option>
                <option value="az" <?php if ($sortOption == 'az') echo 'selected'; ?>>A-Z</option>
                <option value="za" <?php if ($sortOption == 'za') echo 'selected'; ?>>Z-A</option>
            </select>
        </form>
        <form method="POST" action="">
            <label for="search">Cari:</label>
            <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>">
            <input type="submit" value="Search">
        </form>

        <div style="overflow-x:auto;">
            <table border="1" cellspacing="0" cellpadding="10">
                <tr>
                    <th>No</th>
                    <th>Jenis Penataran</th>
                    <th>Tahun</th>
                    <th>Nama</th>
                    <th>Pangkat</th>
                    <th>NRP</th>
                    <th>Nilai Akhir</th>
                    <th>Tempat Tanggal Lahir</th>
                    <th>Kesatuan</th>
                    <th>Alamat</th>
                    <th>Foto</th>
                  
                </tr>
                <?php
                $i = 1; // Initialize counter
                while ($rowAlumnus = $alumnus->fetch_assoc()) {
                ?>
                    <tr align="center">
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($rowAlumnus['jenis_penataran']); ?></td> 
                        <td><?php echo htmlspecialchars($rowAlumnus['tahun']); ?></td> 
                        <td><?php echo htmlspecialchars($rowAlumnus['nama']); ?></td>
                        <td><?php echo htmlspecialchars($rowAlumnus['pangkat']); ?></td>
                        <td><?php echo htmlspecialchars($rowAlumnus['nrp']); ?></td>
                        <td><?php echo htmlspecialchars($rowAlumnus['nilai']); ?></td>
                        <td><?php echo htmlspecialchars($rowAlumnus['ttl']); ?></td>
                        <td><?php echo htmlspecialchars($rowAlumnus['kesatuan']); ?></td>
                        <td><?php echo htmlspecialchars($rowAlumnus['alamat']); ?></td>
                        <td><img src="img-profil/<?php echo htmlspecialchars($rowAlumnus['foto']); ?>" alt="" width="150"></td>

                    </tr>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>

        <!-- Modal -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <form action="" method="post"> 
                    <label for="">Pilih Tahun</label><br>
                    <input type="text" id="tahun" name="tahun" required>                
                    <br><br>
                    <label for="">Pilih Jenis Penataran</label><br> 
                    <select name="jenis_penataran" id="jenis_penataran" required>
                        <option value="Pertahanan Siber">Pertahanan Siber</option>
                        <option value="Pengamanan Sistem Informasi">Pengamanan Sistem Informasi</option>
                        <option value="Pengamanan Jaringan">Pengamanan Jaringan</option>
                        <option value="Teknisi Jaringan Dan Komputer">Teknisi Jaringan Dan Komputer</option>
                        <option value="Administrator Jaringan">Administrator Jaringan</option>
                        <option value="Literasi Komputer">Literasi Komputer</option>
                        <option value="Pemograman Website">Pemograman Website</option>
                        <option value="Pemograman Aplikasi Mobile">Pemograman Aplikasi Mobile</option>
                        <option value="Multimedia, Desain & Grafis">Multimedia, Desain & Grafis</option>
                    </select>
                    <br><br>
                    <button type="submit" name="submit">Konfirmasi</button>
                </form>
            </div>
        </div>

        <script>
            // Get the modal
            var modal = document.getElementById("myModal");

            // Get the button that opens the modal
            var btn = document.getElementById("openModal");

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // When the user clicks the button, open the modal 
            btn.onclick = function() {
                modal.style.display = "block";
            }

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        </script>
    </body>
    </html>
    <?php
} else {
    // Display all alumni data if session variables are not set
    $sortOption = isset($_GET['sort']) ? $_GET['sort'] : 'default';
    $search = isset($_POST['search']) ? $_POST['search'] : '';

    // Determine the SQL ORDER BY clause based on the selected sort option
    switch ($sortOption) {
        case 'largest':
            $orderBy = 'nilai DESC';
            break;
        case 'smallest':
            $orderBy = 'nilai ASC';
            break;
        case 'az':
            $orderBy = 'nama ASC';
            break;
        case 'za':
            $orderBy = 'nama DESC';
            break;
        default:
            $orderBy = 'id';
    }

    // Prepare search query
    $searchQuery = $search ? "WHERE (nama LIKE ? OR nrp LIKE ? OR pangkat LIKE ?)" : '';
    $searchTerm = "%$search%";

    // Prepare final query
    $stmt = $kon->prepare("SELECT * FROM alumnus $searchQuery ORDER BY $orderBy");
    if ($search) {
        $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    }
    $stmt->execute();
    $alumnus = $stmt->get_result();
    ?>
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Daftar Alumnus</title>
    </head>
    <body>
        <h2>Daftar Semua Alumnus</h2>
        <a href="#" id="openModal">Pilih Jenis Penataran</a>
        <form method="GET" action="">
            <label for="sort">Urutkan:</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="default">Default</option>
                <option value="largest" <?php if ($sortOption == 'largest') echo 'selected'; ?>>Nilai Terbesar</option>
                <option value="smallest" <?php if ($sortOption == 'smallest') echo 'selected'; ?>>Nilai Terkecil</option>
                <option value="az" <?php if ($sortOption == 'az') echo 'selected'; ?>>A-Z</option>
                <option value="za" <?php if ($sortOption == 'za') echo 'selected'; ?>>Z-A</option>
            </select>
        </form>
        <form method="POST" action="">
            <label for="search">Cari:</label>
            <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>">
            <input type="submit" value="Search">
        </form>

        <div style="overflow-x:auto;">
            <table border="1" cellspacing="0" cellpadding="10">
                <tr>
                    <th>No</th>
                    <th>Jenis Penataran</th>
                    <th>Tahun</th>
                    <th>Nama</th>
                    <th>Pangkat</th>
                    <th>NRP</th>
                    <th>Nilai Akhir</th>
                    <th>Tempat Tanggal Lahir</th>
                    <th>Kesatuan</th>
                    <th>Alamat</th>
                    <th>Foto</th>
                  
                </tr>
                <?php
                $i = 1; // Initialize counter
                while ($rowAll = $alumnus->fetch_assoc()) {
                ?>
                    <tr align="center">
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($rowAll['jenis_penataran']); ?></td> 
                        <td><?php echo htmlspecialchars($rowAll['tahun']); ?></td> 
                        <td><?php echo htmlspecialchars($rowAll['nama']); ?></td>
                        <td><?php echo htmlspecialchars($rowAll['pangkat']); ?></td>
                        <td><?php echo htmlspecialchars($rowAll['nrp']); ?></td>
                        <td><?php echo htmlspecialchars($rowAll['nilai']); ?></td>
                        <td><?php echo htmlspecialchars($rowAll['ttl']); ?></td>
                        <td><?php echo htmlspecialchars($rowAll['kesatuan']); ?></td>
                        <td><?php echo htmlspecialchars($rowAll['alamat']); ?></td>
                        <td><img src="img-profil/<?php echo htmlspecialchars($rowAll['foto']); ?>" alt="" width="150"></td>
                       
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>

        <!-- Modal -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <form action="" method="post"> 
                    <label for="tahun">Masukkan Tahun</label><br>
                    <input type="text" id="tahun" name="tahun" required>                
                    <br><br>
                    <label for="jenis_penataran">Pilih Jenis Penataran</label><br>  
                    <select name="jenis_penataran" id="jenis_penataran" required>
                        <option value="Pertahanan Siber">Pertahanan Siber</option>
                        <option value="Pengamanan Sistem Informasi">Pengamanan Sistem Informasi</option>
                        <option value="Pengamanan Jaringan">Pengamanan Jaringan</option>
                        <option value="Teknisi Jaringan Dan Komputer">Teknisi Jaringan Dan Komputer</option>
                        <option value="Administrator Jaringan">Administrator Jaringan</option>
                        <option value="Literasi Komputer">Literasi Komputer</option>
                        <option value="Pemograman Website">Pemograman Website</option>
                        <option value="Pemograman Aplikasi Mobile">Pemograman Aplikasi Mobile</option>
                        <option value="Multimedia, Desain & Grafis">Multimedia, Desain & Grafis</option>
                    </select>
                    <br><br>
                    <button type="submit" name="submit">Konfirmasi</button>
                </form>
            </div>
        </div>

        <script>
            // Get the modal
            var modal = document.getElementById("myModal");

            // Get the button that opens the modal
            var btn = document.getElementById("openModal");

            // Get the <span> element that closes the modal
            var span = document.getElementsByClassName("close")[0];

            // When the user clicks the button, open the modal 
            btn.onclick = function() {
                modal.style.display = "block";
            }

            // When the user clicks on <span> (x), close the modal
            span.onclick = function() {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        </script>
    </body>
    </html>
    <?php
}
?>
  <style>
            /* Modal styles */
            .modal {
                display: none; 
                position: fixed; 
                z-index: 1000; 
                left: 0;
                top: 0;
                width: 100%; 
                height: 100%; 
                overflow: auto; 
                background-color: rgba(0, 0, 0, 0.5); 
                padding-top: 60px; 
            }
            .modal-content {
                background-color: #fefefe;
                margin: 15% auto; 
                padding: 20px;
                border: 1px solid #888;
                width: 80%; 
            }
            .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }
            .close:hover,
            .close:focus {
                color: black;
                text-decoration: none;
                cursor: pointer;
            }
        </style>