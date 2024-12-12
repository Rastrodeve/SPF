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
    f_carga_especies();
    get_data_table(0);
    $('.cont-carga').addClass('d-none');
});
function f_carga_especies() {
    $.ajax({
        url: 'operaciones.php?op=1',
        success: function (r) {
            $('#slcEspecie').html(r);
            $('#slcEspecie').select2();
        }
    });
}
function get_data_table(especie) {
    f_carga('cont-table-data');
    $.ajax({
        data:{Id :especie , Inicio: $('#txtIncio').val(), Final : $('#txtFinal').val() },
        type: 'POST',
        url: 'operaciones.php?op=2',
        success: function (r) {
            $('#cont-table-data').html(r);
            confi_tabla('table-data');
        }
    });
}

$("#bnt-consultar").click(function () {
    val = $('#slcEspecie').val();
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

