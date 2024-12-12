$(document).ready(function () {
    $('.cont-carga').addClass('d-none');
    data_user();
    data_log();
    data_soli();
});

function data_user() {
    f_carga("box-profile");
    $.ajax({
        url: 'operaciones.php?op=1',
        success: function (r) {
            $(".box-profile").html(r);
        }
    });
}
function data_log() {
    f_carga("custom-tabs-one-act");
    $.ajax({
        url: 'operaciones.php?op=6',
        success: function (r) {
            $("#custom-tabs-one-act").html(r);
            confi_tabla('tbl_view_log');
        }
    });
}
function data_soli() {
    f_carga("custom-tabs-one-soli");
    $.ajax({
        url: 'operaciones.php?op=7',
        success: function (r) {
            $("#custom-tabs-one-soli").html(r);
            confi_tabla('tbl_view_solici');
        }
    });
}

function data_password() {
    f_carga("modal-content");
    $.ajax({
        url: 'operaciones.php?op=2',
        success: function (r) {
            $("#modal-content").html(r);
        }
    });
}
function data_solicutud(id) {
    f_carga("modal-body");
    $.ajax({
        data:{Id :id},
        type:'POST',
        url: 'operaciones.php?op=8',
        success: function (r) {
            $("#modal-body").html(r);
        }
    });
}

function data_update_user() {
    f_carga("modal-content");
    $.ajax({
        url: 'operaciones.php?op=3',
        success: function (r) {
            $("#modal-content").html(r);
        }
    });
}

function update_user() {
    nombre = $("#txtUser_update").val();
    cargo = $("#txtCargo_update").val();
    if (nombre == '' || cargo == '') {
        Swal.fire({
            icon: 'error',
            html: '<h4>No puede dejar los campos vacios</h4>',
        });
        if (nombre == '') $("#txtUser_update").addClass("is-invalid");
        if (cargo == '') $("#txtCargo_update").addClass("is-invalid");
    } else {
        $("#txtUser_update").removeClass("is-invalid");
        $("#txtCargo_update").removeClass("is-invalid");
        mensaje = '' +
            '<h5><b>Esta editando su información:</b></h5>' +
            '<h6><label>Nombre :</label> ' + nombre + '</h6>' +
            '<h6><label>Cargo :</label> ' + cargo + '</h6>' +
        '';
        Swal.fire({
            title: 'Verifique la información',
            html: mensaje,
            icon: 'warning',
            showCancelButton: false,
            showDenyButton: true,
            confirmButtonColor: '#3085d6',
            denyButtonColor: '#d33',
            confirmButtonText: 'Si, Continuar',
            denyButtonText: 'No, Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "operaciones.php?op=4",
                    data: {
                        Nombre: nombre,
                        Cargo: cargo,
                    },
                    type: 'POST',
                    success: function (r) {
                        if (r == true) {
                            $("#btnCerrar").click();
                            data_user();
                            Swal.fire({
                                icon: 'success',
                                html: '<h4>Datos Actualizados</h4>',
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                html: '<h4><b>Error: </b> ' + r + '</h4>',
                            });
                        }
                    }
                });
            }
        });
    }
}

function update_pass() {
    passold = $("#txtpassold-1").val();
    pass = $("#txtpass").val();
    passconfi = $("#txtpassconfi").val();
    if (pass == '' || passconfi == ''|| passold =='') {
        Swal.fire({
            icon: 'error',
            html: '<h4>No puede dejar los campos vacios</h4>',
        });
        if (passold == '') $("#txtpassold-1").addClass("is-invalid");
        if (pass == '') $("#txtpass").addClass("is-invalid");
        if (passconfi == '') $("#txtpassconfi").addClass("is-invalid");
    }else if(pass != passconfi){
        $("#txtpass").removeClass("is-invalid");
        $("#txtpassconfi").removeClass("is-invalid");
        Swal.fire({
            icon: 'error',
            html: '<h4>Las contraseñas no coinciden</h4>',
        });
        $("#txtpass").addClass("is-warning");
        $("#txtpassconfi").addClass("is-warning");
    }else {
        $("#txtpass").removeClass("is-warning");
        $("#txtpassconfi").removeClass("is-warning");
        mensaje = '' +
            '<h5><b>Esta seguro que quiere cambiar su clave de acceso</b><br> Deberá inicar sesión otra vez</h5>' +
        '';
        Swal.fire({
            title: 'Mensaje de confirmación',
            html: mensaje,
            icon: 'warning',
            showCancelButton: false,
            showDenyButton: true,
            confirmButtonColor: '#3085d6',
            denyButtonColor: '#d33',
            confirmButtonText: 'Si, Continuar',
            denyButtonText: 'No, Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "operaciones.php?op=5",
                    data: {
                        Pass: pass,
                        Passold: passold,
                    },
                    type: 'POST',
                    success: function (r) {
                        if (r == true) {
                            window.location.href="../FilePHP/cerrar.php";
                        } else {
                            Swal.fire({
                                icon: 'error',
                                html: '<h4>' + r + '</h4>',
                            });
                        }
                    }
                });
            }
        });
    }
}
function mostrar_contrasenia(pass,icon) {
    var cambio = document.getElementById(pass);
    if(cambio.type == "password"){
        cambio.type = "text";
        $('#'+icon).removeClass('fas fa-eye').addClass('fa fa-eye-slash');
    }else{
        cambio.type = "password";
        $('#'+icon).removeClass('fa fa-eye-slash').addClass('fas fa-eye');
    } 
}

function f_eliminar_solicitud(id) {
    mensaje = '' +
            '<h5><b>ESTA SEGURO QUE DESEA ELIMINAR ESTA SOLICITUD</b></h5>' +
        '';
        Swal.fire({
            title: 'MENSAJE DE CONFIMARCIÓN',
            html: mensaje,
            icon: 'warning',
            showCancelButton: false,
            showDenyButton: true,
            confirmButtonColor: '#3085d6',
            denyButtonColor: '#d33',
            confirmButtonText: 'Si, Continuar',
            denyButtonText: 'No, Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "operaciones.php?op=9",
                    data: {Id: id},
                    type: 'POST',
                    success: function (r) {
                        if (r == true) {
                            Swal.fire({
                                icon: 'success',
                                html: '<h4>Solicitud Eliminada</h4>',
                            });
                            $("#btnCerrar").click();
                            data_soli();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                html: '<h4>' + r + '</h4>',
                            });
                        }
                    }
                });
            }
        });
}
function f_resul_solicitud(id,estado) {
        if(estado == 1){
            mensaje = '' +
            '<h5><b>ESTA SEGURO QUE DESEA APROBAR ESTA SOLICITUD</b></h5>' +
            '';
        }else{
            mensaje = '' +
            '<h5><b>ESTA SEGURO QUE DESEA RECHAZAR ESTA SOLICITUD</b></h5>' +
            '';
        }
        Swal.fire({
            title: 'MENSAJE DE CONFIMARCIÓN',
            html: mensaje,
            icon: 'warning',
            showCancelButton: false,
            showDenyButton: true,
            confirmButtonColor: '#3085d6',
            denyButtonColor: '#d33',
            confirmButtonText: 'Si, Continuar',
            denyButtonText: 'No, Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "operaciones.php?op=10",
                    data: {Id: id,Estado : estado},
                    type: 'POST',
                    success: function (r) {
                        if (r == true) {
                            Swal.fire({
                                icon: 'success',
                                html: '<h4>Solicitud Aprobada</h4>',
                            });
                            $("#btnCerrar").click();
                            data_soli();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                html: '<h4>' + r + '</h4>',
                            });
                        }
                    }
                });
            }
        });
}
