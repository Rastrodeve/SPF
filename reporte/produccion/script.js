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
function get_data_table(especie,op) {
    f_carga('cont-table-data');
    $.ajax({
        data:{Id :especie , Inicio: $('#txtIncio').val(), Final : $('#txtFinal').val() },
        type: 'POST',
        url: 'operaciones.php?op='+op,
        success: function (r) {
            $('#cont-table-data').html(r);
            confi_tabla('table-data');
        }
    });
}

$("#bnt-consultar").click(function () {
    val = $('#slcEspecie').val();
    op =  $('input:radio[name=radioTipo]:checked').val();
    get_data_table(val,op);
})

function f_generar_pdf() {
    especie = $('#slcEspecie').val();
    op =  $('input:radio[name=radioTipo]:checked').val();
    generar_reporte(especie, (parseInt(op) + 4));
}


function generar_reporte(especie ,op) {
    $.ajax({
        url: 'operaciones.php?op='+op,
        data:{Id :especie , Inicio: $('#txtIncio').val(), Final : $('#txtFinal').val() },
        type: 'POST',
        success: function (r) {
            window.location.href = "../../PDF/index.php";
        }
    });
}
function Imprimir(id) {
    $.ajax({
        url: 'operaciones.php?op=10',
        async: false,
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            inicio = '<html><head><title>VISTA PREVIA</title><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta http-equiv="x-ua-compatible" content="ie=edge">';
            var mywindow = window.open("", "VISTA PREVIA", "height=500px,width=800px");
            css = "<style>@media print {@page { margin: 0;size: auto;}</style>";
            mywindow.document.write(inicio);
            mywindow.document.write(css);
            mywindow.document.write("</head><body >");
            mywindow.document.write(r);
            mywindow.document.write("</body></html>");
            fun = setInterval(function () {
                mywindow.document.close();
                mywindow.focus();
                mywindow.print();
                mywindow.close();
                clearInterval(fun);
            }, 300);
        }
    });
}


