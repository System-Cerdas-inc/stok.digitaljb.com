<?php
include "configuration/config_connect.php";

if (isset($_GET['sku'])) {
    $sku = $_GET['sku'];
    $stmt = $conn->prepare("SELECT barcode FROM barang_detil WHERE id_barang = ?");
    $stmt->bind_param("s", $sku);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $barcodes = [];
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $barcodes[$i] = $row['barcode'];
            $i++;
        }

        echo json_encode(['success' => true, 'data' => $barcodes]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No records found']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'SKU parameter is missing']);
}


$conn->close();
