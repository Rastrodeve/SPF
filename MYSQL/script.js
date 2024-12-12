
$(document).ready(function() {
  $('body').removeClass('overflow');
  $('.main-header').addClass('navbar');
  $('.cont-carga').addClass('d-none');
  $('.select2bs4').select2({
      theme: 'bootstrap4'
    });
  Traer_Tablas($("#select-base").val());
});
$("#btn").click(function () {
  Traer_Consulta();  
});
$("#select-base").change(function () {
  Traer_Tablas($("#select-base").val());
});
function Traer_Tablas(Base) {
  $("#cont-tablas").html("Gargando...");
  $.ajax({
      url: 'operaciones.php?op=1',
      data: {DB : Base},
      type: 'post',
      success : function(r) { 
        $("#cont-tablas").html(r);
      }
  });
}
function Desplegar_T() {
  if ($('#cont-tablas').is(':visible')) {
      $('#cont-tablas').slideUp();
  } else {
      $('#cont-tablas').slideDown();
  } 
}



function Traer_Consulta() {
  $('#cont-table').html("");
  $('.loader').removeClass("d-none");
  consulta = $('#txtCont').val();
  Base = $("#select-base").val();
  $.ajax({
      url: 'operaciones.php?op=2',
      data: {Consulta : consulta, DB : Base},
      type: 'post',
      success : function(r) { 
        $('.loader').addClass("d-none");
        $('#cont-table').html(r);
        confi_tabla();
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