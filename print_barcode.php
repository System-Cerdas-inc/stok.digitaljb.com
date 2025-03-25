<?php
include "configuration/config_barcode.php";
include "configuration/config_connect.php";

$kolom = isset($_POST['kolom']) ? intval($_POST['kolom']) : 1;
$copy = isset($_POST['jumlah']) && $_POST['jumlah'] !== "" ? intval($_POST['jumlah']) : 1;
$barcode = isset($_POST['barcode']) ? $_POST['barcode'] : '';
$serial_number = isset($_POST['serial_number']) ? $_POST['serial_number'] : '';
$generate = isset($_POST['generate']) ? $_POST['generate'] : '';
$kode = isset($_POST['kode']) ? $_POST['kode'] : '';
$counter = 1;

echo "<table cellpadding='10'>";

for ($ucopy = 1; $ucopy <= $copy; $ucopy++) {
    if (($counter - 1) % $kolom === 0) {
        echo "<tr>";
    }

    echo "<td class='merk'>";
    if ($generate == 'SN') {
        echo bar128(stripslashes($serial_number));
    } else {
        echo bar128(stripslashes($barcode));
    }
    echo "</td>";

    if ($counter % $kolom === 0) {
        echo "</tr>";
    }

    $counter++;
}

if (($counter - 1) % $kolom !== 0) {
    echo "</tr>";
}

echo "</table>";
?>

<script>


</script>