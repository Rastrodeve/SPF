$(document).ready(function() {
    $('body').removeClass('overflow');
    $('.cont-carga').addClass('d-none');
    $('.select2bs4').select2({
        theme: 'bootstrap4'
      });
   Cargar_Datos();
   Select_Servicios_YP();
   Cargar_Numeros();
  });
  function soloNumeros_Decimales(e){
    var key = window.Event ? e.which : e.keyCode;
    return (key >= 48 && key <= 57 || key==46 );
  }
  $("#txtHorasEstancia").keypress(function (e) {
    cadena= $("#txtHorasEstancia").val();
    var key = window.event ? e.which : e.keyCode;
    if (key < 48 || key > 57) {
      e.preventDefault();
      }else{
      if (cadena.length > 2) e.preventDefault();
      }
  })
  $("#txtLetraEspecie").keypress(function () {
    cadena= $("#txtLetraEspecie").val();
    var key = window.event ? event.which : event.keyCode;
    if (key < 65 || key > 122 ) {
      event.preventDefault();
    }else{
      if (key > 90 && key < 97 ) {
        event.preventDefault();
      }else {
          if (cadena.length > 0) event.preventDefault();
      }
    }
  })
  
  function Cargar_Datos() {
    $('#cont-datos').html("");
    $('.loader').removeClass("d-none");
    $.ajax({
        url: 'operaciones.php?op=1',
        success : function(r) {
          $('.loader').addClass("d-none");
          $('#cont-datos').html(r);
        }
    });
  }
  function Cargar_Datos_Taza(){
    $("#txtTaza").val("GARGANDO...");
    $("#txtHorasEstancia-taza").val("..");
    $("#slcMinutos-taza").val("00");
    $("#slcSegundos-taza").val("00");
    $.ajax({
        url: 'operaciones.php?op=9',
        success : function(r) {
          if(r != "0"){
            arrayD= r.split("/");
            $("#txtTaza").val(arrayD[0]);
            tiempo = arrayD[1];
            arrayT = tiempo.split(":");
            $("#txtHorasEstancia-taza").val(arrayT[0]);
            $("#slcMinutos-taza").val(arrayT[1]);
            $("#slcSegundos-taza").val(arrayT[2]);
          }else{
  
          }
        }
    });
  }
  
  function Select_Servicios_YP(){
    $.ajax({
        url: 'operaciones.php?op=6',
        success : function(r) {
          $('#slcYupack-corralaje').html(r);
          $('#slcYupack-otros').html(r);
        }
    });
  }
  $("#slcYupack-corralaje").change(function () {
    $("#spnCodigo-Corralaje").html($("#slcYupack-corralaje").val());
  })
  $("#slcYupack-otros").change(function () {
    $("#spnCodigo-otros").html($("#slcYupack-otros").val());
  });
  function editar_otros(id,tipo,ganado) {
    $("#txtId").val(id);
    $("#spnGanado").html(ganado.toUpperCase());
    $("#spnCodigoGanado").html(tipo);
    $("#txtDescrip").val($("#td-otros-descr-"+id).html());
    $("#txtPrecio").val($("#td-otros-precio-"+id).html());
    code = $('#td-otros-Codigo-'+id).html();
    $("#spnCodigo-otros").html(code);
    select_valor =$("#slcYupack-otros option[value="+code+"]").text();
    $('#slcYupack-otros').val(code);
    $('#select2-slcYupack-otros-container').html(select_valor);
    $('#select2-slcYupack-otros-container').prop('title',select_valor);
    $("#btnAbrir-modalOtros").click();
    $("#tituloServicios").html("EDITAR SERVICIO");
  }
  
  $("#btn-insertar-otros").click(function () {
    id =$('#txtId').val();
    if (id==0) {
      Insert_Serivicios();
    }else{
      Update_Otros_Servicios(id);
    }
  })
  
  function Update_Otros_Servicios(id) {
    descripcion=$('#txtDescrip').val();
    precio = $('#txtPrecio').val();
    code = $('#slcYupack-otros').val();
    descripciony =$("#slcYupack-otros option[value="+code+"]").text();
    $.ajax({
      url: "operaciones.php?op=3",
      data: {Descripcion : descripcion,DescripcionY: descripciony,  Yupak : code,Precio: precio,Id : id},
      type:'POST',
      success: function(r) {
        if (r== "1") {
          $("#btnCerrar-otros").click();
          Cargar_Datos();
          Select_Servicios_YP();
          $("#tituloServicios").html("AÑADIR NUEVO SERVICIO");
          Swal.fire({
            icon: 'success',
            html: '<h4>Datos Actualizados</h4>',
          });
        }else{
          Swal.fire({
            icon: 'error',
            html: '<h4><b>Error: </b> '+r+'</h4>',
          });
        }
      }
    });
  }
  function nuevo_servicio(tipo,codigo) {
    $("#spnGanado").html(tipo.toUpperCase());
    $("#spnCodigoGanado").html(codigo);
    $('#txtId').val(0);
    $('#txtDescrip').val("");
    $('#txtPrecio').val("");
    $("#spnCodigo-otros").html('SELECCIONE UNO');
    select_valor =$("#slcYupack-otros option[value='SELECCIONE UNO'").text();
    $('#slcYupack-otros').val('SELECCIONE UNO');
    $('#select2-slcYupack-otros-container').html('SELECCIONE UNO');
    $('#select2-slcYupack-otros-container').prop('title','SELECCIONE UNO');
    $("#btnAbrir-modalOtros").click();
  }
  
  function Insert_Serivicios() {
    descripcion=$('#txtDescrip').val();
    precio = $('#txtPrecio').val();
    code = $('#slcYupack-otros').val();
    codigo = $("#spnCodigoGanado").html();
    descripciony =$("#slcYupack-otros option[value="+code+"]").text();
    $.ajax({
      url: "operaciones.php?op=2",
      data: {Descripcion : descripcion,DescripcionY: descripciony,  Yupak : code,Precio: precio,Especie:codigo},
      type:'POST',
      success: function(r) {
        if (r== "1") {
          $("#btnCerrar-otros").click();
          Cargar_Datos();
          Select_Servicios_YP();
          Swal.fire({
            icon: 'success',
            html: '<h4>Datos Insertados</h4>',
          });
        }else{
          Swal.fire({
            icon: 'error',
            html: '<h4><b>Error: </b> '+r+'</h4>',
          });
        }
      }
    });
  }
  $("#btn-guardar-corralaje").click(function () {
    estado= $('input:radio[name=radio]:checked').val();
    codigo=$('#slcYupack-corralaje').val();
    especie = $("#spnCodigoGanado-corralaje").html();
    $.ajax({
      url: "operaciones.php?op=5",
      data: {Especie : especie,Codigo: codigo,  Estado : estado},
      type:'POST',
      success: function(r) {
        if (r== "1") {
          $("#btnCerrar-Corralaje").click();
          Cargar_Datos();
          Select_Servicios_YP();
          Swal.fire({
            icon: 'success',
            html: '<h4>Datos Actualizados</h4>',
          });
        }else{
          Swal.fire({
            icon: 'error',
            html: '<h4><b>Error: </b> '+r+'</h4>',
          });
        }
      }
    });
  });
  $("#btn-guardar-especie").click(function () {
    descripcion= $('#txtDescripEspecie').val();
    codigo=$('#txtCodigoEscpecie').val();
    tiempo = $('#txtHorasEstancia').val()+":"+$('#slcMinutos').val()+":"+$('#slcSegundos').val();
    letra = $('#txtLetraEspecie').val();
    $.ajax({
      url: "operaciones.php?op=7",
      data: {Descripcion : descripcion,Codigo: codigo,Tiempo: tiempo, Letra:letra},
      type:'POST',
      success: function(r) {
        if (r== "1") {
          $("#btnCerrar-especie").click();
          Cargar_Datos();
          Select_Servicios_YP();
          Swal.fire({
            icon: 'success',
            html: '<h4>ESPECIE GUARDADA</h4>',
          });
        }else{
          Swal.fire({
            icon: 'error',
            html: '<h4><b>Error: </b> '+r+'</h4>',
          });
        }
      }
    });
    $('#txtDescripEspecie').val("");
    $('#txtCodigoEscpecie').val("");
    $('#txtHorasEstancia').val(0);
    $('#slcMinutos').val(0);
    $('#slcSegundos').val(0);
    $('#txtLetraEspecie').val("");
  });
  function Cargar_Numeros() {
    resultado="";
    dato="";
    for (var i = 0; i < 60; i++) {
      dato = i+"";
      if (dato.length == 1) dato= "0"+""+ dato;
      resultado = resultado +'<option value="'+dato+'">'+dato+'</option>';
    }
    $("#slcMinutos").html(resultado);
    $("#slcSegundos").html(resultado);
    $("#slcMinutos-edit").html(resultado);
    $("#slcSegundos-edit").html(resultado);
    $("#slcMinutos-taza").html(resultado);
    $("#slcSegundos-taza").html(resultado)
  }
  $("#btn-guardar-especie-edit").click(function () {
    codigo=$('#spnGanado-edit').html();
    descripcion= $('#txtDescripEspecie-edit').val();
    tiempo = $('#txtHorasEstancia-edit').val()+":"+$('#slcMinutos-edit').val()+":"+$('#slcSegundos-edit').val();
    $.ajax({
      url: "operaciones.php?op=8",
      data: {Descripcion : descripcion,Codigo: codigo,Tiempo: tiempo},
      type:'POST',
      success: function(r) {
        if (r== "1") {
          $("#btnCerrar-especie-edit").click();
          Cargar_Datos();
          Select_Servicios_YP();
          Swal.fire({
            icon: 'success',
            html: '<h4>ESPECIE ACTUALZIADA</h4>',
          });
        }else{
          Swal.fire({
            icon: 'error',
            html: '<h4><b>Error: </b> '+r+'</h4>',
          });
        }
      }
    });
    $('#txtDescripEspecie-edit').val("");
    $('#txtHorasEstancia-edit').val(0);
    $('#slcMinutos-edit').val(0);
    $('#slcSegundos-edit').val(0);
  });
  $("#btn-taza").click(function () {
    taza=$('#txtTaza').val();
    tiempo = $('#txtHorasEstancia-taza').val()+":"+$('#slcMinutos-taza').val()+":"+$('#slcSegundos-taza').val();
    Swal.fire({
      icon: 'info',
      html:'<h6>Se cobrará <b>$ '+taza+'</b> de corralaje, por día o fracción de día, luego de las primeras  <b>'+tiempo+'</b> horas de permanencia</h6>',
      showDenyButton: true,
      confirmButtonText: 'CONFIRMAR',
      denyButtonText: 'CANCELAR',
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        $.ajax({
          url: "operaciones.php?op=10",
          data: {Taza : taza,Tiempo: tiempo},
          type:'POST',
          success: function(r) {
            if (r== "1") {
              Cargar_Datos_Taza();
              Swal.fire({
                icon: 'success',
                html: '<h4>CORRALAJE ACTUALIZADO</h4>',
              });
  
            }else{
              Swal.fire({
                icon: 'error',
                html: '<h4><b>Error: </b> '+r+'</h4>',
              });
            }
          }
        });
      }
    });
  });
  function CambiarEstado(estado,id) {
    if (estado==0) {
      ht="<h4>ESTA SEGURO DE DESACTIVAR EL SERVICIO ?</h4>";
    }
    if (estado==1) {
      ht="<h4>ESTA SEGURO DE ACTIVAR EL SERVICIO ?</h4>";
    }
    Swal.fire({
      icon: 'info',
      html:ht,
      showDenyButton: true,
      confirmButtonText: 'CONFIRMAR',
      denyButtonText: 'CANCELAR',
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        $.ajax({
          url: "operaciones.php?op=11",
          data: {Estado : estado,Id: id},
          type:'POST',
          success: function(r) {
            if (r== "1") {
              Cargar_Datos();
              Select_Servicios_YP();
              Swal.fire({
                icon: 'success',
                html: '<h4>DATOS ACTUALIZADOS</h4>',
              });
  
            }else{
              Swal.fire({
                icon: 'error',
                html: '<h4><b>Error: </b> '+r+'</h4>',
              });
            }
          }
        });
      }
    });
  }
  