<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="shortcut icon" href="../../recursos/icon-r.png">
    <title>REPORTE PRODUCCIÓN</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../../dist/css/adminlte.min.css">


    <!-- DataTables -->
    <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="../../plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="../../plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">

    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="../../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">

    <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="../../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">

    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="../../sweetalert2/dist/sweetalert2.min.css">
    <!-- FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet"> 
    <!-- MY CSS -->
    <link rel="stylesheet" href="../../dist/css/mycss.css">
    <style>
        .c_number{
            font-family: 'Lato', sans-serif;
        }
    </style>
</head>

<body class="layout-top-nav text-sm" oncontextmenu="return false">
    <div class="cont-carga ">
        <div class="pre-loader">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="wrapper">
        <?php
            include '../../FilePHP/menu.php';
            if (isset($_SESSION['MM_Username'])) {
                $array = get_info_user_restore($_SESSION['MM_Username']);
                if ($array[1]==1) header("Location: ../../restore/");
                else data_menu(23);
            }else{
                header("Location: ../../login");
            }
        ?>
        <div class="content-wrapper ">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="m-0 text-dark"><b>REPORTE DE PRODUCCIÓN</b></h5>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">Reporte</li>
                                <li class="breadcrumb-item active">producción</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <div class="container-fluid p-2">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group clearfix">
                            <div class="icheck-success d-inline mr-4">
                                <input type="radio" name="radioTipo"  id="radioSuccess1" checked value="2">
                                <label for="radioSuccess1">
                                    Ingres de animales
                                </label>
                            </div>
                            <div class="icheck-success d-inline mr-4">
                                <input type="radio" name="radioTipo"  id="radioSuccess2" value="3">
                                <label for="radioSuccess2">
                                    Saldos Y Faenamiento
                                </label>
                            </div>
                            <div class="icheck-success d-inline mr-4">
                                <input type="radio" name="radioTipo"  id="radioSuccess3" value="4">
                                <label for="radioSuccess3">
                                    Tasa
                                </label>
                            </div>
                            <div class="icheck-success d-inline mr-4">
                                <input type="radio" name="radioTipo"  id="radioSuccess4" value="5">
                                <label for="radioSuccess4">
                                    Corralaje
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="txtIncio">Fecha inicio:</label>
                                <div class="input-group" id="reservationdate" data-target-input="nearest">
                                    <input type="text" placeholder="dd/mm/yyyy" id="txtIncio"
                                        class="form-control form-control-sm datetimepicker-input"
                                        data-target="#reservationdate" data-inputmask-alias="datetime"
                                        data-inputmask-inputformat="dd/mm/yyyy" data-mask="" im-insert="false"
                                        value="<?php echo date('d-m-Y') ?>" />
                                    <div class="input-group-append" data-target="#reservationdate"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text "><i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="txtFinal">Fecha final:</label>
                                <div class="input-group " id="reservationdate2" data-target-input="nearest">
                                    <input type="text" placeholder="dd/mm/yyyy" id="txtFinal"
                                        class="form-control form-control-sm datetimepicker-input"
                                        data-target="#reservationdate2" data-inputmask-alias="datetime"
                                        data-inputmask-inputformat="dd/mm/yyyy" data-mask="" im-insert="false"
                                        value="<?php echo date('d-m-Y') ?>" />
                                    <div class="input-group-append" data-target="#reservationdate2"
                                        data-toggle="datetimepicker">
                                        <div class="input-group-text "><i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="slcEspecie">Especie:</label>
                                <select id="slcEspecie" class="form-control form-control-sm">
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for=""></label>
                                <button class="btn btn-info btn-block" id="bnt-consultar"><b>BUSCAR</b></button>
                            </div>
                        </div>
                        <hr>
                        <div id="cont-table-data">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../../FilePHP/footer.php';?>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body" id="modal-body">

                </div>
            </div>
        </div>
    </div>

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="../../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../../dist/js/adminlte.min.js"></script>

    <!-- DataTables -->
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <!-- Select2 -->
    <script src="../../plugins/select2/js/select2.full.min.js"></script>
    <!-- Swwralert2  -->
    <script src="../../sweetalert2/dist/sweetalert2.min.js"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="../../plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    <!-- InputMask -->
    <script src="../../plugins/moment/moment.min.js"></script>
    <script src="../../plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="../../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="../../plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <!-- toast -->
    <script src="../../plugins/toastr/toastr.min.js"></script>
    <!-- My scripts  -->
    <script src="../../dist/js/my_scripts.js"></script>
    <script src="script.js"></script>
</body>

</html>