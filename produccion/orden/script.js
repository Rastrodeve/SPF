$(document).ready(function () {
    $('.cont-carga').addClass('d-none');
    Listar_Especies();
});

function Listar_Especies() {
    $.ajax({
        url: 'operaciones.php?op=1',
        success: function (r) {
            $("#slcTipo").html(r);
            $('#slcTipo').select2();
            tipo = $("#slcTipo").val();
            Vista_Previa_Orden(tipo);
        }
    });
}
function recargar_vista_previa(){
    tipo = $("#slcTipo").val();
    Vista_Previa_Orden(tipo);
}
function Vista_Previa_Orden(tipo) {
    f_carga('cont-view');
    $.ajax({
        url: 'operaciones.php?op=2',
        data: {
            Tipo: tipo
        },
        type: 'POST',
        success: function (r) {
            $('#cont-view').html(r);
            confi_tabla('tbl_view_orden');
        }
    });
}
$("#slcTipo").change(function () {
    Tipo = $("#slcTipo").val();
    Vista_Previa_Orden(Tipo);
});

$('.toastsDefaultInfo').click(function () {
    tipo = $("#slcTipo").val();
    Namen = $("#slcTipo option[value=" + tipo + "]").html();
    $.ajax({
        url: 'operaciones.php?op=3',
        data: {
            Tipo: tipo
        },
        type: 'POST',
        success: function (r) {
            $(document).Toasts('create', {
                title: 'TAZAS ' + Namen,
                body: r
            });
        }
    });
});

function View_Informacion(op,id) {
    newop = op + 4;
    view_data(id,newop);
    if (op==1)$("#exampleModalLabel").html("<b>INFORMACIÓN</b>");
    if (op==2)$("#exampleModalLabel").html("<b>LISTADO DE ANIMALES</b>");
	$("#btn-Abrir").click();
}

function view_corraleje(dato1,dato2,dato3,dato4,dato5,dato6,dato7) {
    $("#exampleModalLabel").html("<b>DATOS CORRALAJE</b>");
		enviar = '<h3><b></b></h3>'+
		'<table class="table "><tr>'+
				'<th><h6>Tiempo de Estancia</h6></th>'+
				'<td class="text-right"><h6>'+dato1+'</h6></td>'+
			'</tr><tr>'+
			'	<th><h6>Tiempo sin taza de corralaje</h6></th>'+
			'	<td class="text-right"><h6>'+dato2+'</h6></td>'+
			'</tr><tr>'+
			'	<th><h6>Tiempo a cobrar</h6></th>'+
			'	<td class="text-right"><h6>'+dato3+'</h6></td>'+
			'</tr><tr>'+
			'	<th><h6>Taza por día o fracción de día</h6></th>'+
			'	<td class="text-right"><h6>$ '+dato4+'</h6></td>'+
			'</tr><tr>'+
				'<th><h6>SUBTOTAL:</h6></th>'+
			'	<td class="text-right"><h6>$ '+dato5+'</h6></td>'+
			'</tr><tr>'+
				'<th><h6>CANTIDAD:</h6></th>'+
			'	<td class="text-right"><h6>'+dato7+'</h6></td>'+
			'</tr><tr>'+
			'<th><h6><b>TOTAL:</b></h6></th>'+
		'	<td class="text-right"><h6><b>$ '+dato6+'</b></h6></td>'+
			'</tr></table>';
        $('#cont-modal-body').html(enviar);
        $("#btn-Abrir").click();
}
function view_servicios(dato1,dato2,dato3,dato4) {
    $("#exampleModalLabel").html("<b>CALCULO REALIZADO</b>");
    enviar = '<h3><b>S. '+dato4+'</b></h3>'+
    '<table class="table "><tr>'+
            '<th><h4>Precio Unitario:</h4></th>'+
            '<td class="text-right"><h4>$ '+dato1+'</h4></td>'+
        '</tr><tr>'+
        '	<th><h4>Cantidad:</h4></th>'+
        '	<td class="text-right"><h4>'+dato2+'</h4></td>'+
        '</tr><tr>'+
            '<th><h4><b>TOTAL:</b></h4></th>'+
        '	<td class="text-right"><h4><b>$ '+dato3+'</b></h4></td>'+
        '</tr></table>';
    $('#cont-modal-body').html(enviar);
    $("#btn-Abrir").click();
}


function view_data(id,op) {
    f_carga('cont-modal-body');
    $.ajax({
        url: 'operaciones.php?op='+op,
        data: {
            Id_contador: id
        },
        type: 'POST',
        success: function (r) {
            $('#cont-modal-body').html(r);
            if (op==6) {
                confi_tabla('tbl_table_detalle');
            }
        }
    });
}



function Generar() {
    tipo = $("#slcTipo").val();
    Swal.fire({
        title: '¿Esta seguró que desea continuar?',
        html: '<h6>La orden de producción no podrá ser módificada</h6>',
        showCancelButton: true,
        confirmButtonText: 'CONTINUAR',
        cancelButtonText: 'CANCELAR',
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            $("#btn-generar").attr("disabled", true);
            $("#btn-generar").html("<b>CARGANDO...</b>");
            $.ajax({
                url: 'operaciones.php?op=4',
                data: {
                    Tipo: tipo
                },
                type: 'POST',
                success: function (r) {
                    if (r == true) {
                        window.location.href = "../../PDF/index.php";
                        Vista_Previa_Orden(tipo);
                        $("#btn-generar").attr("disabled", false);
                        $("#btn-generar").html("<b>GENERAR ORDEN</b>");
                    } else {
                        Swal.fire({
                            icon: 'error',
                            html: '<h4>'+ r +'</h4>',
                        });
                        $("#btn-generar").attr("disabled", false);
                        $("#btn-generar").html("<b>GENERAR ORDEN</b>");
                    }
                }
            });
        }
    });
}