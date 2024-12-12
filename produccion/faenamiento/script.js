const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

$(document).ready(function () {
    $('.cont-carga').addClass('d-none');
    get_data();
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

function  f_mensaje() {
    if ($('#slcTipo').val()==0) {
        $data = '<h1 class="text-muted text-center mt-5 mb-5" >'+
                    'SELECCIONE UNA ESPECIE PARA CONTINUAR'+
                '</h1>';
        $("#cont-result").html($data);
    }else{
        $data = '<h1 class="text-muted text-center mt-5 mb-5" >'+
                'SELECCIONAR <b>'+$('#slcTipo option[value="'+$('#slcTipo').val()+'"]').html()+'</b> PARA EL <b>FAENAMEINTO</b>'+
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
function f_regresar() {
    $.ajax({
        url: "operaciones.php?op=5",
        success: function (r) {
            get_data();
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
