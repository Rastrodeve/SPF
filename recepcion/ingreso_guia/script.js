const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

$(document).ready(function () {
    get_select_especies();
    get_select_clientes();
    get_select_provincias();
    $('#reservationdate').datetimepicker({
        format: 'DD/MM/Y'
    });
    $('#reservationdate2').datetimepicker({
        format: 'DD/MM/Y'
    });
    $('#txtFechaGuia').inputmask('dd/mm/yyyy', {
        'placeholder': 'dd/mm/yyyy'
    });
    $('#txtFechaValidez').inputmask('dd/mm/yyyy', {
        'placeholder': 'dd/mm/yyyy'
    });

    $('.select2bs4').select2();
    desplegar_elecion();
    $('.cont-carga').addClass('d-none');
});


function GET_Guias() {
    especie = $("#slcBuscar").val();
    if (especie > 0) {
        f_carga('cont-table-data');
        $.ajax({
            type:'POST',
            data:{Id:especie},
            url: 'operaciones.php?op=1',
            success: function (r) {
                $("#cont-table-data").html(r);
                confi_tabla('tbl_data_table');
            }
        });
    }
}
$("#slcBuscar").change(function () {
    GET_Guias();
});
$("#slcGanado").change(function () {
    if ($("#slcGanado").val() > 0) {
        $.ajax({
            type:'POST',
            data:{Id:$("#slcGanado").val()},
            url: 'operaciones.php?op=7',
            success: function (r) {
                if (r==0) $("#spanEstadoDetalle").html("Detalle de animal automático");
                else if (r==1) $("#spanEstadoDetalle").html("Detalle de animal manual");
                else $("#spanEstadoDetalle").html("No se encontro el estado de detalle");
            }
        });
    }else{
        $("#spanEstadoDetalle").html("Seleccione una especie");
    }
});

function get_select_especies() {
    $.ajax({
        url: 'operaciones.php?op=2',
        success: function (r) {
            // console.log(r);
            $("#slcGanado").html(r);
            $("#slcBuscar").html(r);
        }
    });
}
function get_select_clientes() {
    $.ajax({
        url: 'operaciones.php?op=3',
        success: function (r) {
            $("#slcCliente").html(r);
        }
    });
}
function get_select_provincias() {
    $.ajax({
        url: 'operaciones.php?op=4',
        success: function (r) {
            $("#slcProvincia").html(r);
        }
    });
}
function get_view_detalle_guia(id) {
    f_carga('cont-modal');
    $.ajax({
        data:{Id: id},
        type:'POST',
        url: 'operaciones.php?op=8',
        success: function (r) {
            $("#cont-modal").html(r);
            confi_tabla('tbl_data_visceras');
            confi_tabla('tbl_data_detalle');
        }
    });
}
function get_view_corrales(id) {
    f_carga('cont-modal');
    $.ajax({
        data:{Id: id},
        type:'POST',
        url: 'operaciones.php?op=18',
        success: function (r) {
            $("#cont-modal").html(r);
            confi_tabla('tbl_data_detalle_corral');
        }
    });
}
function get_new_corrales() {
    f_carga('cont-cont_corral');
    $.ajax({
        data:{Id: $("#txtIdGuiaCorral").val()},
        type:'POST',
        url: 'operaciones.php?op=19',
        success: function (r) {
            $("#cont-cont_corral").html(r);
            $('#slcCorral').select2();
        }
    });
}
function f_get_table_corral() {
    f_carga('cont-cont_corral');
    $.ajax({
        data:{Id: $("#txtIdGuiaCorral").val()},
        type:'POST',
        url: 'operaciones.php?op=20',
        success: function (r) {
            $("#cont-cont_corral").html(r);
            confi_tabla('tbl_data_detalle_corral');
        }
    });
}
function function_insert_lugar() {
    cantidad  = $("#txtCantidadCorral").val();
    if (cantidad == ''){
        Swal.fire({
            icon: 'error',
            html: '<h4>Ingrese una cantidad</h4>',
        });
        $("#txtCantidadCorral").addClass("is-invalid");
        return;
    } 
    $("#txtCantidadCorral").removeClass("is-invalid");

    marca  = $("#txtMarca").val();
    if (marca == ''){
        Swal.fire({
            icon: 'error',
            html: '<h4>Ingrese una Marca</h4>',
        });
        $("#txtMarca").addClass("is-invalid");
        return;
    } 
    $("#txtMarca").removeClass("is-invalid");

    corral = $("#slcCorral").val();
    if (corral == 0) {
        Swal.fire({
            icon: 'error',
            html: '<h4>Seleccione un corral</h4>',
        });
        return;
    }
    $.ajax({
        data:{Id: $("#txtIdGuiaCorral").val(),Corral: corral, Cantidad: cantidad, MarcaLugar : marca},
        type:'POST',
        url: 'operaciones.php?op=21',
        success: function (r) {
            if (r == true) {
                Swal.fire({
                    icon: 'success',
                    html: '<h4>Datos agregados</h4>',
                });
                f_get_table_corral();
            }else{
                Swal.fire({
                    icon: 'error',
                    html: '<h4>ERROR: </h4>'+r,
                });
            }
        }
    });

}




function function_delete_lugar(id) {
    $.ajax({
        data:{Id: id},
        type:'POST',
        url: 'operaciones.php?op=22',
        success: function (r) {
            if (r == true) {
                Swal.fire({
                    icon: 'success',
                    html: '<h4>Lugar eliminado</h4>',
                });
                f_get_table_corral();
            }else{
                Swal.fire({
                    icon: 'error',
                    html: '<h4>ERROR: </h4>'+r,
                });
            }
        }
    });

}

function f_update_soli_guia_pro(id) {
    f_carga('cont-modal');
    $.ajax({
        data:{Id: id},
        type:'POST',
        url: 'operaciones.php?op=14',
        success: function (r) {
            $("#cont-modal").html(r);
            $('.input_disablecopypaste').bind('paste', function (e) {
                e.preventDefault();
            });
            $('#slcClienteEdit').select2();
            $('#slcUsuario1').select2();
            $('#slcUsuario2').select2();
        }
    });
}
function f_update_soli_guia_pro_delete(id) {
    f_carga('cont-modal');
    $.ajax({
        data:{Id: id},
        type:'POST',
        url: 'operaciones.php?op=16',
        success: function (r) {
            $("#cont-modal").html(r);
            $('#slcUsuario1').select2();
            $('#slcUsuario2').select2();
        }
    });
}
function f_insert_solicitud() {
    cliente = '';
    m_cliente = '';
    if (!$('#cbxCliente').is(':checked') && !$('#cbxCantidadHembras').is(':checked') && !$('#cbxCantidadMachos').is(':checked') ) {
        Swal.fire({
            icon: 'error',
            html: '<h4>Seleccione por lo menos un campo</h4>',
        });
        return;
    }
    if ($('#cbxCliente').is(':checked')) {
        cliente = $('#slcClienteEdit').val();
        m_cliente = '<h6><b>Cliente: </b> '+$("#slcClienteEdit option[value='" + cliente + "']").html()+'</h6>';
        if (cliente == 0) {
            Swal.fire({
                icon: 'error',
                html: '<h4>Selccione un cliente</h4>',
            });
            $('#slcClienteEdit').addClass('is-invalid');
            return;
        }
    }
    hembra = '';
    m_hembra = '';
    if ($('#cbxCantidadHembras').is(':checked')) {
        hembra = $('#txtHembra_ac').val();
        m_hembra = '<h6><b>Cantidad Hembra: </b> '+hembra+'</h6>';
        if (hembra == '') {
            Swal.fire({
                icon: 'error',
                html: '<h4>Ingrese una cantidad hembra nueva</h4>',
            });
            $('#txtHembra_ac').addClass('is-invalid');
            return;
        }
    }
    
    $('#txtHembra_ac').removeClass('is-invalid');
    macho = '';
    m_macho = '';
    if ($('#cbxCantidadMachos').is(':checked')) {
        macho = $('#txtMacho_ac').val();
        m_macho = '<h6><b>Cantidad Macho: </b> '+macho+'</h6>';
        if (macho == '') {
            Swal.fire({
                icon: 'error',
                html: '<h4>Ingrese una cantidad macho nueva</h4>',
            });
            $('#txtMacho_ac').addClass('is-invalid');
            return;
        }
    }
    $('#txtMacho_ac').removeClass('is-invalid');
    
    usuario1 = $("#slcUsuario1").val();
    usuario2 = $("#slcUsuario2").val();
    if (usuario1 == 0 || usuario2 == 0 ) {
        Swal.fire({
            icon: 'error',
            html: '<h4>Seleccione dos usuarios para que autorice la solicitud</h4>',
        });
        return;
    }
    if (usuario1 ==  usuario2 ) {
        Swal.fire({
            icon: 'error',
            html: '<h4>Los usuarios seleccionados deben ser diferentes</h4>',
        });
        return;
    }

    razon = $("#txtRazon").val();
    if (razon == '') {
        Swal.fire({
            icon: 'error',
            html: '<h4>Ingrese una razón de la modificación</h4>',
        });
        $("#txtRazon").addClass('is-invalid');
        return;
    }
    $("#txtRazon").removeClass('is-invalid');

    mensaje = '' +
        '<h5><b>Esta registrando una nueva solicitud:</b></h5>' +
        m_cliente + m_hembra + m_macho +
        '<h6><label>Usuario 1: </label> ' + $("#slcUsuario1 option[value='" + usuario1 + "']").html() + '</h6>' +
        '<h6><label>Usuario 2: </label> ' + $("#slcUsuario2 option[value='" + usuario2 + "']").html() + '</h6>' +
        '<h6><label>Razón: </label> ' + razon + '</h6>' +
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
            $.ajax({
                url: 'operaciones.php?op=15',
                data: {
                    Id: $("#txtIdGuia").val(),
                    Cliente: cliente,
                    Hembras: hembra,
                    Machos: macho,
                    Usuario1: usuario1,
                    Usuario2: usuario2,
                    Razon: razon
                },
                type: 'POST',
                success: function (r) {
                    console.log(r);
                    if (r == true) {
                        Swal.fire({
                            icon: 'success',
                            html: '<h4>Solicitud generada</h4>',
                        });
                        $("#btnCerrar").click();
                    }else{
                        Swal.fire({
                            icon: 'error',
                            html: '<h4>Error:'+r+'</h4>',
                        });
                    }
                }
            });
        }
    })
}
function f_insert_solicitud_delete() {
    usuario1 = $("#slcUsuario1").val();
    usuario2 = $("#slcUsuario2").val();
    if (usuario1 == 0 || usuario2 == 0 ) {
        Swal.fire({
            icon: 'error',
            html: '<h4>Seleccione dos usuarios para que autorice la solicitud</h4>',
        });
        return;
    }
    if (usuario1 ==  usuario2 ) {
        Swal.fire({
            icon: 'error',
            html: '<h4>Los usuarios seleccionados deben ser diferentes</h4>',
        });
        return;
    }

    razon = $("#txtRazon").val();
    if (razon == '') {
        Swal.fire({
            icon: 'error',
            html: '<h4>Ingrese una razón de la modificación</h4>',
        });
        $("#txtRazon").addClass('is-invalid');
        return;
    }
    $("#txtRazon").removeClass('is-invalid');

    mensaje = '' +
        '<h5><b>Esta registrando una nueva solicitud de eliminación de comprobante de ingreso:</b></h5>' +
        '<h6><label>Usuario 1: </label> ' + $("#slcUsuario1 option[value='" + usuario1 + "']").html() + '</h6>' +
        '<h6><label>Usuario 2: </label> ' + $("#slcUsuario2 option[value='" + usuario2 + "']").html() + '</h6>' +
        '<h6><label>Razón: </label> ' + razon + '</h6>' +
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
            $.ajax({
                url: 'operaciones.php?op=17',
                data: {
                    Id: $("#txtIdGuia").val(),
                    Usuario1: usuario1,
                    Usuario2: usuario2,
                    Razon: razon
                },
                type: 'POST',
                success: function (r) {
                    console.log(r);
                    if (r == true) {
                        Swal.fire({
                            icon: 'success',
                            html: '<h4>Solicitud generada</h4>',
                        });
                        $("#btnCerrar").click();
                    }else{
                        Swal.fire({
                            icon: 'error',
                            html: '<h4>Error:'+r+'</h4>',
                        });
                    }
                }
            });
        }
    })
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
                    ganado = parseInt($(MyRows[i]).find('td:eq(1)').html());
                    hembra = parseInt($(MyRows[i]).find('td:eq(2)').html());
                    macho = parseInt($(MyRows[i]).find('td:eq(3)').html());
                    subtotal = parseInt(hembra) + parseInt(macho);
                    $total += subtotal;
                    ArrayDetalle.push([ganado,hembra,macho]);
                    $mensaje_detalle += ''+
                    '<h6>' + $(MyRows[i]).find('td:eq(0)').html() + '</h6>' +
                    '<h6> Hembras: ' + hembra + '</h6>' +
                    '<h6> Machos: ' + macho + '</h6>' +
                    '<h6>' + subtotal + '</h6>' +
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
                '<h6><label>Cliente: </label> ' +  $("#slcCliente option[value='" + cliente + "']").html() + '</h6>' +
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
    $("#btn-nueva").prop('disabled', true);

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
            // console.log(r);
            if (r==true) {
                $("#txtFechaValidez").val("");
                $("#txtCantidad").val("");
                $("#txtVacunacion").val("000000");
                $("#txtCI").val("");
                $("#txtConductor").val("");
                $("#txtPlaca").val("");
                $('#tbl_detalle > tbody').html('');
                $("#txtObservacion").val("")
                get_select_especies();
                get_select_clientes();
                get_select_provincias();
                fecha = traer_fecha();
                $("#txtGuiaNumero").val(fecha);
                $("#tdTotal").html("0");
                $("#cont-guia-3").slideUp();
                $("#cont-guia-1").slideDown("slow");
                send_data_mail_pdf();
                $("#btn-nueva").prop('disabled', false);
                Swal.fire({
                    icon: 'success',
                    html: '<h4>Datos Ingreados</h4>',
                });
            }else{ 
                $("#btn-nueva").prop('disabled', false);
                Swal.fire({
                    icon: 'error',
                    html: '<h4>'+r+'</h4>',
                });
            }
        }
    });
}

function send_data_mail_pdf() {
    $.ajax({
        url: 'operaciones.php?op=37',
        async: false,
        success: function (r) {
            send_mail_first_2(r);
            window.open("http://172.20.134.6/SIF/PDF/comprobante.php?Data="+r, '_blank');
            // window.location.href = "http://172.20.134.6/SIF/PDF/comprobante.php?Data="+r;
        }
    });
}
function send_mail_first_2(id,tipo=false) {
    $.ajax({
        data:{Id : id, Tipo: tipo},
        type:'POST',
        url: 'operaciones.php?op=36',
        async:false,
        success: function (r) {
            console.log(r);
            if (r == true) {
                Swal.fire({
                    icon: 'success',
                    html: '<h4>Correo enviado correctamente</h4>',
                });
                $("#Modal").modal("hide");
            }else{
                Swal.fire({
                    icon: 'error',
                    html: '<h4>'+r+'</h4>',
                });
                return;
            }
        }
    });
}


function cerrar_alert() {
    $(".swal2-actions .swal2-confirm").click();
}

function traer_fecha() {
    result = "";
    $.ajax({
        url: 'operaciones.php?op=23',
        async: false,
        success: function (r) {
            result = r;
        }
    });
    return result;
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
        fecha = traer_fecha();
        $("#txtGuiaNumero").val(fecha);
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
    window.open("http://172.20.134.6/SIF/PDF/comprobante.php?Data="+id, '_blank');
    // $.ajax({
    //     url: 'operaciones.php?op=13',
    //     async: false,
    //     data: {
    //         Id: id
    //     },
    //     type: 'POST',
    //     success: function (r) {
    //         inicio = '<html><head><title>VISTA PREVIA</title><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta http-equiv="x-ua-compatible" content="ie=edge">';
    //         var mywindow = window.open("", "VISTA PREVIA", "height=500px,width=800px");
    //         css = "<style>@media print {@page { margin: 0;size: auto;}</style>";
    //         mywindow.document.write(inicio);
    //         mywindow.document.write(css);
    //         mywindow.document.write("</head><body >");
    //         mywindow.document.write(r);
    //         mywindow.document.write("</body></html>");
    //         fun = setInterval(function () {
    //             mywindow.document.close();
    //             mywindow.focus();
    //             mywindow.print();
    //             mywindow.close();
    //             GET_Guias();
    //             clearInterval(fun);
    //         }, 300);
    //     }
    // });
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

////// CORREO
function get_send_mail(id) {
    f_carga('cont-modal');
    $.ajax({
        data:{Id: id},
        type:'POST',
        url: 'operaciones.php?op=35',
        success: function (r) {
            $("#cont-modal").html(r);
        }
    });
}
function ayadir_input(contenedor) {
    $(contenedor).append('<input type="text" class="form-control form-control-sm mt-1 col-md-4 mr-2 input-correo"  placeholder="Correo Electronico">');
    $('.input-correo').keydown(function (event) {
        var key = window.Event ? event.which : event.keyCode;
        if (key == 46) {
            console.log($(this).remove());
        }
    })
}


function f_send_mail() {
    if ($("#cont-para input").length < 1 ) {
        Swal.fire({
            icon: 'error',
            html: '<h4>Se debe ingresar por lo menos destinatario</h4>',
        });
    }
    ArrayPara = [];
    $("#cont-para").find("input").each(function() {
        if ($(this).val() == '') {
            $(this).addClass("is-invalid");
        }else{
            emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i; //Se muestra un texto a modo de ejemplo, luego va a ser un icono
            if (emailRegex.test($(this).val())) {
                ArrayPara.push($(this).val());
                $(this).removeClass("is-invalid");
            } else {
                $(this).addClass("is-invalid");
            }
        }
    });
    ArrayCopia = [];
    $("#cont-copia").find("input").each(function() {
        if ($(this).val() == '') {
            $(this).addClass("is-invalid");
        }else{
            emailRegex = /^[-\w.%+]{1,64}@(?:[A-Z0-9-]{1,63}\.){1,125}[A-Z]{2,63}$/i; //Se muestra un texto a modo de ejemplo, luego va a ser un icono
            if (emailRegex.test($(this).val())) {
                ArrayCopia.push($(this).val());
                $(this).removeClass("is-invalid");
            } else {
                $(this).addClass("is-invalid");
            }
        }
    });
    if ($("#cont-para input").length == ArrayPara.length && $("#cont-copia input").length  == ArrayCopia.length) {
        $("#btn-enviar-correo").prop('disabled', true);
        $("#btn-enviar-correo").html("Enviando correo...");
        $.ajax({
            data:{Id : $("#txtId-Proceso").val(), Para : ArrayPara, Copia : ArrayCopia, Observacion : $("#txtObservacion-mail").val() },
            type:'POST',
            url: 'operaciones.php?op=36',
            success: function (r) {
                console.log(r);
                if (r == true) {
                    Swal.fire({
                        icon: 'success',
                        html: '<h4>Correo enviado correctamente</h4>',
                    });
                    $("#Modal").modal("hide");
                }else{
                    Swal.fire({
                        icon: 'error',
                        html: '<h4>'+r+'</h4>',
                    });
                    $("#btn-enviar-correo").prop('disabled', false);
                    return;
                }
            }
        });
    }else{
        Swal.fire({
            icon: 'error',
            html: '<h4>Rectifique todos los errores para continuar</h4>',
        });
    }
}
