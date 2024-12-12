$(document).ready(function () {
    $('.cont-carga').addClass('d-none');
    get_table();
});

function get_table() {
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
        mensaje = '<h6>Esta seguro que desea <b>ACTIVAR</b> el usuario</h6>';
    } else if (estado == 1) {
        mensaje = '<h6>Esta seguro que desea <b>DESACTIVAR</b> el usuario</h6>';
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
                        Swal.fire({
                            icon: 'success',
                            html: '<h4>Datos Actualizados</h4>',
                        });
                        get_table();
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

function get_data_user_edit(cedula) {
    f_carga('modal-content');
    $.ajax({
        url: 'operaciones.php?op=3',
        data: {
            Ruc: cedula
        },
        type: 'POST',
        success: function (r) {
            $('#modal-content').html(r);
        }
    });
}
function get_data_user_new() {
    f_carga('modal-content');
    $.ajax({
        url: 'operaciones.php?op=5',
        success: function (r) {
            $('#modal-content').html(r);
        }
    });
}
function get_data_number(id) {
    f_carga('modal-content');
    $.ajax({
        url: 'operaciones.php?op=7',
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            $('#modal-content').html(r);
        }
    });
}

function get_data_permisos(id) {
    f_carga('modal-content');
    $.ajax({
        url: 'operaciones.php?op=9',
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            $('#modal-content').html(r);
            $('#slcTipoReporte').select2();
        }
    });
}


function f_update_client() {
    numero = $("#txtNumero").val();
    if (numero != "") {
        nombre = $("#txtNameCliente").val();
        marca = $("#txtMarca").val();
        telefono = $("#txtTelefono").val();
        correo = $("#txtCorreo").val();
        contrato = $("#txtContrato").val();
        direcion = $("#txtDireccion").val();
        observacion = $("#txtObservacion").val();
        if (nombre == '' || marca == '' || telefono == '' || correo == '' || contrato == '' || direcion == '' ) {
            Swal.fire({
                icon: 'error',
                html: '<h4>No puede dejar los campos vacios</h4>',
            });
            if (nombre == '') $("#txtNameCliente").addClass("is-invalid");
            if (marca == '') $("#txtMarca").addClass("is-invalid");
            if (telefono == '') $("#txtTelefono").addClass("is-invalid");
            if (correo == '') $("#txtCorreo").addClass("is-invalid");
            if (contrato == '') $("#txtContrato").addClass("is-invalid");
            if (direcion == '') $("#txtDireccion").addClass("is-invalid");
        } else {
            if (nombre == '') $("#txtNameCliente").removeClass("is-invalid");
            if (marca == '') $("#txtMarca").removeClass("is-invalid");
            if (telefono == '') $("#txtTelefono").removeClass("is-invalid");
            if (correo == '') $("#txtCorreo").removeClass("is-invalid");
            if (contrato == '') $("#txtContrato").removeClass("is-invalid");
            if (direcion == '') $("#txtDireccion").removeClass("is-invalid");
            mensaje = '' +
                '<h5><b>Esta editando un cliente:</b></h5>' +
                '<h6><label>Nombre :</label> ' + nombre + '</h6>' +
                '<h6><label>Marca :</label> ' + marca + '</h6>' +
                '<h6><label>Telefono :</label> ' + telefono + '</h6>' +
                '<h6><label>Correo :</label> ' + correo + '</h6>' +
                '<h6><label>Contrato :</label> ' + contrato + '</h6>' +
                '<h6><label>Dirección:</label> ' + direcion + '</h6>' +
                '<h6><label>Observación:</label> ' + observacion + '</h6>' +
                
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
                            Id:numero,
                            Nombre: nombre,
                            Marca: marca,
                            Telefono: telefono,
                            Correo: correo,
                            Contrato: contrato,
                            Direcion: direcion,
                            Observa: observacion
                        },
                        type: 'POST',
                        success: function (r) {
                            if (r == true) {
                                $("#btnCerrar").click();
                                $('#modal-content').html("");
                                get_table();
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

function f_new_client() {
    numero = $("#txtNumero").val();
    if (numero != "") {
        $("#txtNumero").addClass("is-invalid");
        if (numero.length == 10 || numero.length == 13) {
            $("#txtNumero").removeClass("is-invalid");
            nombre = $("#txtNameCliente").val();
            marca = $("#txtMarca").val();
            telefono = $("#txtTelefono").val();
            correo = $("#txtCorreo").val();
            contrato = $("#txtContrato").val();
            direcion = $("#txtDireccion").val();
            observacion = $("#txtObservacion").val();
            if (nombre == '' || marca == '' || telefono == '' || correo == '' || contrato == '' || direcion == '' ) {
                Swal.fire({
                    icon: 'error',
                    html: '<h4>No puede dejar los campos vacios</h4>',
                });
                if (nombre == '') $("#txtNameCliente").addClass("is-invalid");
                else $("#txtNameCliente").removeClass("is-invalid");
                if (marca == '') $("#txtMarca").addClass("is-invalid");
                else $("#txtMarca").removeClass("is-invalid");
                if (telefono == '') $("#txtTelefono").addClass("is-invalid");
                else $("#txtTelefono").removeClass("is-invalid");
                if (correo == '') $("#txtCorreo").addClass("is-invalid");
                else $("#txtCorreo").removeClass("is-invalid");
                if (contrato == '') $("#txtContrato").addClass("is-invalid");
                else $("#txtContrato").removeClass("is-invalid");
                if (direcion == '') $("#txtDireccion").addClass("is-invalid");
                else $("#txtDireccion").removeClass("is-invalid");
            } else {
                $("#txtNameCliente").removeClass("is-invalid");
                $("#txtMarca").removeClass("is-invalid");
                $("#txtTelefono").removeClass("is-invalid");
                $("#txtCorreo").removeClass("is-invalid");
                $("#txtContrato").removeClass("is-invalid");
                $("#txtDireccion").removeClass("is-invalid");
                mensaje = '' +
                    '<h5><b>Esta agregando un cliente:</b></h5>' +
                    '<h6><label>Nombre :</label> ' + nombre + '</h6>' +
                    '<h6><label>Marca :</label> ' + marca + '</h6>' +
                    '<h6><label>Telefono :</label> ' + telefono + '</h6>' +
                    '<h6><label>Correo :</label> ' + correo + '</h6>' +
                    '<h6><label>Contrato :</label> ' + contrato + '</h6>' +
                    '<h6><label>Dirección:</label> ' + direcion + '</h6>' +
                    '<h6><label>Observación:</label> ' + observacion + '</h6>' +
                    
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
                            url: "operaciones.php?op=6",
                            data: {
                                Numero:numero,
                                Nombre: nombre,
                                Marca: marca,
                                Telefono: telefono,
                                Correo: correo,
                                Contrato: contrato,
                                Direcion: direcion,
                                Observa: observacion
                            },
                            type: 'POST',
                            success: function (r) {
                                if (r == true) {
                                    $("#btnCerrar").click();
                                    $('#modal-content').html("");
                                    get_table();
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
        }else{
            $("#txtNumero").addClass("is-invalid");
            Swal.fire({
                icon: 'error',
                html: '<h6>Número de identificación incorrecto<br> <b>Cédula</b> => 10<br> <b>RUC</b> => 13</h6>',
            });
        }
    } else {
        $("#txtNumero").addClass("is-invalid");
        Swal.fire({
            icon: 'error',
            html: '<h4>Debe ingresar un número de identificación para el nuevo cliente</h4>',
        });
    }
}
function f_number_client() {
    numero = $("#txtNumero").val();
    if (numero != "") {
        new_numero = $("#txtNumero_new").val();
        if (new_numero == '') {
            Swal.fire({
                icon: 'error',
                html: '<h4>Ingrese el nuevo número de identifiación</h4>',
            });
            $("#txtNumero_new").addClass("is-invalid");
        } else {
            $("#txtNumero_new").removeClass("is-invalid");
            mensaje = '' +
                '<h5>Esta seguro que desea cambiar el número de identificación a: <b>'+new_numero+'</b></h5>';
            Swal.fire({
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
                            Id:numero,
                            Numero: new_numero,
                        },
                        type: 'POST',
                        success: function (r) {
                            if (r == true) {
                                $("#btnCerrar").click();
                                $('#modal-content').html("");
                                get_table();
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

function f_update_permisos(especie,num) {
    arrayDatos = [];
    $desc='';
    for (i = 1; i <= num; i++){
        $estado = $("#inpEstado-"+especie+"-"+i).val();
        $new_estado = 0; 
        if( $("#inpChex-"+especie+"-"+i).prop('checked') ) $new_estado = 1;
        if ($estado != $new_estado){
            mensaje_ac="Desactivado";
            if ($new_estado==1)mensaje_ac="activado";
            arrayDatos.push(new Array($("#inpChex-"+especie+"-"+i).val(), $new_estado));
            $desc +='<h6>'+$("#tddes-"+especie+"-"+i).html()+' <b> '+mensaje_ac+'</b> </h6>';
        }
    }
    if (arrayDatos.length == 0){
        Swal.fire({
            icon: 'warning',
            html: '<h4>Ningun cambio detectado</h4>',
        });
    }else{
        console.log(arrayDatos);
        mensaje = '' +
        '<h6>Secuencia única para los productos de <b>'+$("#h1Especie-"+especie).html()+'</b></h6>'+$desc
        '';
        Swal.fire({
            title: 'Verificación de la información',
            html: mensaje,
            // icon: 'warning',
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
                    type: 'POST',
                    data: {
                        Id: $("#txtIdCliente").val(),
                        Array: arrayDatos
                    },
                    success: function (r) {
                        if (r == true) {
                            get_data_table_especie($("#txtIdCliente").val(),especie);
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

    // cedula = $("#txtCedula_permisos").val();
    // if (cedula != "") {
    //     cantidad = $("#cant-permisos").val();
    //     if (cantidad != "") {
    //         arrayDatos = [];
    //         for (i = 1; i <= cantidad; i++) {
    //             estado = $("#chbst" + i).val();
    //             input = $("#chb" + i).val();
    //             select = 0;
    //             if ($("#chb" + i).prop('checked')) select = 1;
    //             else select = 0;
    //             if (select != estado) {
    //                 arrayDatos.push(new Array(input, estado, select));
    //             }
    //             // console.log("Input Valor: "+input+" Estado: "+estado+" Selecionado: "+select);
    //         }
    //         if (arrayDatos.length > 0) {
    //             mensaje_permisos = '';
    //             for (i = 0 ; i < arrayDatos.length; i++) {
    //                 value = arrayDatos[i][0];
    //                 if (arrayDatos[i][2]==1) mensaje_permisos += '<h6>Añadir <b>'+$("#lblch"+value).html() +'</b></h6>';
    //                 else mensaje_permisos += '<h6>Eliminar <b>'+$("#lblch"+value).html() +'</b></h6>';
    //             }
    //             mensaje = '' +
    //                 '<h5><b>Listado de permisos:</b></h5>' +mensaje_permisos
    //                 '';
    //             Swal.fire({
    //                 title: 'Verificación de la información',
    //                 html: mensaje,
    //                 icon: 'warning',
    //                 showCancelButton: false,
    //                 showDenyButton: true,
    //                 confirmButtonColor: '#3085d6',
    //                 denyButtonColor: '#d33',
    //                 confirmButtonText: 'Si, Continuar',
    //                 denyButtonText: 'No, Cancelar',
    //             }).then((result) => {
    //                 if (result.isConfirmed) {
    //                     $.ajax({
    //                         url: "operaciones.php?op=8",
    //                         data: {
    //                             Cedula: cedula,
    //                             Array: arrayDatos
    //                         },
    //                         type: 'POST',
    //                         success: function (r) {
    //                             if (r == true) {
    //                                 $("#btnCerrar").click();
    //                                 get_table_user();
    //                                 Swal.fire({
    //                                     icon: 'success',
    //                                     html: '<h4>Datos Actualizados</h4>',
    //                                 });
    //                             } else {
    //                                 Swal.fire({
    //                                     icon: 'error',
    //                                     html: '<h4><b>Error: </b> ' + r + '</h4>',
    //                                 });
    //                             }
    //                         }
    //                     });
    //                 }
    //             });
    //         } else {
    //             Swal.fire({
    //                 icon: 'error',
    //                 html: '<h4>Ningun Permiso para editar</h4>',
    //             });
    //         }
    //     } else {
    //         Swal.fire({
    //             icon: 'error',
    //             html: '<h4>ERROR-86552</h4>',
    //         });
    //     }
    // } else {
    //     Swal.fire({
    //         icon: 'error',
    //         html: '<h4>ERROR-8872621</h4>',
    //     });
    // }
}
function get_data_table_especie(cliente,especie) {
    f_carga('contablees-'+especie);
    $.ajax({
        url: 'operaciones.php?op=11',
        data: {
            Especie: especie,
            Cliente: cliente
        },
        type: 'POST',
        success: function (r) {
            $('#contablees-'+especie).html(r);
        }
    });
}

function update_reporte() {
    estado = $("#slcTipoReporte").val();
    mensaje = "Error";
    if (estado == 0) {
        mensaje = '<h6>Reporte de tipo <b>Normal </b><br>Los reportes se generarán con el logo de RASTRO</h6>';
    } else if (estado == 1) {
        mensaje = '<h6>Reporte de tipo <b>Especial </b><br>Los reportes no se generarán con el logo de RASTRO</h6>';
    }else return;
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
                url: 'operaciones.php?op=13',
                data: {
                    Id: $("#txtIdCliente").val(),
                    Estado: estado
                },
                type: 'POST',
                success: function (r) {
                    if (r == true) {
                        get_data_estado_reporte($("#txtIdCliente").val());
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

function get_data_estado_reporte(cliente) {
    f_carga('cont-estado-reporte');
    $.ajax({
        url: 'operaciones.php?op=12',
        data: {
            Id: cliente
        },
        type: 'POST',
        success: function (r) {
            $('#cont-estado-reporte').html(r);
            $('#slcTipoReporte').select2();
        }
    });
}