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

    <title>PLANTILLA</title>

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
        

    </style>
</head>

<body class="layout-top-nav text-sm" >
    <div class="cont-carga ">
        <div class="pre-loader">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="wrapper" id="goFS">
        <nav class="main-header navbar navbar-expand-md navbar-dark navbar-navy ">
            <div class="container">
                <a class="navbar-brand">
                    <img src="../Recursos/logo.png" alt="Rastro Logo" class="brand-image img-circle elevation-3"
                        style="opacity: .8">
                    <span class="brand-text font-weight-light">EMRAQ-EP</span>
                </a>

                <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                    <!-- Left navbar links -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="../../menuprocesos.php" class="nav-link">Sisprocesos</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false" class="nav-link dropdown-toggle">Recepción</a>
                            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                                <li><a href="#" class="dropdown-item">Detalle de Guía</a></li>
                                <li><a href="#" class="dropdown-item">Por establecer</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false" class="nav-link dropdown-toggle">Producción</a>
                            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                                <li><a href="#" class="dropdown-item">Orden de Producción</a></li>
                                <li><a href="#" class="dropdown-item">Orden Emergente</a></li>
                                <li><a href="#" class="dropdown-item">Lista de Ordenes de Producción</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false" class="nav-link dropdown-toggle">Consultas</a>
                            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                                <li><a href="#" class="dropdown-item">Por Establecer</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false" class="nav-link dropdown-toggle">Administrador</a>
                            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                                <li><a href="#" class="dropdown-item">Especies - Servicios</a></li>
                                <li><a href="#" class="dropdown-item">Productos para Despacho</a></li>
                                <li><a href="#" class="dropdown-item">Permisos de Modulos</a></li>
                                <li><a href="#" class="dropdown-item">Configuración Producción</a></li>
                                <li><a href="#" class="dropdown-item">Descargar M. Despacho</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!-- Right navbar links -->
                <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto pt-2">
                    <label style="color:white;">Módulo Detalle de Guía</label>
                </ul>
            </div>
        </nav>
        <div class="content-wrapper ">
            <div class="container-fluid p-2">
            <input id = "Button1" type = "button" value = "Iniciar pantalla completa" onclick = "kaishi()" />  
 <input id = "Button2" type = "button" value = "Cerrar pantalla completa" onclick = "guanbi()" />  
  
<script>  
         // Iniciar pantalla completa
    function kaishi()  
    {  
        var docElm = document.documentElement;  
        //W3C   
        if (docElm.requestFullscreen) {  
            docElm.requestFullscreen();  
        }  
            //FireFox   
        else if (docElm.mozRequestFullScreen) {  
            docElm.mozRequestFullScreen();  
        }  
                         // Chrome, etc.   
        else if (docElm.webkitRequestFullScreen) {  
            docElm.webkitRequestFullScreen();  
        }  
            //IE11   
        else if (elem.msRequestFullscreen) {  
            elem.msRequestFullscreen();  
        }  
    }  
  
         // Salir de pantalla completa
    function guanbi() {  
  
  
        if (document.exitFullscreen) {  
            document.exitFullscreen();  
        }  
        else if (document.mozCancelFullScreen) {  
            document.mozCancelFullScreen();  
        }  
        else if (document.webkitCancelFullScreen) {  
            document.webkitCancelFullScreen();  
        }  
        else if (document.msExitFullscreen) {  
            document.msExitFullscreen();  
        }  
    }  
  
  
     // oyente de eventos
  
    document.addEventListener("fullscreenchange", function () {  
          
        fullscreenState.innerHTML = (document.fullscreen) ? "" : "not ";  
    }, false);  
      
    document.addEventListener("mozfullscreenchange", function () {  
         
        fullscreenState.innerHTML = (document.mozFullScreen) ? "" : "not ";  
    }, false);  
     
    document.addEventListener("webkitfullscreenchange", function () {  
          
        fullscreenState.innerHTML = (document.webkitIsFullScreen) ? "" : "not ";  
    }, false);  
      
    document.addEventListener("msfullscreenchange", function () {  
          
        fullscreenState.innerHTML = (document.msFullscreenElement) ? "" : "not ";  
    }, false);  
  
  
</script>  



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
    <!-- Bootstrap4 Duallistbox -->
    <script src="../plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    <script src="../sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="script.js"></script>
    <script src="my-scripts.js"></script>
    <script>
        function simulateKeyPress() { 
            var doc = window.document;
            var docEl = doc.getElementById("goFS");

            var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
            var cancelFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen || doc.msExitFullscreen;

            if(!doc.fullscreenElement && !doc.mozFullScreenElement && !doc.webkitFullscreenElement && !doc.msFullscreenElement) {
                requestFullScreen.call(docEl);
            }
            else {
                cancelFullScreen.call(doc);
            }
        } 
        $(document).keydown(function(event) {
            // console.log(event.keyCode)
        });
    </script>
</body>

</html>