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
    <title>ORDEN DE PRODUCCIÓN</title>

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
        .toast-body {
            background: white;
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
            else data_menu(6);
        } else {
            header("Location: ../../login");
        }
        ?>
        <div class="content-wrapper ">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="m-0 text-dark"><b>ORDEN DE PRODUCCIÓN</b>
                                <button class="btn btn-sm" id="btn-full-scream"><i class="fas fa-expand"></i></button>
                            </h5>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">Producción</li>
                                <li class="breadcrumb-item active">Orden de producción</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <div class="container-fluid p-2">
                <div class="card mt-0">
                    <div class="card-header pb-2">
                        <div class="row">
                            <div class="col-6">
                                <h5 class="text-dark mt-1  toastsDefaultInfo" style="cursor:pointer;">Lista de Tazas</h5>
                            </div>
                            <div class="col-6">
                                <div class="float-right">
                                    <select class="form-control form-control-sm  select2bs4 d-inline" id="slcTipo" style="width:300px;cursor:pointer;">
                                        <option value="0">Cargando...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body " id="cont-view"></div>
                </div>
                <?php if ($_SESSION['MM_Username'] == 2) { ?>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#ModalHelp"><b>RECUPERAR ORDEN DE PRODUCCIÓN</b></button>
                <?php } ?>
            </div>
        </div>
        <?php include '../../FilePHP/footer.php'; ?>
    </div>

    <button type="button" class="d-none" id="btn-Abrir" data-toggle="modal" data-target="#ModalDatos">ABRIR</button>
    <!-- Modal -->
    <div class="modal fade" id="ModalDatos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-sm" id="cont-modal-body">
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="ModalHelp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="exampleModalLabel2">Ingrese el número de la orden de producción a recuperar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-sm">
                    <input type="text" class="form form-control" value="" id="txtNumeroGuiaHelp">
                    <br>
                    <button class="btn btn-primary" id="bnt-help"><b>Recuperar</b></button>
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
    <script src="help.js"></script>
</body>

</html>