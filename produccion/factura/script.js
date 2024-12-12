const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

$(document).ready(function () {
    $('.cont-carga').addClass('d-none');
    get_data_seclect_especies()
});

function get_data_seclect_especies() {
    $.ajax({
        url: 'operaciones.php?op=1',
        success: function (r) {
            $('#slcTipo').html(r);
            $('#slcTipo').select2();
            get_data_all();
        }
    });
}

function get_data_all() {
    id = $('#slcTipo').val();
    f_carga('cont-data');
    $.ajax({
        url: 'operaciones.php?op=2',
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            $('#cont-data').html(r);
            $('#slcDataOrden').select2();
            $('#slcDataOrdenEmergente').select2();
            confi_tabla('tbl_orden_actual');
            confi_tabla('tbl_orden_fecha');
            confi_tabla('tbl_orden_turno');
            confi_tabla('tbl_orden_emergente');
            confi_tabla('tbl_orden_urgencias');
        }
    });
}
$("#slcDataOrden").change(function () {
    alert("select");
})
function get_data_orden() {
    get_data_orden_info();
    get_data_orden_table();
}
function get_data_orden_emer() {
    get_data_orden_info_emer()
    get_data_orden_table_emergente()
}

function get_data_orden_table() {
    id = $('#slcDataOrden').val();
    f_carga('cont-table-actual');
    $.ajax({
        url: 'operaciones.php?op=4',
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            $('#cont-table-actual').html(r);
            confi_tabla('tbl_orden_actual');
        }
    });
}
function get_data_orden_table_emergente() {
    id = $('#slcDataOrdenEmergente').val();
    f_carga('cont-table-emergente');
    $.ajax({
        url: 'operaciones.php?op=9',
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            $('#cont-table-emergente').html(r);
            confi_tabla('tbl_orden_emergente');
        }
    });
}

function get_data_orden_info() {
    id = $('#slcDataOrden').val();
    f_carga('cont-info-actual');
    $.ajax({
        url: 'operaciones.php?op=3',
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            $('#cont-info-actual').html(r);
        }
    });
}
function get_data_orden_info_emer() {
    id = $('#slcDataOrden').val();
    f_carga('cont-info-actual');
    $.ajax({
        url: 'operaciones.php?op=10',
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            $('#cont-info-actual').html(r);
        }
    });
}

function get_data_factura(id) {
    f_carga('cont-body');
    $.ajax({
        url: 'operaciones.php?op=5',
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            $('#cont-body').html(r);
        }
    });
}

function f_pagar(id) {
    ht = "<h4>Confirmación de pago <br> Factura: <b>"+ $("#fac-"+id).html()+"</b> <br>Cliente: <b>"+ $("#cli-"+id).html()+"</b></h4>";
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
                        Swal.fire({
                            icon: 'success',
                            html: '<h4>DATOS ACTUALIZADOS</h4>',
                        });
                        get_data_orden_table();
                        get_data_orden_table_emergente();
                    } else {
                        get_data_orden_table();
                        get_data_orden_table_emergente();
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
