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
    <title>TURNOS</title>

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
    <link rel="stylesheet" href="../../sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../../dist/css/mycss.css">

    <style>
        #cont-table-detalle {
            max-height: 400px;
            margin-top: 20px;
            overflow-y: auto;
        }

        #cont-table-detalle table thead {
            position: sticky;
            top: 0px;
        }

        .my_badge {
            cursor: pointer;
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
            if ($array[1] == 1) header("Location: ../../restore/");
            else data_menu(30);
        } else {
            header("Location: ../../login");
        }
        ?>
        <div class="content-wrapper ">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="m-0 text-dark"><b>VERIFICACIÓN DE TURNO</b>
                                <button class="btn btn-sm" id="btn-full-scream"><i class="fas fa-expand"></i></button>
                            </h5>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">Producción</li>
                                <li class="breadcrumb-item active">Verificación de turno</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <div class="container-fluid p-2">
                <div class="row">
                    <div class="form-group col-md-6">
                        <div class="custom-control custom-radio d-inline mr-3">
                            <input class="custom-control-input" type="radio" value="1" id="customRadio1" name="customRadio" style="cursor: pointer;">
                            <label for="customRadio1" class="custom-control-label" style="cursor: pointer;">Guía de Movilización</label>
                        </div>
                        <div class="custom-control custom-radio d-inline mr-3">
                            <input class="custom-control-input" type="radio" value="2" id="customRadio2" name="customRadio" style="cursor: pointer;">
                            <label for="customRadio2" class="custom-control-label" style="cursor: pointer;">Comprobante</label>
                        </div>
                        <div class="custom-control custom-radio d-inline mr-3">
                            <input class="custom-control-input" type="radio" value="3" id="customRadio3" name="customRadio" style="cursor: pointer;">
                            <label for="customRadio3" class="custom-control-label" style="cursor: pointer;">Cliente</label>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-5" id="cont-busqueda">
                    </div>
                </div>
                <button class="btn btn-danger" onclick="get_data_all(4,'')"><b>PAGO PENDIENTES</b></button>
                <button class="btn btn-info" onclick="get_data_all(5,'')"><b>PAGADOS</b></button>
                <hr>
                <div class=" card card-body" id="cont-data">
                </div>
            </div>
        </div>
        <?php include '../../FilePHP/footer.php'; ?>
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-scrollable text-sm modal-lg" role="document">
            <div class="modal-content " id="modal-content">
                <div class="modal-header ">
                    <h5 class="modal-title text-muted " id="modalLabel">
                        <b>Detalle de la factura</b>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="cont-body">
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
    <!-- toast -->
    <script src="../../plugins/toastr/toastr.min.js"></script>
    <!-- My scripts  -->
    <script src="../../dist/js/my_scripts.js"></script>
    <script src="script.js"></script>
</body>

</html>