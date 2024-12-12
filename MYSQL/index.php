<?php
session_start();
if (!isset($_SESSION['MM_Username'])) {
  header("location: ../../acceso.php");
}  
$resultado='<select class="custom-select" id="select-base">';
  $enlace = mysql_connect('localhost', "root", "T3cn0l0g14*15");
    $lista_bd = mysql_list_dbs($enlace);
    while ($fila = mysql_fetch_object($lista_bd)) {
      $resultado=$resultado."<option>".$fila->Database."</option>";
      }
  $resultado= $resultado.'</select>';
?>

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

  <title>CONSULTAS</title>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">


    <!-- DataTables -->
  <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

  <!-- Select2 -->
  <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="../plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
  <!-- Sweetalert2 -->
  <link rel="stylesheet" href="../sweetalert2/dist/sweetalert2.min.css">
  <script src="../sweetalert2/dist/sweetalert2.all.js"></script>

  <style>
    .my-border{
      border-right: 1px solid #C0C0C0;
      border-top: 1px solid #C0C0C0;
      border-bottom: 1px solid #C0C0C0;
      padding-top: 10px;
      padding-bottom: 10px;
    }
    .my-border hr{
      margin-top: 0px;
      background: #808080;
    }
    .tbl_detalle th{
      padding:0px; 
    }
    
    .overflow{
      overflow:hidden; 
    }
    .cont-carga{
    width: 100%;
    height: 100vh;
    background: #E6E6E6;
    display: flex;
    align-items: center;
    position: absolute;
    top: 0;
    left: 0;
    overflow:hidden; 
    justify-content: center;
    z-index: 100;
    }
    .pre-loader{
      position: absolute;
      left: 50%;
      top: 50%;
    }
    .pre-loader > span{
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
    .pre-loader > span:nth-child(1){
      left: -4.5em;
      animation-delay: 0s;
    }
    .pre-loader > span:nth-child(2){
      left: -1.5em;
      animation-delay: 0.1s;
    }
    .pre-loader > span:nth-child(3){
      left: 1.5em;
      animation-delay: 0.2s;
    }
    .pre-loader > span:nth-child(4){
      left: 4.5em;
      animation-delay: 0.3s;
    }
    @keyframes wave {
      0%,
      75%,
      100%{
        transform: translateY(0) scale(1);
      }
      25%{
        transform: translateY(2.5em);
      }
      50%{
        transform: translateY(-2.5em) scale(1.1);
      }
    }
     .loader{
      border: 16px solid   #d0d3d4 ; /* #212f3c */
      border-top: 16px solid #212f3c; /* #800000 */
      border-radius: 50%;
      width: 100px;
      height: 100px;
      position: relative;
      left: 50%;
      transform:translateY(-50%);
      animation: spin 2s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .my-span{
      cursor: pointer;
      font-size: 15px;
    }
  </style>
</head>
<body class="hold-transition layout-top-nav overflow">
  <div class="cont-carga " >
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
      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse order-3 " id="navbarCollapse">
        <!-- Left navbar links -->
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
    <div class="content-header  "    >
      <div class="container-fluid" >
        <div class="row "  >
          <div class="col-md-12" >
            <h1 class="m-0 text-dark"><b>GENERARDOR DE CONSULTAS</b></h1>  
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="row">
                  <div class="col-md-2 mb-2" >
                    <?php echo $resultado; ?>
                  </div>
                  <div class="col-md-2 mb-2" >
                    <button class="btn btn-light" onclick="Desplegar_T()"><b>Desplegar tablas</b></button>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 mb-2" id="cont-tablas" style="display: none;"></div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <textarea class="form-control" id="txtCont"></textarea>
                        <button id="btn" class="btn btn-info float-right mt-2"><b>CONSULTAR</b></button>
                    </div>   

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="loader d-none" ></div>
                        <div id="cont-table"></div>    
                    </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    
                  </div>
                </div>
            </div>
        </div>
      </div>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
  </div>
  <!-- ./wrapper -->
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

<script src="script.js"></script>
</body>
</html>

