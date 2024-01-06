<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "proje";

// MYSQL BAĞLANTISI
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// YENİ DERS EKLEME
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $ders_adi = $_POST["ders_adi"];
    $hoca_adi = $_POST["hoca_adi"];
    $ders_sinif = $_POST["ders_sinif"];

    //AYNI ISIMDE BIR HOCA VAR MI?
    $sql_check_existing_hoca = "SELECT hoca_id FROM hocalar WHERE hoca_adi = '$hoca_adi'";
    $result_existing_hoca = $conn->query($sql_check_existing_hoca);

    if ($result_existing_hoca->num_rows > 0) {
        // AYNI ISIMDE BIR HOCA ZATEN VARSA,HOCAYA DERS EKLE
        $row_existing_hoca = $result_existing_hoca->fetch_assoc();
        $hoca_id = $row_existing_hoca['hoca_id'];
    } else {
        // YENI BIR HOCA OLUŞTUR
        $sql_insert_hoca = "INSERT INTO hocalar (hoca_adi) VALUES ('$hoca_adi')";
        
        if ($conn->query($sql_insert_hoca) === TRUE) {
            $hoca_id = $conn->insert_id;
        } else {
            echo "error:Hata: " . $conn->error;
            exit; 
        }
    }

    // YENI DERS IÇIN OTO DERS_ID 
    $sql_get_next_ders_id = "SELECT MAX(ders_id) + 1 AS next_ders_id FROM dersler";
    $result = $conn->query($sql_get_next_ders_id);
    $row = $result->fetch_assoc();
    $ders_id = $row['next_ders_id'];


    $sql_insert_ders = "INSERT INTO dersler (ders_id, ders_adi, ders_sinif, saat, gun, renk) VALUES ($ders_id, '$ders_adi', $ders_sinif, 'A', 'A', '0')";
    
    // İŞLEMLERI GERÇEKLEŞTIR VE SONUCU BILDIR
    if ($conn->query($sql_insert_ders) === TRUE) {
        // DERS VE HOCA ILIŞKISINI EKLE
        $sql_insert_hoca_ders = "INSERT INTO hoca_ders (hoca_id, ders_id) VALUES ($hoca_id, $ders_id)";

        if ($conn->query($sql_insert_hoca_ders) === TRUE) {
            echo "Ders ve hoca başarıyla eklendi.";
        } else {
            echo "error:Hata: " . $conn->error;
        }
    } else {
        echo "error:Hata: " . $conn->error;
    }
}




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // GÜNCELLEME
    if (isset($_POST["update"])) {
        $ders_id = $_POST["ders_id"];
        $ders_adi = $_POST["ders_adi"];
        $ders_sinif = $_POST["ders_sinif"];

        $sql = "UPDATE dersler SET ders_adi='$ders_adi', ders_sinif='$ders_sinif' WHERE ders_id=$ders_id";

        if ($conn->query($sql) === TRUE) {
            
            echo "success:Ders başarıyla güncellendi.";
        } else {
            echo "error:Hata: " . $sql . "<br>" . $conn->error;
        }
    }

    // SİLME
    elseif (isset($_POST["delete"])) {
        $ders_id = $_POST["ders_id"];

        $sql = "DELETE FROM dersler WHERE ders_id=$ders_id";

        if ($conn->query($sql) === TRUE) {
          
            echo "Ders başarıyla silindi.";
        } else {
            echo "error:Hata: " . $sql . "<br>" . $conn->error;
        }
    }
}

// BÜTÜN DERSLERI VE HOCA ADLARINI GETIR
$sql = "SELECT d.*, h.hoca_adi FROM dersler d
        LEFT JOIN hoca_ders hd ON d.ders_id = hd.ders_id
        LEFT JOIN hocalar h ON hd.hoca_id = h.hoca_id";
$result = $conn->query($sql);

// VERILERI DÖNGÜ ILE AL
$dersler = array();
while ($row = $result->fetch_assoc()) {
    $dersler[] = $row;
}


$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ders Düzenleme</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        #form-container {
            width: 70%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: #fff;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
        }

        .edit-btn, .delete-btn {
            padding: 8px;
            background-color: #3498db;
            color: #fff;
            cursor: pointer;
            border: none;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 40%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover, .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .notification {
        display: none;
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #4CAF50;
        color: white;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 2;
        }

    </style>
</head>
<body>
    <!-- Bildirim Mesajı -->
    <div id="notification" class="notification"></div>
        <!-- Ders Ekleme Butonu -->
        <button onclick="openAddModal()">Ders Ekle</button>
    <div id="form-container">
        <h2>Ders Düzenleme</h2>

        <table>
            <tr>
                <th>Ders ID</th>
                <th>Ders Adı</th>
                <th>Hoca Adı</th>
                <th>Sınıf</th>
                <th>İşlemler</th>
            </tr>
            <?php foreach ($dersler as $ders): ?>
                <tr>
                    <td><?php echo $ders['ders_id']; ?></td>
                    <td><?php echo $ders['ders_adi']; ?></td>
                    <td><?php echo $ders['hoca_adi']; ?></td>
                    <td><?php echo $ders['ders_sinif']; ?></td>
                    <td>
                        <button class="edit-btn" onclick="openModal('<?php echo $ders['ders_id']; ?>')">Düzenle</button>
                        <form style="display:inline;" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="ders_id" value="<?php echo $ders['ders_id']; ?>">
                            <button type="submit" name="delete" onclick="return confirm('Bu dersi silmek istediğinizden emin misiniz?')" class="delete-btn">Sil</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Ders Ekleme Modalı -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddModal()">&times;</span>
            <h2>Ders Ekleme Formu</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="ders_adi">Ders Adı:</label>
                <input type="text" name="ders_adi" id="add_ders_adi" required>

                <label for="hoca_adi">Hoca Adı:</label>
                <input type="text" name="hoca_adi" id="add_hoca_adi" required>

                <label for="ders_sinif">Sınıf:</label>
                <input type="text" name="ders_sinif" id="add_ders_sinif" required>

                <button type="submit" name="add">Dersi Ekle</button>
            </form>
        </div>
    </div>

    <!-- Düzenleme Modalı -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Ders Düzenleme Formu</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="ders_adi">Ders Adı:</label>
                <input type="text" name="ders_adi" id="edit_ders_adi" required>

                <label for="hoca_adi">Hoca Adı:</label>
                <input type="text" name="hoca_adi" id="edit_hoca_adi" required>

                <label for="ders_sinif">Sınıf:</label>
                <input type="text" name="ders_sinif" id="edit_ders_sinif" required>

                <input type="hidden" name="ders_id" id="edit_ders_id">
                <button type="submit" name="update">Dersi Güncelle</button>
            </form>
        </div>
    </div>

    <script>
         function openAddModal() {
            var modal = document.getElementById('addModal');
            modal.style.display = 'block';
        }

        function closeAddModal() {
            var modal = document.getElementById('addModal');
            modal.style.display = 'none';
        }
    function openModal(ders_id) {
        var modal = document.getElementById('editModal');
        modal.style.display = 'block';

        // SEÇILEN DERSIN BILGILERINI MODAL IÇINDEKI FORM ALANLARINA DOLDUR
        var ders = <?php echo json_encode($dersler); ?>;
        var selectedDers = ders.find(function(d) { return d.ders_id == ders_id; });

        document.getElementById('edit_ders_adi').value = selectedDers.ders_adi;
        document.getElementById('edit_hoca_adi').value = selectedDers.hoca_adi;
        document.getElementById('edit_ders_sinif').value = selectedDers.ders_sinif;
        document.getElementById('edit_ders_id').value = selectedDers.ders_id;
    }

    function closeModal() {
        var modal = document.getElementById('editModal');
        modal.style.display = 'none';
    }

    //MODAL DIŞINA TIKLANINCA KAPA
    window.onclick = function(event) {
        var modal = document.getElementById('editModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // BİLDİİRM MESAJI
    function showNotification(message) {
        var notification = document.getElementById('notification');
        notification.innerHTML = message;
        notification.style.display = 'block';
        setTimeout(function() {
            notification.style.display = 'none';
        }, 3000);

        // ÜSTE ÇIK
        window.scrollTo(0, 0);
    }

    // PHP KODU ILE BILDIRIM MESAJINI KONTROL ET
    <?php
    if (isset($_POST["update"]) || isset($_POST["delete"])) {
        $result = explode(":", $conn->query($sql));
        $status = $result[0];
        $message = $result[1];

        if ($status === "success") {
            echo "showNotification('$message');";
        } elseif ($status === "error") {
            echo "showNotification('Hata: $message');";
        }
    }
    ?>
</script>

</body>
</html>


