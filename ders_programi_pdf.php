<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "proje";

$conn = new mysqli($servername, $username, $password, $dbname);

// BAĞLANTI KONTROLÜ
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// TCPDF KULLANMA
require_once('tcpdf/tcpdf.php');

// TCPDF'nin FONKSİYONU İLE FONT EKLEME
TCPDF_FONTS::addTTFfont('tcpdf/fonts/dejavusans/DejaVuSans.ttf', 'TrueTypeUnicode', '', 32);

// TCPDF NESNESİ
$pdf = new TCPDF();


$pdf->SetFont('dejavusans', '',11, '', false);

$pdf->SetTitle('Ders Programı');

$pdf->AddPage();

// VERİ ÇEKME
$sql = "SELECT d.ders_adi, d.ders_sinif, h.hoca_adi, d.gun, d.saat
        FROM dersler d
        JOIN hoca_ders hd ON d.ders_id = hd.ders_id
        JOIN hocalar h ON hd.hoca_id = h.hoca_id";

$result = $conn->query($sql);

// PROGRAMI BAŞLAT
$schedule = [
    "Pazartesi" => [],
    "Salı" => [],
    "Çarşamba" => [],
    "Perşembe" => [],
    "Cuma" => [],
];

// DERS BILGISINI PROGRAMA EKLE
while ($row = $result->fetch_assoc()) {
    $day = $row["gun"];
    $hour = $row["saat"];
    $courseInfo = "{$row["ders_adi"]}<br>{$row["hoca_adi"]}<br>({$row["ders_sinif"]})";
    $schedule[$day][$hour] = $courseInfo;
}

// TABLO OLUŞTUR
$html = '<h2>Ders Programı</h2>
         <table border="1">
            <tr>
                <th>Saat</th>
                <th>Pazartesi</th>
                <th>Salı</th>
                <th>Çarşamba</th>
                <th>Perşembe</th>
                <th>Cuma</th>
            </tr>';

$hours = ["10:00", "11:00", "12:00", "13:00", "14:00", "15:00"];
foreach ($hours as $hour) {
    $html .= "<tr>";
    $html .= "<td>$hour</td>";
    foreach ($schedule as $day => $daySchedule) {
        $html .= isset($daySchedule[$hour]) ? "<td>{$daySchedule[$hour]}</td>" : "<td></td>";
    }
    $html .= "</tr>";
}

$html .= '</table>';

// PDF'E HTML İÇERİĞİNİ EKLE
$pdf->writeHTML($html, true, false, true, false, '');

// PDF'İ OLUŞTUR
$pdf->Output('Ders_Programi.pdf', 'I'); // 'I' parametresi sayfanın kendisini çağırarak indirmeyi sağlar

// VERITABANI BAĞLANTISINI KAPAT
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ders Programı</title>
    <style>
       
    </style>
</head>
<body>
    <h2>Ders Programı</h2>
    <table border="1">
        <tr>
            <th>Saat</th>
            <th>Pazartesi</th>
            <th>Salı</th>
            <th>Çarşamba</th>
            <th>Perşembe</th>
            <th>Cuma</th>
        </tr>
        <?php
        // SAATLER VE GÜNLER ÜZERİNDE DÖN
        $hours = ["10:00", "11:00", "12:00", "13:00", "14:00", "15:00"];
        foreach ($hours as $hour) {
            echo "<tr>";
            echo "<td>$hour</td>";
            foreach ($schedule as $day => $daySchedule) {
                echo isset($daySchedule[$hour]) ? "<td>{$daySchedule[$hour]}</td>" : "<td></td>";
            }
            echo "</tr>";
        }
        ?>
    </table>
<form method="post">
        <button type="submit" name="createPDF">PDF Olarak Kaydet</button>
    </form>
</body>

</html>
<?php
