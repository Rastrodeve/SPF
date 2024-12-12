<?php
    
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

    <title>Restablecer contraseña</title>

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

<body class="hold-transition lockscreen">
    <div class="lockscreen-wrapper" >
        <div class="lockscreen-logo">
            <a href="#"><b>EMRAQ</b>-EP</a>
        </div>
        <!-- User name -->
        <div class="lockscreen-name text-left" style="padding-left:120px;"  >
            <span class=''>
            <?php 
                include '../FilePHP/menu.php';
                if (isset($_SESSION['MM_Username'])) {
                    $array = get_info_user_restore($_SESSION['MM_Username']);
                    if ($array[1]=='0') header("Location: ../perfil/");
                    else {
                        echo $array[0];
                    }
                }else{
                    header("Location: ../login");
                }
            ?>
            </span>
        </div>

        <!-- START LOCK SCREEN ITEM -->
        <div class="lockscreen-item mb-2 mt-0">
            <!-- lockscreen image -->
            <div class="lockscreen-image">
                <img src="../recursos/user-rastro.png" alt="User Image">
            </div>
            <!-- /.lockscreen-image -->

            <!-- lockscreen credentials (contains the form) -->
            <form class="lockscreen-credentials" id="form-restore">
                <div class="input-group">
                    <input type="hidden" id="txtUser" value="<?php echo $_SESSION['MM_Username']?>">
                    <input type="password" class="form-control" placeholder="Nueva contraseña" name="pass_new" id="pass_new">
                    <div class="input-group-append">
                        <button type="submit" class="btn" id="btn_restore" ><i class="fas fa-arrow-right text-muted"></i></button>
                    </div>
                </div>
            </form>
            <!-- /.lockscreen credentials -->
        </div>
        <div class="text-center mb-4">
            <div class="icheck-primary">
                <input type="checkbox" id="mostrar_contrasena">
                <label style="cursor:pointer;" for="mostrar_contrasena">Mostrar contraseña</label>
            </div>
        </div>
        <!-- /.lockscreen-item -->
        <div class="help-block text-center text-muted">
            Ingrese su nueva contraseña
        </div>
        <div class="text-center">
            <a href="../FilePHP/cerrar.php">o inicie sesión con otro usuario</a>
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