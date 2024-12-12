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
    <title>CLIENTES - INTRODUCTORES</title>

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
        .custom-control-label {
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -khtml-user-select: none;
            -ms-user-select: none;
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
                else data_menu(10);
            }else{
                header("Location: ../../login");
            }
        ?>
        <div class="content-wrapper ">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="m-0 text-dark"><b>CLIENTES - INTRODUCTORES</b></b></h5>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">Administrador</li>
                                <li class="breadcrumb-item active">clientes - introductores</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <div class="container-fluid p-2">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-sm btn-info mb-3" onclick="get_data_user_new()"
                                    data-toggle="modal" data-target="#modal"><b>NUEVO</b></button>
                            </div>
                        </div>
                        <div id="cont-view"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../../FilePHP/footer.php';?>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-scrollable text-sm modal-lg" role="document">
            <div class="modal-content  " id="modal-content">
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title" id="modalLabel">
                        <b>Información del cliente</b>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 class="text-muted">
                        Permisos para el cliente Jairo Stalin Castillo Tandazo
                    </h6>
                    <hr>
                    <div class="row">
                        <div class="col-md-2">
                            <label>Tipo de reporte:</label>
                        </div>
                        <div class="col-md-4">
                            <select  id="slcTipoReporte" class="form-control form-control-sm select2bs4 ">
                                <option value="0">Normal</option>
                                <option value="1">Especial</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <p  style="font-weight: lighter;font-size:13px;" class="mb-0 pb-0">
                                <b>Normal:</b> Aparecera el logo de rastro en el reporte <br>
                                <b>Especial:</b> El logo no rastro no se muestra en el reporte
                            </p>
                        </div>
                    </div>
                    <hr>
                    <span style="font-weight: lighter;font-size:13px;" class="mb-3">
                        <b>Seleccione los productos que desea que tenga una secuencia unica.</b>
                    </span>
                    <div class="card collapsed-card">
                        <div class="card-header" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"
                            style="cursor: pointer;">
                            <h1 class="card-title">
                                <b>G. BOVINO</span></b>
                            </h1>
                            <div class="card-tools">
                                <span class="text-muted">3 de 4</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="mt-2 table table-sm table-bordered table-striped ">
                                <thead>
                                    <tr>
                                        <th>Nro.</th>
                                        <th>Producto</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>1</th>
                                        <td>Prueba</td>
                                        <td>
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="customCheckbox2" checked="">
                                                <label for="customCheckbox2" class="custom-control-label">Secuencia única</label>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button class="btn btn-info btn-sm float-right mt-2">
                                <b>GUARDAR CAMBIOS</b>
                            </button>
                        </div>
                    </div>
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