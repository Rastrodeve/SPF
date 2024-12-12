$(document).ready(function () {
    $('.cont-carga').addClass('d-none');
    Cargar_Datos();
    // $('.select2bs4').select2();
});

// Traer informacion
    function Cargar_Datos() {
        f_carga('cont-datos');
        $.ajax({
            url: 'operaciones.php?op=1',
            success: function (r) {
                $('#cont-datos').html(r);
            }
        });
    }

    function get_data_new() {
        f_carga('modal-content');
        $.ajax({
            url: 'operaciones.php?op=2',
            success: function (r) {
                $('#modal-content').html(r);
                $('.input_disablecopypaste').bind('paste', function (e) {
                    e.preventDefault();
                });
            }
        });
    }
    function get_data_op(op,id) {
        f_carga('modal-content');
        $.ajax({
            type: 'POST',
            data: {Id: id},
            url: 'operaciones.php?op='+op,
            success: function (r) {
                $('#modal-content').html(r);
                $('.input_disablecopypaste').bind('paste', function (e) {
                    e.preventDefault();
                });
            }
        });
    }

    
//Insertar 
    function f_new() {
        datos = ["#txtEmpresa","#txtLocalidad","#txtCaja"];
        if (comprobar_vacios(datos)) {
            empresa =$("#txtEmpresa").val();
            localidad =$("#txtLocalidad").val();
            caja = $("#txtCaja").val();
            mensaje = '' +
                '<h5><b>Nuevo configuración yupak:</b></h5>' +
                '<h6><label>Código de la empresa: </label> ' + empresa + '</h6>' +
                '<h6><label>Código de localidad: </label> ' + localidad + '</h6>' +
                '<h6><label>Número de caja:</label> '+ caja + ' </h6>' +
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
                        url: "operaciones.php?op=3",
                        type: 'POST',
                        data: { Empresa : empresa, Localidad : localidad, Caja : caja },
                        success: function (r) {
                            if(r==true){
                                Cargar_Datos();
                                $("#btnCerrar").click();
                                $('#modal-content').html('');
                                Swal.fire({
                                    icon: 'success',
                                    html: '<h4>Datos Actualizados</h4>',
                                });
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    html: '<h4><b>Error: </b> ' + r + '</h4>',
                                });
                            }
                            
                        }
                    });
                }
            });
        }else{
            Swal.fire({
                icon: 'error',
                html: '<h6>Complete todos los campos para continuar</h6>',
            });
        }
    }
//Actualizar
function f_update_empr() {
    datos = ["#txtEmpresa"];
    if (comprobar_vacios(datos)) {
        empresa =$("#txtEmpresa").val();
        mensaje = '' +
            '<h5><b>Cambio del Código de la empresa:</b></h5>' +
            '<h6><label>Nuevo código de la empresa: </label> ' + empresa + '</h6>' +
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
                    url: "operaciones.php?op=7",
                    type: 'POST',
                    data: {Id : $("#txtId").val(), Variable : empresa},
                    success: function (r) {
                        if(r==true){
                            $("#btnCerrar").click();
                            $('#modal-content').html('');
                            Cargar_Datos();
                            Swal.fire({
                                icon: 'success',
                                html: '<h4>Datos Actualizados</h4>',
                            });
                        }else{
                            Swal.fire({
                                icon: 'error',
                                html: '<h4><b>Error: </b> ' + r + '</h4>',
                            });
                        }
                        
                    }
                });
            }
        });
    }else{
        Swal.fire({
            icon: 'error',
            html: '<h6>Complete todos los campos para continuar</h6>',
        });
    }
}
function f_update_loca() {
    datos = ["#txtLocalidad"];
    if (comprobar_vacios(datos)) {
        variable =$("#txtLocalidad").val();
        mensaje = '' +
            '<h5><b>Cambio del Código de localidad:</b></h5>' +
            '<h6><label>Nuevo código de localidad: </label> ' + variable + '</h6>' +
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
                    url: "operaciones.php?op=8",
                    type: 'POST',
                    data: {Id : $("#txtId").val(), Variable : variable},
                    success: function (r) {
                        if(r==true){
                            $("#btnCerrar").click();
                            $('#modal-content').html('');
                            Cargar_Datos();
                            Swal.fire({
                                icon: 'success',
                                html: '<h4>Datos Actualizados</h4>',
                            });
                        }else{
                            Swal.fire({
                                icon: 'error',
                                html: '<h4><b>Error: </b> ' + r + '</h4>',
                            });
                        }
                        
                    }
                });
            }
        });
    }else{
        Swal.fire({
            icon: 'error',
            html: '<h6>Complete todos los campos para continuar</h6>',
        });
    }
}
function f_update_caja() {
    datos = ["#txtCaja"];
    if (comprobar_vacios(datos)) {
        variable =$("#txtCaja").val();
        mensaje = '' +
            '<h5><b>Cambio del Número de caja:</b></h5>' +
            '<h6><label>Nuevo número de caja: </label> ' + variable + '</h6>' +
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
                    url: "operaciones.php?op=9",
                    type: 'POST',
                    data: {Id : $("#txtId").val(), Variable : variable},
                    success: function (r) {
                        if(r==true){
                            $("#btnCerrar").click();
                            $('#modal-content').html('');
                            Cargar_Datos();
                            Swal.fire({
                                icon: 'success',
                                html: '<h4>Datos Actualizados</h4>',
                            });
                        }else{
                            Swal.fire({
                                icon: 'error',
                                html: '<h4><b>Error: </b> ' + r + '</h4>',
                            });
                        }
                        
                    }
                });
            }
        });
    }else{
        Swal.fire({
            icon: 'error',
            html: '<h6>Complete todos los campos para continuar</h6>',
        });
    }
}

//Otras funciones
    function onlynumber(e) {
        var key = window.Event ? e.which : e.keyCode;
        if (key < 48 || key > 57) e.preventDefault(); 
    }

    function comprobar_vacios(datos) {
        cont=0;
        for (i=0; i < datos.length; i++) {
            if ($(datos[i]).val()==''){
                cont++;
                $(datos[i]).addClass("is-invalid");
            }else $(datos[i]).removeClass("is-invalid");
        }
        if (cont==0)return true;
        else return false;
    }
