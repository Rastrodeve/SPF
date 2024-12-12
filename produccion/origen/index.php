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
    <title>GUÍA DE ORIGEN</title>

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

        .select2-container--default .select2-selection--single {
            height: 50px !important;
            padding: 14px 16px;
            font-size: 24px;
            line-height: 1.33;
            border-radius: 6px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            top: 85% !important;
        }

        .select2-results__options {
            font-size: 24px;
        }

        .select2-search {
            font-size: 24px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #CCC !important;
            box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset;
            transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s;
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
                else data_menu(20);
            }else{
                header("Location: ../../login");
            }
        ?>
        <div class="content-wrapper ">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="m-0 text-dark"><b>GUÍA DE ORIGEN</b>
                            <button class="btn btn-sm" id="btn-full-scream" ><i class="fas fa-expand"></i></button>
                        </h5>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">Producción</li>
                                <li class="breadcrumb-item active">Guía de origen</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <div class="container-fluid p-2" id="contenedor">

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
    <div class="modal fade" id="modal2" tabindex="-1" role="dialog" aria-labelledby="modal2Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document" id="modal-dialog">
            <div class="modal-content" id="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="modalLabel">
                        <b>SELECCIONAR ENFERMEDADES</b>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card card-light ">
                                <div class="card-header border-transparent " data-card-widget="collapse"
                                    style="cursor:pointer;">
                                    <h3 class="card-title text-muted "><b>Higado</b></h3>
                                </div>
                                <div class="card-body ">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnCerrar-especie" class="btn btn-light"
                        data-dismiss="modal"><b>CERRAR</b></button>
                    <button type="button" class="btn btn-primary" id="btn-guardar-especie">
                        <b>GUARDAR</b>
                    </button>
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