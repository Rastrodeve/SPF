
$(document).ready(function () {

});

$("#btn-buscar-turno").click(function () {
    guia = $("#txt-busqueda-turno").val();
    f_carga('cont-table-data-turno');
    $.ajax({
        type:'POST',
        data:{Guia:guia},
        url: 'operaciones_turnos.php?op=1',
        success: function (r) {
            console.log(r)
            console.log("Hola")
            $("#cont-table-data-turno").html(r);
            // confi_tabla('tbl_data_table');
        }
    });
})

function select_data_guia(id) {
    console.log(id)
    $.ajax({
        type:'POST',
        data:{Guia:id, Turno:turno, Cliente: cliente},
        url: 'operaciones_turnos.php?op=2',
        success: function (r) {
            console.log(r)
            $("#cont-table-data-turno").html(r);
            // confi_tabla('tbl_data_table');
        }
    });
}
function continuar_f() {
    data_s = f_verificar()
    if ( data_s.length < 1) {
        Swal.fire({
            icon: 'error',
            html: '<h4>Error: Complete todos los campos para continuar</h4>',
        });
        return
    }
    Swal.fire({
        title: 'Verifique la información para continuar',
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
                type:'POST',
                data:{Guia:$("#txtGuia-Ingreso").val(), Data: data_s},
                url: 'operaciones_turnos.php?op=3',
                success: function (r) {
                    if( $('#cbxPredio').is(':checked')) {
                        $("#txt-busqueda-turno").val($("#txt-nuevo_guia_numero").val());
                        // console.log($("#txt-nuevo_guia_numero").val());
                    }
                    $("#cont-table-data-turno").html(r);
                    // confi_tabla('tbl_data_table');
                }
            });
        }
    })
}
function f_verificar() {
    data = ["txtFechaGuia","txtFechaValidez2","slcProvincia2","txtVacunacion2","txtCI2","txtConductor2","slcVehiculo2","txtPlaca2"];
    cont = 0;
    result = [];
    for (let index = 0; index < data.length; index++) {
        if ($("#" + data[index]).val() == "") {
            $("#" + data[index]).addClass("is-invalid");
            $("#slcGanado-span-seleceted").addClass("is-invalid");
            cont++;
        } else {
            $("#" + data[index]).removeClass("is-invalid");
            $("#slcGanado-span-seleceted").removeClass("is-invalid");
            result.push($("#" + data[index]).val())
        }
    }
    cont2 = 0;
    if( $('#cbxPredio').is(':checked')) {
        cont2 = 1;
        guia_nueva = $("#txt-nuevo_guia_numero").val();
        if (guia_nueva == '') {
            $("#txt-nuevo_guia_numero").addClass("is-invalid");
        }else{
            $("#txt-nuevo_guia_numero").removeClass("is-invalid");
            result.push(guia_nueva)
        }
    }
    if ((result.length - (data.length+ cont2)) != 0)return [];
    return result;
}



function corral_data(id) {
    Swal.fire({
        title: '¿Esta seguro de habilitar el turno?',
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
                type:'POST',
                data:{Guia:id, GuiaSave: $("#txt_input_guia_nueva").val()},
                url: 'operaciones_turnos.php?op=4',
                success: function (r) {
                    // $("#cont-table-data-turno").html(r);
                    if (r == true) {
                        // alert("Se debe imprimir el comprobante y mostrar la parte para registar el corralaje ("+id)
                        window.open("http://172.20.134.6/SIF/PDF/comprobante.php?Data="+id, '_blank');
                        $("#btn-cerrar-modal_prueba").click();
                        send_mail_first_2(id);
                        $("#Modal").modal();
                        get_view_corrales(id);
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

function f_guardar_turno() {
    $id = $("#txtInputGuiaProcesoCorral").val()
    Swal.fire({
        title: 'Esta seguro que desea continuar',
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
                url: 'operaciones_turnos.php?op=5',
                data: {
                    Id: $id,
                    Corral: $("#slcCorralNuevo").val(),
                    Cantidad: $("#slcCorralNuevoCantidad").val()
                },
                type: 'POST',
                success: function (r) {
                    $("#btn-cerrar-modal_prueba").click();
                    console.log(r);
                    if (r == true) {
                        Swal.fire({
                            icon: 'success',
                            html: '<h4>Turno Habilitado</h4>',
                        });
                        window.location.href = "http://172.20.134.6/SPF/PDF/comprobante.php?Data="+$id;
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




// -------------------------------------------------------------

function my_select_style_mobile(id,b,tam) {
    data = '';
    $("#"+id).addClass("d-none");
    $("#"+id).after('<span class="form-control form-control-'+tam+'" style="cursor:pointer" id="'+id+'-span-seleceted" onclick="open_modal(\''+id+ '\','+b+')"  >'+
                $( "#"+id+' option[value="'+$('#'+id).val()+'"]').html()+
                    '</span>');
    
    $("body").append(my_select_style_mobile_generate_modal(id));
}

function open_modal(id,b) {
    data = '';
    $("#"+id+"-Modal").modal();
    var option = $("#"+id).find('option');
    for (var i = 0; i < option.length; i++){
        selected = '';
        if ($(option[i]).val() == $('#'+id).val()) selected = 'select_active';
        data += my_select_style_mobile_generate_option($(option[i]).val(),$(option[i]).html(),selected,id);
    }
    buscador = '';
    if (b == 1) {
        buscador =   '<div class="p-2 header-buscador" ><div class="input-group input-group-lg">'+
                        '<div class="input-group-prepend bg-light">'+
                            '<span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>'+
                        '</div>'+
                        '<input type="search" class="form-control" id="'+id+'-input-buscar" name="'+id+'-input-buscar"  onkeyup="my_select_style_mobile_funtion_buscar(\''+id+'\')"  placeholder="Buscar" >'+
                    '</div></div>';
    }
    $("#"+id+"-Modal .modal-body").html(buscador+data);
    $("#"+id+"-input-buscar").focus();

}

function my_select_style_mobile_funtion_buscar(id) {
    var buscar = $("#"+id+"-input-buscar").val();
    var span = $("#"+id+'-Modal').find('.modal-body').find('span');
    buscar = buscar.toLocaleUpperCase();
    for (let index = 0; index < span.length; index++) {
        variable = $(span[index]).data("buscar")+'';
        variable = variable.toLocaleUpperCase();
        if (!variable.includes(buscar))$(span[index]).addClass("d-none");
        else $(span[index]).removeClass("d-none")
    }
}

function my_select_style_mobile_generate_modal(id) {
    return ' <div class="modal fade" id="'+id+'-Modal" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle"'+
    'aria-hidden="true">'+
    '<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">'+
        '<div class="modal-content">'+
            '<div class="modal-body p-0" >'+
            '</div>'+
        '</div>'+
    '</div>'+
'</div>';
}


function my_select_style_mobile_generate_option(value,option,selected,id) {
    return '<span data-value="'+value+'"  data-buscar="'+option+'"   class="form-control form-control-lg rounded-0 select-span '+selected+'" onclick="my_select_style_mobile_funcion_click(\''+value+'\',\''+ id +'\')"  >'+option+'</span>';
}
function my_select_style_mobile_funcion_click(value,id) {
    $('#'+id).val(value);
    $( "#"+id+' option[value="'+value+'"]').attr('selected', 'selected');;
    $("#"+id+'-span-seleceted').html($( "#"+id+' option[value="'+$('#'+id).val()+'"]').html());
    $('#'+id+'-Modal').modal('hide');
    $("#"+id+'-Modal .modal-body span').removeClass('select_active');
    var span = $("#"+id+'-Modal').find('.modal-body').find('span');
    for (let index = 0; index < span.length; index++) {
        if (value == $(span[index]).data("value")) $(span[index]).addClass("select_active");
    }
    $('#'+id).val(value).change();
}
// -----------------------------------------------

function select_checkbox() {
    if( $('#cbxPredio').is(':checked')) {
        $("#cont-nuevo_numero_guia").slideDown();
    }else{
        $("#cont-nuevo_numero_guia").slideUp();
    }
}