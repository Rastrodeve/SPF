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
function get_data_procesar(id) {
    $.ajax({
        data: {
            Id: id
        },
        type: 'POST',
        url: 'operaciones.php?op=4',
        success: function (r) {
            get_data();
        }
    });
}
function f_get_data_trans() {
    f_carga('cont-transpo');
    $.ajax({
        data: {
            Id: $("#txtRuc").val(),
        },
        type: 'POST',
        url: 'operaciones.php?op=6',
        success: function (r) {
            $("#cont-transpo").html(r);
        }
    });
}
function regresar() {
    $.ajax({
        url: 'operaciones.php?op=5',
        success: function (r) {
            get_data();
        }
    });
}
function regresar_2() {
    $.ajax({
        url: 'operaciones.php?op=8',
        success: function (r) {
            get_data();
        }
    });
}
function regresar_3() {
    $.ajax({
        url: 'operaciones.php?op=18',
        success: function (r) {
            get_data();
        }
    });
}
/////////////////////////////////////
function f_continuar_guia(){
    provincia = $("#txtProvincia").val()
    if(provincia == '' || provincia == 0){
        Swal.fire({
            icon: 'error',
            html: '<h4><b>SELECCIONE UNA PROVINCIA</b></h4>',
        });
        $("#txtProvincia").addClass("is-invalid");
        return;
    }
    $("#txtProvincia").removeClass("is-invalid");
    canton = $("#txtCanton").val()
    if(canton == '' || canton == 0){
        Swal.fire({
            icon: 'error',
            html: '<h4><b>SELECCIONE UN CANTON</b></h4>',
        });
        $("#txtCanton").addClass("is-invalid");
        return;
    }
    $("#txtCanton").removeClass("is-invalid");
    parroquia = $("#txtParroquia").val()
    if(parroquia == '' || parroquia == 0 ){
        Swal.fire({
            icon: 'error',
            html: '<h4><b>SELECCIONE UNA PARROQUIA</b></h4>',
        });
        $("#txtParroquia").addClass("is-invalid");
        return;
    }
    $("#txtParroquia").removeClass("is-invalid");

    direccion = $("#txtDireccion").val();
    if(direccion == ''){
        Swal.fire({
            icon: 'error',
            html: '<h4><b>INGRESE UNA DIRECCIÓN</b></h4>',
        });
        $("#txtDireccion").addClass("is-invalid");
        return;
    }
    $("#txtDireccion").removeClass("is-invalid");

    
    placa = $('#slcPlaca').val()
    if (placa == undefined) {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>INGRESE UN TRANSPORTISTA</b></h4>',
        });
        return;
    }
    if(placa == '' || placa == 0 ){
        Swal.fire({
            icon: 'error',
            html: '<h4><b>SELECCIONE UN TRANSPORTE</b></h4>',
        });
        return;
    }
    obsrvaciones = $("#txtObservaciones").val()
    $.ajax({
        url: "operaciones.php?op=7",
        data: {
            ArrayDatos: [parroquia,direccion,placa,obsrvaciones,$("#slcProductoMovilizar").val()]
        },
        type: 'POST',
        success: function (r) {
            get_data();
        }
    });

}
function f_nuevo_prdocuto() {
    var MyRows = $('#cont-table-subproductos').find('tbody').find('tr');
    // var MyRows = $('#cont-table-prodcutos').find('tbody').find('tr');
    if (MyRows.length > 0) {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>LA TABLA DE LOS SUBPRODUCTOS DEBE ESTAR VACÍA PARA CONTINUAR</b></h4>',
        });
        return;
    }
    $("#modal2").modal("show");
    f_carga('modal-content');
    $.ajax({
        url: 'operaciones.php?op=9',
        success: function (r) {
            $('#modal-content').html(r);
        }
    });
}
function f_completar(id) {
    var MyRows = $('#table-'+id).find('tbody').find('tr');
    for (var i = 0; i < MyRows.length; i++) {
        if ($("#chb-"+id).is(':checked')) {
            if ($(MyRows[i]).find('td:eq(2)').find('input').prop('checked'))$("#chb-"+id+'-'+(i+1)).click();
        }else{
            if (!$(MyRows[i]).find('td:eq(2)').find('input').prop('checked'))$("#chb-"+id+'-'+(i+1)).click();
        }
    }
}

function f_comprobar_todos(id,con) {
    varibale = f_comprobar_todos_marcados(id);
    if (varibale == 0 && $("#chb-"+id+'-'+con).prop('checked')) {
        if ($("#chb-"+id).is(':checked')) $("#chb-"+id).click();
    }else{
        if (varibale == 1 && !$("#chb-"+id+'-'+con).prop('checked')) $("#chb-"+id).click();
        // if (!$("#chb-"+id).is(':checked')) ;
    }
    // 
}
function f_comprobar_todos_marcados(id) {
    var MyRows = $('#table-'+id).find('tbody').find('tr');
    cont = 0
    for (var i = 0; i < MyRows.length; i++) {
        if (!$(MyRows[i]).find('td:eq(2)').find('input').prop('checked'))cont++;
    }
    return cont;
}
function f_guardar_productos(){
    cant = $("#inptCantidad").val();
    if(cant < 1){
        Swal.fire({
            icon: 'error',
            html: '<h4><b>NO HAY DATOS A PROCESAR</b></h4>',
        });
        return ;
    }
    ArrayDatos = []
    for (let index = 1; index <= cant; index++) {
        Orden = $("#inptIdPro-"+index).val()
        var MyRows = $('#table-'+Orden).find('tbody').find('tr');
        for (var i = 0; i < MyRows.length; i++) {
            if ($(MyRows[i]).find('td:eq(2)').find('input').prop('checked')) ArrayDatos.push($(MyRows[i]).find('td:eq(2)').find('input').val());
            
        }
    }
    if (ArrayDatos.length == 0) {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>NIGUN DATO SELECCIONADO</b></h4>',
        });
        return ;
    }
    $.ajax({
        url: "operaciones.php?op=10",
        data: {
            ArrayDatos: ArrayDatos
        },
        type: 'POST',
        success: function (r) {
            $("#btnCerrar").click();
            f_table_productos();
        }
    });
}
function f_table_productos() {
    f_carga("cont-table-prodcutos");
    $.ajax({
        url: "operaciones.php?op=11",
        success: function (r) {
            $('#cont-table-prodcutos').html(r);
        }
    });
}
function f_eliminar_producto(id) {
    $.ajax({
        type : 'POST',
        data:{Id : id},
        url: "operaciones.php?op=12",
        success: function (r) {
            if (r == true) {
                f_table_productos();
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
function f_nuevo_subprdocuto() {
    var MyRows = $('#cont-table-prodcutos').find('tbody').find('tr');
    if (MyRows.length > 0) {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>LA TABLA DE LOS RODUCTOS DEBE ESTAR VACÍA PARA CONTINUAR</b></h4>',
        });
        return;
    }
    $("#modal2").modal("show");
    f_carga('modal-content');
    $.ajax({
        url: 'operaciones.php?op=13',
        success: function (r) {
            $('#modal-content').html(r);
        }
    });
}
function f_completar_2(id) {
    var MyRows = $('#table-'+id).find('tbody').find('tr');
    for (var i = 0; i < MyRows.length; i++) {

        if ($("#chb-"+id).is(':checked')) {
            if ($(MyRows[i]).find('td:eq(4)').find('input').prop('checked'))$("#chb-"+id+'-'+(i+1)).click();
        }else{
            if (!$(MyRows[i]).find('td:eq(4)').find('input').prop('checked'))$("#chb-"+id+'-'+(i+1)).click();
        }
    }
}
function f_comprobar_todos_2(id,con) {
    varibale = f_comprobar_todos_marcados_2(id);
    if (varibale == 0 && $("#chb-"+id+'-'+con).prop('checked')) {
        if ($("#chb-"+id).is(':checked')) $("#chb-"+id).click();
    }else{
        if (varibale == 1 && !$("#chb-"+id+'-'+con).prop('checked')) $("#chb-"+id).click();
    }
}
function f_comprobar_todos_marcados_2(id) {
    var MyRows = $('#table-'+id).find('tbody').find('tr');
    cont = 0
    for (var i = 0; i < MyRows.length; i++) {
        if (!$(MyRows[i]).find('td:eq(4)').find('input').prop('checked'))cont++;
    }
    return cont;
}
function f_guardar_subproductos(){
    cant = $("#inptCantidad").val();
    if(cant < 1){
        Swal.fire({
            icon: 'error',
            html: '<h4><b>NO HAY DATOS A PROCESAR</b></h4>',
        });
        return ;
    }
    ArrayDatos = []
    for (let index = 1; index <= cant; index++) {
        Orden = $("#inptIdPro-"+index).val()
        var MyRows = $('#table-'+Orden).find('tbody').find('tr');
        for (var i = 0; i < MyRows.length; i++) {
            if ($(MyRows[i]).find('td:eq(4)').find('input').prop('checked')){
                cantidad = $(MyRows[i]).find('td:eq(3)').find('input').val();
                if (cantidad == '') {
                    $(MyRows[i]).find('td:eq(3)').find('input').addClass('is-invalid');
                }else{
                    if (cantidad > parseInt($(MyRows[i]).find('td:eq(2)').html())) {
                        $(MyRows[i]).find('td:eq(3)').find('input').addClass('is-invalid');
                        Swal.fire({
                            icon: 'error',
                            html: '<h4><b>LA CANTIDAD INGRESADA NO CORRESPONDE CON EL SALDO</b></h4>',
                        });
                        return;
                    }else{
                        if (cantidad ==0) {
                            $(MyRows[i]).find('td:eq(3)').find('input').addClass('is-invalid');
                            Swal.fire({
                                icon: 'error',
                                html: '<h4><b>LA CANTIDAD INGRESADA NO PUEDE SER 0</b></h4>',
                            });
                            return;
                        }else{
                            $(MyRows[i]).find('td:eq(3)').find('input').removeClass('is-invalid');
                            ArrayDatos.push([$(MyRows[i]).find('td:eq(4)').find('input').val(),cantidad]);
                        }
                    }
                }
            }
            
        }
    }
    if (ArrayDatos.length == 0) {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>NINGUN DATO SELECCIONADO</b></h4>',
        });
        return ;
    }
    $.ajax({
        url: "operaciones.php?op=14",
        data: {
            ArrayDatos: ArrayDatos
        },
        type: 'POST',
        success: function (r) {
            $("#btnCerrar").click();
            f_table_subproductos();
        }
    });
}
function f_table_subproductos() {
    f_carga("cont-table-subproductos");
    $.ajax({
        url: "operaciones.php?op=15",
        success: function (r) {
            $('#cont-table-subproductos').html(r);
        }
    });
}
function f_eliminar_subproducto(id) {
    $.ajax({
        type : 'POST',
        data:{Id : id},
        url: "operaciones.php?op=16",
        success: function (r) {
            if (r == true) {
                f_table_subproductos();
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
function f_guardar_guia() {
    producto = $('#table-pro').find('tbody').find('tr');
    subproducto = $('#table-subp').find('tbody').find('tr');
    if (producto.length == 0 && subproducto.length == 0 ) {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>INGRESE POR LO MENOS UN PRODUCTO O SUBPRODUCTO</b></h4>',
        });
        return;
    }
    ht =    '<h5><b>ESTA INGRESANDO UNA NUEVA GUÍA DE ORIGEN</b></h5>'+
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
                url: "operaciones.php?op=17",
                success: function (r) {
                    if (r == true) {
                        window.location.href = "../../PDF/index.php";
                        get_data();
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
///////////////////////////////

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

function select_canton() {
    $('#txtCanton').html('Cargando...');
    $.ajax({
        data:{Id: $("#txtProvincia").val()},
        type:'POST',
        url: 'operaciones.php?op=19',
        success: function (r) {
            $('#txtCanton').html(r);
        }
    });
}
function select_parroquia() {
    $('#txtParroquia').html('Cargando...');
    $.ajax({
        data:{Id: $("#txtCanton").val()},
        type:'POST',
        url: 'operaciones.php?op=20',
        success: function (r) {
            $('#txtParroquia').html(r);
        }
    });
}

function Cantones(provincia) {
	var ARRAY_PRO = [];
	if (provincia=="AZUAY") {
		ARRAY_PRO = ["CAMILO PONCE ENRIQUEZ","CHORDELEG","CUENCA","EL PAN","GIRON",
		"GUACHAPALA","GUALACEO","NABON","ONA","PAUTE","PUCARA","SAN FERNANDO",
		"SANTA ISABEL","SEVILLA DE ORO","SIGSIG"];

	}else if (provincia=="BOLIVAR") {
		ARRAY_PRO=["CALUMA","CHILLANES","CHIMBO","ECHEANDIA",
		"GUARANDA","LAS NAVES","SAN MIGUEL"];

	}else if (provincia=="CANAR") {
		ARRAY_PRO = ["AZOGUES","BIBLIAN","CANAR","DELEG",
		"EL TAMBO","LA TRONCAL","SUSCAL"];

	}else if (provincia=="CARCHI") {
		ARRAY_PRO =["BOLIVAR","ESPEJO","MIRA","MONTUFAR",
		"SAN PEDRO DE HUACA","TULCAN"];

	}else if (provincia=="COTOPAXI") {
		ARRAY_PRO = ["LA MANA","LATACUNGA","PANGUA",
		"PUJILI","SALCEDO","SAQUISILI","SIGCHOS"];

	}else if (provincia=="CHIMBORAZO") {
		ARRAY_PRO =["ALAUSI","CHAMBO","CHUNCHI","COLTA",
		"CUMANDA","GUAMOTE","GUANO","PALLATANGA","PENIPE","RIOBAMBA"];

	}else if (provincia=="EL ORO") {
		ARRAY_PRO =["ARENILLAS","ATAHUALPA","BALSAS","CHILLA",
		"EL GUABO","HUAQUILLAS","LAS LAJAS","MACHALA","MARCABELI",
		"PASAJE","PINAS","PORTOVELO","SANTA ROSA","ZARUMA"];

	}else if (provincia=="ESMERALDAS") {
		ARRAY_PRO = ["ATACAMES","ELOY ALFARO","ESMERALDAS",
		"MUISNE","QUININDE","RIOVERDE","SAN LORENZO"];

	}else if (provincia=="GUAYAS") {
		ARRAY_PRO  = ["ALFREDO BAQUERIZO MORENO","BALAO","BALZAR",
		"COLIMES","CORONEL MARCELINO MARIDUEÑA","DAULE","DURAN",
		"EL EMPALME","EL TRIUNFO","GENERAL ANTONIO ELIZALDE","GUAYAQUIL",
		"ISIDRO AYORA","LOMAS DE SARGENTILLO","MILAGRO","NARANJAL","NARANJITO",
		"NOBOL","PALESTINA","PEDRO CARBO","PLAYAS","SALITRE","SAMBORONDON",
		"SAN JACINTO DE YAGUACHI","SANTA LUCIA","SIMON BOLIVAR"];

	}else if (provincia=="IMBABURA") {
		ARRAY_PRO = ["ANTONIO ANTE","CANTON COTACACHI",
		"CANTON OTAVALO","CANTON PIMAMPIRO","CANTON SAN MIGUEL DE URCUQUI",
		"IBARRA"];

	}else if (provincia=="LOJA") {
		ARRAY_PRO  = ["CALVAS","CATAMAYO","CELICA","CHAGUARPAMBA",
		"ESPINDOLA","GONZANAMA","LOJA","MACARA","OLMEDO","PALTAS",
		"PINDAL","PUYANGO","QUILANGA","SARAGURO","SOZORANGA","ZAPOTILLO"];

	}else if (provincia=="LOS RIOS") {
		ARRAY_PRO = ["BABA","BABAHOYO","BUENA FE","MOCACHE",
		"MONTALVO","PALENQUE","PUEBLOVIEJO","QUEVEDO","QUINSALOMA",
		"URDANETA","VALENCIA","VENTANAS","VINCES"];

	}else if (provincia=="MANABI") {
		ARRAY_PRO = ["24 DE MAYO","BOLIVAR","CHONE","EL CARMEN",
		"FLAVIO ALFARO","JAMA","JARAMIJO","JIPIJAPA","JUNIN","MANTA",
		"MONTECRISTI","OLMEDO","PAJAN","PEDERNALES","PICHINCHA",
		"PORTOVIEJO","PUERTO LOPEZ","ROCAFUERTE","SAN VICENTE","SANTA ANA",
		"SUCRE","TOSAGUA"];

	}else if (provincia=="MORONA SANTIAGO") {
		ARRAY_PRO  =["GUALAQUIZA","HUAMBOYA","LIMON INDANZA",
		"LOGRONO","MORONA","PABLO SEXTO","PALORA","SAN JUAN BOSCO",
		"SANTIAGO","SUCUA","TAISHA","TIWINTZA"];

	}else if (provincia=="NAPO") {
		ARRAY_PRO = ["ARCHIDONA","CARLOS JULIO AROSEMENA TOLA",
		"EL CHACO","QUIJOS","TENA"];

	}else if (provincia=="PASTAZA") {
		ARRAY_PRO =["ARAJUNO","MERA","PASTAZA","SANTA CLARA"];

	}else if (provincia=="PICHINCHA") {
		ARRAY_PRO  = ["CAYAMBE","MEJIA","PEDRO MONCAYO",
		"PEDRO VICENTE MALDONADO","PUERTO QUITO",
		"QUITO","RUMINAHUI","SAN MIGUEL DE LOS BANCOS"];

	}else if (provincia=="TUNGURAHUA") {
		ARRAY_PRO =["AMBATO","BANOS DE AGUA SANTA","CEVALLOS",
		"MOCHA","PATATE","QUERO","SAN PEDRO DE PELILEO",
		"SANTIAGO DE PILLARO","TISALEO"];

	}else if (provincia=="ZAMORA CHINCHIPE") {
		ARRAY_PRO =["CENTINELA DEL CONDOR","CHINCHIPE","EL PANGUI",
		"NANGARITZA","PALANDA","PAQUISHA","YACUAMBI","YANTZAZA","ZAMORA"];

	}else if (provincia=="GALAPAGOS") {
		ARRAY_PRO =["ISABELA","SAN CRISTOBAL","SANTA CRUZ"];

	}else if (provincia=="SUCUMBIOS") {
		ARRAY_PRO =["CASCALES","CUYABENO","GONZALO PIZARRO",
		"LAGO AGRIO","PUTUMAYO","SHUSHUFINDI","SUCUMBIOS"];

	}else if (provincia=="ORELLANA") {
		ARRAY_PRO = ["AGUARICO","LA JOYA DE LOS SACHAS","LORETO","ORELLANA"];

	}else if (provincia=="SANTA ELENA") {
		ARRAY_PRO  =["LA LIBERTAD","SALINAS","SANTA ELENA"];

	}else if (provincia=="SANTO DOMINGO DE LOS TSACHILAS") {
		ARRAY_PRO =["LA CONCORDIA","SANTO DOMINGO"];
	}

	return ARRAY_PRO;
}