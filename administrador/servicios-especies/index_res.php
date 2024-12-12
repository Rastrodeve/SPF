<?php
// session_start();
// if (!isset($_SESSION['MM_Username'])) {
//   header("location: ../../acceso.php");
// }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="shortcut icon" href="../../Recursos/servicios_config.ico">
    <title>Especies - Servicios</title>

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
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="../../sweetalert2/dist/sweetalert2.min.css">
    <script src="../../sweetalert2/dist/sweetalert2.all.js"></script>

    <style>
        .my-border {
            border-right: 1px solid #C0C0C0;
            border-top: 1px solid #C0C0C0;
            border-bottom: 1px solid #C0C0C0;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .my-border hr {
            margin-top: 0px;
            background: #808080;
        }

        .tbl_detalle th {
            padding: 0px;
        }

        .overflow {
            overflow: hidden;
        }

        .cont-carga {
            width: 100%;
            height: 100vh;
            background: #E6E6E6;
            display: flex;
            align-items: center;
            position: absolute;
            top: 0;
            left: 0;
            overflow: hidden;
            justify-content: center;
            z-index: 100;
        }

        .pre-loader {
            position: absolute;
            left: 50%;
            top: 50%;
        }

        .pre-loader>span {
            height: 2em;
            width: 2em;
            background: #454545;
            display: block;
            position: absolute;
            left: 0px;
            top: 0px;
            border-radius: 50%;
            animation: wave 2s ease-in-out infinite;
        }

        .pre-loader>span:nth-child(1) {
            left: -4.5em;
            animation-delay: 0s;
        }

        .pre-loader>span:nth-child(2) {
            left: -1.5em;
            animation-delay: 0.1s;
        }

        .pre-loader>span:nth-child(3) {
            left: 1.5em;
            animation-delay: 0.2s;
        }

        .pre-loader>span:nth-child(4) {
            left: 4.5em;
            animation-delay: 0.3s;
        }

        @keyframes wave {

            0%,
            75%,
            100% {
                transform: translateY(0) scale(1);
            }

            25% {
                transform: translateY(2.5em);
            }

            50% {
                transform: translateY(-2.5em) scale(1.1);
            }
        }

        .loader {
            border: 16px solid #d0d3d4;
            /* #212f3c */
            border-top: 16px solid #212f3c;
            /* #800000 */
            border-radius: 50%;
            width: 100px;
            height: 100px;
            position: relative;
            left: 50%;
            transform: translateY(-50%);
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .my-span {
            cursor: pointer;
            font-size: 15px;
        }
    </style>
</head>

<body class="hold-transition layout-top-nav overflow">
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
            <div class="container-fluid">
                <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse order-3 " id="navbarCollapse">
                    <!-- Left navbar links -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="../../menuprocesos.php" class="nav-link bg-white "
                                style="border-radius: 5px;"><b>SISTEMA DE PROCESOS</b> </a>
                        </li>
                    </ul>
                </div>
                <ul class="order-1 order-md-3 navbar-nav navbar-no-expand  bg-light" style="border-radius: 5px;">
                    <img src="../../imagenes_new/Logo-rastro.png" width="150px">
                </ul>
            </div>
        </nav>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header  ">
                <div class="container-fluid">
                    <div class="row ">
                        <div class="col-md-12">
                            <h1 class="m-0 text-dark">
                                <b>CONFIGURACIÓN DE ESPECIES - SERVICIOS</b>
                            </h1>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->
            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <!-- <div class="loader d-none"></div>
                    <div> -->
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary  pl-5 pr-5" data-toggle="modal"
                                    data-target="#modalEspecies">
                                    <span><i class="fas fa-plus"></i></span>
                                    <b>AÑADIR ESPECIE</b>
                                </button>
                                <button data-toggle="modal" data-target="#modalTaza" type="button"
                                    class="btn btn-secondary  pl-5 pr-5" onclick="Cargar_Datos_Taza()">
                                    <span><i class="fas fa-cog"></i></span>
                                    <b>TAZA DE CORRALAJE</b>
                                </button>

                                <hr>
                            </div>
                        </div>

                        <div id="cont-datos"></div>
                    </div>
                </div>
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>
    <!-- ./wrapper -->
    <!-- REQUIRED SCRIPTS -->

    <button data-toggle="modal" data-target="#modalOtros" id="btnAbrir-modalOtros" style="display:none;"></button>
    <div class="modal fade" id="modalOtros" tabindex="-1" role="dialog" aria-labelledby="modalOtrosLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title" id="modalOtrosLabel">
                        <b id="tituloServicios">AÑADIR NUEVO SERVICIO</b>
                        <input type="hidden" id="txtId" value="0">
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>TIPO DE GANADO:</label>
                    <span id="spnGanado"></span>
                    (<span id="spnCodigoGanado"></span>)
                    <hr>
                    <label>Descripción del Servicio</label>
                    <input type="text" id="txtDescrip" class="form-control" placeholder="Descripción">
                    <hr>
                    <label>Selecione un servicio Yupak</label> (<span id="spnCodigo-otros"></span>)
                    <select id="slcYupack-otros" class="select2bs4 " style="width: 100%;">
                        <option value="CARGANDO..">CARGANDO..</option>
                    </select>
                    <hr>
                    <label>Precio </label> (Eje: 18.34)
                    <input type="text" id="txtPrecio" class="form-control" placeholder="00.00"
                        onKeyPress="return soloNumeros_Decimales(event)" />
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnCerrar-otros" class="btn btn-secondary"
                        data-dismiss="modal"><b>CANCELAR</b></button>
                    <button type="button" class="btn btn-primary" id="btn-insertar-otros">
                        <b>GUARDAR</b>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <button data-toggle="modal" data-target="#modalCorralaje" id="btnAbrir-modalCorralaje"
        style="display:none;"></button>
    <div class="modal fade" id="modalCorralaje" tabindex="-1" role="dialog" aria-labelledby="modalCorralajeLabel"
        aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="modalCorralajeLabel">
                        <b>CONFIGURACIÓN DEL CORRALAJE</b>
                        <input type="hidden" id="txtId" value="0">
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>TIPO DE ESPECIE:</label>
                    <span id="spnGanado-corralaje"></span>
                    (<span id="spnCodigoGanado-corralaje"></span>)
                    <hr>
                    <label>ESTADO DE CORRALAJE:</label>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="radio" id="rd1" value="1">
                                <label class="form-check-label" for="rd1">Corralaje Activado</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="radio" id="rd2" value="0">
                                <label class="form-check-label" for="rd2">Corralaje Desactivado</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <label>SELECCIONE CÓDIGO YUPAK:</label> (<span id="spnCodigo-Corralaje"></span>)
                    <select id="slcYupack-corralaje" class="select2bs4" style="width:100%;">
                        <option value="tgb_bo">CARGANDO..</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnCerrar-Corralaje" class="btn btn-light"
                        data-dismiss="modal"><b>CERRAR</b></button>
                    <button type="button" class="btn btn-primary" id="btn-guardar-corralaje">
                        <b>GUARDAR</b>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEspecies" tabindex="-1" role="dialog" aria-labelledby="modalEspeciesLabel"
        aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title" id="modalEspeciesLabel">
                        <b>AÑADIR UNA NUEVA ESPECIE</b>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- <label>COMPLETAR TODOS LOS CAMPOS</label>
        <hr> -->
                    <label>Codigo de la especie:</label>
                    <input type="text" class="form-control" id="txtCodigoEscpecie" placeholder="tg_bov001">
                    <hr>
                    <label>Descripción :</label>
                    <input type="text" class="form-control" id="txtDescripEspecie" placeholder="G. Bovino">
                    <hr>
                    <div class="form-group row">
                        <span for="txtLetraEspecie" class="col-sm-10 col-form-label">
                            <b>Letra para el comprobante:</b> (2021<b>B</b>00001)</span>
                        <div class="col-sm-2">
                            <input type="text" class="form-control text-center" id="txtLetraEspecie"
                                onkeyup="javascript:this.value=this.value.toUpperCase();" style="width:55px"
                                placeholder="B">
                        </div>
                    </div>

                    <hr>
                    <label>Estancia Mínima:</label>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="txtHorasEstancia">Horas:</label>
                            <div class="input-group ">
                                <input type="text" class="form-control text-center" id="txtHorasEstancia" value="0">
                                <div class="input-group-append">
                                    <label class="input-group-text" for="txtHorasEstancia">H</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="slcMinutos">Minutos:</label>
                            <div class="input-group mb-3">
                                <select class="custom-select" id="slcMinutos">
                                </select>
                                <div class="input-group-append">
                                    <label class="input-group-text" for="slcMinutos">M</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="slcSegundos">Segundos:</label>
                            <div class="input-group mb-3">
                                <select class="custom-select" id="slcSegundos">
                                </select>
                                <div class="input-group-append">
                                    <label class="input-group-text" for="slcSegundos">S</label>
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

    <button data-toggle="modal" data-target="#modalEspecies-edit" id="btnAbrir-editarespecie"
        style="display:none;"></button>
    <div class="modal fade" id="modalEspecies-edit" tabindex="-1" role="dialog" aria-labelledby="modalEspecieseditLabel"
        aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title" id="modalEspecieseditLabel">
                        <b>EDITAR ESPECIE</b>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label class="mb-0">CÓDIGO DE ESPECIE:</label> <span id="spnGanado-edit"></span>
                    <hr>
                    <label>Descripción :</label>
                    <input type="text" class="form-control" id="txtDescripEspecie-edit" placeholder="G. Bovino">
                    <hr>
                    <label>Letra para el comprobante: </label> <span id="spnLetra">B</span>
                    <hr>
                    <label>Estancia Mínima:</label>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="txtHorasEstancia-edit">Horas:</label>
                            <div class="input-group ">
                                <input type="text" class="form-control text-center" id="txtHorasEstancia-edit"
                                    value="0">
                                <div class="input-group-append">
                                    <label class="input-group-text" for="txtHorasEstancia-edit">H</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="slcMinutos-edit">Minutos:</label>
                            <div class="input-group mb-3">
                                <select class="custom-select" id="slcMinutos-edit">
                                </select>
                                <div class="input-group-append">
                                    <label class="input-group-text" for="slcMinutos-edit">M</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="slcSegundos-edit">Segundos:</label>
                            <div class="input-group mb-3">
                                <select class="custom-select" id="slcSegundos-edit">
                                </select>
                                <div class="input-group-append">
                                    <label class="input-group-text" for="slcSegundos-edit">S</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnCerrar-especie-edit" class="btn btn-light"
                        data-dismiss="modal"><b>CERRAR</b></button>
                    <button type="button" class="btn btn-primary" id="btn-guardar-especie-edit">
                        <b>GUARDAR</b>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTaza" tabindex="-1" role="dialog" aria-labelledby="modalTazaLabel"
        aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title" id="modalTazaLabel">
                        <b>EDITAR TAZA DE CORRALAJE</b>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>Taza de Corralaje:</label> (Eje 0.83)
                    <div class="input-group ">
                        <input type="text" class="form-control" id="txtTaza" placeholder="0.00"
                            onKeyPress="return soloNumeros_Decimales(event)">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                        </div>
                    </div>
                    <hr>
                    <label>Tiempo permitido antes de empezar a cobrar corralaje:</label>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="txtHorasEstancia-taza">Horas:</label>
                            <div class="input-group ">
                                <input type="text" class="form-control text-center" id="txtHorasEstancia-taza"
                                    value="0">
                                <div class="input-group-append">
                                    <label class="input-group-text" for="txtHorasEstancia-taza">H</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="slcMinutos-taza">Minutos:</label>
                            <div class="input-group mb-3">
                                <select class="custom-select" id="slcMinutos-taza">
                                </select>
                                <div class="input-group-append">
                                    <label class="input-group-text" for="slcMinutos-taza">M</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="slcSegundos-taza">Segundos:</label>
                            <div class="input-group mb-3">
                                <select class="custom-select" id="slcSegundos-taza">
                                </select>
                                <div class="input-group-append">
                                    <label class="input-group-text" for="slcSegundos-taza">S</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnCerrar-taza" class="btn btn-light"
                        data-dismiss="modal"><b>CERRAR</b></button>
                    <button type="button" class="btn btn-primary" id="btn-taza">
                        <b>GUARDAR</b>
                    </button>
                </div>
            </div>
        </div>
    </div>

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
    <!-- Bootstrap4 Duallistbox -->
    <script src="../../plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    <script src="script_res.js"></script>
    <script type="text/javascript">
        function confi_corralaje(tipo, especie, yupak, estado) {
            if (estado == 0) $("#rd2").prop("checked", true);
            else $("#rd1").prop("checked", true);
            $("#spnGanado-corralaje").html(tipo.toUpperCase());
            $("#spnCodigoGanado-corralaje").html(especie);
            select_valor = $("#slcYupack-corralaje option[value='" + yupak + "'").text();
            $("#spnCodigo-Corralaje").html(yupak);
            $('#slcYupack-corralaje').val(yupak);
            $('#select2-slcYupack-corralaje-container').html(select_valor);
            $('#select2-slcYupack-corralaje-container').prop('title', select_valor);
            $("#btnAbrir-modalCorralaje").click();
        }

        function editar_especie(ganado, especie, letra, tiempo) {
            $("#spnGanado-edit").html(especie);
            $("#txtDescripEspecie-edit").val(ganado);
            $("#spnLetra").html(letra);
            arrayD = tiempo.split(":");
            $('#txtHorasEstancia-edit').val(arrayD[0]);
            $('#slcMinutos-edit').val(arrayD[1]);
            $('#slcSegundos-edit').val(arrayD[2]);
            $("#btnAbrir-editarespecie").click();
        }
    </script>
</body>

</html>