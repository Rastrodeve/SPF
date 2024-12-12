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
    <title>ESPECIES - SERVICIOS</title>

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
        #cont-img {
            cursor: pointer;
        }

        #cont-img:hover>img {
            opacity: 0.1;
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
                else data_menu(8);
            }else{
                header("Location: ../../login");
            }
        ?>
        <div class="content-wrapper ">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="m-0 text-dark"><b>ESPECIES - SERVICIOS</b></h5>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">Administrador</li>
                                <li class="breadcrumb-item active">Especies - servicios</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <div class="container-fluid p-2">
                <div class="row mb-2">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-outline-secondary pl-5 pr-5"
                            onclick="get_data_new_especie()" data-toggle="modal" data-target="#modal">
                            <span><i class="fas fa-plus"></i></span>
                            <b>AÑADIR ESPECIE</b>
                        </button>
                        <hr>
                    </div>
                </div>
                <div id="cont-datos"></div>
            </div>
        </div>
        <?php include '../../FilePHP/footer.php';?>
    </div>

    <!-- Modal de nueva especie -->
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content" id="modal-content">
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title" id="modalLabel">
                        <b>AÑADIR UNA NUEVA ESPECIE</b>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Nombre de la Especie:</label> <span class="text-muted">(max 30)</span>
                            <input type="text" class="form-control form-control-sm" maxlength="30"
                                id="txtCodigoEscpecie" placeholder="G. BOVINO">
                        </div>
                        <div class="col-md-4">
                            <label>Tipo de Ganado: </label>
                            <select id="slcLinea" class="form-control form-control-sm select2bs4"
                                style="width:100%;cursor:pointer">
                                <option value="1">Ganado Menor</option>
                                <option value="0">Ganado Mayor</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Letra: </label>
                            <input type="text" class="form-control text-center form-control-sm" id="txtLetraEspecie"
                                onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="1" placeholder="B">
                        </div>
                    </div>
                    <hr>
                    <label class="mb-0">Estancia Mínima:</label>
                    <span style="font-weight: lighter;font-size:13px;" class="text-muted">
                        Corresponde al tiempo que debe superar un animal para ser faenado, cuando se supere este tiempo
                        el animal aparecerá en la orden de producción
                    </span>
                    <div class="row">
                        <div class="col-4">
                            <label for="txtHorasEstancia">Horas:</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control text-center" maxlength="3" id="txtHorasEstancia"
                                    value="0">
                                <div class="input-group-append">
                                    <label class="input-group-text" for="txtHorasEstancia">H</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <label for="slcMinutos">Minutos:</label>
                            <div class="input-group mb-3 input-group-sm">
                                <select class="custom-select" id="slcMinutos">
                                </select>
                                <div class="input-group-append">
                                    <label class="input-group-text" for="slcMinutos">M</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <label for="slcSegundos">Segundos:</label>
                            <div class="input-group mb-3 input-group-sm">
                                <select class="custom-select" id="slcSegundos">
                                </select>
                                <div class="input-group-append">
                                    <label class="input-group-text" for="slcSegundos">S</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <label>Corralaje:</label>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" style="cursor:pointer;" type="radio" name="radio"
                                    id="rd1" value="1" checked>
                                <label class="form-check-label" style="cursor:pointer;" for="rd1">Activado</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" style="cursor:pointer;" type="radio" name="radio"
                                    id="rd2" value="0">
                                <label class="form-check-label" style="cursor:pointer;" for="rd2">Desactivado</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div id="cont-active">
                        <label>Tiempo permitido antes de empezar a cobrar corralaje:</label>
                        <div class="row">
                            <div class="col-4">
                                <label for="txtHorasCorralaje">Horas:</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control text-center " id="txtHorasCorralaje"
                                        maxlength="3" value="0">
                                    <div class="input-group-append">
                                        <label class="input-group-text" for="txtHorasCorralaje">H</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <label for="slcMinutos">Minutos:</label>
                                <div class="input-group mb-3 input-group-sm">
                                    <select class="custom-select" id="slcMinutos">
                                    </select>
                                    <div class="input-group-append">
                                        <label class="input-group-text" for="slcMinutos">M</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <label for="slcSegundos">Segundos:</label>
                                <div class="input-group mb-3 input-group-sm">
                                    <select class="custom-select" id="slcSegundos">
                                    </select>
                                    <div class="input-group-append">
                                        <label class="input-group-text" for="slcSegundos">S</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Taza de Corralaje:</label>
                                <div class="input-group ">
                                    <input type="text" class="form-control form-control-sm" id="txtTaza"
                                        placeholder="0.00" onKeyPress="return soloNumeros_Decimales(event)">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label for="slcYupack">Código Yupak para el corralaje:</label>
                                <select id="slcYupack" class="form-control select2bs4 " style="width: 100%;">
                                    <option value="CARGANDO..">CARGANDO..</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <label>Detalle de especie</label>
                    <p>
                        SELECCIONE <b>SI</b> o <b>NO</b> detallar indiviudalmente una guia de proceso.
                        <span class="text-muted">Esta información será utilizada al momento de generar la <b>Orden de
                                Producción</b></span>
                    </p>
                    <select id="slcDetalle" class="form-control form-control-sm select2bs4"
                        style="width:100%;cursor:pointer;">
                        <option style="cursor:pointer;" value="0" selected="true">NO, DETALLAR</option>
                        <option style="cursor:pointer;" value="1">SI, DETALLAR</option>
                    </select>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="file-upload" class="btn btn-success">
                                Seleccionar imagen
                            </label>
                        </div>
                    </div>
                    <div id="preview" style="width:100%;text-align:center;"></div>
                    <input id="file-upload" class="d-none" type="file" />
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
    <!-- Modal editar taza corralaje -->
    <div class="modal fade" id="modalN" tabindex="-1" role="dialog" aria-labelledby="modalNLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content" id="modalN-content">
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title" id="modalNLabel">
                        <b>EDITAR IMAGEN</b>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-muted">G. BOVINO</h6>
                        </div>
                    </div>
                    <hr class="mt-2">
                    <div id="cont-img">
                        <img id="img-view" src="../../recursos/especies/null.png" alt="NULL" width="100%">
                    </div>
                    <input type="file" class="d-none" id="file-img-new">
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

    <!-- FIM MODEL  -->
    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="../../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../../dist/js/adminlte.min.js"></script>
    <!-- Select2 -->
    <script src="../../plugins/select2/js/select2.full.min.js"></script>

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