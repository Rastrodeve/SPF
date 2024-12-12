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

    <title>SUBIR DETALLE DE GUÍA</title>

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

        .fileContainer {
            position: relative;
        }

        .fileContainer [type=file] {
            cursor: pointer;
            display: block;
            height: 100%;
            width: 100%;
            opacity: 0;
            position: relative;
            z-index: 100;
        }

        #form-file {
            cursor: pointer;
            display: block;
            height: 100%;
            width: 100%;
            opacity: 0;
            position: relative;
            z-index: 10;
        }

        .fileContainer p {
            font-size: 20px;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            text-align: center;
        }

        .fileContainer p i {
            font-size: 100px;
        }

        .fileContainer p label {
            font-size: 15px;
        }

        .fileContainer p span {
            font-size: 11px;
        }

        .fileContainer {
            border-radius: 20px;
            width: 100%;
            height: 300px;
            border-style: dotted;
            border-color: #d5dbdb;
            background: #f2f3f4;
        }

        .fileContainer [type=file] {
            cursor: pointer;
        }

        .barra {
            background-color: #f3f3f3;
            border-radius: 10px;
            height: 30px;
        }

        #barra_estado span {
            color: #fff;
            font-weight: bold;
            line-height: 30px;
        }

        .barra_azul {
            background-color: #247cc0;
            border-radius: 10px;
            display: block;
            height: 30px;
            line-height: 30px;
            text-align: center;
            width: 0%;
        }

        .barra_verde {
            background-color: #2ea265 !important;
        }

        .barra_roja {
            background-color: #ed3152;
        }
        #tbl_data_excel tr{
            cursor: pointer;
        }
    </style>
</head>

<body class="layout-top-nav body-sm" oncontextmenu="return false">
    <div class="cont-carga ">
        <div class="pre-loader">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand-md navbar-dark navbar-navy ">
            <div class="container">
                <a class="navbar-brand">
                    <img src="../../Recursos/logo.png" alt="Rastro Logo" class="brand-image img-circle elevation-3"
                        style="opacity: .8">
                    <span class="brand-text font-weight-light">EMRAQ-EP</span>
                </a>

                <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse"></div>
                <!-- Right navbar links -->
                <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto pt-2">
                    <label style="color:white;">DETALLE DE GUÍA</label>
                </ul>
            </div>
        </nav>
        <div class="content-wrapper ">
            <div class="container-fluid p-2">
                <div class="card mt-4 pt-2">
                    <div class="card-body pt-3">
                        <div id="cont-selecionar">
                            <h5>
                                <button class="btn btn-success btn-sm float-right"><b>Descargar formato</b></button>
                                Selecciona tú archivo de <b>excel</b> que contiene la información
                            </h5>
                            <div class="fileContainer mt-3">
                                <p id="cont-vista-doc">
                                    <i class="fas fa-file-excel"></i>
                                    <br>
                                    <br>
                                    Arrastra el archivo a esta zona
                                    <br>o <br>
                                    <button class="btn btn-info mt-2"><b>Selecionar archivo</b></button>
                                </p>
                                <form id="form-file">
                                    <input type="file" id="file_select" name="archivo" accept=".xlsx, .xls, .csv" />
                                </form>
                            </div>
                            <h6 id="cont-btn-siguiente"></h6>
                        </div>
                        <form id="cont-subir" class="d-none">
                            <h5 id="mensaje_up">
                                Subiendo archivo, por favor <b>espere</b>
                            </h5>
                            <div class="barra">
                                <div class="barra_azul" id="barra_estado">
                                    <span></span>
                                </div>
                            </div>
                            <h6>
                                <button class="btn btn-outline-danger mt-2 "><b>CANCELAR</b></button>
                            </h6>
                        </form>
                        <div style="width: 100%;"  >
                            <div class="row">
                                <div class="col-8" id="cont-datos-excel"></div>
                            </div>
                        </div>
                        <h6 id="cont-btns" class="d-none">
                            <button class="btn btn-outline-danger mt-2" onclick="regresar1()"><b>REGRESAR</b></button>
                            <button class="btn btn-info mt-2 float-right"><b>SIGUIENTE <i class="fas fa-angle-right"></i></b></button>
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary d-none" id="open-modal-edit" data-toggle="modal"
        data-target="#ModalEdit">
    </button>

    <!-- Modal -->
    <div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="ModalEditTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="ModalEditLongTitle">
                        <b>EDICIÓN DE REGISTRO</b>
                    </h5>
                    <button type="button" class="close" id="btn-cerrar-edit" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="cont-modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><b>CANCELAR</b></button>
                    <button type="button" class="btn btn-primary" id="btn-guardar-edit"><b>GUARDAR CAMBIOS</b></button>
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

        <!-- Swwralert2  -->
        <script src="../../sweetalert2/dist/sweetalert2.min.js"></script>
    <!-- toast -->
    <script src="../../plugins/toastr/toastr.min.js"></script>
    <!-- My scripts  -->
    <script src="../../dist/js/my_scripts.js"></script>

    <script src="script.js"></script>
</body>

</html>