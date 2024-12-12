	$(document).ready(function() {
     $('body').removeClass('overflow');
     $('.main-header').addClass('navbar');
     $('.cont-carga').addClass('d-none');
     Traer_Guias();
  });
function Traer_Guias() {
  $.ajax({
      url: 'operaciones.php?op=1',
      success : function(r) { 
        $('#cont_tabla_guia').html(r);
        $('.cont-carga-table').addClass('d-none');
        confi_tabla();
      }
  });
}
function confi_tabla() {
  $("#tbl_guia_proceso").DataTable({
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
//Selecionar guia para realizar el detalle 
function Mostrar(id) {
  comprobante = $("#tr-"+id).find("td").eq(0).html();
  ex_comprobante = $('#comprobante').html();
  if (comprobante != ex_comprobante) {
    $('#comprobante').html(comprobante);
    Nombre = $("#tr-"+id).find("td").eq(2).html();
    observacion= " <b id='obs'>"+ $("#tr-"+id).find("td").eq(6).html()+"</b>";
    $('#cliente').html(Nombre+observacion);

    $('#tipo').html($("#tr-"+id).find("td").eq(3).html());
    $('#cantidad').html($("#tr-"+id).find("td").eq(4).html());

    //Confi Inicio
    $('#body-detalle').html("");
    $('#btn-guardar').attr("disabled", true);
    $('#btn-agregar').attr("disabled", false);
    //Limpiar
    $('#btn-agregar').html('<b>AGREGAR <i class="fas fa-plus ml-2"></i></b>');
    $('#txtCoidgo').val("");
    $('#txtPeso').val("");
    $('#txtId').val("");
    $('#text-condicion').html("");
  }
  $('#cont_tabla_guia').slideUp("slow");
  $('#cont-detalle').slideDown("slow");
  $('#titulo').slideUp("slow");  
}
 function Regresar() {
  Traer_Guias();
  $('#cont_tabla_guia').slideDown("slow");
  $('#titulo').slideDown("slow");
  $('#cont-detalle').slideUp("slow");
}
$('#btn-agregar').click(function () {
  codigo = $('#txtCoidgo').val();
  peso = $('#txtPeso').val();
  if (codigo=="" || peso=="") {
   Swal.fire({
        icon: 'error',
        html: '<h4>Complete <b>TODOS</b> los campos</h4>',
      });
  }else if(isNaN(peso)==true){
    Swal.fire({
        icon: 'error',
        html: '<h4>Solo se permiten <b>números</b> para el campo <b>Peso</b></h4>',
      });
  }else{
    if (comprobar_codigo_base(codigo)==true) {
      Swal.fire({
        icon: 'error',
        html: '<h4>El código <b>'+codigo+'</b> <br>Esta ocupado por una animal que aun no ha sido procesado <b>(Faenado)</b></h4>',
      });
    }else if(comprobar_codigo_tabla(codigo)==true){
      Swal.fire({
        icon: 'error',
        html: '<h4>El código <b>'+codigo+'</b> <br>Esta ocupado por otro animal en esta <b>Tabla</b></h4>',
      });

    }else{
      Id=$('#txtId').val();
      if (Id=="") {
        Insertar(codigo,peso);
      }else{
        actualizar(codigo,peso,Id);
      }
    }
  }
});
function comprobar_codigo_tabla(codigo) {
  cantidad = $('#body-detalle >tr').length;
  contador =0;
  codigo = codigo.trim();
  Id=$('#txtId').val();
  if (Id =="") {
    for (var i=0;i <cantidad;i++ ) {
      tdcode = $("#col-code-"+(i+1)).html();
      tdcode = tdcode.trim();
      if (codigo==tdcode) {
        contador++;
      } 
    }
  }else{
    for (var i=0;i <cantidad;i++ ) {
      tdcode = $("#col-code-"+(i+1)).html();
      tdcode = tdcode.trim();
      if (codigo==tdcode ) {
        if ($("#col-code-"+Id).html()!=codigo) {
          contador++;
        }
      } 
    }
  }
  if(contador==0){
    return false
  }else{
    return true;
  }
}

function comprobar_codigo_base(codigo) {
 var resultado="";
  $.ajax({
    async: false,
    url: "operaciones.php?op=3",
    data:{Code : codigo},
    type: 'POST',
    success: function(result) {
      resultado = result;
    }
  });
  return resultado; 
}

  function Insertar(codigo,peso) {
  cantida = $('#body-detalle >tr').length;
  if ( cantida <= $('#cantidad').html()) {
    ca = $('#cantidad').html();
    if (cantida == (ca-1) ) {
    $('#btn-agregar').attr("disabled", true);
    $('#btn-guardar').attr("disabled", false);
    }
    fila="<tr id='de-"+(cantida+1)+"'>";
    columa1="<td>"+ (cantida+1) +"</td>";
    columa2="<td id='col-code-"+(cantida+1)+"'>"+codigo+"</td>";
    columa3="<td id='col-peso-"+(cantida+1)+"'>"+peso+"</td>";
    columa4 ='<td  style="max-width:70px;"><button class="btn btn-info btn-sm" onclick="selecionar('+(cantida+1)+')"><b>SELECCIONAR <i class="fas fa-check ml-2"></i></b></button></td>'
    fila=fila+columa1+columa2+columa3+columa4+"</tr>";
    $("#body-detalle").append(fila);
    $('#txtCoidgo').val("");
    $('#txtPeso').val("");
  }
}
function actualizar(codigo,peso,id) {
  $('#col-code-'+id).html(codigo);
  $('#col-peso-'+id).html(peso);
  $('#txtCoidgo').val("");
  $('#txtPeso').val("");
  $('#txtId').val("");
  $('#text-condicion').html("");
  if ($('#body-detalle >tr').length == $('#cantidad').html()) {
    $('#btn-agregar').attr("disabled", true);
    $('#btn-guardar').attr("disabled", false);
  }else{
    $('#btn-agregar').attr("disabled", false);
    $('#btn-guardar').attr("disabled", true);
  }
  $('#btn-agregar').html('<b>AGREGAR <i class="fas fa-plus ml-2"></i></b>');
}
function selecionar(id) {
  $('#txtCoidgo').val($("#de-"+id).find("td").eq(1).html());
  $('#txtPeso').val($("#de-"+id).find("td").eq(2).html());
  $('#txtId').val($("#de-"+id).find("td").eq(0).html());
  $('#text-condicion').html('Item selecionado: <b>N° '+id+'</b>');
  $('#btn-agregar').attr("disabled", false);
  $('#btn-agregar').html('<b>ACTUALIZAR <i class="fas fa-pen-alt ml-2"></i></b>');
  $('#btn-guardar').attr("disabled", true);
}
function LeerPesotxt() {
  var peso="";
  $.ajax({
    async: false,
    url: "pesar.php",
    dataType: "text",
    success: function(result) {
      peso = result;
    }
  });
  return peso;
}
// console.log(LeerPesotxt());
// setInterval('Write_Peso()',100);
// function Write_Peso() {
//   peso = LeerPesotxt();
//   // peso = aleatorio();
//   $('#h1-peso').html(peso+" Kg");
// }


$('#btn-pesar').click(function() {
  peso = LeerPesotxt();
  $('#txtPeso').val(peso);
});
$('#btn-guardar').click(function() {
  LeerTabla();  
});

function LeerTabla() {
  $('#btn-guardar').html("<b>Guardando...</b>");
  $('#btn-guardar').attr("disabled", true);
  cantidad = $('#body-detalle >tr').length;
  cont =0;
  for (var i=1; i <= cantidad; i++) {
    code = $("#de-"+i).find("td").eq(1).html();
    peso= $("#de-"+i).find("td").eq(2).html();
    guia = $('#comprobante').html();
    if (!Guardar_Detalle(code,peso,guia))cont++;
  }
  $('#btn-guardar').html('<b>GUARDAR <i class="fas fa-save ml-2"></i></b>');
  $('#btn-guardar').attr("disabled", false);
  if (cont==0) {
    Swal.fire({
        icon: 'success',
        html: '<h4>Datos Guardados</h4>',
      });
    //Confi Inicio
    $('#body-detalle').html("");
    $('#btn-guardar').attr("disabled", true);
    $('#btn-agregar').attr("disabled", false);
    //Limpiar
    $('#btn-agregar').html('<b>AGREGAR <i class="fas fa-plus ml-2"></i></b>');
    $('#txtCoidgo').val("");
    $('#txtPeso').val("");
    $('#txtId').val("");
    $('#text-condicion').html("");
    Regresar();
  }else{
    Swal.fire({
        icon: 'error',
        html: '<h4>Error no se insertaron los datos</h4>',
      });
  }

}



function Guardar_Detalle(code,peso,guia) {
  fecha = Traer_Fecha();
  var result=false;
  $.ajax({
    async:false,
    url: 'operaciones.php?op=2',
    data:{Codigo : code,Peso : peso ,NGuia : guia,Fecha :fecha},
    type: 'POST',
    success : function(r) { 
    result = r;
    }
  });  
  return result;
}
function Traer_Fecha() {
  var result=false;
  $.ajax({
    async:false,
    url: 'operaciones.php?op=4',
    success : function(r) { 
    result = r;
    }
  });  
  return result;
}