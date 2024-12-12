const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

$(document).ready(function () {
    $('.cont-carga').addClass('d-none');
    GET_Guias();
});

function GET_Guias() {
    f_carga('contenedor-lista-guia');
    $.ajax({
        url: 'operaciones.php?op=1',
        success: function (r) {
            $('#contenedor-lista-guia').html(r);
            $("#h5-titulo").html("Selecciona una Guía");
            $("#btn-recargar").html("<b>RECARGAR</b>");
            confi_tabla('tbl_detalle_guia');
        }
    });
}

function Mostrar(id) {
    f_carga('contenedor-lista-guia');
    $.ajax({
        url: 'operaciones.php?op=2',
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            $('#contenedor-lista-guia').html(r);
            $("#h5-titulo").html("Detalle la Guía");
            $("#btn-recargar").html("<b>REGRESAR</b>");
            $("#txtCoidgo").focus();
            bloquear_botones();
        }
    });
}

function Ayadir_Temporal() {
    codigo = $('#txtCoidgo').val();
    peso = $('#txtPeso').val();
    if (codigo == "" || peso == "") {
        cadena = 'Complete <b>TODOS</b> los campos';
        Mensaje_Error(cadena);
    } else if (isNaN(peso) == true) {
        cadena = 'Solo se permiten <b>números</b> para el campo <b>Peso</b>';
        Mensaje_Error(cadena);
    } else {
        if (comprobar_codigo_base(codigo) == true) {
            cadena = 'El código <b>' + codigo + '</b> <br>Esta ocupado por una animal que aun no ha sido procesado <b>(Faenado)</b>';
            Mensaje_Error(cadena);
        } else if (comprobar_codigo_temporal(codigo) == true) {
            cadena = 'El código <b>' + codigo + '</b> <br>Esta ocupado por otro animal en el detalle <b>Temporal</b>';
            Mensaje_Error(cadena);
        } else {
            id = $("#inputId").val();
            result2 = comprobar_total(id);
            if (result2 == true) {
                Insert_Temporal(codigo, peso, id);
                $("#txtCoidgo").focus();
            } else if (result2 == false) {
                cadena = "No pruede ingresar una cantidad mayor a la requerida en la guía";
                Mensaje_Error(cadena);
            } else {
                Mensaje_Error(result2);
            }
        }
    }
}

function comprobar_codigo_base(codigo) {
    var resultado = "";
    $.ajax({
        async: false,
        url: "operaciones.php?op=3",
        data: {
            Code: codigo
        },
        type: 'POST',
        success: function (result) {
            resultado = result;
        }
    });
    return resultado;
}

function comprobar_total(id) {
    var resultado = "";
    $.ajax({
        async: false,
        url: "operaciones.php?op=7",
        data: {
            Id: id
        },
        type: 'POST',
        success: function (result) {
            resultado = result;
        }
    });
    return resultado;
}

function comprobar_codigo_temporal(codigo) {
    var resultado = "";
    $.ajax({
        async: false,
        url: "operaciones.php?op=4",
        data: {
            Code: codigo
        },
        type: 'POST',
        success: function (result) {
            resultado = result;
        }
    });
    return resultado;
}


function Insert_Temporal(code, peso, id) {
    $.ajax({
        async: false,
        url: 'operaciones.php?op=5',
        data: {
            Codigo: code,
            Peso: peso,
            Id_Contador: id
        },
        type: 'POST',
        success: function (r) {
            if (r == true) {
                Toast.fire({
                    icon: 'success',
                    title: 'Registro guardado'
                });
                $("#txtCoidgo").val("");
                $("#txtPeso").val("");
            } else {
                Mensaje_Error(r);
            }
            Cargar_Tabla(id);
            Cargar_Manual(id);
            Cargar_Restante(id);
            bloquear_botones();
        }
    });
}

function Cargar_Tabla(id) {
    f_carga('bd_tabla');
    $.ajax({
        url: "operaciones.php?op=6",
        data: {
            Id: id
        },
        type: 'POST',
        success: function (result) {
            $("#bd_tabla").html(result);
        }
    });
}

function bloquear_botones() {
    id = $("#inputId").val();
    result2 = comprobar_total(id);
    if (result2 == true) {
        $("#ayadir_tem").attr("disabled", false);
        $("#guardar_detalle").attr("disabled", true);
    } else if (result2 == false) {
        $("#ayadir_tem").attr("disabled", true);
        $("#guardar_detalle").attr("disabled", false);
    } else {
        $("#ayadir_tem").attr("disabled", true);
        $("#guardar_detalle").attr("disabled", true);
        console.log("Error: RECEP001 -" + result2);
    }
}

function Cargar_Manual(id) {
    f_carga('div_manual');
    $.ajax({
        url: "operaciones.php?op=8",
        data: {
            Id: id
        },
        type: 'POST',
        success: function (result) {
            $("#div_manual").html(result);
        }
    });
}

function Cargar_Restante(id) {
    f_carga('div_restante');
    $.ajax({
        url: "operaciones.php?op=9",
        data: {
            Id: id
        },
        type: 'POST',
        success: function (result) {
            $("#div_restante").html(result);
        }
    });
}

function Pesar() {
    max = 120;
    min = 70;
    peso = Math.random() * (max - min) + min;
    $("#txtPeso").val(peso.toFixed(2));
}

function Pesar_nuevo() {
    max = 120;
    min = 70;
    peso = Math.random() * (max - min) + min;
    $("#txtPesoNuevo").val(peso.toFixed(2));
}

function enter_pesar(e) {
    var key = window.Event ? e.which : e.keyCode;
    if (key == 13) {
        Pesar();
        $("#ayadir_tem").focus();
    }
}

function Mensaje_Error(cadena) {
    Swal.fire({
        icon: 'error',
        html: '<h4>' + cadena + '</h4>',
        didClose: () => {$("#txtCoidgo").focus()},
    });
    $(".swal2-confirm").focus();
}

function editar(id) {
    f_carga('cont-modal-body');
    $.ajax({
        url: "operaciones.php?op=10",
        data: {
            Id: id
        },
        type: 'POST',
        success: function (result) {
            if (result == false) {
                Swal.fire({
                    icon: 'error',
                    html: '<h4>ERROR: REP-0001</h4>',
                });
            } else {
                $("#cont-modal-body").html(result);
                $("#txtCodigoEditar").focus();
                $("#open-modal-edit").click();
            }
        }
    });

}
$("#btn-guardar-edit").click(function () {
    codigo = $('#txtCodigoEditar').val();
    peso = $('#txtPesoNuevo').val();
    razon = $('#txtMotivo').val();
    if (codigo == "" || peso == "" || razon == "") {
        cadena = 'Complete <b>TODOS</b> los campos';
        Mensaje_Error_2(cadena);
    } else if (isNaN(peso) == true) {
        cadena = 'Solo se permiten <b>números</b> para el campo <b>Peso</b>';
        Mensaje_Error_2(cadena);
    } else {
        if ($("#txtCodigoActual").val() != codigo) {
            if (comprobar_codigo_base(codigo) == true) {
                cadena = 'El código <b>' + codigo + '</b> <br>Esta ocupado por una animal que aun no ha sido procesado <b>(Faenado)</b>';
                Mensaje_Error_2(cadena);
            } else if (comprobar_codigo_temporal(codigo) == true) {
                cadena = 'El código <b>' + codigo + '</b> <br>Esta ocupado por otro animal en el detalle <b>Temporal</b>';
                Mensaje_Error_2(cadena);
            } else {
                id = $("#txtId_Editar").val();
                if (id != "") {
                    Update_Registro(codigo, peso, id, razon);
                } else {
                    Mensaje_Error_2("ERROR: REP-00002");
                }
            }
        } else {
            if (comprobar_codigo_base(codigo) == true) {
                cadena = 'El código <b>' + codigo + '</b> <br>Esta ocupado por una animal que aun no ha sido procesado <b>(Faenado)</b>';
                Mensaje_Error_2(cadena);
            } else {
                id = $("#txtId_Editar").val();
                if (id != "") {
                    Update_Registro(codigo, peso, id, razon);
                } else {
                    Mensaje_Error_2("ERROR: REP-00002");
                }
            }
        }
    }
});

function Mensaje_Error_2(cadena) {
    Swal.fire({
        icon: 'error',
        html: '<h4>' + cadena + '</h4>',
        didClose: () => {
            $("#txtCodigoEditar").focus();
        }
    });
    $(".swal2-confirm").focus();
}

function Update_Registro(code, peso, id, razon) {
    $.ajax({
        async: false,
        url: 'operaciones.php?op=11',
        data: {
            Codigo: code,
            Peso: peso,
            Id: id,
            Razon: razon
        },
        type: 'POST',
        success: function (r) {
            if (r == true) {
                Cargar_Fila_Code(id);
                Cargar_Fila_Peso(id);
                Cargar_Manual($("#inputId").val());
                Cargar_Restante($("#inputId").val());
                $("#btn-cerrar-edit").click();
                Toast.fire({
                    icon: 'success',
                    title: 'Registro actualizado'
                });

            } else {
                Mensaje_Error(r);
            }
        }
    });
}

function Cargar_Fila_Code(id) {
    f_carga("td-code-" + id);
    $.ajax({
        url: "operaciones.php?op=12",
        data: {
            Id: id
        },
        type: 'POST',
        success: function (result) {
            $("#td-code-" + id).html(result);
        }
    });
}

function Cargar_Fila_Peso(id) {
    f_carga("td-peso-" + id);
    $.ajax({
        url: "operaciones.php?op=13",
        data: {
            Id: id
        },
        type: 'POST',
        success: function (result) {
            $("#td-peso-" + id).html(result);
        }
    });
}

function Guardar_Detalle_completo() {
    Swal.fire({
        title: '¿Esta seguró que desea guardar?',
        html: '<h6>La información guardada no podrá ser modificada</h6>',
        showCancelButton: true,
        confirmButtonText: 'GUARDAR',
        cancelButtonText: 'CANCELAR',
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            f_guardar_detalle_completo();
        }
    })
}

function f_guardar_detalle_completo() {
    Swal.fire({
        title: 'Guardando...',
        html: 'Espere....',
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
            $.ajax({
                url: "operaciones.php?op=14",
                data: {
                    Id: $("#inputId").val()
                },
                type: 'POST',
                success: function (result) {
                    Swal.close();
                    if (result == true) {
                        GET_Guias();
                    }else{
                        Mensaje_Error_2(result);
                    }
                }
            });
        },
    })

}