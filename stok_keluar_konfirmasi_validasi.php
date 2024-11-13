<?php
include "configuration/config_connect.php";

if (isset($_POST['validasi'])) {
    $nota = $_POST['nota'];
    $no = $_POST['no'];
    $kembali = $_POST['kembali'];

    $sql1 = "SELECT * FROM surat WHERE nota = '$nota';";
    $query1 = mysqli_query($conn, $sql1);
    $num_rows1 = mysqli_num_rows($query1);
    $assoc1 = mysqli_fetch_assoc($query1);
    $validasi = $assoc1['verifikasi'];
    if ($num_rows1 > 0) {
        if ($validasi == '0') {
            for ($i = 0; $i < count($no); $i++) {
                $sql2 = "SELECT * FROM stok_keluar_daftar WHERE nota = '$nota' AND no = '$no[$i]';";
                $query2 = mysqli_query($conn, $sql2);
                $row2 = mysqli_fetch_assoc($query2);
                $barcode = $row2['barcode'];
                $kode_barang = $row2['kode_barang'];
                $jumlah = intval($row2['jumlah']);
                $kembali_int = intval($kembali[$i]);
                $jumlah_keluar = $jumlah - $kembali_int;
                $update1 = "UPDATE barang_detil SET terjual = terjual + $jumlah_keluar, sisa = (sisa - $jumlah_keluar) WHERE barcode = '$barcode';";
                $update2 = "UPDATE barang SET terjual = (terjual + $jumlah_keluar), sisa = (sisa - $jumlah_keluar) WHERE no = '$kode_barang';";
                $update3 = "UPDATE stok_keluar_daftar SET jumlah_keluar = $jumlah_keluar, jumlah_kembali = $kembali_int WHERE nota = '$nota' AND no = '$no[$i]';";
                $query3 = mysqli_query($conn, $update1);
                $query4 = mysqli_query($conn, $update2);
                $query5 = mysqli_query($conn, $update3);
                // echo $update1 . "<br>";
                // echo $update2 . "<br>";
                // echo $update3 . "<br>";
                // echo "<br>";
            }
            $update4 = "UPDATE surat SET verifikasi = '1' WHERE nota = '$nota';";
            $query6 = mysqli_query($conn, $update4);
            echo "<script>alert('Berhasil validasi stok keluar');</script>";
            echo "<script>window.location.href = 'surat_kelola';</script>";
        } else {
            echo "<script>alert('Stok keluar sudah divalidasi');</script>";
            echo "<script>window.location.href = 'stok_keluar_konfirmasi?nota=$nota';</script>";
        }
    } else {
        echo "<script>alert('Gagal validasi stok keluar');</script>";
        echo "<script>window.location.href = 'stok_keluar_konfirmasi?nota=$nota';</script>";
    }
} else {
    echo "<script>alert('Anda tidak memiliki akses untuk halaman ini');</script>";
    echo "<script>window.location.href = 'surat_kelola';</script>";
}
