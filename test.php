<?php
include "configuration/config_connect.php";

$con = $_GET['con'];
if ($con == 'show_table') {
    // Koneksi ke Redis
$redis = new Redis();
$redis->connect('localhost', 6379);

// Hapus semua cache
$redis->flushAll();

    $kode = $_GET['kode'];

    // Query untuk mengambil data
    $sql = mysqli_query($conn, "SELECT * FROM barang_detil WHERE id_barang = '$kode';");

    // Menyiapkan tabel HTML
    $table = '';
    $no = 1;
    while ($row = mysqli_fetch_assoc($sql)) {
        $table .= '<tr>';
        $table .= '<td>' . $no++ . '</td>';
        $table .= '<td>' . $row['barcode'] . '</td>';
        $table .= '<td>' . $row['jumlah_masuk'] . '</td>';
        $table .= '<td>
                        <button class="btn btn-warning btn-sm" type="button" onclick="edit_barcode(' . "'" . $row['barcode'] . "'" . ', ' . "'" . $row['jumlah_masuk'] . "'" . ');">Ubah</button>
                        <button class="btn btn-danger btn-sm" type="button" onclick="hapus_barcode(' . "'" . $row['id'] . "'" . ');">Hapus</button>
                    </td>';
        $table .= '</tr>';
    }

    // Mengirimkan tabel HTML
    echo $table;
} elseif ($con == 'hapus') {
    $id = $_GET['id'];

    $sql2 = "DELETE FROM barang_detil WHERE id = '" . $id . "';";
    $delete = mysqli_query($conn, $sql2);
    if ($delete) {
        echo "berhasil";
    } else {
        echo "gagal";
    }
}
