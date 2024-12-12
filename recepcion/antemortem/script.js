const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

$(document).ready(function () {
    // get_select_especies();
    // confi_tabla("tbl_data_dictamen");
    f_start();
    $('.cont-carga').addClass('d-none');

});
function f_start() {
    f_carga('cont-data');
    $.ajax({
        url: 'operaciones.php?op=1',
        success: function (r) {
            $("#cont-data").html(r);
            confi_tabla('tbl_data_table');
            $('#slcBuscar').select2();
        }
    });
}

function GET_Formulario() {
    alert("hello");
    /*
    especie = $("#slcBuscar").val();
    if (especie > 0) {
        $.ajax({
            type:'POST',
            data:{Id:especie},
            url: 'operaciones.php?op=2',
            success: function (r) {
                f_start();
            }
        });
    }
        */
}

function GET_Guias() {
    especie = $("#slcBuscar").val();
    if (especie > 0) {
        $.ajax({
            type:'POST',
            data:{Id:especie},
            url: 'operaciones.php?op=2',
            success: function (r) {
                f_start();
            }
        });
    }
}
function GET_Guias_table() {
    f_carga('cont-table-data');
    $.ajax({
        url: 'operaciones.php?op=9',
        success: function (r) {
            $("#cont-table-data").html(r);
            confi_tabla('tbl_data_table');
        }
    });
}
function f_regresar() {
    $.ajax({
        url: 'operaciones.php?op=8',
        success: function (r) {
            f_start();
        }
    });
}

function get_data_guia_ant(id) {
    $.ajax({
        type:'POST',
        data:{Id:id},
        url: 'operaciones.php?op=3',
        success: function (r) {
            f_start();
        }
    });
}
function f_get_data_new_inspeccion() {
    f_carga('contenedor-resultados');
    $.ajax({
        url: 'operaciones.php?op=4',
        success: function (r) {
            $("#contenedor-resultados").html(r);
            $('.input_disablecopypaste').bind('paste', function (e) {
                e.preventDefault();
            });
        }
    });
}
function f_get_data_table() {
    f_carga('contenedor-resultados');
    $.ajax({
        url: 'operaciones.php?op=5',
        success: function (r) {
            $("#contenedor-resultados").html(r);
            confi_tabla('tbl_data_table');
        }
    });
}
function f_get_restante() {
    dato = $("#slcSexo").val();
    f_carga('spnRestante');
    $.ajax({
        type : 'POST',
        data : {Tipo : dato},
        url: 'operaciones.php?op=6',
        success: function (r) {
            $("#txtCantidad").val(r);
            $("#spnRestante").html(r);
        }
    });
}

function f_get_data_dictamen() {
    if (!$('#cbhxMacho').is(':checked') && !$('#cbhxhembra').is(':checked') ){
        Swal.fire({
            icon: 'error',
            html: '<h4>Debe seleccionar por lo menos uno (Machos o Hembras)</h4>',
        });
        return ;
    } 
    mensaje_macho = '';
    mensaje_hembra = '';
    macho = $("#txtCantidad").val();
    hembra = $("#txtCantidad2").val(); 
    if ($('#cbhxMacho').is(':checked')) {
        mensaje_macho = '<h6><span class="text-muted">Macho: </span> <label>'+macho+'</h6>';
        if (macho == '' || macho == 0 ) {
            Swal.fire({
                icon: 'error',
                html: '<h4>Ingrese una cantidad</h4>',
            });
            $("#txtCantidad").addClass('is-invalid');
            return;
        }else $("#txtCantidad").removeClass('is-invalid');
    }else{
        console.log("No Seleccionado");
        macho = 0;
        $("#txtCantidad").removeClass('is-invalid');
    }
    if ($('#cbhxhembra').is(':checked')) {
        if (hembra == '' || hembra == 0 ) {
            Swal.fire({
                icon: 'error',
                html: '<h4>Ingrese una cantidad</h4>',
            });
            $("#txtCantidad2").addClass('is-invalid');
            return;
        }else {
            mensaje_hembra = '<h6><span class="text-muted">Hembra: </span> <label>'+hembra+'</h6>';
            $("#txtCantidad2").removeClass('is-invalid');
        }
    }else{
        hembra = 0;
        $("#txtCantidad2").removeClass('is-invalid');
    }
    
    etapaProductiva = $("#txtEtpProductiva").val();
    decomiso = 0; 
    if ($('#chbDecomiso').prop('checked')){
        decomiso = 1; 
    }
    aprovechamiento = 0; 
    if ($('#chbAprovechamiento').prop('checked')){
        aprovechamiento = 1; 
    }

    animal = 0;
    causa = '';
    mensaje_animal = '';
    dictamen = $('input:radio[name=radioDictamen]:checked').val();
    if ($('#chbAnimal').prop('checked')){
        animal = 1;
        causa = $("#txtCausa").val();
        mensaje_animal = '<h6><label>ANIMAL MUERTO</label></h6><h6><span class="text-muted">POSIBLE CAUSA:</span> <label> '+causa+'</label></h6>';
        if (causa =='') {
            Swal.fire({
                icon: 'error',
                html: '<h4>Sí, se seleccionada la opción <b>ANIMAL MUERTO</b> se debe ingresar la posible causa de muerte </h4>',
            });
            $("#txtCausa").addClass("is-invalid");
            return;
        }else $("#txtCausa").removeClass("is-invalid");
        if (dictamen == "0") {
            Swal.fire({
                icon: 'error',
                html: '<h4>Sí, se seleccionada la opción <b>ANIMAL MUERTO</b> el dictamen debe ser <b>SACRIFICIO URGENTE</b></h4>',
            });
            return;
        }

    }else $("#txtCausa").removeClass("is-invalid");
    genero = $("#slcSexo").val();
    mensaje_dictamen = '';
    // if (dictamen==0) mensaje_dictamen = 'FAENAMIENTO NORMAL';
    if (dictamen==0) mensaje_dictamen = 'MATANZA NORMAL';
    // else if (dictamen==1) mensaje_dictamen = 'SACRIFICIO URGENTE';
    else if (dictamen==1) mensaje_dictamen = 'MATANZA DE EMERGENCIA';
    else if (dictamen==2) mensaje_dictamen = 'SACRIFICIO SANITARIO';
    else if (dictamen==3) mensaje_dictamen = 'MATANZA BAJO PRECAUCIONES ESPECIALES';
    else if (dictamen==4) mensaje_dictamen = 'APLAZAMIENTO DE MATANZA';
    else return 'ERROR-212';
    cantidad = $("#cantidadcabeceras").val();
    detalle  = '';
    array = new Array();
    for (i= 1; i <= cantidad; i++) {
        idcabecera = $("#txtCabecera-"+i).val();
        cantidaditem = $("#cantidaditem-"+idcabecera).val();
        detalle += "<b>"+$("#h3Cabecera-"+i).html()+": </b> '";
        for (j= 1; j <= cantidaditem; j++) {
            if ($('#chb-'+idcabecera +'-'+j).prop('checked')) {
                array.push($('#chb-'+idcabecera +'-'+j).val());
                detalle += $('label[for = "chb-'+idcabecera +'-'+j+'"]').html()+" - ";
            }
        }
        detalle += "'<br>";
    }
    
    observacion = $("#txtObservacion").val();
    mensaje = '' +
        '<h5><label>DICTAMEN: ' + mensaje_dictamen + '</label> </h5>' +
        ''+ mensaje_hembra +mensaje_macho+ mensaje_animal + detalle+
        '<h6><span class="text-muted">OBSERVACIÓN: </span>' + observacion + '</h6>' +
        '';

        Swal.fire({
            title: 'Verifique la información',
            html: mensaje,
            // icon: 'warning',
            showCancelButton: false,
            showDenyButton: true,
            confirmButtonColor: '#3085d6',
            denyButtonColor: '#d33',
            confirmButtonText: 'Si, Continuar',
            denyButtonText: 'No, Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                enviar = false;
                if(array.length > 0)enviar = array;
                $.ajax({
                    url: 'operaciones.php?op=7',
                    data: {
                        Macho: macho,
                        Hembra:  hembra,
                        EtapaProductiva:  etapaProductiva,
                        Decomiso: decomiso,
                        Aprovechamiento: aprovechamiento,
                        Dictamen: dictamen,
                        Animal :animal,
                        Causa: causa,
                        ArrayDeta: enviar,
                        Observaciones: observacion,
                    },
                    type: 'POST',
                    success: function (r) {
                        if (r==true) {
                            Swal.fire({
                                icon: 'success',
                                html: '<h4>Datos Ingreados</h4>',
                            });
                            f_start();
                        }else{ 
                            Swal.fire({
                                icon: 'error',
                                html: '<h4>'+r+'</h4>',
                            });
                        }
                    }
                });
            }
        })




    
    // alert(mensaje);
    // console.log(array);
    
}









function f_OnlyCant(e) {
    cadena = $("#txtCantidad").val();
    var key = window.Event ? e.which : e.keyCode;
    if (key < 48 || key > 57) {
        e.preventDefault();
    }
}
$('.input_disablecopypaste').bind('paste', function (e) {
    e.preventDefault();
});


$("#btn-nueva").click(function (event) {
        guia_m = $("#txtGuiaNumero").val();
        fecha_guia = $("#txtFechaGuia").val();
        fecha_validez = $("#txtFechaValidez").val();
        provincia = $("#slcProvincia").val();
        code_vacu = $("#txtVacunacion").val();
        cedula_con = $("#txtCI").val();
        conductor = $("#txtConductor").val();
        vehiculo = $("#slcVehiculo").val();
        placa = $("#txtPlaca").val();
        cliente = $("#slcCliente").val();
        if (!comprobar_vacio()) {
            Swal.fire({
                icon: 'error',
                html: '<h4>Complete todos los campos</h4>',
            });
        } else {
            var MyRows = $('#tbl_detalle').find('tbody').find('tr');
            ArrayDetalle = [];
            $mensaje_detalle="";
            $total=0;
            if (MyRows.length==0) {
                Swal.fire({
                    icon: 'error',
                    html: '<h4>Debe agregar la descripción de la guía de movilización</h4>',
                });
                return;
            }else if(cliente == 0){
                Swal.fire({
                    icon: 'error',
                    html: '<h4>Seleccione un cliente para continuar</h4>',
                });
            }else{
                for (var i = 0; i < MyRows.length; i++) {
                    cliente = parseInt($(MyRows[i]).find('td:eq(1)').html());
                    hembra = parseInt($(MyRows[i]).find('td:eq(2)').html());
                    macho = parseInt($(MyRows[i]).find('td:eq(3)').html());
                    subtotal = parseInt(hembra) + parseInt(macho);
                    $total += subtotal;
                    ArrayDetalle.push([ganado,hembra,macho]);
                    $mensaje_detalle += ''+
                    '<h6>' + $(MyRows[i]).find('td:eq(0)').html() + '</h6>' +
                    '<h6> Hembras: ' + hembra + '</h6>' +
                    '<h6> Machos: ' + macho + '</h6>' +
                    '<h6>' + subotal + '</h6>' +
                    '<hr class="mb-0 mt-0">' +
                    '';
                }
            }
            // $('#myModalExito').modal('show');
            mensaje = '' +
                '<h5><b>Esta ingresando una guía de movilización:</b></h5>' +
                '<h6><label>Guía de movilización: </label> ' + guia_m + '</h6>' +
                '<h6><label>Fecha de guía: </label> ' + fecha_guia + '</h6>' +
                '<h6><label>Fecha de validez: </label> ' + fecha_validez + '</h6>' +
                '<h6><label>Provincia: </label> ' + $("#slcProvincia option[value='" + provincia + "']").html() + '</h6>' +
                '<h6><label>C. Vacunación: </label> ' + code_vacu + '</h6>' +
                '<h6><label>C.I. conductor: </label> ' + cedula_con + '</h6>' +
                '<h6><label>Nombre del conductor: </label> ' + conductor + '</h6>' +
                '<h6><label>Vehiculo: </label> ' + vehiculo + '</h6>' +
                '<h6><label>Placa: </label> ' + placa + '</h6>' +
                '<h6><label>Cliente: </label> ' +  $("#slcCliente option[value='" + slcCliente + "']").html() + '</h6>' +
                '<h6><label>DETALLE DE LA GUIA</label></h6>' + $mensaje_detalle+
                '<h6><label>CANTIDAD TOTAL: '+total+'</label></h6>'+
                '';
            Swal.fire({
                title: 'Verifique la información',
                html: mensaje,
                // icon: 'warning',
                showCancelButton: false,
                showDenyButton: true,
                confirmButtonColor: '#3085d6',
                denyButtonColor: '#d33',
                confirmButtonText: 'Si, Continuar',
                denyButtonText: 'No, Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log(ArrayDetalle);
                    insert_guia_movilizacion( guia_m, fecha_guia, fecha_validez, provincia, code_vacu, cedula_con, conductor, vehiculo, placa,cliente,ArrayDetalle);
                }
            })
        }
});

$("#btn-detalle").click(function () {
    ganado = $("#slcGanado").val();
    hembra = $("#txtHembra").val();
    macho = $("#txtMacho").val();
    if(ganado == 0){
        Swal.fire({
            icon: 'error',
            html: '<h4>Seleccione una especie animal para continuar</h4>',
        });
    }else if(hembra < 1 &&  macho < 1){
        if(hembra < 1)$("#txtHembra").addClass("is-invalid");
        if(macho < 1)$("#txtMacho").addClass("is-invalid");
    }else{
        $("#txtHembra").removeClass("is-invalid");
        $("#txtMacho").removeClass("is-invalid");
        var MyRows = $('#tbl_detalle').find('tbody').find('tr');
        for (var i = 0; i < MyRows.length; i++) {
            if($(MyRows[i]).find('td:eq(1)').html() == ganado ){
                Swal.fire({
                    icon: 'error',
                    html: '<h4>La especie seleccionada ya se encuentra agregada</h4>',
                });
                return;
            }
        }
        count = 0;
        if (MyRows.length == 0)count = MyRows.length  + 1;
        else {
            for (var i = 0; i <  MyRows.length; i++) {
                count++;
            }
            count = count +1;
        }
        subotal = parseInt(hembra) + parseInt(macho);
        tr = "<tr id='tr-"+count+"'>"+
            // "<td>"+$("#slcCliente option[value='"+cliente+"']").html()+"</td>"+
            // "<td class='d-none' >"+ cliente+"</td>"+
            "<td>"+$("#slcGanado option[value='"+ ganado +"']").html()+"</td>"+
            "<td class='d-none' >"+ganado+"</td>"+
            "<td >"+ parseInt(hembra) +"</td>"+
            "<td >"+ parseInt(macho) +"</td>"+
            "<td >"+ subotal +"</td>"+
            "<td> <button class='btn btn-sm btn-danger' onclick='f_eliminar_detalle("+count+")'>"+
                    "<i class='fas fa-trash-alt'></i>"+
            "</button></td>"+
            "</tr>";
        $('#tbl_detalle > tbody').append(tr);
        total=0;
        var MyRows = $('#tbl_detalle').find('tbody').find('tr');
        for (var i = 0; i <  MyRows.length; i++) {
            total = (total + parseInt($(MyRows[i]).find('td:eq(4)').html()));
        }
        $("#tdTotal").html(total);
    }
})
function f_eliminar_detalle(id) {
    $("#tr-"+id).remove();
    total=0;
    var MyRows = $('#tbl_detalle').find('tbody').find('tr');
    for (var i = 0; i <  MyRows.length; i++) {
        total = (total + parseInt($(MyRows[i]).find('td:eq(4)').html()));
    }
    $("#tdTotal").html(total);
}

function comprobar_vacio() {
    ArrayId = ['txtGuiaNumero', 'txtFechaGuia', 'txtFechaValidez', 'txtVacunacion', 'txtCI', 'txtConductor', 'txtPlaca'];
    cont = 0;
    for (let index = 0; index < ArrayId.length; index++) {
        if ($("#" + ArrayId[index]).val() == "") {
            $("#" + ArrayId[index]).addClass("is-invalid");
            cont++;
        } else {
            $("#" + ArrayId[index]).removeClass("is-invalid");
        }
    }
    if (cont == 0) return true;
    else return false;

}

function insert_guia_movilizacion( guia_m, fecha_guia, fecha_validez, provincia, code_vacu, cedula_con, conductor, vehiculo, placa,cliente,ArrayDetalle) {
    $.ajax({
        async: false,
        url: 'operaciones.php?op=5',
        data: {
            Guia: guia_m,
            Fecha_Guia: fecha_guia,
            Fecha_Validez: fecha_validez,
            Provincia: provincia,
            Code_Vacuna: code_vacu,
            Cedula_Condu: cedula_con,
            Conductor: conductor,
            Vehiculo: vehiculo,
            Placa: placa,
            Observacion : $("#txtObservacion").val(),
            Cliente: cliente,
            Array:ArrayDetalle
        },
        type: 'POST',
        success: function (r) {
            if (r==true) {
                $("#txtGuiaNumero").val("");
                $("#txtFechaValidez").val("");
                $("#txtCantidad").val("");
                $("#txtVacunacion").val("");
                $("#txtCI").val("");
                $("#txtConductor").val("");
                $("#txtPlaca").val("");
                $('#tbl_detalle > tbody').html('');
                $("#txtObservacion").val("")
                get_select_especies();
                get_select_clientes();
                get_select_provincias();
                $("#tdTotal").html("0");
                Swal.fire({
                    icon: 'success',
                    html: '<h4>Datos Ingreados</h4>',
                });
                $("#cont-guia-3").slideUp();
                $("#cont-guia-1").slideDown("slow");
            }else{ 
                Swal.fire({
                    icon: 'error',
                    html: '<h4>'+r+'</h4>',
                });
            }
        }
    });
}



function cerrar_alert() {
    $(".swal2-actions .swal2-confirm").click();
}






function comprobar_fecha(fecha) {
    result = "";
    $.ajax({
        url: 'operaciones.php?op=12',
        async: true,
        data: {
            Fecha: fecha
        },
        type: 'POST',
        success: function (r) {
            result = r;
        }
    });
    return result;
}

// Código QR
var dato = "";
cont = 0;
arrayInput2 = ["txtGuiaNumero", "Origen", "txtFechaValidez", "txtPlaca"];

$('input:radio[name=customRadio]').click(function () {
    desplegar_elecion();
})

function desplegar_elecion() {
    if ($("#rdQr").is(":checked")) {
        $("#cont-qr").slideDown();
        $("#txtCodigoQR").focus();
        cantidad = arrayInput2.length;
        for (i = 0; i < cantidad; i++) {
            $("#" + arrayInput2[i]).prop('disabled', true);
            $("#" + arrayInput2[i]).val('');
            $("#spnErrorValidez").html("");
        }
    }
    if ($("#rdBlanco").is(":checked")) {
        $("#cont-qr").slideUp();
        cantidad = arrayInput2.length;
        for (i = 0; i < cantidad; i++) {
            $("#" + arrayInput2[i]).prop('disabled', false);
            $("#" + arrayInput2[i]).val('');
            // $("#slcGanado").val("0");
            // cadena = $("#slcGanado option[value='0']").html();
            // $('#select2-slcGanado-container').html(cadena);
            // $('#select2-slcGanado-container').prop('title', cadena);
            $("#spnErrorValidez").html("");
        }
        $("#txtGuiaNumero").focus();
    }
}


$("#txtCodigoQR").keyup(function (event) {
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if (keycode == '13') {
        event.preventDefault();
        cadena = $("#txtCodigoQR").val();
        $("#txtCodigoQR").val(cadena + "&");
        cont++;
    }
    if (cont == 7) {
        mayus = 0;
        if (event.originalEvent.getModifierState('CapsLock')) {
            mayus = 1;
        }
        texto = Transformar_texto($("#txtCodigoQR").val(), mayus);
        Designar(texto);
    }

});

function Transformar_texto(cadena, mayus) {
    array = cadena.split("&");
    cantidad = array.length;
    result = "";
    for (var i = 0; i < (cantidad - 1); i++) {
        if (i != 1 && i != 3) {
            if (i != 5) {
                if (mayus == 1) {
                    narray = array[i].split("ñ");
                    result = result + narray[1] + "&";
                } else {
                    narray = array[i].split("Ñ");
                    result = result + narray[1] + "&";
                }
            } else {
                narray = array[i].split(" ");
                farray = narray[1].split("/");
                fecha = farray[0] + "/" + farray[1] + "/" + farray[2];
                result = result + fecha + "&";
            }
        }
    }
    return result;
}

function Designar(cadena) {
    array = cadena.split("&");
    cantidad = array.length;
    for (var i = 0; i < (cantidad - 1); i++) {
        // $("#" + arrayInput[i]).val(array[i].trim());
        importe = array[i].trim();
        regresar = importe.toString().replace(/\'/g, '-');
        $("#" + arrayInput2[i]).val(regresar);
        if (i != 2) {
            $("#" + arrayInput2[i]).prop('disabled', true);
        } else {
            $("#" + arrayInput2[i]).prop('disabled', false);
        }
    }
    $("#txtVacunacion").focus();
    $("#txtCodigoQR").val("");
    result = comprobar_fecha($("#txtFechaValidez").val());
    if (result == true) $("#spnErrorValidez").html("");
    else $("#spnErrorValidez").html("Guía de movilización vencida");

    dato = "";
    cont = 0;
}

// Imprimir
function Imprimir(id) {
    $.ajax({
        url: 'operaciones.php?op=13',
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
                GET_Guias();
                clearInterval(fun);
            }, 300);
        }
    });
}

///REPORTE
function generar_reporte(id) {
    $.ajax({
        url: 'operaciones.php?op=6',
        data: {
            Id: id
        },
        type: 'POST',
        success: function (r) {
            window.location.href = "../../PDF/index.php";
        }
    });
}


//COMANDOS

$("#btn-next-1").click(function () {
    if (!comprobar_vacio()) {
        Swal.fire({
            icon: 'error',
            html: '<h4>Complete todos los campos</h4>',
        });
        return;
    }else{
        $("#cont-guia-1").slideUp();
        $("#cont-guia-2").slideDown("slow");
    }
});
$("#btn-next-2").click(function () {
    var MyRows = $('#tbl_detalle').find('tbody').find('tr');
    ArrayDetalle = [];
    $mensaje_detalle="";
    $total=0;
    cliente = $("#slcCliente").val();
    if (MyRows.length==0) {
        Swal.fire({
            icon: 'error',
            html: '<h4>Debe agregar la descripción de la guía de movilización</h4>',
        });
        return;
    }else if(cliente == 0){
        Swal.fire({
            icon: 'error',
            html: '<h4>Seleccione un cliente para continuar</h4>',
        });
    }else{
        $("#cont-guia-2").slideUp();
        $("#cont-guia-3").slideDown("slow");
    }
});
$("#btn-afther-1").click(function () {
    $("#cont-guia-2").slideUp();
    $("#cont-guia-1").slideDown("slow");
});
$("#btn-afther-2").click(function () {
    $("#cont-guia-3").slideUp();
    $("#cont-guia-2").slideDown("slow");
});