<?php
include "configuration/config_connect.php";

$con = $_GET['con'];
if ($con == 'show_table') {
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
        $table .= '<td>
                        <button class="btn btn-warning btn-sm" type="button" onclick="edit_barcode(' . "'" . $row['barcode'] . "'" . ', ' . "'" . $row['jumlah_masuk'] . "'" . ');">Ubah</button>
                        <button class="btn btn-danger btn-sm" type="button" onclick="hapus_barcode(' . "'" . $row['id'] . "'" . ');">Hapus</button>
                    </td>';
        $table .= '</tr>';
    }

    // Mengirimkan tabel HTML
    echo $table;
} elseif ($con == 'simpan') {
    $kode = $_GET['kode'];
    $barcode = $_GET['barcode'];
    $stok = $_GET['stok'];

    //check barcode
    $check = mysqli_query($conn, "SELECT id, jumlah_masuk FROM barang_detil WHERE barcode = '$barcode' AND id_barang = '$kode';");
    $row = mysqli_fetch_array($check);
    if (isset($row['id'])) {
        echo "gagal";
    } else {
        $sql2 = "insert into barang_detil (id_barang, barcode, terjual, terbeli) values('$kode','$barcode', '0', '0')";
        $insert = mysqli_query($conn, $sql2);
        if ($insert) {
            echo "berhasil";
        } else {
            echo "gagal";
        }
    }
} elseif ($con == 'hapus') {
    $id = $_GET['id'];

    $sql2 = "DELETE FROM barang_detil WHERE id = '" . $id . "';";
    $delete = mysqli_query($conn, $sql2);
    if ($delete) {
        echo "berhasil";
    } else {
        echo "gagal";
    }
} elseif ($con == 'ubah_barcode') {
    $kode = $_GET['kode'];
    $barcode = $_GET['barcode'];

    $sql2 = "UPDATE barang_detil SET barcode = '$barcode' WHERE barcode = '$kode';";
    $update = mysqli_query($conn, $sql2);
    if ($update) {
        echo "berhasil";
    } else {
        echo "gagal";
    }
}
