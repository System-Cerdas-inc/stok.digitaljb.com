<?php
include "../../configuration/config_connect.php";
include "../../configuration/config_session.php";
include "../../configuration/config_chmod.php";
include "../../configuration/config_etc.php";
$forward = $_GET['forward'];
$no = $_GET['no'];
$nota = $_GET['nota'];
$jml = $_GET['jumlah'];
$barcode = $_GET['barcode'];
$chmod = $_GET['chmod'];
$forwardpage = $_GET['forwardpage'];
?>

<?php
if ($chmod == '4' || $chmod == '5' || $_SESSION['jabatan'] == 'admin' || $_SESSION['jabatan'] == 'guru') {


  $sqle3 = "SELECT * FROM barang_detil where barcode='$barcode'";
  $hasile3 = mysqli_query($conn, $sqle3);
  $row = mysqli_fetch_assoc($hasile3);
  $masuk = $row['terbeli'] - $jml;
  $stok = $row['sisa'] - $jml;
  $sqla = mysqli_query($conn, "UPDATE barang_detil SET sisa='$stok', terbeli='$masuk' WHERE barcode='$barcode'");
  mysqli_query($conn, $sqla);

  $sqle4 = "SELECT * FROM barang where barcode='$barcode'";
  $hasile4 = mysqli_query($conn, $sqle4);
  $row4 = mysqli_fetch_assoc($hasile4);
  $masuk4 = $row4['terbeli'] - $jml;
  $stok4 = $row4['sisa'] - $jml;
  $sqlb = mysqli_query($conn, "UPDATE barang SET sisa='$stok4', terbeli='$masuk4' WHERE barcode='$barcode'");
  mysqli_query($conn, $sqlb);

  $sql = "delete from $forward where no='" . $no . "'";
  if (mysqli_query($conn, $sql)) {

    $sqlb = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM stok_masuk_daftar WHERE nota='$nota'"));
    if ($sqlb == 0) {

      $sqlc = mysqli_query($conn, "DELETE FROM stok_masuk WHERE nota='$nota'");
    }

?>



    <body onload="setTimeout(function() { document.frm1.submit() }, 10)">
      <form action="<?php echo $baseurl; ?>/<?php echo $forwardpage; ?>?nota=<?php echo $nota; ?>" name="frm1" method="post">

        <input type="hidden" name="hapusberhasil" value="1" />

      <?php
    } else {
      ?>

        <body onload="setTimeout(function() { document.frm1.submit() }, 10)">
          <input type="hidden" name="hapusberhasil" value="2" />
        <?php
      }
    } else {

        ?>

        <body onload="setTimeout(function() { document.frm1.submit() }, 10)">
          <form action="<?php echo $baseurl; ?>/<?php echo $forwardpage; ?>?nota=<?php echo $nota; ?>" name="frm1" method="post">


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
          <meta http-equiv="refresh" content="10;url=jump?forward=<?php echo $forward . '&'; ?>forwardpage=<?php echo $forwardpage . '&'; ?>chmod=<?php echo $chmod; ?>">
        </body>