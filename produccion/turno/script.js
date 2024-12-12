const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

$(document).ready(function () {
    $('.cont-carga').addClass('d-none');
});



$('input[name=customRadio]').change(function () {
    $.ajax({
        url: 'operaciones.php?op=1',
        data: {tipo: $(this).val()},
        type: 'POST',
        success: function (r) {
            $('#cont-busqueda').html(r);
        }
    });
})

function f_buscar_data(){
    $opcion = $('input:radio[name=customRadio]:checked').val()
    $valor = '';
    if ($opcion == 1 || $opcion == 2) $valor = $("#txtBuscartext").val();
    if ($opcion == 3) $valor = $("#slcClientes_search").val();
    if ($valor== '' || $valor == 0)  {
        Swal.fire({
            icon: 'error',
            html: '<h4><b>Error: </b> Complete la información</h4>',
        });
        return;
    }
    get_data_all($opcion, $valor);
}

function get_data_all(op,valor) {
    f_carga('cont-data');
    $.ajax({
        url: 'operaciones.php?op=2',
        data: {Opcion: op, Valor: valor},
        type: 'POST',
        success: function (r) {
            $('#cont-data').html(r);
            confi_tabla('table');
        }
    });
}






function f_registrar(estado,id,cont) {
    ht = "<h4>Confirmar el pago</h4>";
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
                url: "operaciones.php?op=3",
                data: {
                    Id: id, Estado:estado 
                },
                type: 'POST',
                success: function (r) {
                    console.log(r);
                    if (r == true) {
                        $("#td-"+cont).remove()
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
function get_data_orden_fecha() {
    id = $('#slcTipo').val();
    f_carga('cont-table-otras');
    $.ajax({
        url: 'operaciones.php?op=7',
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            $('#cont-table-otras').html(r);
            confi_tabla('tbl_orden_fecha');
        }
    });
}
function f_pagar_otras(id) {
    ht = "<h4>Confirmación de pago <br> Factura: <b>"+ $("#fac-"+id).html()+"</b><br>Orden:"+$("#ord-"+id).html()+"  <br>Cliente: <b>"+ $("#cli-"+id).html()+"</b></h4>";
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
                    Id: id
                },
                type: 'POST',
                success: function (r) {
                    if (r == true) {
                        get_data_orden_fecha();
                        Swal.fire({
                            icon: 'success',
                            html: '<h4>DATOS ACTUALIZADOS</h4>',
                        });
                    } else {
                        get_data_orden_fecha();
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
function get_data_turno(){
    id = $('#slcTipo').val();
    f_carga('custom-tabs-five-normal');
    $.ajax({
        url: 'operaciones.php?op=8',
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            $('#custom-tabs-five-normal').html(r);
            confi_tabla('tbl_orden_turno');
        }
    });
}
function get_data_urgencias(){
    id = $('#slcTipo').val();
    f_carga('custom-tabs-five-normal-2');
    $.ajax({
        url: 'operaciones.php?op=11',
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            $('#custom-tabs-five-normal-2').html(r);
            confi_tabla('tbl_orden_urgencias');
        }
    });
}
