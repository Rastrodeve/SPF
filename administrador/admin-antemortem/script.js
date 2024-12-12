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
    function get_new() {
        f_carga('modal-content');
        $.ajax({
            url: 'operaciones.php?op=2',
            success: function (r) {
                $('#modal-content').html(r);
            }
        });
    }
    function get_update(id) {
        f_carga('modal-content');
        $.ajax({
            type:'POST',
            data:{Id: id},
            url: 'operaciones.php?op=4',
            success: function (r) {
                $('#modal-content').html(r);
            }
        });
    } 
    function get_new_item(id) {
        f_carga('modal-content');
        $.ajax({
            type:'POST',
            data:{Id: id},
            url: 'operaciones.php?op=7',
            success: function (r) {
                $('#modal-content').html(r);
            }
        });
    } 
    function get_data_update_item(id) {
        f_carga('modal-content');
        $.ajax({
            type:'POST',
            data:{Id: id},
            url: 'operaciones.php?op=10',
            success: function (r) {
                $('#modal-content').html(r);
            }
        });
    }  
//Insertar 
    function f_new_cabecera() {
        datos = ["#txtCabecera"];
        if (comprobar_vacios(datos)) {
            cabecera =$("#txtCabecera").val();
            mensaje = '' +
                '<h5><b>Esta ingresando una nueva cabecera:</b></h5>' +
                '<h6><label>Descripción: </label> ' + cabecera + '</h6>' +
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
                        data: {Descripcion : cabecera},
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
    function f_new_item() {
        datos = ["#txtItem"];
        if (comprobar_vacios(datos)) {
            cabecera =$("#txtItem").val();
            mensaje = '' +
                '<h5><b>Esta agregando un nuevo item:</b></h5>' +
                '<h6><label>Descripción del item: </label> ' + cabecera + '</h6>' +
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
                        data: {Id: $('#txtIdCabecera').val(),Descripcion : cabecera},
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
//Cambiar estado
    function f_estado(estado, id) {
        if (estado == 0) {
            ht = "<h4>¿ESTA SEGURO DE ACTIVAR ACTIVAR LA CABECERA ?</h4>";
        }
        if (estado == 1) {
            ht = "<h4>¿ESTA SEGURO DE DESACTIVAR LA CABECERA ?</h4>";
        }
        Swal.fire({
            icon: 'info',
            html: ht,
            showDenyButton: true,
            confirmButtonText: 'SI, CONFIRMAR',
            denyButtonText: 'NO, CANCELAR',
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $.ajax({
                    url: "operaciones.php?op=6",
                    data: {
                        Estado: estado,
                        Id: id
                    },
                    type: 'POST',
                    success: function (r) {
                        if (r == true) {
                            Cargar_Datos();
                            Swal.fire({
                                icon: 'success',
                                html: '<h4>DATOS ACTUALIZADOS</h4>',
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                html: '<h4><b>Error: </b> ' + r + '</h4>',
                            });
                        }
                    }
                });
            }
        });
    }
    function f_estado_item(estado, id) {
        if (estado == 0) {
            ht = "<h4>¿ESTA SEGURO DE ACTIVAR ACTIVAR EL ITEM ?</h4>";
        }
        if (estado == 1) {
            ht = "<h4>¿ESTA SEGURO DE DESACTIVAR EL ITEM ?</h4>";
        }
        Swal.fire({
            icon: 'info',
            html: ht,
            showDenyButton: true,
            confirmButtonText: 'SI, CONFIRMAR',
            denyButtonText: 'NO, CANCELAR',
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $.ajax({
                    url: "operaciones.php?op=9",
                    data: {
                        Estado: estado,
                        Id: id
                    },
                    type: 'POST',
                    success: function (r) {
                        if (r == true) {
                            Cargar_Datos();
                            Swal.fire({
                                icon: 'success',
                                html: '<h4>DATOS ACTUALIZADOS</h4>',
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                html: '<h4><b>Error: </b> ' + r + '</h4>',
                            });
                        }
                    }
                });
            }
        });
    }
    
//Actualizar
    function f_update_cabecera() {
        datos = ["#txtCabecera"];
        if (comprobar_vacios(datos)) {
            cabecera =$("#txtCabecera").val();
            mensaje = '' +
                '<h5><b>Esta editando una cabecera:</b></h5>' +
                '<h6><label>Nueva descripción: </label> ' + cabecera + '</h6>' +
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
                        url: "operaciones.php?op=5",
                        type: 'POST',
                        data: {Id: $('#txtIdCabecera').val(),Descripcion : cabecera},
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
    function f_update_item() {
        datos = ["#txtItem"];
        if (comprobar_vacios(datos)) {
            cabecera =$("#txtItem").val();
            mensaje = '' +
                '<h5><b>Esta editando un item:</b></h5>' +
                '<h6><label>Nueva descripción del item: </label> ' + cabecera + '</h6>' +
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
                        url: "operaciones.php?op=11",
                        type: 'POST',
                        data: {Id: $('#txtIdItem').val(),Descripcion : cabecera},
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
