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
    <title>INGRESO DE GUÍA</title>

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
    <!-- MY CSS -->
    <link rel="stylesheet" href="../../dist/css/mycss.css">
</head>

<body class="layout-top-nav text-sm">
    <!-- oncontextmenu="return false" -->
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
                else data_menu(2);
            }else{
                header("Location: ../../login");
            }
        ?>
        <div class="content-wrapper ">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="m-0 text-dark"><b>INGRESO Y DISTRIBUCIÓN DE GUÍA</b></h5>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">Recepción</li>
                                <li class="breadcrumb-item active">Ingreso y distribución de guía</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <div class="container-fluid p-2">
                <div class="card card-secondary card-tabs">
                    <div class="card-header p-0 pt-1 border-bottom-0 ">
                        <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill"
                                    href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home"
                                    aria-selected="true">
                                    Guía de movilización
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill"
                                    href="#custom-tabs-three-profile" role="tab"
                                    aria-controls="custom-tabs-three-profile" aria-selected="false">
                                    Distribuir guía
                                    <!--  -->
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-three-messages-tab" data-toggle="pill"
                                    href="#custom-tabs-three-messages" role="tab"
                                    aria-controls="custom-tabs-three-messages" aria-selected="false">
                                    Lista de Procesado
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-three-tabContent">
                            <div class="tab-pane fade active show" id="custom-tabs-three-home" role="tabpanel"
                                aria-labelledby="custom-tabs-three-home-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row ">
                                            <div class="col-md-6 text-center">
                                                <div class="custom-control custom-radio d-inline">
                                                    <input class="custom-control-input" type="radio" id="rdQr"
                                                        name="customRadio" style="cursor:pointer" checked>
                                                    <label for="rdQr" style="cursor:pointer"
                                                        class="custom-control-label">
                                                        Ingreso QR
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-center">
                                                <div class="custom-control custom-radio d-inline">
                                                    <input class="custom-control-input" type="radio" id="rdBlanco"
                                                        name="customRadio" style="cursor:pointer">
                                                    <label for="rdBlanco" style="cursor:pointer"
                                                        class="custom-control-label">
                                                        Ingreso Manual
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row" id="cont-qr">
                                            <div class="col-md-4 text-md-right">
                                                <label for="slcGanado" class="col-form-label">
                                                    Lea el código de barras</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control form-control-sm"
                                                    id="txtCodigoQR">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="mt-0 mb-3">
                                <form id="form-datos-guia">
                                    <div class="row ">
                                        <!-- <div class="col-md-3"></div> -->
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <div class="col-md-4 text-md-right">
                                                    <label for="slcGanado" class="col-form-label">Tipo de
                                                        ganado:</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select id="slcGanado"
                                                        class="form-control form-control-sm  select2bs4"
                                                        style="cursor:pointer;"></select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4 text-md-right">
                                                    <label class="col-form-label">Comprobante de Ingreso: </label>
                                                </div>
                                                <div class="col-md-8">
                                                    <h6 class="text-muted form-control form-control-sm"
                                                        id="lblComprobante">
                                                    </h6>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4 text-md-right">
                                                    <label for="txtGuiaNumero" class="col-form-label">Guía
                                                        número:</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control form-control-sm mt-1"
                                                        id="txtGuiaNumero" placeholder="Número de guía de Movilización">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4 text-md-right">
                                                    <label for="txtFechaGuia" class="col-form-label">Fecha guía:</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="input-group mt-1" id="reservationdate"
                                                        data-target-input="nearest">
                                                        <input type="text" placeholder="dd/mm/yyyy" id="txtFechaGuia"
                                                            class="form-control form-control-sm datetimepicker-input"
                                                            data-target="#reservationdate"
                                                            data-inputmask-alias="datetime"
                                                            data-inputmask-inputformat="dd/mm/yyyy" data-mask=""
                                                            im-insert="false" value="<?php echo date('d-m-Y') ?>" />
                                                        <div class="input-group-append" data-target="#reservationdate"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text bg-success"><i
                                                                    class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4 text-md-right">
                                                    <label for="txtFechaValidez" class="col-form-label">Fecha
                                                        Validez:</label>
                                                </div>
                                                <div class="col-md-8 ">
                                                    <div class="input-group mt-1" id="reservationdate2"
                                                        data-target-input="nearest">
                                                        <input type="text" placeholder="dd/mm/yyyy" id="txtFechaValidez"
                                                            class="form-control form-control-sm datetimepicker-input"
                                                            data-target="#reservationdate2"
                                                            data-inputmask-alias="datetime"
                                                            data-inputmask-inputformat="dd/mm/yyyy" data-mask=""
                                                            im-insert="false" />
                                                        <div class="input-group-append " data-target="#reservationdate2"
                                                            data-toggle="datetimepicker">
                                                            <div class="input-group-text bg-danger"><i
                                                                    class="fa fa-calendar"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="error invalid-feedback d-block"
                                                        id="spnErrorValidez"></span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4 text-md-right">
                                                    <label for="txtCantidad" class="col-form-label">Cantidad:</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" onKeyPress="f_OnlyCant(event)"
                                                        class="form-control form-control-sm mt-1" id="txtCantidad"
                                                        placeholder="Cantidad de ganado">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4 text-md-right">
                                                    <label for="slcProvincia" class="col-form-label">Provincia
                                                        origen:</label>
                                                </div>
                                                <div class="col-md-8 text-sm">
                                                    <select id="slcProvincia"
                                                        class="form-control form-control-sm select2bs4"
                                                        style="width: 100%;margin-top:10px;">
                                                        <option value="0">Cargando..</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4 text-md-right">
                                                    <label for="txtVacunacion" class="col-form-label">Código
                                                        vacunación:</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control form-control-sm "
                                                        id="txtVacunacion" placeholder="Código de vacunación">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4 text-md-right">
                                                    <label for="txtCI" class="col-form-label">C.I. Conductor</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control form-control-sm mt-1"
                                                        id="txtCI" placeholder="C.I. del conductor">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4 text-md-right">
                                                    <label for="txtConductor" class="col-form-label">Nombre
                                                        Conductor:</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control form-control-sm mt-1"
                                                        id="txtConductor" placeholder="Nombre del conductor">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4 text-md-right">
                                                    <label for="slcVehiculo" class="col-form-label">Vehiculo:</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select id="slcVehiculo"
                                                        class="form-control form-control-sm select2bs4">
                                                        <option value="CAMION">CAMION</option>
                                                        <option value="CAMIONETA">CAMIONETA</option>
                                                        <option value="AUTOMIVIL">AUTOMOVIL</option>
                                                        <option value="OTRO">OTRO</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4 text-md-right">
                                                    <label for="txtPlaca" class="col-form-label">Placa Vehiculo:</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control form-control-sm mt-1"
                                                        id="txtPlaca" placeholder="Placa del vehiculo">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3"></div>
                                    </div>
                                    <center>
                                        <button class="btn btn-info mt-3">
                                            <b>GUARDAR CAMBIOS</b>
                                        </button>
                                    </center>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel"
                                aria-labelledby="custom-tabs-three-profile-tab">

                            </div>
                            <div class="tab-pane fade" id="custom-tabs-three-messages" role="tabpanel"
                                aria-labelledby="custom-tabs-three-messages-tab">
                                <div class="form-group row">
                                    <div class="col-md-3">
                                        <select id="slcGanado-pro" class="form-control form-control-sm  select2bs4"
                                            style="cursor:pointer;">
                                        </select>
                                    </div>
                                </div>
                                <div id="cont-table-guia-proceso">

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
        <?php
            include '../../FilePHP/footer.php';
        ?>
    </div>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#Modal">
        Launch demo modal
    </button>

    <!-- Modal -->
    <div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group">
                        <label>
                            Razón por la cual el transporte no llego a tiempo:<br>
                            <span class="text-muted">la fecha de la guía de movilización se encuentra vencida</span>
                        </label>
                        <textarea class="form-control form-control-sm" id="txtRazon" rows="3"
                            placeholder="Razón..."></textarea>
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