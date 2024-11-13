
<!DOCTYPE html>
<html>
<?php
include "configuration/config_etc.php";
include "configuration/config_include.php";
etc();
encryption();
session();
connect();
head();
body();
timing();
//alltotal();
pagination();
?>

<?php
if (!login_check()) {
?>
  <meta http-equiv="refresh" content="0; url=logout" />
<?php
  exit(0);
}
?>
<div class="wrapper">
  <?php
  theader();
  menu();
  ?>
  <div class="content-wrapper">
    <section class="content-header">
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <!-- ./col -->

          <!-- SETTING START-->

          <?php
          error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
          include "configuration/config_chmod.php";
          $halaman = "stok_out"; // halaman
          $dataapa = "Stok Keluar"; // data
          $tabeldatabase = "stok_keluar"; // tabel database
          $tabel = "stok_keluar_daftar";
          $chmod = $chmenu5; // Hak akses Menu
          $forward = mysqli_real_escape_string($conn, $tabeldatabase); // tabel database
          $forwardpage = mysqli_real_escape_string($conn, $halaman); // halaman
          $search = $_POST['search'];
          $insert = $_POST['insert'];




          function autoNumber()
          {
            include "configuration/config_connect.php";
            global $forward;
            $query = "SELECT MAX(no) as max_id FROM stok_keluar ORDER BY no";
            $result = mysqli_query($conn, $query);
            $data = mysqli_fetch_array($result);
            $id_max = $data['max_id'];
            $sort_num = (int) $id_max;
            $sort_num++;
            $new_code = sprintf("%04s", $sort_num);
            return $new_code;
          }
          ?>


          <!-- SETTING STOP -->


          <!-- BREADCRUMB -->

          <ol class="breadcrumb ">
            <li><a href="<?php echo $_SESSION['baseurl']; ?>">Dashboard </a></li>
            <li><a href="<?php echo $halaman; ?>"><?php echo $dataapa ?></a></li>
            <?php

            if ($search != null || $search != "") {
            ?>
              <li> <a href="<?php echo $halaman; ?>">Data <?php echo $dataapa ?></a></li>
              <li class="active"><?php
                                  echo $search;
                                  ?></li>
            <?php
            } else {
            ?>
              <li class="active">Data <?php echo $dataapa ?></li>
            <?php
            }
            ?>
          </ol>

          <!-- BREADCRUMB -->

          <?php
          //fungsi menangkap barcode
          $check_data = 'hidden';
          if (isset($_POST['barcode'])) {
            if (isset($_POST['new_bc'])) {
              $new_sn = $_POST["new_sn"];
              $kode_produk = $_POST["produk"];
              $new_bc = $_POST["barcode"];

              $sql1 = "SELECT `barang`.* FROM `barang` WHERE `barang`.`kode`='$kode_produk'";
              $que1 = mysqli_query($conn, $sql1);
              $dat1 = mysqli_fetch_assoc($que1);
              $id_brg = $dat1["barcode"];
              $sql2_check = "SELECT `barang_detil`.* FROM `barang_detil` WHERE `barang_detil`.`barcode`='$new_bc'";
              $que2_check = mysqli_query($conn, $sql2_check);
              $rows2_check = mysqli_num_rows($que2_check);
              if ($rows2_check > 0) {
                echo "<script type='text/javascript'>  alert('Barang dengan barcode $new_bc sudah ada!');</script>";
                echo "<script type='text/javascript'>window.location = 'stok_out';</script>";
              } else {
                $sql2 = "INSERT INTO `barang_detil`(`id_barang`, `barcode`) VALUES ('$id_brg','$new_bc')";
                $que2 = mysqli_query($conn, $sql2);
                if ($que2) {
                  echo "<script type='text/javascript'>  alert('Berhasil menambahkan Serial Number baru'); </script>";
                  echo "<script type='text/javascript'>window.location = 'stok_out';</script>";
                } else {
                  echo "<script type='text/javascript'>  alert('Gagal, Periksa kembali input anda!'); </script>";
                }
              }
            } else {
              $barcode = mysqli_real_escape_string($conn, $_POST["barcode"]);
              $sql1 = "SELECT `barang`.nama,`barang`.sisa AS avail, `barang_detil`.* FROM `barang` INNER JOIN `barang_detil` ON `barang_detil`.`id_barang` = `barang`.`barcode` WHERE `barang_detil`.barcode='$barcode'";
              $query = mysqli_query($conn, $sql1);
              $data = mysqli_fetch_assoc($query);
              $check_data = (mysqli_num_rows($query) > 0) ? null : 'hidden';
              $nama = $data['nama'];
              $kode = $data['id'];
              $stok = $data['avail'];
              $bc = $data['barcode'];
              $stok_detil = $data['sisa'];

              $jumlah = '1';
            }
          }
          ?>
          <!-- tambah -->
          <?php

          if (isset($_POST["keluar"])) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
              $nota = mysqli_real_escape_string($conn, $_POST["nota"]);
              $kode = mysqli_real_escape_string($conn, $_POST["kode"]);
              $nama = mysqli_real_escape_string($conn, $_POST["nama"]);
              $bc = mysqli_real_escape_string($conn, $_POST["bc"]);
              $jumlah = mysqli_real_escape_string($conn, $_POST["jumlah"]);
              $barcode_new = mysqli_real_escape_string($conn, $_POST["barcode_new"]);

              $kegiatan = "Stok Keluar";
              $status = "pending";
              $usr = $_SESSION['nama'];
              $today = date('Y-m-d');

              $q_brg = mysqli_query($conn, "SELECT * FROM barang WHERE kode='$kode'");
              $row_brg = mysqli_fetch_assoc($q_brg);
              //jumlah barang all
              $jml_all_br = $row_brg['sisa'] + $jumlah;
              $jml_all_tb_br = $row_brg['terbeli'] + $jumlah;

              //cek data stok_keluar_daftar
              $cek_stok_keluar = mysqli_query($conn, "SELECT * FROM stok_keluar_daftar WHERE nota='$nota' AND kode_barang='$kode'");
              if (mysqli_num_rows($cek_stok_keluar) > 0) {
                $q = mysqli_fetch_assoc($cek_stok_keluar);
                $cart = $q['jumlah'];
                $newcart = $cart + $jumlah;
                $sqlu = "UPDATE stok_keluar_daftar SET jumlah='$newcart' where nota='$nota' AND kode_barang='$kode'";
                $ucart = mysqli_query($conn, $sqlu);
                if ($ucart) {
                  $sql3 = "UPDATE mutasi SET jumlah='$newcart' WHERE keterangan='$nota' AND kegiatan='$kegiatan' ";
                  $upd = mysqli_query($conn, $sql3);

                  echo "<script type='text/javascript'>  alert('Jumlah Stok keluar telah ditambah!');</script>";
                  echo "<script type='text/javascript'>window.location = '$halaman';</script>";
                } else {
                  echo "<script type='text/javascript'>  alert('GAGAL, Periksa kembali input anda!');</script>";
                }
              } else {

                $sql2 = "INSERT INTO stok_keluar_daftar (nota, kode_barang, nama, jumlah, barcode) VALUES( '$nota','$kode','$nama','$jumlah','$barcode_new')";
                $insertan = mysqli_query($conn, $sql2);
                if ($insertan) {

                  $sql9 = "INSERT INTO mutasi (namauser, tgl, kodebarang, sisa, jumlah, kegiatan, keterangan, status) VALUES('$usr','$today','$kode','$jml_all_br','$jumlah','$kegiatan','$nota','pending')";
                  $mutasi = mysqli_query($conn, $sql9);

                  echo "<script type='text/javascript'>  alert('Produk telah dimasukan dalam daftar!');</script>";
                  echo "<script type='text/javascript'>window.location = '$halaman';</script>";
                } else {
                  echo "<script type='text/javascript'>  alert('GAGAL memasukan produk, periksa kembali!');</script>";
                }
              }

              //update barang
              $q_jml_brg = mysqli_query($conn, "SELECT * FROM barang_detil WHERE barcode='$barcode_new';");
              $row_jml_brg = mysqli_fetch_assoc($q_jml_brg);
              if (mysqli_num_rows($q_jml_brg) > 0) {
                //update stok berdasarkan barcode
                $jml_br = $row_jml_brg['jumlah_keluar'] + $jumlah;
                $jml_tb_br = $row_jml_brg['terbeli'] + $jumlah;

                $q_detail_stok = mysqli_query($conn, "UPDATE barang_detil SET jumlah_keluar_p='$jml_br', terjual_p='$jml_tb_br' WHERE id='" . $row_jml_brg['id'] . "';");
                if ($q_detail_stok) {
                  //update jumlah barang all
                  // $sql_update_jml_all_barang = mysqli_query($conn, "UPDATE barang SET sisa='$jml_all_br', terbeli='$jml_all_tb_br' WHERE kode='$kode';");
                }
              } else {
                //cek jumlah barcode pada barang
                $cek_bc_all = mysqli_query($conn, "SELECT * FROM barang a, barang_detil b WHERE a.sku = b.id_barang AND a.kode='$kode' AND b.barcode = '" . $barcode_new . "';");
                if (mysqli_num_rows($cek_bc_all) == 1) {
                  $row_cek_bc_all = mysqli_fetch_assoc($cek_bc_all);
                  //update stok dengan barcode yg ada
                  $jml_br2 = $row_cek_bc_all['jumlah_keluar'] + $jumlah;
                  $jml_tb_br2 = $row_cek_bc_all['terbeli'] + $jumlah;

                  $q_detail_stok = mysqli_query($conn, "UPDATE barang_detil SET jumlah_keluar_p='$jml_br2', terjual_p='$jml_tb_br2' WHERE id='" . $row_cek_bc_all['id'] . "';");
                  if ($q_detail_stok) {
                    //update jumlah barang all
                    // $sql_update_jml_all_barang = mysqli_query($conn, "UPDATE barang SET sisa='$jml_all_br', terbeli='$jml_all_tb_br' WHERE kode='$kode';");
                  }
                } else {
                  //update stok barcode baru
                  $q_detail_stok = mysqli_query($conn, "INSERT INTO barang_detil (id_barang, barcode, terbeli, jumlah_keluar) VALUES('SKU$kode','$barcode_new','$jumlah','$jumlah')");
                  if ($q_detail_stok) {
                    //update jumlah barang all
                    // $sql_update_jml_all_barang = mysqli_query($conn, "UPDATE barang SET sisa='$jml_all_br', terbeli='$jml_all_tb_br' WHERE kode='$kode';");
                  }
                }
              }
            }
          }
          ?>
          <!-- BOX INSERT BERHASIL -->

          <script>
            window.setTimeout(function() {
              $("#myAlert").fadeTo(500, 0).slideUp(1000, function() {
                $(this).remove();
              });
            }, 5000);
          </script>


          <!-- BOX INFORMASI -->
          <?php
          if ($chmod >= 2 || $_SESSION['jabatan'] == 'admin') {
          ?>


            <!-- KONTEN BODY AWAL -->
            <!-- Default box -->
            <div class="col-lg-6 col-xs-12">
              <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Form Stok Keluar</h3>

                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                      <i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
                      <i class="fa fa-times"></i></button>
                  </div>
                </div>
                <div class="box-body">

                  <body OnLoad='document.getElementById("barcode").focus();'>

                    <div class="row">
                      <div class="col-sm-6">
                        <button class="btn btn-info btn-block" type="button" id="startButton">Open Kamera</button>
                      </div>
                      <div class="col-sm-6">
                        <button class="btn btn-info btn-block" type="button" id="resetButton">Reset</button>
                      </div>
                    </div>
                    <br>
                    <div>
                      <video id="video" width="100%" height="200" style="border: 1px solid gray"></video>
                    </div>

                    <div id="sourceSelectPanel" style="display:none" class="form-group">
                      <select id="sourceSelect" style="col-sm-8" class="form-control">
                      </select>
                    </div>
                    <br>
                    <form method="POST" action="">
                      <div class="row">
                        <?php if (isset($_POST['barcode'])) { ?>
                          <div class="form-group col-md-12 col-xs-12">
                            <?php if (isset($_POST['barcode']) && $check_data == null) { ?>
                              <div class="alert alert-success">
                                <strong>Barang Ditemukan</strong>
                              </div>
                            <?php }
                            if (isset($_POST['barcode']) && $check_data != null) { ?>
                              <div class="alert alert-danger">
                                <strong>Barang Tidak Ditemukan</strong>
                              </div>
                            <?php } ?>
                          </div>
                        <?php } ?>
                        <div class="form-group col-md-12 col-xs-12">
                          <div class="col-sm-12">
                            <div class="checkbox">
                              <label>
                                <input type="checkbox" name="new_sn" id="new_sn" value="1"> Serial Number Baru ?
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="form-group col-md-12 col-xs-12 hidden" id="choose_barang">
                          <label for="barang" class="col-sm-2 control-label">Pilih Barang:</label>
                          <div class="col-sm-10">
                            <select class="form-control select2" name="produk" id="produk" style="width: 100%;">
                              <option selected="selected"> Pilih Barang</option>
                              <?php
                              error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
                              $sql = mysqli_query($conn, "SELECT a.kode, a.nama, a.hargabeli, a.hargajual, a.sisa, a.sku FROM barang a;");
                              while ($row = mysqli_fetch_assoc($sql)) {
                                echo "<option value='" . $row['kode'] . "' nama='" . $row['nama'] . "' hargabeli='" . $row['hargabeli'] . "' hargajual='" . $row['hargajual'] . "' kode='" . $row['kode'] . "' stok='" . $row['sisa'] . "'>" . $row['sku'] . " | " . $row['nama'] . "</option>";
                              }
                              ?>
                            </select>
                          </div>
                        </div>
                        <div class="form-group col-md-12 col-xs-12" id="cari_barcode">
                          <label for="label_barcode" id="label_barcode" class="col-sm-2 control-label">Barcode / SN:</label>
                          <div class="col-sm-8">
                            <input type="text" class="form-control" id="barcode" name="barcode" autocomplete="off">
                          </div>
                          <div class="col-sm-2" id="btn_aksi">
                            <button type="submit" class="btn btn-info btn-block">Cari</button>
                          </div>
                        </div>
                      </div>
                    </form>
                    <hr>

                    <form method="post" action="" id="Myform" class="<?= $check_data ?>">
                      <div class="row">
                        <div class="form-group col-md-12 col-xs-12">
                          <label for="barang" class="col-sm-2 control-label">Nama Produk:</label>
                          <div class="col-sm-10">
                            <input type="text" class="form-control" readonly id="nama" name="nama" value="<?php echo $nama; ?>">
                            <input class="form-control" readonly id="kode" name="kode" value="<?php echo $kode; ?>">
                            <input hidden class="form-control" readonly id="nota" name="nota" value="<?php echo autoNumber(); ?>">
                            <input type="hidden" class="form-control" readonly id="bc" name="bc" value="<?php echo $bc; ?>">
                            <input class="form-control" readonly id="barcode_new" name="barcode_new" value="<?= isset($_POST["barcode"]) ? $_POST["barcode"] : ""; ?>">
                          </div>

                        </div>
                      </div>

                      <?php
                      error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
                      ?>

                      <div class="row">
                        <div class="form-group col-md-12 col-xs-12">
                          <label for="barang" class="col-sm-2 control-label">Stok Tersedia:</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="stok" name="stok" value="<?php echo $stok; ?>" readonly>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="form-group col-md-12 col-xs-12">
                          <label for="barang" class="col-sm-2 control-label">Stok Detil:</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="stok_detil" name="stok_detil" value="<?php echo $stok_detil; ?>" readonly>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="form-group col-md-12 col-xs-12">
                          <label for="barang" class="col-sm-2 control-label">Jumlah:</label>
                          <div class="col-sm-5">
                            <input type="text" class="form-control" id="jumlah" name="jumlah" value="<?php echo $jumlah; ?>">
                          </div>
                          <div class="col-sm-5">
                            <button type="submit" name="keluar" class="btn bg-orange btn-flat btn-block">Tambahkan</button>
                          </div>
                        </div>
                      </div>


                    </form>
                </div>

                <!-- /.box-body -->
              </div>
            </div>

            <div class="col-lg-6 col-xs-12">
              <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Daftar Keluar</h3>

                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                      <i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
                      <i class="fa fa-times"></i></button>
                  </div>
                </div>
                <div class="box-body">



                  <div class="row">
                    <div class="col-md-12">
                      <div class="box box-success">
                        <div class="box-header with-border">

                        </div>

                        <?php
                        error_reporting(E_ALL ^ E_DEPRECATED);

                        $sql    = "select * from stok_keluar_daftar where nota =" . autoNumber() . " order by no";
                        $result = mysqli_query($conn, $sql);
                        $rpp    = 30;
                        $reload = "$halaman" . "?pagination=true";
                        $page   = intval(isset($_POST["page"]) ? $_POST["page"] : 0);



                        if ($page <= 0)
                          $page = 1;
                        $tcount  = mysqli_num_rows($result);
                        $tpages  = ($tcount) ? ceil($tcount / $rpp) : 1;
                        $count   = 0;
                        $i       = ($page - 1) * $rpp;
                        $no_urut = ($page - 1) * $rpp;
                        ?>
                        <div class="box-body table-responsive">
                          <table class="data table table-hover table-bordered">
                            <thead>
                              <tr>
                                <th style="width:10px">No</th>
                                <th>Nama Barang</th>
                                <th>Barcode Barang</th>
                                <th style="width:10%">Jumlah Keluar</th>

                                <?php if ($chmod >= 3 || $_SESSION['jabatan'] == 'admin') { ?>
                                  <th style="width:10px">Opsi</th>
                                <?php } else {
                                } ?>
                              </tr>
                            </thead>
                            <?php
                            error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
                            while (($count < $rpp) && ($i < $tcount)) {
                              mysqli_data_seek($result, $i);
                              $fill = mysqli_fetch_array($result);

                              if (isset($fill['barcode'])) {
                                $escaped_barcode = mysqli_real_escape_string($conn, $fill['barcode']);
                                $barc = $escaped_barcode;
                              } else {
                                // Tindakan alternatif jika $fill['barcode'] tidak terdefinisi atau null
                                $barc = '-';
                              }
                            ?>
                              <tbody>
                                <tr>
                                  <td><?php echo ++$no_urut; ?></td>


                                  <td><?php echo mysqli_real_escape_string($conn, $fill['nama']); ?></td>
                                  <td><?php echo $barc; ?></td>
                                  <td><?php echo mysqli_real_escape_string($conn, $fill['jumlah']); ?></td>

                                  <td>
                                    <?php if ($chmod >= 4 || $_SESSION['jabatan'] == 'admin') { ?>
                                      <button type="button" class="btn btn-danger btn-xs" onclick="window.location.href='component/delete/delete_stok?get=<?php echo 'in' . '&'; ?>barang=<?php echo $fill['kode_barang'] . '&'; ?>barcode=<?php echo $fill['barcode'] . '&'; ?>jumlah=<?php echo $fill['jumlah'] . '&'; ?>&kode=<?php echo $kode . '&'; ?>no=<?php echo $fill['no'] . '&'; ?>forward=<?php echo $tabel . '&'; ?>forwardpage=<?php echo "" . $forwardpage . '&'; ?>chmod=<?php echo $chmod; ?>'">Hapus</button>
                                    <?php } else {
                                    } ?>
                                  </td>
                                </tr>
                              <?php
                              $i++;
                              $count++;
                            }

                              ?>
                              </tbody>
                          </table>
                          <div align="right">
                            <?php
                            if ($tcount >= $rpp) {
                              echo paginate_one($reload, $page, $tpages);
                            } ?>
                          </div>


                        </div>

                      </div>


                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12">
                      <div class="box box-danger">
                        <div class="box-header with-border">

                          <form method="post" action="" class=" <?= ($tcount > 0) ? null : $check_data ?>">
                            <div class="row">
                              <div class="form-group col-md-12 col-xs-12">
                                <label for="barang" class="col-sm-3 control-label">Penanggung Jawab:</label>
                                <div class="col-sm-9">
                                  <select class="form-control select2" style="width: 100%;" name="penanggung_jawab">
                                    <?php
                                    $sql = mysqli_query($conn, "select * from penanggung_jawab");
                                    while ($row = mysqli_fetch_assoc($sql)) {
                                      if ($penanggung_jawab == $row['kode'])
                                        echo "<option value='" . $row['nama'] . "' selected='selected'>" . $row['notelp'] . " | " . $row['nama'] . "</option>";
                                      else
                                        echo "<option value='" . $row['nama'] . "'>" . $row['notelp'] . " | " . $row['nama'] . "</option>";
                                    }
                                    ?>
                                  </select>
                                </div>
                              </div>
                              <div class="form-group col-md-12 col-xs-12">
                                <label for="barang" class="col-sm-3 control-label">Tujuan:</label>
                                <div class="col-sm-9">
                                  <input class="form-control" rows="6" id="tujuan" name="tujuan" placeholder="Masukan tujuan" required>
                                </div>
                              </div>
                              <div class="form-group col-md-12 col-xs-12">
                                <label for="barang" class="col-sm-3 control-label">Keterangan:</label>
                                <div class="col-sm-9">
                                  <textarea class="form-control" rows="6" id="keterangan" name="keterangan" placeholder="Masukan Keterangan" required></textarea>
                                </div>
                              </div>

                            </div>
                            <br>
                            <input type="hidden" class="form-control" readonly id="notae" name="notae" value="<?php echo autoNumber(); ?>">

                            <div class="row">
                              <div class="form-group col-md-12 col-xs-12">

                                <button type="submit" name="simpan" class="btn btn-flat bg-purple btn-block">SIMPAN</button>

                              </div>
                            </div>
                          </form>


                        </div>
                      </div>
                    </div>
                  </div>





                </div>

                <!-- /.box-body -->
              </div>
            </div>
            <?php

            if (isset($_POST["simpan"])) {
              if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $nota = mysqli_real_escape_string($conn, $_POST["notae"]);
                $sup = mysqli_real_escape_string($conn, $_POST["penanggung_jawab"]);
                $tgl = date('Y-m-d');
                $usr = $_SESSION['nouser'];
                $cab = $_SESSION['cab'];
                $keterangan = mysqli_real_escape_string($conn, $_POST["keterangan"]);
                $tujuan = mysqli_real_escape_string($conn, $_POST["tujuan"]);

                $kegiatan = "Stok Keluar";

                $sql2 = "INSERT INTO stok_keluar (nota, cabang, tgl, penanggung_jawab, userid, keterangan, tujuan) VALUES( '$nota','$cab','$tgl','$sup','$usr', '$keterangan', '$tujuan')";
                $insertan = mysqli_query($conn, $sql2);

                $brg = "SELECT * FROM stok_keluar_daftar WHERE nota='$nota'";
                $cekbrg = mysqli_query($conn, $brg);

                $terbeli_b = 0;
                $terbeli_s = 0;
                while ($row = mysqli_fetch_assoc($cekbrg)) {
                  $upd_1 = "UPDATE barang_detil SET terjual=terjual+$row[jumlah], sisa=sisa-$row[jumlah] WHERE id='$row[kode_barang]'";
                  $upd_q_1 = mysqli_query($conn, $upd_1);
                  $sel_1 = "SELECT * FROM barang_detil WHERE id='$row[kode_barang]'";
                  $cek_1 = mysqli_query($conn, $sel_1);
                  $row_1 = mysqli_fetch_assoc($cek_1);
                  $id_barang = $row_1['id_barang'];
                  $upd_2 = "UPDATE barang SET terjual=terjual+$row[jumlah], sisa=sisa-$row[jumlah] WHERE barcode='$id_barang'";
                  $upd_q_2 = mysqli_query($conn, $upd_2);
                }

                $mut = "UPDATE mutasi SET status='berhasil' WHERE keterangan='$nota' AND kegiatan='stok keluar'";
                $muta = mysqli_query($conn, $mut);


                echo "<script type='text/javascript'>  alert('Stok selesai dimasukan!');</script>";
                echo "<script type='text/javascript'>window.location = 'surat_buat?q=$nota';</script>";
              }
            } ?>
        </div>

      <?php
          } else {
      ?>
        <div class="callout callout-danger">
          <h4>Info</h4>
          <b>Hanya user tertentu yang dapat mengakses halaman <?php echo $dataapa; ?> ini .</b>
        </div>
      <?php
          }
      ?>
      <!-- ./col -->
      </div>

      <!-- /.row -->
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <!-- /.Left col -->
      </div>
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php footer(); ?>
  <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- Script -->

<script src="dist/plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="libs/1.11.4-jquery-ui.min.js"></script>

<script type="text/javascript" src="https://unpkg.com/@zxing/library@latest/umd/index.min.js"></script>

<script>
  $("#produk").on("change", function() {

    var nama = $("#produk option:selected").attr("nama");
    var kode = $("#produk option:selected").attr("kode");
    var stok = $("#produk option:selected").attr("stok");
    var jumlah_keluar = $("#produk option:selected").attr("jumlah_keluar");


    $("#nama").val(nama);
    $("#stok").val(stok);
    $("#kode").val(kode);
    $("#stok_detil").val(jumlah_keluar);

    $("#jumlah").val(1);
  });

  $("#new_sn").on("change", function() {
    let this_checked = $(this).is(":checked");
    if (this_checked) {
      $("#label_barcode").html("Masukan SN");
      $("#btn_aksi").html('<button type="submit" name="new_bc" value="new_bc" class="btn btn-success btn-block">Tambah</button>');
      $('#choose_barang').removeClass("hidden");
    } else {
      $("#label_barcode").html("Barcode / SN");
      $("#btn_aksi").html('<button type="submit" name="find_bc" class="btn btn-info btn-block">Cari</button>');
      $('#choose_barang').addClass("hidden");
    }
  })

  $("#cari_berd").on("change", function() {
    let cari = $("#cari_berd").val();
    if (cari == "BarcodeSN") {
      $('#cari_barcode').removeClass("hidden");
      $('#cari_kode').addClass("hidden");
    } else if (cari == "Kode") {
      $('#cari_barcode').addClass("hidden");
      $('#cari_kode').removeClass("hidden");
    } else {
      $('#cari_barcode').addClass("hidden");
      $('#cari_kode').addClass("hidden");
    }
  })

  function copy_barcode() {
    const input1Value = document.getElementById('barcode').value;
    document.getElementById('barcode_new').value = input1Value;
  }

  function show_barcode(params) {
    // Ambil elemen input pertama
    var sourceInput = document.getElementById('barcode');
    // Ambil elemen input kedua
    var targetInput = document.getElementById('barcode_new');
    // Salin nilai dari input pertama ke input kedua
    targetInput.value = sourceInput.value;
  }

  window.addEventListener('load', function() {
    let selectedDeviceId;
    const codeReader = new ZXing.BrowserMultiFormatReader()
    console.log('ZXing code reader initialized')
    codeReader.listVideoInputDevices()
      .then((videoInputDevices) => {
        const sourceSelect = document.getElementById('sourceSelect')
        selectedDeviceId = videoInputDevices[0].deviceId
        if (videoInputDevices.length >= 1) {
          videoInputDevices.forEach((element) => {
            const sourceOption = document.createElement('option')
            sourceOption.text = element.label
            sourceOption.value = element.deviceId
            sourceSelect.appendChild(sourceOption)
          })

          sourceSelect.onchange = () => {
            selectedDeviceId = sourceSelect.value;
          };

          const sourceSelectPanel = document.getElementById('sourceSelectPanel')
          sourceSelectPanel.style.display = 'block'
        }

        document.getElementById('startButton').addEventListener('click', () => {
          codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
            if (result) {
              console.log(result)
              $('#barcode').val(result.text);
              $('#barcode_new').val(result.text);
              // document.getElementById('result').textContent = result.text
            }
            if (err && !(err instanceof ZXing.NotFoundException)) {
              console.error(err)
              // $('#result').val();
              // document.getElementById('result').textContent = err
            }
          })
          console.log(`Started continous decode from camera with id ${selectedDeviceId}`)
        })

        document.getElementById('resetButton').addEventListener('click', () => {
          codeReader.reset()
          $('#barcode').val('');
          // document.getElementById('result').textContent = '';
          console.log('Reset.')
        })

      })
      .catch((err) => {
        console.error(err)
      })
  })
</script>


<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<script src="dist/bootstrap/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="dist/plugins/morris/morris.min.js"></script>
<script src="dist/plugins/sparkline/jquery.sparkline.min.js"></script>
<script src="dist/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="dist/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="dist/plugins/knob/jquery.knob.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="dist/plugins/daterangepicker/daterangepicker.js"></script>
<script src="dist/plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="dist/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script src="dist/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="dist/plugins/fastclick/fastclick.js"></script>
<script src="dist/js/app.min.js"></script>
<script src="dist/js/demo.js"></script>
<script src="dist/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="dist/plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="dist/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="dist/plugins/fastclick/fastclick.js"></script>
<script src="dist/plugins/select2/select2.full.min.js"></script>
<script src="dist/plugins/input-mask/jquery.inputmask.js"></script>
<script src="dist/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
<script src="dist/plugins/input-mask/jquery.inputmask.extensions.js"></script>
<script src="dist/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script src="dist/plugins/iCheck/icheck.min.js"></script>

<!--fungsi AUTO Complete-->

<script>
  $(function() {
    //Initialize Select2 Elements
    $(".select2").select2();

    //Datemask dd/mm/yyyy
    $("#datemask").inputmask("yyyy-mm-dd", {
      "placeholder": "yyyy/mm/dd"
    });
    //Datemask2 mm/dd/yyyy
    $("#datemask2").inputmask("yyyy-mm-dd", {
      "placeholder": "yyyy/mm/dd"
    });
    //Money Euro
    $("[data-mask]").inputmask();

    //Date range picker
    $('#reservation').daterangepicker();
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      format: 'YYYY/MM/DD h:mm A'
    });
    //Date range as a button
    $('#daterange-btn').daterangepicker({
        ranges: {
          'Hari Ini': [moment(), moment()],
          'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Akhir 7 Hari': [moment().subtract(6, 'days'), moment()],
          'Akhir 30 Hari': [moment().subtract(29, 'days'), moment()],
          'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
          'Akhir Bulan': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate: moment()
      },
      function(start, end) {
        $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
      }
    );

    //Date picker
    $('#datepicker').datepicker({
      autoclose: true
    });

    $('.datepicker').datepicker({
      dateFormat: 'yyyy-mm-dd'
    });

    //Date picker 2
    $('#datepicker2').datepicker('update', new Date());

    $('#datepicker2').datepicker({
      autoclose: true
    });

    $('.datepicker2').datepicker({
      dateFormat: 'yyyy-mm-dd'
    });


    //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass: 'iradio_minimal-blue'
    });
    //Red color scheme for iCheck
    $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
      checkboxClass: 'icheckbox_minimal-red',
      radioClass: 'iradio_minimal-red'
    });
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass: 'iradio_flat-green'
    });

    //Colorpicker
    $(".my-colorpicker1").colorpicker();
    //color picker with addon
    $(".my-colorpicker2").colorpicker();

    //Timepicker
    $(".timepicker").timepicker({
      showInputs: false
    });
  });
</script>
</body>

</html>