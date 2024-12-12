const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});
const TamMB = 1;
$(document).ready(function () {
    f_menu(9);
    $('.cont-carga').addClass('d-none');
    // comprobar_data_excel();
});


$("#file_select").on("change", (e) => {
    const archivo = $(e.target)[0].files[0];
    let nombArchivo = archivo.name;
    let tamArchivo= archivo.size;
    var extension = nombArchivo.split(".").slice(-1);
    extension = extension[0];
    let extensiones = ["xlsx", "xls", "csv"];
    if (extensiones.indexOf(extension) === -1) {
        Swal.fire({
            icon: 'error',
            html: '<h4>Solo se permiten archivos Excel <br> <b>.xlsx - .xls - .csv  </b></h4>',
        });
        $(".swal2-confirm").focus();
        e.preventDefault();
    } else {
        Kb_tam = parseInt(tamArchivo / 1024);
        kb_per = 1024 * TamMB;
        if (Kb_tam > kb_per) {
            Swal.fire({
                icon: 'error',
                html: '<h4>El archivo no puede superar los <b>'+TamMB+' MB</b> </h4>',
            });
        }else{
            btn = '<button class="btn btn-info mt-3 float-right" onclick="update_file()" >'+
            '<b>SIGUIENTE <i class="fas fa-angle-right"></i></b>'+
            '</button>';
            mensaje = '<img src="../../Recursos/icon_excel.ico" alt="Icono Excel"'+
            'width="150px";>'+
            '<br>'+
            '<label >'+nombArchivo+'</label>'+
            '<br>'+
            '<span class="info-box-text text-center text-muted">'+
                'Presione un click para volver a selecionar un archivo'+
            '</span>';
            $("#cont-btn-siguiente").html(btn);
            $("#cont-vista-doc").html(mensaje);
        }
    }
});
function update_file() {
    const archivo = $("#file_select")[0].files[0];
    let nombArchivo = archivo.name;
    let tamArchivo= archivo.size;
    var extension = nombArchivo.split(".").slice(-1);
    extension = extension[0];
    let extensiones = ["xlsx", "xls", "csv"];
    if (extensiones.indexOf(extension) === -1) {
        Swal.fire({
            icon: 'error',
            html: '<h4>Solo se permiten archivos Excel <br> <b>.xlsx - .xls - .csv  </b></h4>',
        });
        $(".swal2-confirm").focus();
        e.preventDefault();
    } else {
        Kb_tam = parseInt(tamArchivo / 1024);
        kb_per = 1024 * TamMB;
        if (Kb_tam > kb_per) {
            Swal.fire({
                icon: 'error',
                html: '<h4>El archivo no puede superar los <b>'+TamMB+' MB</b> </h4>',
            });
        }else{
            $("#cont-selecionar").addClass("d-none");
            $("#cont-subir").removeClass("d-none");
            subir_archivo();
        }
    }
}
function subir_archivo(){
    let form = document.getElementById('cont-subir');
    let barra_estado = form.children[1].children[0],
                span = barra_estado.children[0],
        botom_cancelar = form.children[2].children[0];

        //peticion
        let peticion = new XMLHttpRequest();

        //progreso
        peticion.upload.addEventListener("progress", (event) => {
            let porcentaje = Math.round((event.loaded / event.total) * 100);

            console.log(porcentaje);

            barra_estado.style.width = porcentaje+'%';
            span.innerHTML=porcentaje+'%';
        });

        //finalizado
        peticion.addEventListener("load", () =>{
            barra_estado.classList.add('barra_verde');
            span.innerHTML = "Proceso completado";
            botom_cancelar.classList.add('d-none');
            // $("#cont-btns").removeClass("d-none");
            $("#cont-subir").addClass("d-none");
            read_data_excel();
        });

        //enviar datos
        form_su  = document.getElementById('form-file');
        peticion.open('post', 'operaciones.php?op=1');
        peticion.send(new FormData(form_su));

        //cancelar
        botom_cancelar.addEventListener("click", () =>{
            peticion.abort();
            barra_estado.classList.remove('barra_verde');
            barra_estado.classList.add('barra_roja');
            span.innerHTML="proceso cancelado";
        });

}


function read_data_excel() {
    $("#cont-datos-excel").removeClass("d-none");
    f_carga('cont-datos-excel');
    $.ajax({
        url: "operaciones.php?op=2",
        success: function (result) {
            $("#cont-datos-excel").html(result);
            confi_tabla('tbl_data_excel');
        }
    });
}

function comprobar_data_excel() {
    f_carga('cont-datos-excel');
    $.ajax({
        url: "operaciones.php?op=3",
        success: function (result) {
            if (result != false) {
                $("#cont-datos-excel").html(result);
                $("#cont-selecionar").addClass("d-none");
                $("#cont-datos-excel").removeClass("d-none");
                confi_tabla('tbl_data_excel');
            }else if (result == false){
                $("#cont-datos-excel").addClass("d-none");
                $("#cont-selecionar").removeClass("d-none");
            }
        }
    });
}
function regresar() {
    Swal.fire({
        title: '¿Esta seguró?',
        html: '<h6>Se perderá toda la informacion presente en la tabla</h6>',
        showCancelButton: true,
        confirmButtonText: 'SI, CONTINUAR',
        cancelButtonText: 'CANCELAR',
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            $.ajax({
                url: "operaciones.php?op=4",
                success: function (result) {
                    comprobar_data_excel();
                }
            });
        }
    })
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
        didClose: () => {
            $("#txtCoidgo").focus();
        }
    });
    $(".swal2-confirm").focus();
}

function editar(id) {
    f_carga('cont-modal-body');
    $.ajax({
        url: "operaciones.php?op=5",
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
    if (codigo == "" || peso == "" ) {
        cadena = 'Complete <b>TODOS</b> los campos';
        Mensaje_Error_2(cadena);
    } else if (isNaN(peso) == true) {
        cadena = 'Solo se permiten <b>números</b> para el campo <b>Peso</b>';
        Mensaje_Error_2(cadena);
    } else {
        id = $("#txtId_Editar").val();
        if ($("#txtCodigoActual").val() != codigo) {
            if ( comprobar_codigo_base(codigo) == true) {
                cadena = 'El código <b>' + codigo + '</b> <br>Esta ocupado por una animal que aun no ha sido procesado <b>(Faenado)</b>';
                Mensaje_Error_2(cadena);
            } else if (comprobar_codigo_temporal(codigo) == true) {
                cadena = 'El código <b>' + codigo + '</b> <br>Esta ocupado por otro animal en el detalle <b>Temporal</b>';
                Mensaje_Error_2(cadena);
            } else if (comprobar_codigo_temporal_excel(codigo,id) == true) {
                cadena = 'El código <b>' + codigo + '</b> <br>Esta ocupado por otro lado animal en una <b>Importacion</b>';
                Mensaje_Error_2(cadena);
            } else {
                if (id != "") {
                    Update_Registro(codigo, peso, id);
                } else {
                    Mensaje_Error_2("ERROR: REP-00002");
                }
            }
        } else {
            if (comprobar_codigo_base(codigo) == true) {
                cadena = 'El código <b>' + codigo + '</b> <br>Esta ocupado por una animal que aun no ha sido procesado <b>(Faenado)</b>';
                Mensaje_Error_2(cadena);
            }else if (comprobar_codigo_temporal_excel(codigo,id) == true) {
                cadena = 'El código <b>' + codigo + '</b> <br>Esta ocupado por otro lado animal en una <b>Importacion</b>';
                Mensaje_Error_2(cadena);
            }  else {
                if (id != "") {
                    Update_Registro(codigo, peso, id);
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

function Update_Registro(code, peso, id) {
    $.ajax({
        async: false,
        url: 'operaciones.php?op=6',
        data: {
            Codigo: code,
            Peso: peso,
            Id: id
        },
        type: 'POST',
        success: function (r) {
            if (r == true) {
                $("#btn-cerrar-edit").click();
                Toast.fire({
                    icon: 'success',
                    title: 'Registro actualizado'
                });
                comprobar_data_excel();
            } else {
                Mensaje_Error(r);
            }
        }
    });
}

function comprobar_codigo_base(codigo) {
    var resultado = "";
    $.ajax({
        async: false,
        url: "operaciones.php?op=7",
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
function comprobar_codigo_temporal(codigo) {
    var resultado = "";
    $.ajax({
        async: false,
        url: "operaciones.php?op=8",
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
function comprobar_codigo_temporal_excel(codigo,id) {
    var resultado = "";
    $.ajax({
        async: false,
        url: "operaciones.php?op=9",
        data: {
            Code: codigo,
            Id: id
        },
        type: 'POST',
        success: function (result) {
            resultado = result;
        }
    });
    return resultado;
}