<?php
include "../../configuration/config_connect.php";
include "../../configuration/config_session.php";
include "../../configuration/config_chmod.php";
include "../../configuration/config_etc.php";
$forward = $_GET['forward'];
$no = $_GET['no'];
$chmod = $_GET['chmod'];
$forwardpage = $_GET['forwardpage'];
$kode = $_GET['kode'];
$jumlah = $_GET['jumlah'];
$barang = $_GET['barang'];
$barcode = $_GET['barcode'];
$get = $_GET['get'];

?>

<?php
if ($chmod == '4' || $chmod == '5' || $_SESSION['jabatan'] == 'admin') {

  if ($get == 'out') {
    $sqle3 = "SELECT * FROM barang where kode='$barang'";
    $hasile3 = mysqli_query($conn, $sqle3);
    $row = mysqli_fetch_assoc($hasile3);
    $terjualawal = $row['terjual'];
    $sisa = $row['sisa'];

    $terjualakhir = $terjualawal - $jumlah;
    $sisaakhir = $sisa + $jumlah;

    $sqll3 = "UPDATE barang SET terjual='$terjualakhir', sisa='$sisaakhir' where kode='$barang'";

    $updatestok = mysqli_query($conn, $sqll3);

    $sqla = "delete from $forward where no='" . $no . "'";
  } else {
    $sqle3 = "SELECT * FROM barang where kode='$barang'";
    $hasile3 = mysqli_query($conn, $sqle3);
    $row = mysqli_fetch_assoc($hasile3);
    $sisaawal = $row['sisa'];
    $terbeliawal = $row['terbeli'];
    $terjualawal = $row['terjual'];
    if ($jumlah >= $terbeliawal) {
      $terbeliakhir = $jumlah - $terbeliawal;
    } else if ($jumlah <= $terbeliawal) {
      $terbeliakhir = $terbeliawal - $jumlah;
    }
    $sisaakhir = $terbeliakhir - $terjualawal;

    //get data barang detail
    $hasile4 = mysqli_query($conn, "SELECT * FROM barang_detil where id_barang='SKU$barang' AND barcode = '$barcode';");
    $row4 = mysqli_fetch_assoc($hasile4);
    $sisaawal4 = $row4['jumlah_masuk'];
    $terbeliawal4 = $row4['terbeli'];
    $terjualawal4 = $row4['terjual'];
    if ($jumlah >= $terbeliawal4) {
      $terbeliakhir4 = $jumlah - $terbeliawal4;
    } else if ($jumlah <= $terbeliawal4) {
      $terbeliakhir4 = $terbeliawal4 - $jumlah;
    }
    $sisaakhir4 = $terbeliakhir4 - $terjualawal4;
    //cek jumlah sisa
    if ($sisaakhir4 == '0') {
      mysqli_query($conn, "DELETE from barang_detil where id='" . $row4['id'] . "'");
    } else {
      mysqli_query($conn, "UPDATE barang_detil SET terbeli='$terbeliakhir4', jumlah_masuk='$sisaakhir4' where id='" . $row4['id'] . "'");
    }

    $sql3 = "UPDATE barang SET terbeli='$terbeliakhir', sisa='$sisaakhir' where kode='$barang'";
    $updatestok = mysqli_query($conn, $sql3);
    $sqla = "delete from $forward where no='" . $no . "'";
  }


  if (mysqli_query($conn, $sqla)) {


?>

    <body onload="setTimeout(function() { document.frm1.submit() }, 10)">
      <form action="<?php echo $baseurl; ?>/<?php echo $forwardpage; ?>" name="frm1" method="post">

        <input type="hidden" name="kode" value="<?php echo $kode; ?>" />
        <input type="hidden" name="hapusberhasil" value="1" />

      <?php
    } else {
      ?>

        <body onload="setTimeout(function() { document.frm1.submit() }, 10)">
          <input type="hidden" name="kode" value="<?php echo $kode; ?>" />
          <input type="hidden" name="hapusberhasil" value="2" />
        <?php
      }
    } else {

        ?>

        <body onload="setTimeout(function() { document.frm1.submit() }, 10)">
          <form action="<?php echo $baseurl; ?>/<?php echo $forwardpage; ?>" name="frm1" method="post">

            <input type="hidden" name="kode" value="<?php echo $kode; ?>" />
            <input type="hidden" name="hapusberhasil" value="2" />
          <?php
        }
          ?>
          <table width="100%" align="center" cellspacing="0">
            <tr>
              <td height="500px" align="center" valign="middle"><img src="../../dist/img/load.gif">
            </tr>
          </table>


          </form>
          <meta http-equiv="refresh" content="10;url=jump?kode=<?php echo $kode . '&'; ?>forward=<?php echo $forward . '&'; ?>forwardpage=<?php echo $forwardpage . '&'; ?>chmod=<?php echo $chmod; ?>">
        </body>