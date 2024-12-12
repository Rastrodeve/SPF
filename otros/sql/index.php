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
    <title>SQL</title>

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
    <!-- FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
    <!-- MY CSS -->
    <link rel="stylesheet" href="../../dist/css/mycss.css">
    <style>
        .card li {
            list-style: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lib-sub1 a {
            cursor: pointer;
        }

        .card li>a {
            cursor: pointer;
            color: #454545;
        }

        .card li>a:hover {
            color: #000080;
        }

        #menu > ul {
            padding: 5px;
            /* margin: 0px; */
            width:100%;
            padding-bottom:140px;
        }

        .lib-sub1 {
            padding: 5px;
        }

        #sidebar {
            scrollbar-width: thin;
            scrollbar-color: #808080 #C0C0C0;
            overflow-y: scroll;
        }

        .cursor-pointer {
            cursor: pointer;
        }


        #sidebar::-webkit-scrollbar {
            -webkit-appearance: none;
        }

        #sidebar::-webkit-scrollbar:vertical {
            width: 8px;
        }

        #sidebar::-webkit-scrollbar-button:increment,
        #sidebar::-webkit-scrollbar-button {
            display: none;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background-color: #797979;
            border-radius: 20px;
            border: 2px solid #f1f2f3;
        }

        #sidebar::-webkit-scrollbar-track {
            border-radius: 10px;
        }
    </style>
</head>

<body class="layout-top-nav " oncontextmenu="return false">
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
                else data_menu(28);
                $sidebar_cc = 'style="width: 250px;"';
                $contendio_cc = 'style="margin-left:250px;"';
                if ($_SESSION['SENTENCIA-MYSQL'][3] == 1) {
                    $sidebar_cc = '';
                    $contendio_cc = '';
                }
            }else{
                header("Location: ../../login");
            }
        ?>
        <div class="content-wrapper ">
            <div class="content-header ">
                <div class="container-fluid ">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="m-0 text-dark"><b>SQL</b></h5>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">Otros</li>
                                <li class="breadcrumb-item active">sql</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <div class="container-fluid p-2 text-sm ">
                <div id="sidebar" class="sidebar card card-body" <?php echo $sidebar_cc;?> >
                </div>
                <div  id="contenido"  <?php echo $contendio_cc;?> >
                    <div class="card card-body pt-1" id="card-data" >

                    </div>
                </div>
                <div class="row d-none">
                    <div class="col-lg-2">
                        <div class="card ">
                            <div class="card-header p-0 m-1">
                                <center>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </center>
                            </div>
                            <div class="card-body p-0" id="cont-menu-raiz-">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-10">
                        <div class="card">
                            <div class="card-body" id="card-data">

                            </div>
                        </div>
                    </div>
                </div>
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
    <script>
        function mostrar() {
            document.getElementById("sidebar").style.width = "250px";
            document.getElementById("contenido").style.marginLeft = "250px";
            document.getElementById("abrir").style.display = "none";
            document.getElementById("cerrar").style.display = "inline";
            f_select(0,3);
        }

        function ocultar() {
            document.getElementById("sidebar").style.width = "0";
            document.getElementById("contenido").style.marginLeft = "0";
            document.getElementById("abrir").style.display = "inline";
            document.getElementById("cerrar").style.display = "none";
            f_select(1,3);
        }
        $(window).scroll(function () {
            if ($(this).scrollTop() > 50) {
                document.getElementById("sidebar").style.top = "0px";
            }else{
                document.getElementById("sidebar").style.top = "120px";
            }
        })
    </script>

</body>

</html>