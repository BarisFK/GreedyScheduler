<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "proje";

$conn = new mysqli($servername, $username, $password, $dbname);

// BAĞLANTI KONTROLU
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// VERİ ÇEKME
$sql = "SELECT d.ders_id, d.ders_adi, d.ders_sinif, h.hoca_adi, d.gun, d.saat
        FROM dersler d
        JOIN hoca_ders hd ON d.ders_id = hd.ders_id
        JOIN hocalar h ON hd.hoca_id = h.hoca_id";

$result = $conn->query($sql);

// OLASI GÜN-SAAT KOMBINASYONLARI İÇİN DIZI
$dayHours = [];
$days = ["Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma"];
$hours = ["10:00", "11:00", "12:00", "13:00", "14:00", "15:00"];

foreach ($days as $day) {
    foreach ($hours as $hour) {
        $dayHours[] = ["day" => $day, "hour" => $hour];
    }
}

// GRAFI DERSLER VE ÇAKIŞMALARIYLA DOLDUR, GÜN-SAAT KOMBINASYONLARINI RASTGELE ATA
$graph = [];
while ($row = $result->fetch_assoc()) {
    $course = [
        "ders_id" => $row["ders_id"],
        "ders_adi" => $row["ders_adi"],
        "ders_sinif" => $row["ders_sinif"],
        "hoca_adi" => $row["hoca_adi"],
        "conflicts" => [],
        "day_hour" => null, 
        "color" => null, 
    ];

    // DERSİ GRAFA EKLE
    $graph[$row["ders_id"]] = $course;


    $courseId = null; // veya başka bir değerle başlatabilirsiniz
    //DERSLER ÇAKIŞIYOR MU DİYE KONTROL ET
    foreach ($graph as $otherCourseId => $otherCourse) {
        // AYNI DERS İSE ATLA
    if ($courseId == $otherCourseId) {
        continue;
    }

    if (
        $course["ders_sinif"] == $otherCourse["ders_sinif"] ||
        $course["hoca_adi"] == $otherCourse["hoca_adi"]
    ) {
        // ÇAKIŞAN DERSİ ÇAKIŞMALAR DİZİSİNE EKLE
        $graph[$row["ders_id"]]["conflicts"][] = $otherCourseId;
        $graph[$otherCourseId]["conflicts"][] = $row["ders_id"];
    }
    }

    // RASTGELE BIR GÜN-SAAT KOMBINASYONU ATAYIN
    shuffle($dayHours);
    $randomDayHour = array_shift($dayHours);

    // VERITABANINDAKİ GÜN VE SAAT DEĞERLERINI GÜNCELLE
    $updateSql = "UPDATE dersler SET gun = '{$randomDayHour["day"]}', saat = '{$randomDayHour["hour"]}' WHERE ders_id = '{$row["ders_id"]}'";
    $conn->query($updateSql);

    $graph[$row["ders_id"]]["day_hour"] = $randomDayHour;
}

// GREEDY ALGORİTMASI 
$coloring = [];
$usedColors = [];

foreach ($graph as $courseId => $course) {
    // MEVCUT DERSIN KOMŞULARINI AL
    $neighbors = $course["conflicts"];

    // MEVCUT DERS IÇIN UYGUN EN KÜÇÜK RENGI BUL
    foreach ($usedColors as $color) {
        $conflict = false;
        foreach ($neighbors as $neighborId) {
            if (
                isset($coloring[$neighborId]) &&
                $coloring[$neighborId]["color"] == $color
            ) {
                $conflict = true;
                break;
            }
        }
        if (!$conflict) {
            $graph[$courseId]["color"] = $color;
            break;
        }
    }

    // EĞER UYGUN RENK YOKSA, YENI BIR RENK EKLE
    if ($graph[$courseId]["color"] === null) {
        $newColor = count($usedColors);
        $usedColors[] = $newColor;
        $graph[$courseId]["color"] = $newColor;
    }

    // RENGI DERSLE ILIŞKILENDIR
    $coloring[$courseId] = [
        "course" => $course,
        "color" => $graph[$courseId]["color"],
    ];

    // VERITABANINDA RENGI GÜNCELLE
    $updateColorSql = "UPDATE dersler SET renk = '{$graph[$courseId]["color"]}' WHERE ders_id = '{$courseId}'";
    $conn->query($updateColorSql);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ders Programı</title>
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        #graph-container {
            width: 80%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
        }

        th {
            background-color: #3498db;
            color: #fff;
        }

        .day-cell {
            font-weight: bold;
            color: #555;
        }

        .hour-cell {
            font-style: italic;
            color: #777;
        }

        <?php foreach ($coloring as $entry) {
            $color = $entry["color"];
            echo ".color-$color { background-color: #" .
                dechex(rand(0x000000, 0xffffff)) .
                "; }";
        } ?>
    </style>
</head>
<body>

<div id="table-container">
    <h2>Ders Programı</h2>
        
    <table>
        <tr>
            <th></th>
            <th class="day-cell">Pazartesi</th>
            <th class="day-cell">Salı</th>
            <th class="day-cell">Çarşamba</th>
            <th class="day-cell">Perşembe</th>
            <th class="day-cell">Cuma</th>
        </tr>
        <?php
        // PROGRAMI BAŞLAT
        $schedule = [
            "Pazartesi" => [],
            "Salı" => [],
            "Çarşamba" => [],
            "Perşembe" => [],
            "Cuma" => [],
        ];

        foreach ($coloring as $entry) {
            $course = $entry["course"];
            $color = $entry["color"];
            $class = "color-$color";

            // DERS BILGISINI AIT OLDUGU HÜCREYE EKLE
            $day = $course["day_hour"]["day"];
            $hour = $course["day_hour"]["hour"];
            $schedule[$day][
                $hour
            ] = "<td class=\"$class\">{$course["ders_adi"]}<br>{$course["hoca_adi"]}<br>({$course["ders_sinif"]})</td>";
        }

          // FALAN  FILAN DONGUSU
        foreach ($hours as $hour) {
            echo "<tr><td class=\"hour-column hour-cell\">$hour</td>";
            foreach ($days as $day) {
                echo isset($schedule[$day][$hour])
                    ? $schedule[$day][$hour]
                    : "<td></td>";
            }
            echo "</tr>";
        }
        ?>
    </table>
    <button onclick="window.location.href='veri_duzenle.php'">Veri Düzenle</button>
    <button onclick="openDersProgrami()">Ders Programını Onayla</button>
    <script>
        function openDersProgrami() {
        
            window.open('ders_programi_pdf.php', '_blank');
        }
    </script>
</div>

</body>
</html>
