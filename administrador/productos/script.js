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
    function get_data_new_producto(id) {
        f_carga('modal-content');
        $.ajax({
            type: 'POST',
            data: {Id: id},
            url: 'operaciones.php?op=2',
            success: function (r) {
                $('#modal-content').html(r);
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
            url: 'operaciones.php?op=5',
            success: function (r) {
                $('#conttable-'+id).html(''+r);
            }
        });
    }
    function get_data_update_producto(id) {
        f_carga('modal-content');
        $.ajax({
            type: 'POST',
            data: {Id: id},
            url: 'operaciones.php?op=6',
            success: function (r) {
                $('#modal-content').html(r);
                $('.input_disablecopypaste').bind('paste', function (e) {
                    e.preventDefault();
                });
            }
        });
    }
    function get_ganchos() {
        f_carga('modal-content');
        $.ajax({
            url: 'operaciones.php?op=9',
            success: function (r) {
                $('#modal-content').html(r);
            }
        });
    }
    function get_data_new_gancho() {
        f_carga('cont-data-gancho');
        $.ajax({
            url: 'operaciones.php?op=10',
            success: function (r) {
                $('#cont-data-gancho').html(r);
                $('.input_disablecopypaste').bind('paste', function (e) {
                    e.preventDefault();
                });
            }
        });
    }
    function Cargar_Datos_table_gancho() {
        f_carga('cont-data-gancho');
        $.ajax({
            url: 'operaciones.php?op=12',
            success: function (r) {
                $('#cont-data-gancho').html(r);
            }
        });
    }
    function get_data_update_gancho(id) {
        f_carga('cont-data-gancho');
        $.ajax({
            type: 'POST',
            data: {Id:id},
            url: 'operaciones.php?op=14',
            success: function (r) {
                $('#cont-data-gancho').html(r);
                console.log(r);
                $("#btn-nuevo").html("<b>CANCELAR</b>");
                $('.input_disablecopypaste').bind('paste', function (e) {
                    e.preventDefault();
                });
                $("#btn-nuevo").click(function () {
                    $("#btn-nuevo").html('<b onclick="get_data_new_gancho()" >AÑADIR GANCHO</b>');
                    $('#cont-data-gancho').html('');
                })
            }
        });
    }
    
//Insertar 
    function f_new_producto() {
        datos = ["#txtProducto","#txtCodigo","#txtFecha","txtConservar"];
        if (comprobar_vacios(datos)) {
            producto =$("#txtProducto").val();
            codigo =$("#txtCodigo").val();
            parte = $("#slcParte").val();
            tiempo = $("#txtFecha").val();
            conservar = $("#txtConservar").val();
            mensaje = '' +
                '<h5><b>Esta ingresando un nuevo producto:</b></h5>' +
                '<h6><label>Nombre del Producto: </label> ' + producto + '</h6>' +
                '<h6><label>Código: </label> ' + codigo + '</h6>' +
                '<h6><label>Parte:</label> '+ parte + ' </h6>' +
                '<h6><label>Tiempo de caducidad:</label> '+ tiempo + ' días </h6>' +
                '<h6><label>Conservar a:</label> '+ conservar + ' °C </h6>' +
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
                        data: {Id : $("#txtIdEspecie").val(), Producto : producto, Codigo : codigo, Parte : parte ,Tiempo :tiempo ,Conservar :conservar,Observaciones : $("#txtObservacion").val()},
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
    function f_new_gancho() {
        datos = ["#txtDescripcion","#txtDescuento"];
        if (comprobar_vacios(datos)) {
            gancho =$("#txtDescripcion").val();
            descuento =$("#txtDescuento").val();
            mensaje = '' +
                '<h5><b>Esta ingresando un nuevo gancho:</b></h5>' +
                '<h6><label>Descripción: </label> ' + gancho + '</h6>' +
                '<h6><label>Descuento: </label> ' + descuento + '</h6>' +
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
                        data: { Gancho : gancho, Descuento : descuento, Observaciones : $("#txtObservacion").val()},
                        success: function (r) {
                            if(r==true){
                                Cargar_Datos_table_gancho();
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
    function f_estado_producto(estado, id,especie) {
        if (estado == 0) {
            ht = "<h4>¿ESTA SEGURO DE ACTIVAR EL SERVICIO ?</h4>";
        }
        if (estado == 1) {
            ht = "<h4>¿ESTA SEGURO DE DESACTIVAR EL SERVICIO ?</h4>";
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
                    url: "operaciones.php?op=4",
                    data: {
                        Estado: estado,
                        Id: id
                    },
                    type: 'POST',
                    success: function (r) {
                        if (r == true) {
                            Cargar_Datos_table(especie);
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
    function f_predeterminado(estado, id,especie) {
        if (estado == 0) {
            ht = "<h5>¿ESTA SEGURO QUE DESEA ELIMINAR ESTE PRODUCTO COMO PREDETERMINADO PARA ESTA ESPECIE?</h5>"+
            "<h6 class='text-muted'>Cuando se detalle individualmente una guia de proceso no se usará este producto como predeterminado</h6>";
        }
        if (estado == 1) {
            ht = "<h5>¿ESTA SEGURO QUE DESEA AÑADIR ESTE PRODUCTO COMO PREDETERMINADO PARA ESTA ESPECIE?</h5>"+
            "<h6 class='text-muted'>Cuando se detalle individualmente una guia de proceso se usará este producto como predeterminado</h6>";
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
                            Cargar_Datos_table(especie);
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
    function f_estado_gancho(estado, id) {
        if (estado == 0) {
            ht = "<h5>¿ESTA SEGURO QUE DESEA ACTIVAR ESTE GANCHO?</h5>";
        }
        if (estado == 1) {
            ht = "<h5>¿ESTA SEGURO QUE DESEA INACTIVAR ESTE GANCHO?</h5>";
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
                    url: "operaciones.php?op=13",
                    data: {
                        Estado: estado,
                        Id: id
                    },
                    type: 'POST',
                    success: function (r) {
                        if (r == true) {
                            Cargar_Datos_table_gancho();
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
    function f_update_producto() {
        datos = ["#txtProducto","#txtCodigo","#txtFecha","txtConservar"];
            if (comprobar_vacios(datos)) {
                producto =$("#txtProducto").val();
                codigo =$("#txtCodigo").val();
                parte = $("#slcParte").val();
                tiempo = $("#txtFecha").val();
                conservar = $("#txtConservar").val();
                mensaje = '' +
                    '<h5><b>Esta ingresando un nuevo producto:</b></h5>' +
                    '<h6><label>Nombre del Producto: </label> ' + producto + '</h6>' +
                    '<h6><label>Código: </label> ' + codigo + '</h6>' +
                    '<h6><label>Parte:</label> '+ parte + ' </h6>' +
                    '<h6><label>Tiempo de caducidad:</label> '+ tiempo + ' días </h6>' +
                    '<h6><label>Conservar a:</label> '+ conservar + ' °C </h6>' +
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
                            data: {Id : $("#txtIdProducto").val(), Producto : producto, Codigo : codigo, Parte : parte ,Tiempo :tiempo ,Conservar: conservar,Observaciones : $("#txtObservacion").val()},
                            success: function (r) {
                                if(r==true){
                                    Cargar_Datos_table($("#txtIdEspecie").val());
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
    function f_update_gancho() {
        datos = ["#txtDescripcion","#txtDescuento"];
        if (comprobar_vacios(datos)) {
            gancho =$("#txtDescripcion").val();
            descuento =$("#txtDescuento").val();
            mensaje = '' +
                '<h5><b>Esta actualizando un gancho:</b></h5>' +
                '<h6><label>Descripción: </label> ' + gancho + '</h6>' +
                '<h6><label>Descuento: </label> ' + descuento + '</h6>' +
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
                        url: "operaciones.php?op=15",
                        type: 'POST',
                        data: {Id: $("#txtIdGancho").val(), Gancho : gancho, Descuento : descuento, Observaciones : $("#txtObservacion").val()},
                        success: function (r) {
                            if(r==true){
                                Cargar_Datos_table_gancho();
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
    function onlynumber_nega(e) {
        cant = $("#txtConservar").val();
        var key = window.Event ? e.which : e.keyCode;
        if(cant.length==0){
            if (key < 44 || key > 57 ) e.preventDefault(); 
            else {
                if (key == 46 || key == 47) e.preventDefault(); 
            }
        }else{
            if (key < 48 || key > 57) e.preventDefault(); 
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
    function handleNumber(event, mask) {
        /* numeric mask with pre, post, minus sign, dots and comma as decimal separator
            {}: positive integer
            {10}: positive integer max 10 digit
            {,3}: positive float max 3 decimal
            {10,3}: positive float max 7 digit and 3 decimal
            {null,null}: positive integer
            {10,null}: positive integer max 10 digit
            {null,3}: positive float max 3 decimal
            {-}: positive or negative integer
            {-10}: positive or negative integer max 10 digit
            {-,3}: positive or negative float max 3 decimal
            {-10,3}: positive or negative float max 7 digit and 3 decimal
        */
        with(event) {
            stopPropagation()
            preventDefault()
            if (!charCode) return
            var c = String.fromCharCode(charCode)
            if (c.match(/[^-\d,]/)) return
            with(target) {
                var txt = value.substring(0, selectionStart) + c + value.substr(selectionEnd)
                var pos = selectionStart + 1
            }
        }
        var dot = count(txt, /\./, pos)
        txt = txt.replace(/[^-\d,]/g, '')
    
        var mask = mask.match(/^(\D*)\{(-)?(\d*|null)?(?:,(\d+|null))?\}(\D*)$/);
        if (!mask) return // meglio exception?
        var sign = !!mask[2],
            decimals = +mask[4],
            integers = Math.max(0, +mask[3] - (decimals || 0))
        if (!txt.match('^' + (!sign ? '' : '-?') + '\\d*' + (!decimals ? '' : '(,\\d*)?') + '$')) return
    
        txt = txt.split(',')
        if (integers && txt[0] && count(txt[0], /\d/) > integers) return
        if (decimals && txt[1] && txt[1].length > decimals) return
        txt[0] = txt[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.')
    
        with(event.target) {
            value = mask[1] + txt.join(',') + mask[5]
            selectionStart = selectionEnd = pos + (pos == 1 ? mask[1].length : count(value, /\./, pos) - dot)
        }
    
        function count(str, c, e) {
            e = e || str.length
            for (var n = 0, i = 0; i < e; i += 1)
                if (str.charAt(i).match(c)) n += 1
            return n
        }
    }
