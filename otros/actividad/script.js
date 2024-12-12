$(document).ready(function () {
    $('#reservationdate').datetimepicker({
        format: 'DD/MM/Y'
    });
    $('#reservationdate2').datetimepicker({
        format: 'DD/MM/Y'
    });
    $('#txtIncio').inputmask('dd/mm/yyyy', {
        'placeholder': 'dd/mm/yyyy'
    });
    $('#txtFinal').inputmask('dd/mm/yyyy', {
        'placeholder': 'dd/mm/yyyy'
    });
    f_carga_select();
    get_data_table(0);
    $('.cont-carga').addClass('d-none');
});
function f_carga_select() {
    $.ajax({
        url: 'operaciones.php?op=1',
        success: function (r) {
            $('#slcUsuario').html(r);
            $('#slcUsuario').select2();
        }
    });
}
function get_data_table(id) {
    f_carga('cont-table-data');
    $.ajax({
        data:{Id :id , Inicio: $('#txtIncio').val(), Final : $('#txtFinal').val(), IpUsu : $('#txtIp').val(), Accion : $('#txtAccion').val(), Detalle : $('#txtDetalle').val(), Comentario : $('#txtComentario').val() },
        type: 'POST',
        url: 'operaciones.php?op=2',
        success: function (r) {
            $('#cont-table-data').html(r);
            confi_tabla('table-data');
        }
    });
}

$("#bnt-consultar").click(function () {
    val = $('#slcUsuario').val();
    get_data_table(val);
})

function get_data() {
    f_carga('contenedor');
    $.ajax({
        url: 'operaciones.php?op=1',
        success: function (r) {
            $('#contenedor').html(r);
            confi_tabla('tbl_table_emergente');
            confi_tabla('tbl_mis_decomisos');
            f_mensaje();
        }
    });
}

