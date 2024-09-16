<!DOCTYPE html>
<html>
<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

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
                    $nota_sk = $_GET['nota'];
                    if (!isset($nota_sk)) {
                        echo "<script type='text/javascript'>
                            alert('Nota tidak ditemukan');
                            window.history.back();
                        </script>";
                    }

                    error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
                    include "configuration/config_chmod.php";
                    $halaman = "stok_keluar_konfirmasi?nota=" . $nota_sk; // halaman
                    $dataapa = "Konfirmasi Stok Keluar"; // data
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
                            <li class="active"><?php echo $search; ?></li>
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

                    if (isset($_GET['barcode'])) {
                        $barcode = mysqli_real_escape_string($conn, $_GET["barcode"]);
                        $sql1 = "SELECT * FROM stok_keluar_daftar where nota = '$nota_sk' AND barcode='$barcode';";
                        $query = mysqli_query($conn, $sql1);
                        $data = mysqli_fetch_assoc($query);
                        $nama = $data['nama'];
                        $kode = $data['no'];
                        $stok_keluar = $data['jumlah'];

                        $jumlah = '1';
                    }
                    ?>
                    <!-- tambah -->
                    <?php

                    if (isset($_POST["keluar"])) {
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $kode = mysqli_real_escape_string($conn, $_POST["kode"]);
                            $nama = mysqli_real_escape_string($conn, $_POST["nama"]);
                            $jumlah = mysqli_real_escape_string($conn, $_POST["jumlah"]);
                            $stok_keluar = mysqli_real_escape_string($conn, $_POST["stok_keluar"]);

                            //get stok_keluar_daftar
                            $brg_utama = mysqli_query($conn, "SELECT * from stok_keluar_daftar WHERE no='$kode';");
                            $ass_utama = mysqli_fetch_assoc($brg_utama);
                            $stok_kl = $ass_utama['jumlah_keluar'] + $jumlah;
                            $id_kl = $ass_utama['kode_barang'];
                            $bc_kl = $ass_utama['barcode'];

                            $kegiatan = "Stok Keluar";
                            $status = "pending";
                            $usr = $_SESSION['nama'];
                            $today = date('Y-m-d');
                            if ($stok_kl <= $stok_keluar) {

                                $sqlx = "UPDATE stok_keluar_daftar SET jumlah_keluar='$stok_kl' WHERE no='$kode';";
                                $updx = mysqli_query($conn, $sqlx);
                                if ($updx) {
                                    //get stok barang detil
                                    $brg_detil = mysqli_query($conn, "SELECT * from barang_detil WHERE id = '$id_kl' AND barcode = '$bc_kl';");
                                    $row_brg_detil = mysqli_fetch_assoc($brg_detil);
                                    $terjual_kl = $row_brg_detil['terjual'] + $jumlah;
                                    $jumlah_masuk_kl = $row_brg_detil['jumlah_masuk'] - $jumlah;

                                    $sqly = "UPDATE barang_detil SET terjual='$terjual_kl', jumlah_masuk='$jumlah_masuk_kl' WHERE id = '$id_kl' AND barcode = '$bc_kl';";
                                    $updy = mysqli_query($conn, $sqly);
                                    if ($updy) {
                                        echo "<script type='text/javascript'>  alert('Jumlah daftar stok keluar telah diupdate!');</script>";
                                    } else {
                                        echo "<script type='text/javascript'>  alert('Gagal mengupdate jumlah stok!');</script>";
                                    }
                                } else {
                                    echo "<script type='text/javascript'>  alert('Gagal mengupdate jumlah stok!');</script>";
                                }
                            } else {

                                echo "<script type='text/javascript'>  alert('Jumlah keluar tidak boleh lebih besar dari stok tersedia!');</script>";
                                echo "<script type='text/javascript'>window.location = '$halaman';</script>";
                            }
                        }
                    } ?>
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
                        <div class="col-lg-5 col-xs-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Form Konfirmasi Stok Keluar</h3>

                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                                            <i class="fa fa-minus"></i></button>
                                        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
                                            <i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">

                                    <body OnLoad='document.getElementById("barcode").focus();'>

                                        <div class="nav-tabs-custom nav-fill">
                                            <ul class="nav nav-tabs">
                                                <li class="active"><a href="#tab_1" data-toggle="tab">Barcode</a></li>
                                                <li><a href="#tab_2" data-toggle="tab">Pilih Barang</a></li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab_1">
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

                                                    <div id="sourceSelectPanel" style="display:none">
                                                        <label for="sourceSelect">Ubah kamera:</label>
                                                        <select id="sourceSelect" style="max-width:400px">
                                                        </select>
                                                    </div>
                                                    <br>
                                                    <form method="get" action="">
                                                        <div class="row">
                                                            <input type="text" hidden class="form-control" readonly name="nota" value="<?php echo $nota_sk; ?>">
                                                            <div class="form-group col-md-12 col-xs-12">
                                                                <label for="barang" class="col-sm-2 control-label">Barcode:</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" class="form-control" id="barcode" name="barcode">
                                                                </div>
                                                                <div class="col-sm-2">
                                                                    <button type="submit" class="btn btn-info btn-block">Cari</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <!-- /.tab-pane -->
                                                <div class="tab-pane" id="tab_2">
                                                    <div class="row">
                                                        <div class="form-group col-md-12 col-xs-12">
                                                            <label for="barang" class="col-sm-2 control-label">Pilih Barang:</label>
                                                            <div class="col-sm-10">
                                                                <select class="form-control select2" style="width: 100%;" name="produk" id="produk">
                                                                    <option selected="selected"> Pilih Barang</option>
                                                                    <?php
                                                                    error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
                                                                    $sql = mysqli_query($conn, "SELECT * FROM stok_keluar_daftar WHERE nota='$nota_sk' ORDER BY no;");
                                                                    while ($row = mysqli_fetch_assoc($sql)) {
                                                                        if ($barcode == $row['barcode'])
                                                                            echo "<option value='" . $row['no'] . "' nama='" . $row['nama'] . "' kode='" . $row['no'] . "' stok='" . $row['jumlah'] . "' selected='selected'>" . $row['nama'] . " | " . $row['barcode'] . "</option>";
                                                                        else
                                                                            echo "<option value='" . $row['no'] . "' nama='" . $row['nama'] . "' kode='" . $row['no'] . "' stok='" . $row['jumlah'] . "'>" . $row['nama'] . " | " . $row['barcode'] . "</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.tab-pane -->
                                            </div>
                                            <!-- /.tab-content -->
                                        </div>

                                        <form method="post" action="">
                                            <div class="row">
                                                <div class="form-group col-md-12 col-xs-12">
                                                    <label for="barang" class="col-sm-2 control-label">Nama Produk:</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" readonly id="nama" name="nama" value="<?php echo $nama; ?>">
                                                        <input type="text" hidden class="form-control" readonly id="kode" name="kode" value="<?php echo $kode; ?>">
                                                    </div>

                                                </div>
                                            </div>

                                            <?php
                                            error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
                                            ?>
                                            <div class="row">
                                                <div class="form-group col-md-12 col-xs-12">
                                                    <label for="barang" class="col-sm-2 control-label">Stok Awal:</label>
                                                    <div class="col-sm-5">
                                                        <input type="text" class="form-control" id="stok_keluar" name="stok_keluar" value="<?php echo $stok_keluar; ?>" readonly>
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
                                                        <button type="submit" name="keluar" class="btn bg-maroon btn-flat btn-block">Tambahkan</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </form>
                                </div>

                                <!-- /.box-body -->
                            </div>
                        </div>
                        <div class="col-lg-7 col-xs-12">
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

                                                $sql    = "select * from stok_keluar_daftar where nota =" . $nota_sk . " order by no";
                                                $result = mysqli_query($conn, $sql);
                                                $rpp    = 30;
                                                $reload = "$halaman" . "?pagination=true";
                                                $page   = intval(isset($_GET["page"]) ? $_GET["page"] : 0);



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
                                                                <th style="width:10%">Stok Awal</th>
                                                                <th style="width:10%">Stok Akhir</th>

                                                                <?php if ($chmod >= 3 || $_SESSION['jabatan'] == 'admin') { ?>
                                                                    <th style="width:10px" hidden>Opsi</th>
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
                                                                    <td><?php echo mysqli_real_escape_string($conn, $fill['jumlah_keluar']); ?></td>

                                                                    <td hidden>
                                                                        <?php if ($chmod >= 4 || $_SESSION['jabatan'] == 'admin') { ?>
                                                                            <button type="button" class="btn btn-danger btn-xs" onclick="window.location.href='component/delete/delete_stok?get=<?php echo 'out' . '&'; ?>barang=<?php echo $fill['kode_barang'] . '&'; ?>jumlah=<?php echo $fill['jumlah'] . '&'; ?>&kode=<?php echo $kode . '&'; ?>no=<?php echo $fill['no'] . '&'; ?>forward=<?php echo $tabel . '&'; ?>forwardpage=<?php echo "" . $forwardpage . '&'; ?>chmod=<?php echo $chmod; ?>'">Hapus</button>
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
                                                    <div align="right"><?php if ($tcount >= $rpp) {
                                                                            echo paginate_one($reload, $page, $tpages);
                                                                        } ?></div>


                                                </div>

                                            </div>


                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="box box-danger">
                                                <div class="box-header with-border">

                                                    <form method="post" action="">

                                                        <div class="row">
                                                            <div class="form-group col-md-12 col-xs-12">
                                                                <label for="barang" class="col-sm-2 control-label">Keterangan:</label>
                                                                <div class="col-sm-10">
                                                                    <input type="text" class="form-control" name="ket">
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <br>
                                                        <input type="hidden" class="form-control" readonly id="notae" name="notae" value="<?php echo autoNumber(); ?>">

                                                        <div class="row">
                                                            <div class="form-group col-md-12 col-xs-12">
                                                                <div class="col-lg-12">
                                                                    <button type="submit" name="simpan" class="btn btn-flat bg-teal btn-block">SIMPAN</button>
                                                                </div>

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
                                //get barang detil
                                $query_sk_daftar = mysqli_query($conn, "SELECT id_barang, SUM(terjual) AS terjual, SUM(terbeli) AS terbeli, SUM(jumlah_masuk) AS jumlah from barang_detil GROUP BY id_barang;");
                                // $row_sk_daftar = mysqli_fetch_assoc($query_sk_daftar);
                                while ($row_sk_daftar = mysqli_fetch_assoc($query_sk_daftar)) {
                                    $no_terjual = $row_sk_daftar['terjual'];
                                    $no_terbeli = $row_sk_daftar['terbeli'];
                                    $no_jumlah = $row_sk_daftar['jumlah'];
                                    mysqli_query($conn, "UPDATE barang SET terjual='$no_terjual', terbeli='$no_terbeli', sisa='$no_jumlah' WHERE sku = '" . $row_sk_daftar['id_barang'] . "';");
                                }
                                echo "<script type='text/javascript'>  alert('Konfirmasi Stok keluar berhasil disimpan');</script>";
                                echo "<script type='text/javascript'>window.location = 'surat_kelola';</script>";
                            }
                        }
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
<script src='jquery-3.1.1.min.js' type='text/javascript'></script>

<!-- jQuery UI -->
<link href='jquery-ui.min.css' rel='stylesheet' type='text/css'>
<script src='jquery-ui.min.js' type='text/javascript'></script>

<script src="dist/plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="libs/1.11.4-jquery-ui.min.js"></script>

<script type="text/javascript" src="https://unpkg.com/@zxing/library@latest/umd/index.min.js"></script>


<script>
    $("#produk").on("change", function() {

        var nama = $("#produk option:selected").attr("nama");
        var kode = $("#produk option:selected").attr("kode");
        var stok_keluar = $("#produk option:selected").attr("stok");


        $("#nama").val(nama);
        $("#kode").val(kode);
        $("#stok_keluar").val(stok_keluar);
        $("#jumlah").val(1);
    });

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

<!--AUTO Complete-->

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