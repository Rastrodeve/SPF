const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

$(document).ready(function () {
    $('.cont-carga').addClass('d-none');
    get_data();
    // $('#slcProducto').select2();
});
function f_restrincion(e) {
    var key = window.event ? e.which : e.keyCode;
    if (key < 48 || key > 57) {
        e.preventDefault();
    }
}

function get_data() {
    f_carga('contenedor');
    $.ajax({
        url: 'operaciones.php?op=1',
        success: function (r) {
            $('#contenedor').html(r);
            $('#slcTipo').select2();
            confi_tabla('tbl_table');
            confi_tabla('tbl_table_emergente');
            confi_tabla('tbl_mis_decomisos');
            f_mensaje();
        }
    });
}
function get_data_table() {
    f_carga('cont-table-fae');
    $.ajax({
        url: 'operaciones.php?op=3',
        success: function (r) {
            $('#cont-table-fae').html(r);
            confi_tabla('tbl_table');
        }
    });
}
function get_data_mis_decomisos() {
    f_carga('custom-tabs-five-overlay-dark-2');
    $.ajax({
        url: 'operaciones.php?op=22',
        success: function (r) {
            $('#custom-tabs-five-overlay-dark-2').html(r);
            confi_tabla('tbl_mis_decomisos');
        }
    });
}
function get_data_emergente() {
    f_carga('con-table-emer');
    $.ajax({
        url: 'operaciones.php?op=23',
        success: function (r) {
            $('#con-table-emer').html(r);
            confi_tabla('tbl_table_emergente');
        }
    });
}
function f_get_table_faenameinto() {
    $.ajax({
        url: "operaciones.php?op=2",
        data: {
            Id: $('#slcTipo').val()
        },
        type: 'POST',
        success: function (r) {
            get_data();
        }
    });
}
function f_cancelar_2() {
    $.ajax({
        url: "operaciones.php?op=18",
        success: function (r) {
            get_data();
        }
    });
}
function f_get_decomiso(op,id) {
    $.ajax({
        url: "operaciones.php?op=7",
        data: {
            Id: id,
            OPCION: op
        },
        type: 'POST',
        success: function (r) {
            get_data();
            $("#btn-cerrar").click();
        }
    });
}
function f_cancelar_1() {
    $.ajax({
        url: "operaciones.php?op=12",
        success: function (r) {
            get_data();
        }
    });
}


function get_data_procesar(id) {
    f_carga('modal-body');
    $.ajax({
        data: {
            Id: id
        },
        type: 'POST',
        url: 'operaciones.php?op=4',
        success: function (r) {
            $('#modal-body').html(r);
            $('.input_disablecopypaste').bind('paste', function (e) {
                e.preventDefault();
            });
        }
    });
}
function get_data_view_decomiso(id) {
    f_carga('modal-body');
    $.ajax({
        data: {
            Id: id
        },
        type: 'POST',
        url: 'operaciones.php?op=24',
        success: function (r) {
            $('#modal-body').html(r);
        }
    });
}

function f_nueva_enfermedad() {
    f_carga("modal-content");
    $.ajax({
        url: "operaciones.php?op=8",
        success: function (r) {
            $('#modal-content').html(r);
        }
    });
}
function f_nuevo_decomiso() {
    f_carga("modal-content");
    $.ajax({
        url: "operaciones.php?op=13",
        success: function (r) {
            $('#modal-content').html(r);
        }
    });
}
function f_table_enfermedad() {
    f_carga("cont-table-enfermedades");
    $.ajax({
        url: "operaciones.php?op=10",
        success: function (r) {
            $('#cont-table-enfermedades').html(r);
        }
    });
}
function f_table_decomisos() {
    f_carga("cont-table-decomisos");
    $.ajax({
        url: "operaciones.php?op=15",
        success: function (r) {
            $('#cont-table-decomisos').html(r);
        }
    });
}
function f_eliminar_enfermedad(id) {
    $.ajax({
        type : 'POST',
        data:{Id : id},
        url: "operaciones.php?op=11",
        success: function (r) {
            if (r == true) {
                f_table_enfermedad();
                Swal.fire({
                    icon: 'success',
                    html: '<h4>DATOS ACTUALIZADOS</h4>',
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
function f_eliminar_decomiso(id) {
    $.ajax({
        type : 'POST',
        data:{Id : id},
        url: "operaciones.php?op=16",
        success: function (r) {
            if (r == true) {
                f_table_enfermedad();
                f_table_decomisos();
                Swal.fire({
                    icon: 'success',
                    html: '<h4>DATOS ACTUALIZADOS</h4>',
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

function f_insert_decomisos() {
    var MyRows = $('#table-inset-decomisos').find('tbody').find('tr');
    if (MyRows.length==0) {
        Swal.fire({
            icon: 'error',
            html: '<h4>No se encontraron decomisos</h4>',
        });
        return;
    }else{
        ArrayDecomisos = [];
        for (var i = 0; i < MyRows.length; i++) {
            if ($(MyRows[i]).find('td:eq(0)').find('input').prop('checked')) {
                if ($(MyRows[i]).find('td:eq(4)').find('input').val() > 0 ) {
                    ArrayDecomisos.push([parseInt($(MyRows[i]).find('td:eq(0)').find('input').val()),parseInt($(MyRows[i]).find('td:eq(4)').find('input').val()),parseInt($(MyRows[i]).find('td:eq(5)').html())]);
                    $(MyRows[i]).find('td:eq(4)').find('input').removeClass("is-invalid");
                }else{
                    $(MyRows[i]).find('td:eq(4)').find('input').addClass("is-invalid");
                    Swal.fire({
                        icon: 'error',
                        html: '<h4><b>Complete la cantidad</b> </h4>',
                    });
                    return ;
                }
            }
        }
        if (ArrayDecomisos.length==0) {
            Swal.fire({
                icon: 'error',
                html: '<h4><b>No se ha seleccionado nigun decomiso</b> </h4>',
            });
        }else{
            $.ajax({
                url: "operaciones.php?op=14",
                data: {
                    ArrayDatos: ArrayDecomisos
                },
                type: 'POST',
                success: function (r) {
                    if (r == true) {
                        $("#btnCerrar").click();
                        f_table_decomisos();
                        f_table_enfermedad();
                        Swal.fire({
                            icon: 'success',
                            html: '<h4>DATOS ACTUALIZADOS</h4>',
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
    }
}


function f_insert_enfermedades() {
    arrayDatos = [];
    $Mensaje ="<h5></h5>";
    $cant = $("#inpCantidadSubproductos").val();
    for (i = 1; i <= $("#inpCantidadSubproductos").val(); i++) {
        $Subproducto = $("#inptIdSub-"+i).val();
        $cant2 = $("#inpCantidad-"+$Subproducto).val();
        $Mensaje +="<h5><b>"+$("#lbl-"+i).html()+"</b></h5>";
        for (j = 1; j <= $cant2; j++) {
            if( $("#chb-"+$Subproducto+"-"+j).prop('checked')) {
                cantidad = $("#txtCantidad-"+$Subproducto +"-"+j).val();
                if (cantidad == 0 || cantidad == 0) {
                    $("#txtCantidad-"+$Subproducto +"-"+j).addClass("is-invalid");
                    Swal.fire({
                        icon: 'error',
                        html: '<h4><b>Complete la cantidad</b> </h4>',
                    });
                    return;
                }else{
                    $("#txtCantidad-"+$Subproducto +"-"+j).removeClass("is-invalid");
                    idEnfer = $("#chb-"+$Subproducto+"-"+j).val();
                    titulo = $("label[for='chb-"+$Subproducto +"-"+j+"']" ).html();
                    arrayDatos.push([$Subproducto,idEnfer,cantidad]);
                }
            }else{
                // console.log("No seleccionado");
            }
        }
    }
    if (arrayDatos.length == 0) {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>No se ha seleccionado niguna enfermedad</b> </h4>',
        });
        return;
    }else{
        $.ajax({
            url: "operaciones.php?op=9",
            data: {
                ArrayDatos: arrayDatos
            },
            type: 'POST',
            success: function (r) {
                if (r == true) {
                    $("#btnCerrar").click();
                    f_table_enfermedad();
                    Swal.fire({
                        icon: 'success',
                        html: '<h4>DATOS ACTUALIZADOS</h4>',
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
}
function f_guardar_decomiso() {
    ht =    '<h5><b>ESTA INGRESANDO UN DECOMISO</b></h5>'+
            '<h6><b>Verique la información antes de continuar</b></h6>'+
            '';
    Swal.fire({
        icon: 'info',
        html: ht,
        showDenyButton: true,
        confirmButtonText: 'SI, DATOS VERIFICADOS',
        denyButtonText: 'NO, CANCELAR',
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            $.ajax({
                url: "operaciones.php?op=19",
                success: function (r) {
                    if (r == true) {
                        get_data();
                        Swal.fire({
                            icon: 'success',
                            html: '<h4>DATOS ACTUALIZADOS</h4>',
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
function f_guardar_decomiso_suprodcutos() {
    ht =    '<h5><b>ESTA INGRESANDO UN DECOMISO</b></h5>'+
            '<h6><b>Verique la información de las tablas, antes de continuar</b></h6>'+
            '';
    Swal.fire({
        icon: 'info',
        html: ht,
        showDenyButton: true,
        confirmButtonText: 'SI, DATOS VERIFICADOS',
        denyButtonText: 'NO, CANCELAR',
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            $.ajax({
                url: "operaciones.php?op=21",
                success: function (r) {
                    if (r == true) {
                        get_data();
                        Swal.fire({
                            icon: 'success',
                            html: '<h4>DATOS ACTUALIZADOS</h4>',
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
function  f_mensaje() {
    if ($('#slcTipo').val()==0) {
        $data = '<h1 class="text-muted text-center mt-5 mb-5" >'+
                    'SELECCIONE UNA ESPECIE PARA CONTINUAR'+
                '</h1>';
        $("#cont-result").html($data);
    }else{
        $data = '<h1 class="text-muted text-center mt-5 mb-5" >'+
                'SELECCIONAR <b>'+$('#slcTipo option[value="'+$('#slcTipo').val()+'"]').html()+'</b>'+
            '</h1>'+
            '<center>'+
                '<button class="btn btn-info btn-lg" onclick="f_get_table_faenameinto()">'+
                    '<b>CONTINUAR</b>'+
                    '<i class="fas fa-caret-right ml-3"></i>'+
                '</button>'+
            '</center>';
        $("#cont-result").html($data);
    }
}
function get_data_decomiso(id,op) {
    f_carga("cont-contenido");
    $.ajax({
        url: "operaciones.php?op="+op,
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            // $("#btn-cerrar").click();
            $("#cont-contenido").html(r);
        }
    });
}
function get_data_decomiso(id,op) {
    f_carga("cont-contenido");
    $.ajax({
        url: "operaciones.php?op="+op,
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            // $("#btn-cerrar").click();
            $("#cont-contenido").html(r);
        }
    });
}
function f_regresar() {
    $.ajax({
        url: "operaciones.php?op=5",
        success: function (r) {
            get_data();
        }
    });
}

function f_siguiente() {
    producto = $("#slcProducto").val();
    if (producto == 0){
        Swal.fire({
            icon: 'error',
            html: '<h4><b>Seleccione un producto</b></h4>',
        });
        return;
    }
    cantidad = $("#txtCantidad").val();
    if (cantidad == 0 || cantidad == '' ) {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>Ingresa la cantidad a decomisar</b></h4>',
        });
        return;
    }
    if (!$("#cbxHembra").prop('checked') && !$("#cbxMacho").prop('checked')  ) {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>Marca por lo menos uno  <b>MACHO</b> o <b>HEMBRA</b></b></h4>',
        });
        return;
    }
    causa = $("#txtCausa").val();
    if (causa== '') {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>Ingrese la causa del decomiso</b></h4>',
        });
        return;
    }

    genero =  $('input:radio[name=radio]:checked').val();
    hembra = "";
    macho = "";
    if (genero == 0) {
        hembra = "<b>Hembra</b>";
    }
    else if (genero == 1 ) {
        macho = "<b>Macho</b>";
    }else {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>ERRORR-1212</b></h4>',
        });
        return;
    }
    ht = "<h4><b>Confirmación de información</b></h4>"+
    "<h5>Producto: <b> "+ $("#slcProducto option[value='"+producto+"']").html()+ "</b> animales</h5>"+
    "<h5>Cantidad: <b>"+ cantidad+"</b></h5>"+
    "<h5>Para: "+hembra+" "+macho+"</h5>"+
    "<h5>Causa: <b>"+ causa+"</b></h5>"+

    "";

    Swal.fire({
        icon: 'info',
        html: ht,
        showDenyButton: true,
        confirmButtonText: 'SI, CONFIRMAR',
        denyButtonText: 'NO, CANCELAR',
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            array = [producto,cantidad,causa,genero];//Id del producto, Cantidad, Causa,(0 : hembra,1 : macho)
            $.ajax({
                url: "operaciones.php?op=17",
                data: {
                    arrayDatos: array 
                },
                type: 'POST',
                success: function (r) {
                    if (r == true) {
                        get_data();
                        Swal.fire({
                            icon: 'success',
                            html: '<h4>DATOS ACTUALIZADOS</h4>',
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

function f_proccesar(id) {
    cantidad = $("#txtCantidad").val();
    if (cantidad == ""){
        Swal.fire({
            icon: 'error',
            html: '<h4><b>INGRESE LA CANTIDAD A PROCESAR</b></h4>',
        });
        return;
    }
    ht = "<h4><b>Confirmación de registro de faenamiento</b></h4>"+
        "<h5>Esta seguro que desea procesar <b> "+ cantidad+ "</b> animales</h5>"+
        "<h5>Para  <b> "+ $("#spanCliente").html()+ "</b></h5>";
    Swal.fire({
        icon: 'info',
        html: ht,
        showDenyButton: true,
        confirmButtonText: 'SI, CONFIRMAR',
        denyButtonText: 'NO, CANCELAR',
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            $.ajax({
                url: "operaciones.php?op=6",
                data: {
                    Id: id,
                    Cantidad: cantidad
                },
                type: 'POST',
                success: function (r) {
                    if (r == true) {
                        get_data();
                        $("#btn-cerrar").click();
                        Swal.fire({
                            icon: 'success',
                            html: '<h4>DATOS ACTUALIZADOS</h4>',
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
function f_guardar_parcial() {
    producto = $("#slcProducto").val();
    if (producto == 0){
        Swal.fire({
            icon: 'error',
            html: '<h4><b>Seleccione un producto</b></h4>',
        });
        $("#slcProducto").addClass("is-invalid");
        return;
    }
    $("#slcProducto").removeClass("is-invalid");
    cantidad = $("#txtCantidad").val();
    if (cantidad == 0 || cantidad == '' ) {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>Ingresa la cantidad a decomisar</b></h4>',
        });
        $("#txtCantidad").addClass("is-invalid");
        return;
    }
    $("#txtCantidad").removeClass("is-invalid");
    if (!$("#cbxHembra").prop('checked') && !$("#cbxMacho").prop('checked')  ) {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>Marca por lo menos uno  <b>MACHO</b> o <b>HEMBRA</b></b></h4>',
        });
        return;
    }
    causa = $("#txtCausa").val();
    if (causa== '') {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>Ingrese la causa del decomiso parcial</b></h4>',
        });
        $("#txtCausa").addClass("is-invalid");
        return;
    }
    $("#txtCausa").removeClass("is-invalid");

    genero =  $('input:radio[name=radio]:checked').val();
    hembra = "";
    macho = "";
    if (genero == 0) {
        hembra = "<b>Hembra</b>";
    }
    else if (genero == 1 ) {
        macho = "<b>Macho</b>";
    }else {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>ERRORR-1212</b></h4>',
        });
        return;
    }
    ht = "<h4><b>Confirmación de información</b></h4>"+
    "<h5>Producto: <b> "+ $("#slcProducto option[value='"+producto+"']").html()+ "</b></h5>"+
    "<h5>Cantidad: <b>"+ cantidad+"</b></h5>"+
    "<h5>Para: "+hembra+" "+macho+"</h5>"+
    "<h5>Causa: <b>"+ causa+"</b></h5>"+

    "";

    Swal.fire({
        icon: 'info',
        html: ht,
        showDenyButton: true,
        confirmButtonText: 'SI, CONFIRMAR',
        denyButtonText: 'NO, CANCELAR',
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            array = [producto,cantidad,causa,genero];//Id del producto, Cantidad, Causa,(0 : hembra,1 : macho)
            $.ajax({
                url: "operaciones.php?op=20",
                data: {
                    arrayDatos: array 
                },
                type: 'POST',
                success: function (r) {
                    if (r == true) {
                        get_data();
                        Swal.fire({
                            icon: 'success',
                            html: '<h4>DATOS ACTUALIZADOS</h4>',
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

function Generar_Acta() {
    Swal.fire({
        title: '¿Esta seguró que desea continuar?',
        html: '<h6>El acta generada no podra ser modificada</h6>',
        showCancelButton: true,
        confirmButtonText: 'CONTINUAR',
        cancelButtonText: 'CANCELAR',
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            $.ajax({
                url: 'operaciones.php?op=25',
                success: function (r) {
                    if (r == true) {
                        window.location.href = "../../PDF/index.php";
                        get_data_mis_decomisos();
                    } else {
                        get_data_mis_decomisos();
                        Swal.fire({
                            icon: 'error',
                            html: '<h4>'+ r +'</h4>',
                        });
                    }
                }
            });
        }
    });
}