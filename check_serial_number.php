<?php
include "configuration/config_connect.php";

if (isset($_GET['sku'])) {
    $sku = $_GET['sku'];
    $stmt = $conn->prepare("SELECT barcode, sisa, terjual, terbeli, jumlah_masuk, jumlah_keluar FROM barang_detil WHERE id_barang = ?");
    $stmt->bind_param("s", $sku);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $barcodes = [];
        $first_data = [];
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            if ($i == 0) {
                $first_data = $row;
            }
            $barcodes[$i] = $row['barcode'];
            $i++;
        }

        echo json_encode(['success' => true, 'data' => $barcodes, 'first_data' => $first_data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No records found']);
    }
    $stmt->close();
} else if (isset($_GET['sn'])) {
    $serial_number = $_GET['sn'];
    $stmt = $conn->prepare("SELECT sisa, terjual, terbeli, jumlah_masuk, jumlah_keluar FROM barang_detil WHERE barcode = ?");
    $stmt->bind_param("s", $serial_number);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No records found']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'SKU parameter is missing']);
}


$conn->close();
