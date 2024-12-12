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

<body class="layout-top-nav text-sm" oncontextmenu="return false">
    <!--  -->
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
            else data_menu(2);
        } else {
            header("Location: ../../login");
        }
        ?>
        <div class="content-wrapper ">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="m-0 text-dark"><b>INGRESO Y DISTRIBUCIÓN DE GUÍA</b>
                                <button class="btn btn-sm" id="btn-full-scream"><i class="fas fa-expand"></i></button>
                            </h5>
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
                <button class="btn btn-primary mb-2" type="button" data-toggle="modal" data-target="#exampleModal"><b>TURNOS</b></button>
                <div class="card card-secondary card-tabs">
                    <div class="card-header p-0 pt-1 border-bottom-0 ">
                        <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true">
                                    Nueva guía de movilización
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">
                                    Lista de comprobantes
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-three-tabContent">
                            <div class="tab-pane fade active show" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                                <div id="cont-guia-1">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row ">
                                                <div class="col-md-6 text-center">
                                                    <div class="custom-control custom-radio d-inline">
                                                        <input class="custom-control-input" type="radio" id="rdQr" name="customRadio" style="cursor:pointer" checked>
                                                        <label for="rdQr" style="cursor:pointer" class="custom-control-label">
                                                            Ingreso QR
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 text-center">
                                                    <div class="custom-control custom-radio d-inline">
                                                        <input class="custom-control-input" type="radio" id="rdBlanco" name="customRadio" style="cursor:pointer">
                                                        <label for="rdBlanco" style="cursor:pointer" class="custom-control-label">
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
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control form-control-sm" id="txtCodigoQR">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="mt-0 mb-3">
                                    <div class="row ">
                                        <div class="col-12 col-lg-4  mt-2">
                                            <label for="txtGuiaNumero">* Guía número movilización: </label> <span class="text-muted ml-2">(max 20 caracteres)</span>
                                            <input type="text" class="form-control form-control-sm " id="txtGuiaNumero" maxlength="30" placeholder="Número de guía de Movilización">
                                        </div>
                                        <div class="col-12 col-lg-2 mt-2">
                                            <label for="txtFechaGuia">* Fecha guía:</label>
                                            <div class="input-group" id="reservationdate" data-target-input="nearest">
                                                <input type="text" placeholder="dd/mm/yyyy" id="txtFechaGuia" class="form-control form-control-sm datetimepicker-input" data-target="#reservationdate" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask="" im-insert="false" value="<?php echo date('d-m-Y') ?>" />
                                                <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                                    <div class="input-group-text bg-success"><i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-2 mt-2">
                                            <label for="txtFechaValidez">* Fecha Validez:</label>
                                            <div class="input-group " id="reservationdate2" data-target-input="nearest">
                                                <input type="text" placeholder="dd/mm/yyyy" id="txtFechaValidez" class="form-control form-control-sm datetimepicker-input" data-target="#reservationdate2" data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy" data-mask="" im-insert="false" />
                                                <div class="input-group-append " data-target="#reservationdate2" data-toggle="datetimepicker">
                                                    <div class="input-group-text bg-danger"><i class="fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="error invalid-feedback d-block" id="spnErrorValidez"></span>
                                        </div>
                                        <div class="col-12 col-lg-2 mt-2">
                                            <label for="slcProvincia">* Provincia origen:</label>
                                            <select id="slcProvincia" class="form-control form-control-sm select2bs4" style="width: 100%;margin-top:10px;"> </select>
                                        </div>
                                        <div class="col-12 col-lg-2 mt-2">
                                            <label for="txtVacunacion">* Código vacunación:</label>
                                            <input type="text" class="form-control form-control-sm" value="000000" id="txtVacunacion" placeholder="00000">
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12 col-lg-2 mt-2">
                                            <label for="txtCI">* C.I. Conductor:</label> <span class="text-muted ml-2">(max 10)</span>
                                            <input type="text" class="form-control form-control-sm " id="txtCI" placeholder="1234567890" maxlength="10">
                                        </div>
                                        <div class="col-12 col-lg-3 mt-2">
                                            <label for="txtConductor">* Nombre Conductor:</label> <span class="text-muted ml-2">(max 40)</span>
                                            <input type="text" class="form-control form-control-sm " id="txtConductor" placeholder="Nombre y Apellido" maxlength="40">
                                        </div>
                                        <div class="col-12 col-lg-2 mt-2" sty>
                                            <label for="slcVehiculo">* Vehiculo:</label>
                                            <select id="slcVehiculo" class="form-control form-control-sm select2bs4" style="width: 100%;">
                                                <option value="CAMION">CAMION</option>
                                                <option value="CAMIONETA">CAMIONETA</option>
                                                <option value="AUTOMIVIL">AUTOMOVIL</option>
                                                <option value="OTRO">OTRO</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-lg-2 mt-2">
                                            <label for="txtPlaca">* Placa Vehiculo:</label> <span class="text-muted ml-2">(max 8)</span>
                                            <input type="text" class="form-control form-control-sm " id="txtPlaca" placeholder="Placa del vehiculo" maxlength="8">
                                        </div>
                                    </div>
                                    <button class="btn btn-info btn-sm mt-3 float-right" id="btn-next-1">
                                        <b>SIGUIENTE</b>
                                    </button>
                                </div>
                                <div id="cont-guia-2" style="display:none;">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label for="slcCliente">* Selecione un cliente:</label>
                                            <div class="row">
                                                <div class="col-11">
                                                    <select id="slcCliente" class="form-control form-control-sm select2bs4" style="cursor:pointer;"></select>
                                                </div>
                                                <div class="col-1">
                                                    <button class="btn btn-outline-dark btn-sm" onclick="get_select_clientes()"><i class="fas fa-circle-notch"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-lg-3"></div>
                                        <div class="col-lg-5">
                                            <div class="form-group row mb-0">
                                                <div class="col-lg-12">
                                                    <label for="slcGanado" class="col-form-label text-muted">
                                                        Descripción de la guía de movilización
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-4 text-lg-right">
                                                    <label for="slcGanado" class="col-form-label mt-3">* Tipo de
                                                        ganado:</label>
                                                </div>
                                                <div class="col-lg-8">
                                                    <span class="text-muted" id="spanEstadoDetalle"></span>
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-10">
                                                            <select id="slcGanado" class="form-control form-control-sm select2bs4 " style="cursor:pointer;width:100%;"></select>
                                                        </div>
                                                        <div class="col-2">
                                                            <button class="btn btn-outline-dark btn-sm" onclick="get_select_especies()"><i class="fas fa-circle-notch"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4 text-lg-right">
                                                    <label for="txtHembra" class="col-form-label">* HEMBRAS:</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" onKeyPress="f_OnlyCant(event)" maxlength="4" class="form-control form-control-sm mt-1 input_disablecopypaste" id="txtHembra" placeholder="Cantidad de ganado">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4 text-lg-right">
                                                    <label for="txtMacho" class="col-form-label">* MACHOS:</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" onKeyPress="f_OnlyCant(event)" maxlength="4" class="form-control form-control-sm mt-1 input_disablecopypaste" id="txtMacho" placeholder="Cantidad de ganado">
                                                </div>
                                            </div>
                                            <div class="form-group row mt-0">
                                                <div class="col-md-12 mt-0">
                                                    <center>
                                                        <button class="btn btn-sm btn-success" id="btn-detalle">
                                                            <b>AÑADIR</b>
                                                        </button>
                                                    </center>
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <div class="col-md-12 ">
                                                    <table id="tbl_detalle" class="table table-bordered table-striped table-sm table-hover text-center">
                                                        <thead>
                                                            <th>GANADO</th>
                                                            <th>HEMBRAS</th>
                                                            <th>MACHOS</th>
                                                            <th>TOTAL</th>
                                                            <th class="text-center">ACCIONES</th>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th colspan="3" class="text-right">Cantidad Total</th>
                                                                <th class="text-center" id="tdTotal">0</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4"></div>
                                    </div>
                                    <button class="btn btn-info btn-sm mt-3 " id="btn-afther-1">
                                        <b>ATRAS</b>
                                    </button>
                                    <button class="btn btn-info btn-sm mt-3 float-right" id="btn-next-2">
                                        <b>SIGUIENTE</b>
                                    </button>
                                </div>
                                <div id="cont-guia-3" style="display:none;">
                                    <div class="form-group">
                                        <label for="txtObservacion">Observaciones de ingreso:</label>
                                        <textarea id="txtObservacion" class="form-control form-control-sm" rows="3"></textarea>
                                    </div>
                                    <button class="btn btn-info btn-sm mt-3 " id="btn-afther-2">
                                        <b>ATRAS</b>
                                    </button>
                                    <button class="btn btn-info mt-3 float-right" id="btn-nueva">
                                        <b>GUARDAR GUÍA DE MOVILIZACIÓN</b>
                                    </button>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <select id="slcBuscar" class="form-control form-control-sm select2bs4"></select>
                                    </div>
                                </div>
                                <hr>
                                <div id="cont-table-data"></div>
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
    <!-- Modal -->
    <div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content" id="cont-modal">
                <div class="modal-header">
                    <h5 class="modal-title"><b>SOLICITUD DE EDICIÓN</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="text-muted">
                        COMPROBANTE: <b>2022B0001</b>
                    </h5>
                    <div class="row">
                        <div class="col-6">
                            <label>CLIENTE ACTUAL:</label>
                            <span class="form-control form-control-sm">JAIRO CASTILLO</span>
                        </div>
                        <div class="col-6">
                            <label>CLIENTE NUEVO:</label>
                            <select class="form-control form-control-sm">
                                <option value="">JAIRO CASTILLO</option>
                                <option value="">JAIRO CASTILLO</option>
                                <option value="">JAIRO CASTILLO</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <label>CANTIDAD HEMBRAS ACTUAL:</label>
                            <span class="form-control form-control-sm">10</span>
                        </div>
                        <div class="col-6">
                            <label>CANTIDAD HEMBRAS NUEVO:</label>
                            <input type="text" class="form-control form-control-sm input_disablecopypaste" onKeyPress="f_OnlyCant(event)" maxlength="4" id="txtHembra_ac" placeholder="Solo números">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <label>CANTIDAD MACHOS ACTUAL:</label>
                            <span class="form-control form-control-sm">10</span>
                        </div>
                        <div class="col-6">
                            <label>CANTIDAD MACHOS NUEVO:</label>
                            <input type="text" class="form-control form-control-sm input_disablecopypaste" onKeyPress="f_OnlyCant(event)" maxlength="4" id="txtMacho_ac" placeholder="Solo números">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick=""><b>GUARDAR</b></button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="btnCerrar"><b>CANCELAR</b></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Turnos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="input-group  ">
                        <input type="text" class="form-control text-sm-center text-md-left" id="txt-busqueda-turno">
                        <div class="input-group-append">
                            <button class="input-group-text" id="btn-buscar-turno">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div id="cont-table-data-turno"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btn-cerrar-modal_prueba" data-dismiss="modal">Cerrar</button>
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
    <script src="script_turnos.js"></script>
</body>

</html>