$(document).ready(function () {
    $('.cont-carga').addClass('d-none');
    get_table_user();
    // $('#slcDepartamento-edit').select2();
});

function get_table_user() {
    f_carga('cont-view');
    $.ajax({
        url: 'operaciones.php?op=1',
        success: function (r) {
            $('#cont-view').html(r);
            confi_tabla('tbl_view_user');
        }
    });
}

function cambiar_estado(cedula, estado) {
    mensaje = "Error";
    if (estado == 0) {
        mensaje = '<h6>Esta seguro que desea <b>DESACTIVAR</b> el usuario</h6>';
    } else if (estado == 1) {
        mensaje = '<h6>Esta seguro que desea <b>ACTIVAR</b> el usuario</h6>';
    }
    Swal.fire({
        title: '¿Confirmación?',
        html: mensaje,
        showCancelButton: true,
        confirmButtonText: 'CONTINUAR',
        cancelButtonText: 'CANCELAR',
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            $.ajax({
                url: 'operaciones.php?op=2',
                data: {
                    Cedula: cedula,
                    Estado: estado
                },
                type: 'POST',
                success: function (r) {
                    if (r == true) {
                        get_table_user();
                        Swal.fire({
                            icon: 'success',
                            html: '<h4>Datos Actualizados</h4>',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            html: r,
                        });
                    }
                }
            });
        }
    });
}

function get_data_user(cedula) {
    f_carga('modal-content');
    $.ajax({
        url: 'operaciones.php?op=3',
        data: {
            Cedula: cedula
        },
        type: 'POST',
        success: function (r) {
            $('#modal-content').html(r);
        }
    });
}

function get_data_pass(cedula) {
    f_carga('modal-content');
    $.ajax({
        url: 'operaciones.php?op=5',
        data: {
            Cedula: cedula
        },
        type: 'POST',
        success: function (r) {
            $('#modal-content').html(r);
        }
    });
}

function get_data_permisos(cedula) {
    f_carga('modal-content');
    $.ajax({
        url: 'operaciones.php?op=7',
        data: {
            Cedula: cedula
        },
        type: 'POST',
        success: function (r) {
            $('#modal-content').html(r);
        }
    });
}
function get_data_cedula_user(cedula) {
    f_carga('modal-content');
    $.ajax({
        url: 'operaciones.php?op=11',
        data: {
            Cedula: cedula
        },
        type: 'POST',
        success: function (r) {
            $('#modal-content').html(r);
        }
    });
}

function mostrar_contrasenia() {
    var cambio = document.getElementById("txtpassrestor");
    if (cambio.type == "password") {
        cambio.type = "text";
        $('#icon').removeClass('fas fa-eye').addClass('fa fa-eye-slash');
    } else {
        cambio.type = "password";
        $('#icon').removeClass('fa fa-eye-slash').addClass('fas fa-eye');
    }
}

function update_user() {
    cedula = $("#txtCedula_edit").val();
    if (cedula != "") {
        nombre = $("#txtNameUser-edit").val();
        cargo = $("#txtCargo-edit").val();
        departamento = $("#txtDepartamento-edit").val();
        if (nombre == '' || cargo == '' || departamento =='') {
            Swal.fire({
                icon: 'error',
                html: '<h4>No puede dejar los campos vacios</h4>',
            });
            if (nombre == '') $("#txtNameUser-edit").addClass("is-invalid");
            if (cargo == '') $("#txtCargo-edit").addClass("is-invalid");
            if (departamento == '') $("#txtDepartamento-edit").addClass("is-invalid");
        } else {
            $("#txtNameUser-edit").removeClass("is-invalid");
            $("#txtCargo-edit").removeClass("is-invalid");
            $("#txtDepartamento-edit").removeClass("is-invalid");
            mensaje = '' +
                '<h5><b>Esta editando un usuario:</b></h5>' +
                '<h6><label>Nombre :</label> ' + nombre + '</h6>' +
                '<h6><label>Cargo :</label> ' + cargo + '</h6>' +
                '<h6><label>Departamento:</label> ' +  departamento + '</h6>' +
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
                            Cedula: cedula,
                            Nombre: nombre,
                            Cargo: cargo,
                            Departamento: departamento
                        },
                        type: 'POST',
                        success: function (r) {
                            if (r == true) {
                                $("#btnCerrar").click();
                                get_table_user();
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
    } else {
        Swal.fire({
            icon: 'error',
            html: '<h4>ERROR-8872621</h4>',
        });
    }
}

function update_password() {
    cedula = $("#txtCedula_pass").val();
    if (cedula != "") {
        new_pass = $("#txtpassrestor").val();
        if (new_pass == '') {
            $("#txtpassrestor").addClass("is-invalid");
            Swal.fire({
                icon: 'error',
                html: '<h4>No puede dejar los campos vacios</h4>',
            });
        } else {
            $("#txtpassrestor").removeClass("is-invalid");
            mensaje = '' +
                '<h5><b>Esta seguro de restablecer la contraseña:</b></h5>' +
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
                        url: "operaciones.php?op=6",
                        data: {
                            Cedula: cedula,
                            Pass: new_pass
                        },
                        type: 'POST',
                        success: function (r) {
                            if (r == true) {
                                $("#btnCerrar").click();
                                get_table_user();
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
    } else {
        Swal.fire({
            icon: 'error',
            html: '<h4>ERROR-8872621</h4>',
        });
    }
}

function update_permisos() {
    $("#btn-action").prop('disabled', true);
    cedula = $("#txtCedula_permisos").val();
    if (cedula != "") {
        cantidad = $("#cant-permisos").val();
        if (cantidad != "") {
            arrayDatos = [];
            for (i = 1; i <= cantidad; i++) {
                estado = $("#chbst" + i).val();
                input = $("#chb" + i).val();
                select = 0;
                if ($("#chb" + i).prop('checked')) select = 1;
                else select = 0;
                if (select != estado) {
                    arrayDatos.push(new Array(input, estado, select));
                }
                // console.log("Input Valor: "+input+" Estado: "+estado+" Selecionado: "+select);
            }
            if (arrayDatos.length > 0) {
                mensaje_permisos = '';
                for (i = 0 ; i < arrayDatos.length; i++) {
                    value = arrayDatos[i][0];
                    if (arrayDatos[i][2]==1) mensaje_permisos += '<h6>Añadir <b>'+$("#lblch"+value).html() +'</b></h6>';
                    else mensaje_permisos += '<h6>Eliminar <b>'+$("#lblch"+value).html() +'</b></h6>';
                }
                mensaje = '' +
                    '<h5><b>Listado de permisos:</b></h5>' +mensaje_permisos
                    '';
                Swal.fire({
                    title: 'Verificación de la información',
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
                            url: "operaciones.php?op=8",
                            data: {
                                Cedula: cedula,
                                Array: arrayDatos
                            },
                            type: 'POST',
                            success: function (r) {
                                if (r == true) {
                                    $("#btnCerrar").click();
                                    get_table_user();
                                    Swal.fire({
                                        icon: 'success',
                                        html: '<h4>Datos Actualizados</h4>',
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        html: '<h4><b>Error: </b> ' + r + '</h4>',
                                    });
                                    $("#btn-action").prop('disabled', false);
                                }
                            }
                        });
                    }
                    $("#btn-action").prop('disabled', false);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    html: '<h4>Ningun Permiso para editar</h4>',
                });
                $("#btn-action").prop('disabled', false);
            }
        } else {
            Swal.fire({
                icon: 'error',
                html: '<h4>ERROR-86552</h4>',
            });
            $("#btn-action").prop('disabled', false);
        }
    } else {
        Swal.fire({
            icon: 'error',
            html: '<h4>ERROR-8872621</h4>',
        });
        $("#btn-action").prop('disabled', false);
    }
}

function get_data_new_user() {
    f_carga('modal-content');
    $.ajax({
        url: 'operaciones.php?op=9',
        success: function (r) {
            $('#modal-content').html(r);
        }
    });
}
function new_user() {
    cedula = $("#txtCedula").val();
    $("#txtCedula").removeClass("is-invalid");
    if (cedula != "") {
        nombre = $("#txtNameUser").val();
        cargo = $("#txtCargo").val();
        departamento = $("#txtDepartamento").val();
        if (nombre == '' || cargo == '' || departamento =='' ) {
            Swal.fire({
                icon: 'error',
                html: '<h4>No puede dejar los campos vacios</h4>',
            });
            if (nombre == '') $("#txtNameUser").addClass("is-invalid");
            if (cargo == '') $("#txtCargo").addClass("is-invalid");
            if (departamento == '') $("#txtDepartamento").addClass("is-invalid");
        } else {
            $("#txtNameUser").removeClass("is-invalid");
            $("#txtCargo").removeClass("is-invalid");
            $("#txtDepartamento").removeClass("is-invalid");
            mensaje = '' +
                '<h5><b>Esta agrenado un nuevo usuario:</b></h5>' +
                '<h6><label>Usuario :</label> ' + cedula + '</h6>' +
                '<h6><label>Nombre y Apellido :</label> ' + nombre + '</h6>' +
                '<h6><label>Cargo :</label> ' + cargo + '</h6>' +
                '<h6><label>Departamento:</label> ' +  departamento + '</h6>' +
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
                        url: "operaciones.php?op=10",
                        data: {
                            Cedula: cedula,
                            Nombre: nombre,
                            Cargo: cargo,
                            Departamento: departamento
                        },
                        type: 'POST',
                        success: function (r) {
                            if (r == true) {
                                $("#btnCerrar").click();
                                get_table_user();
                                Swal.fire({
                                    icon: 'success',
                                    html: '<h4>Datos Actualizados</h4>',
                                });
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
    } else {
        $("#txtCedula").addClass("is-invalid");
        Swal.fire({
            icon: 'error',
            html: '<h4>No puede dejar el campo Usuario vacío</h4>',
        });
    }
}
function update_user_cedula() {
    cedula = $("#txtCedula_user").val();
    if (cedula != "") {
        new_Cedula = $("#txtNewCedula").val();
        if (new_Cedula == '' ) {
            Swal.fire({
                icon: 'error',
                html: '<h4>No puede dejar los campos vacios</h4>',
            });
            if (new_Cedula == '') $("#txtNewCedula").addClass("is-invalid");
        } else {
            $("#txtNewCedula").removeClass("is-invalid");
            mensaje = '' +
                '<h5><b>Esta seguro de cambiar el usuario:</b></h5>' +
                '<h6><label>Nuevo Usuario :</label> ' + new_Cedula + '</h6>' +
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
                        url: "operaciones.php?op=12",
                        data: {
                            Id: cedula,
                            Cedula: new_Cedula,
                        },
                        type: 'POST',
                        success: function (r) {
                            if (r == true) {
                                $("#btnCerrar").click();
                                get_table_user();
                                Swal.fire({
                                    icon: 'success',
                                    html: '<h4>Datos Actualizados</h4>',
                                });
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
    } else {
        Swal.fire({
            icon: 'error',
            html: '<h4>ERROR-1212</h4>',
        });
    }
}