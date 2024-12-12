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
    <link rel="shortcut icon" href="../recursos/icon-r.png">
    <title>PROCESO</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">


    <!-- DataTables -->
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="../plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
    <link rel="stylesheet" href="../sweetalert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../dist/css/mycss.css">
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
            include '../FilePHP/menu.php';
            if (isset($_SESSION['MM_Username'])) {
                $array = get_info_user_restore($_SESSION['MM_Username']);
                if ($array[1]==1) header("Location: ../restore/");
                else data_menu_peril_proceso();
            }else{
                header("Location: ../login");
            }
        ?>
        <div class="content-wrapper ">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="m-0 text-dark"><b>PROCESO DE PRODUCCIÓN</b>
                            <button class="btn btn-sm" id="btn-full-scream" ><i class="fas fa-expand"></i></button>
                        </h5>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <div class="container-fluid p-2">
                <h4>Trabajando...</h4>
                <div class="row d-none">
                    <div class="col-md-3">

                        <!-- Profile Image -->
                        <div class="card card-navy card-outline">
                            <div class="card-body box-profile" id="box-profile">
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-9">
                        <div class="card card-navy card-tabs">
                            <div class="card-header p-0 pt-1 ">
                                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="custom-tabs-one-soli-tab" data-toggle="pill"
                                            href="#custom-tabs-one-soli" role="tab"
                                            aria-controls="custom-tabs-one-nuevo" aria-selected="false"
                                            style="">Solicitudes</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " id="custom-tabs-one-manu-tab" data-toggle="pill"
                                            href="#custom-tabs-one-manu" role="tab"
                                            aria-controls="custom-tabs-one-importar" aria-selected="true"
                                            style="">Manuales</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " id="custom-tabs-one-act-tab" data-toggle="pill"
                                            href="#custom-tabs-one-act" role="tab"
                                            aria-controls="custom-tabs-one-importar" aria-selected="true"
                                            style="">Actividad</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-one-tabContent">
                                    <div class="tab-pane fade active show" id="custom-tabs-one-soli" role="tabpanel"
                                        aria-labelledby="custom-tabs-one-soli-tab">
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-one-manu" role="tabpanel"
                                        aria-labelledby="custom-tabs-one-manu-tab">
                                        <h1>MANUALES</h1>
                                    </div>
                                    <div class="tab-pane fade" id="custom-tabs-one-act" role="tabpanel"
                                        aria-labelledby="custom-tabs-one-act-tab">
                                        <h1>ACTIVIDADES</h1>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
            </div>
        </div>
        <?php include '../FilePHP/footer.php';?>
    </div>


    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" id="modal-content" >
            </div>
        </div>
    </div>
    <div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content" id="cont-modal" >
                <div class="modal-header">
                    <h5 class="modal-title"><b>SOLICITUD</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-body" ></div>
            </div>
        </div>
    </div>

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>

    <!-- DataTables -->
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <!-- Select2 -->
    <script src="../plugins/select2/js/select2.full.min.js"></script>
    <!-- Swwralert2  -->
    <script src="../sweetalert2/dist/sweetalert2.min.js"></script>
    <!-- toast -->
    <script src="../plugins/toastr/toastr.min.js"></script>
    <!-- My scripts  -->
    <script src="../dist/js/my_scripts.js"></script>
    <script src="script.js"></script>
</body>

</html>