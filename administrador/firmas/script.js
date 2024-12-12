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
    function get_data_new_firma(id) {
        f_carga('modal-content');
        $.ajax({
            type: 'POST',
            data: {Id: id},
            url: 'operaciones.php?op=2',
            success: function (r) {
                $('#modal-content').html(r);
                $('.select2bs4').select2();
                $('.input_disablecopypaste').bind('paste', function (e) {
                    e.preventDefault();
                });
            }
        });
    }
    function get_data_update_firma(id) {
        f_carga('modal-content');
        $.ajax({
            type: 'POST',
            data: {Id: id},
            url: 'operaciones.php?op=4',
            success: function (r) {
                $('#modal-content').html(r);
                $('.select2bs4').select2();
                $('.input_disablecopypaste').bind('paste', function (e) {
                    e.preventDefault();
                });
            }
        });
    }
    function Cargar_Datos_table(id) {
        f_carga('conttable-'+id);
        $.ajax({
            type : 'POST',
            data : { Id:id},
            url: 'operaciones.php?op=7',
            success: function (r) {
                $('#conttable-'+id).html(''+r);
            }
        });
    }
    function get_data_update_mover(id) {
        f_carga('modal-content');
        $.ajax({
            type: 'POST',
            data: {Id: id},
            url: 'operaciones.php?op=9',
            success: function (r) {
                $('#modal-content').html(r);
                $('.select2bs4').select2();
            }
        });
    }
    
//Insertar 
    function f_new_firma() {
        datos = ["#txtNombre","#txtDescripxion1"];
        if (comprobar_vacios(datos)) {
            nombre =$("#txtNombre").val();
            desc1 =$("#txtDescripxion1").val();
            desc2 = $("#txtDescripxion2").val();
            orden = $("#slcOrden").val();
            mensaje = '' +
                '<h5><b>Esta ingresando una nueva firma:</b></h5>' +
                '<h6><label>Nombre:  </label> ' + nombre + '</h6>' +
                '<h6><label>Descrición 2: </label> ' + desc1 + '</h6>' +
                '<h6><label>Descrición 2:</label> '+ desc2 + ' </h6>' +
                '<h6><label>Orden:</label> '+ orden + ' días </h6>' +
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
                        data: {Id : $("#txtId").val(), Nombre : nombre, Descrip1 : desc1, Descrip2 : desc2 ,Orden :orden},
                        success: function (r) {
                            if(r==true){
                                Cargar_Datos_table($("#txtId").val());
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
    function f_estado_firma(estado, id, tipo) {
        if (estado == 0) {
            ht = "<h4>¿ESTA SEGURO QUE DESEA ACTIVAR LA FIRMA?</h4>";
        }
        if (estado == 1) {
            ht = "<h4>¿ESTA SEGURO QUE DESEA DESACTIVAR LA FIRMA?</h4>";
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
                            Cargar_Datos_table(tipo);
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
    function f_estado_ignorar(estado, id, tipo) {
        if (estado == 0) {
            ht = "<h4>¿IGNORAR FIRMA?</h4><h6>Toda la información de esta <b>firma</b> será tomada en cuenta al momento de generar el reporte</h6>";
        }
        if (estado == 1) {
            ht = "<h4>¿IGNORAR FIRMA?</h4><h6>Solo se tomará en cuenta la información de <b>Descripción 2</b><br>El nombre y la descripción 1 pertenecerán al nombre y cargo del usuario que genere el reporte</h6>";
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
                    url: "operaciones.php?op=8",
                    data: {
                        Estado: estado,
                        Id: id
                    },
                    type: 'POST',
                    success: function (r) {
                        if (r == true) {
                            Cargar_Datos_table(tipo);
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
    function f_update_firma() {
        datos = ["#txtNombre","#txtDescripxion1"];
        if (comprobar_vacios(datos)) {
            nombre =$("#txtNombre").val();
            desc1 =$("#txtDescripxion1").val();
            desc2 = $("#txtDescripxion2").val();
            orden = $("#slcOrden").val();
            mensaje = '' +
                '<h5><b>Esta actualizando una firma:</b></h5>' +
                '<h6><label>Nombre:  </label> ' + nombre + '</h6>' +
                '<h6><label>Descrición 2: </label> ' + desc1 + '</h6>' +
                '<h6><label>Descrición 2:</label> '+ desc2 + ' </h6>' +
                '<h6><label>Orden:</label> '+ orden + ' días </h6>' +
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
                        data: {Id : $("#txtId").val(), Nombre : nombre, Descrip1 : desc1, Descrip2 : desc2 ,Orden :orden},
                        success: function (r) {
                            if(r==true){
                                Cargar_Datos_table($("#txtIdTipo").val());
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
    function f_move_firma(id) {
        tipo=$("#slcTipo").val();
        ht = "<h4>¿ESTA SEGURO QUE DESEA CAMBIAR ESTA A FIRMNA A <b>"+$("#slcTipo option[value="+tipo+"]").html();+"</b>?</h4>";
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
                    url: "operaciones.php?op=10",
                    data: {
                        Tipo: tipo,
                        Id: id
                    },
                    type: 'POST',
                    success: function (r) {
                        if (r == true) {
                            Cargar_Datos();
                            $("#btnCerrar").click();
                            $('#modal-content').html('');
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
