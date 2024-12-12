
$(document).ready(function() {
	$('body').removeClass('overflow');
	$('.main-header').addClass('navbar');
	$('.cont-carga').addClass('d-none');
	Listar_Especies();
  // Tipo = $("#slcTipo").val();
  // Traer_Consulta(Tipo);
  // confi_tabla();
});
function Listar_Especies() {
	$.ajax({
    url: 'operaciones.php?op=1',
    success : function(r) {
      $("#slcTipo").html(r);
			tipo = $("#slcTipo").val();
			Vista_Previa_Orden(tipo)
  	}
	});
}
function Listar_Tazas(tipo) {
	$.ajax({
    url: 'operaciones.php?op=3',
		data:{ Tipo: tipo},
		type: 'POST',
    success : function(r) {
      $("#cont-tazas").html(r);
  	}
	});
}
function Vista_Previa_Orden(tipo) {
	$('.loader').css("display","block");
  $('#cont-table').html("");
	$.ajax({
      url: 'operaciones.php?op=2',
			data:{ Tipo: tipo},
			type: 'POST',
      success : function(r) {
        $('.loader').css("display","none");
        $('#cont-table').html(r);
        confi_tabla();
		Listar_Tazas(tipo);
      }
  });
}

function confi_tabla() {
  $("#tbl_guia_detalle").DataTable({
      "responsive": true,
      "autoWidth": false,
      "language": {
        "emptyTable": "No hay información",
        "info": "Mostrando de _START_ a _END_ de _TOTAL_ Resultados",
        "infoEmpty": "Mostrando 0 a 0 de 0 Resultados",
        "infoFiltered": "(Filtrado de _MAX_ total Resultados)",
        "infoPostFix": "",
        "thousands": ",",
        "lengthMenu": "Mostrar _MENU_ Resultados",
        "loadingRecords": "Cargando...",
        "processing": "Procesando...",
        "search": "Buscar:",
        "zeroRecords": "SIN RESULTADOS ENCONTRADOS",
        "paginate": {
            "first": "Primero",
            "last": "Ultimo",
            "next": "Siguiente",
            "previous": "Anterior"
        },
      },
  });
}

$("#slcTipo").change(function (){
  Tipo = $("#slcTipo").val();
  Vista_Previa_Orden(Tipo);

});

function Generar(tipo) {
  $("#btn-generar").attr("disabled", true);
  $("#btn-generar").html("<b>CARGANDO...</b>");
  $.ajax({
      url: 'operaciones.php?op=4',
			data:{ Tipo: tipo},
			type: 'POST',
      success : function(r) {
				console.log(r);
        if (r=="yes") {
					window.location.href="../PDF/index.php";
					Vista_Previa_Orden(tipo);
          $("#btn-generar").attr("disabled",false);
          $("#btn-generar").html("<b>GENERAR ORDEN</b>");
        }else{
					alert(r);
					$("#btn-generar").attr("disabled",false);
          $("#btn-generar").html("<b>GENERAR ORDEN</b>");
				}
      }
  });
}

$("input:radio[name=busqueda]").change(function() {
 var op = $('input:radio[name=busqueda]:checked').val();
 if (op==0) {
  $('#div-fecha').slideUp();
  Traer_Consulta();
 }else if (op==1) {
  $('#div-fecha').slideDown();
  $('#cont-table').html("");
 }
});

function Fecha() {
  $.ajax({
      url: 'operaciones.php?op='+2,
      success : function(r) {
        // console.log(r);
        $('#h3_Fecha').html(r);
      }
  });
}
function View_Informacion(op,dato1,dato2,dato3,dato4,dato5,dato6,dato7) {
	enviar="";
	if (op==0) {
		$("#exampleModalLabel").html("<b>DATOS DEL CLIENTE</b>");
		enviar = ''+
		'<table class="table "><tr>'+
				'<th><h5>Nombres y Apellidos:</h5></th>'+
				'<td><h5> '+dato3+'</h5></td>'+
			'</tr><tr>'+
			'	<th><h5>Ruc:</h6></th>'+
			'	<td><h5>'+dato2+'</h5></td>'+
			'</tr><tr>'+
				'<th><h5>N° Guía:</h5></th>'+
			'	<td><h5>'+dato1+'</h5></td>'+
			'</tr></table>';
	}
	if (op==1) {
		$("#exampleModalLabel").html("<b>CALCULO REALIZADO</b>");
		enviar = '<h3><b>S. '+dato4+'</b></h3>'+
		'<table class="table "><tr>'+
				'<th><h4>Precio Unitario:</h4></th>'+
				'<td><h4>$ '+dato1+'</h4></td>'+
			'</tr><tr>'+
			'	<th><h4>Cantidad:</h4></th>'+
			'	<td><h4>'+dato2+'</h4></td>'+
			'</tr><tr>'+
				'<th><h4><b>TOTAL:</b></h4></th>'+
			'	<td><h4><b>$ '+dato3+'</b></h4></td>'+
			'</tr></table>';
	}
	if (op==2) {
		$("#exampleModalLabel").html("<b>DATOS CORRALAJE</b>");
		enviar = '<h3><b></b></h3>'+
		'<table class="table "><tr>'+
				'<th><h4>Tiempo de Estancia</h4></th>'+
				'<td><h4>'+dato1+'</h4></td>'+
			'</tr><tr>'+
			'	<th><h4>Tiempo sin taza de corralaje</h4></th>'+
			'	<td><h4>'+dato2+'</h4></td>'+
			'</tr><tr>'+
			'	<th><h4>Tiempo a cobrar</h4></th>'+
			'	<td><h4>'+dato3+'</h4></td>'+
			'</tr><tr>'+
			'	<th><h4>Taza por día o fracción de día</h4></th>'+
			'	<td><h4>$ '+dato4+'</h4></td>'+
			'</tr><tr>'+
				'<th><h4>SUBTOTAL:</h4></th>'+
			'	<td><h4>$ '+dato5+'</h4></td>'+
			'</tr><tr>'+
				'<th><h4>CANTIDAD:</h4></th>'+
			'	<td><h4>'+dato7+'</h4></td>'+
			'</tr><tr>'+
			'<th><h4><b>TOTAL:</b></h4></th>'+
		'	<td><h4><b>$ '+dato6+'</b></h4></td>'+
			'</tr></table>';
	}
		$(".modal-body").html(enviar);
	$("#btn-Abrir").click();
}