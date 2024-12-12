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

    <title>FAENAMIENTO</title>

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

        .custom-control-label {
            cursor: pointer;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>
</head>

<body class="layout-top-nav ">
    <div class="row p-3">
        <div class="col-md-6">
            <input type="text" class="form-control" id="txtCode">
        </div>
        <div class="col-md-6">
            <button class="btn btn-info" id="btn"><b>AGREGAR</b></button>
        </div>
    </div>
    <label id="mensaje"></label>


    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- bs-custom-file-input -->
    <script src="../plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <!-- Swwralert2  -->
    <script src="../sweetalert2/dist/sweetalert2.min.js"></script>
    <!-- toast -->
    <script src="../plugins/toastr/toastr.min.js"></script>
    <script>
        $("#btn").click(function () {
            code = $("#txtCode").val();
            if (code != "") {
                $.ajax({
                    url: 'operaciones.php?op=1',
                    data: {
                        Codigo: code
                    },
                    type: 'POST',
                    success: function (r) {
                        $("#mensaje").html(r);
                    }
                });
            }else{
                alert("Vacio");
            }
        });
    </script>
</body>

</html>