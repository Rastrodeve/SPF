$(document).ready(function () {
    $('.cont-carga').addClass('d-none');
    Cargar_Datos();
    // $('.select2bs4').select2();
});

// Traer informacion
    function Cargar_Datos() {
        f_carga('cont-body');
        $.ajax({
            url: 'operaciones.php?op=1',
            success: function (r) {
                $('#cont-body').html(r);
                confi_tabla('table-data');
            }
        });
    }
    function get_data_new() {
        f_carga('modal-content');
        $.ajax({
            url: 'operaciones.php?op=2',
            success: function (r) {
                $('#modal-content').html(r);
            }
        });
    }
    function get_data_update(id) {
        f_carga('modal-content');
        $.ajax({
            type:'POST',
            data: {Id: id},
            url: 'operaciones.php?op=5',
            success: function (r) {
                $('#modal-content').html(r);
            }
        });
    }
    function get_data_vehiculo(id) {
        f_carga('modal-content');
        $.ajax({
            type:'POST',
            data: {Id: id},
            url: 'operaciones.php?op=7',
            success: function (r) {
                $('#modal-content').html(r);
                confi_tabla("table-data-vehidulo");
            }
        });
    }
    function Cargar_Datos_vehiculo(){
        f_carga('cont-table');
        $.ajax({
            type:'POST',
            data:{Id : $("#txtTransportista").val()},
            url: 'operaciones.php?op=9',
            success: function (r) {
                $('#cont-table').html(r);
                confi_tabla('table-data-vehidulo');
            }
        });
    }
    function get_data_new_vehiculo(){
        f_carga('cont-table');
        $.ajax({
            url: 'operaciones.php?op=10',
            success: function (r) {
                $('#cont-table').html(r);
            }
        });
    }
    function get_data_update_vehiculo(id){
        f_carga('cont-table');
        $.ajax({
            type : 'POST',
            data: {Id: id},
            url: 'operaciones.php?op=12',
            success: function (r) {
                $('#cont-table').html(r);
            }
        });
    }
    
    
//Insertar 
    function f_new() {
        datos = ["#txtDescripcion","#txtIdentificacion"];
        if (comprobar_vacios(datos)) {
            nombre =$("#txtDescripcion").val();
            iden =$("#txtIdentificacion").val();
            mensaje = '' +
                '<h5><b>Esta ingresando un nuevo transportista:</b></h5>' +
                '<h6><label>Identificación: </label> ' + iden + '</h6>' +
                '<h6><label>Razón social: </label> ' + nombre + '</h6>' +
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
                        data: {Identificacion: iden,Nombre: nombre},
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
    function f_new_vehiculo() {
        datos = ["#txtPlaca"];
        if (comprobar_vacios(datos)) {
            placa =$("#txtPlaca").val();
            mensaje = '' +
                '<h5><b>Esta ingresando un nuevo Vehículo:</b></h5>' +
                '<h6><label>Placa: </label> ' + placa + '</h6>' +
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
                        data: {Placa: placa,Id: $('#txtTransportista').val()},
                        success: function (r) {
                            if(r==true){
                                Cargar_Datos();
                                Cargar_Datos_vehiculo();
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
            ht = "<h4>¿ESTA SEGURO QUE DESEA ACTIVAR EL TRANSPORTISTA ?</h4>";
        }
        if (estado == 1) {
            ht = "<h4>¿ESTA SEGURO QUE DESEA DESACTIVAR EL TRANSPORTISTA ?</h4>";
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
    function f_update() {
        datos = ["#txtDescripcion","#txtIdentificacion"];
        if (comprobar_vacios(datos)) {
            nombre =$("#txtDescripcion").val();
            iden =$("#txtIdentificacion").val();
            mensaje = '' +
                '<h5><b>Esta ingresando un nuevo transportista:</b></h5>' +
                '<h6><label>Identificación: </label> ' + iden + '</h6>' +
                '<h6><label>Razón social: </label> ' + nombre + '</h6>' +
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
                        url: "operaciones.php?op=6",
                        type: 'POST',
                        data: {Identificacion: iden,Nombre: nombre,Id : $("#txtTransportista").val()},
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
    function f_estado_2(estado, id) {
        if (estado == 0) {
            ht = "<h4>¿ESTA SEGURO QUE DESEA ACTIVAR EL VEHICULO ?</h4>";
        }
        if (estado == 1) {
            ht = "<h4>¿ESTA SEGURO QUE DESEA DESACTIVAR EL VEHICULO ?</h4>";
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
                            Cargar_Datos();
                            Cargar_Datos_vehiculo();
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
    function f_update_heviculo() {
        datos = ["#txtPlaca"];
        if (comprobar_vacios(datos)) {
            placa =$("#txtPlaca").val();
            mensaje = '' +
                '<h5><b>Esta actualizando un nuevo Vehículo:</b></h5>' +
                '<h6><label>Nueva Placa: </label> ' + placa + '</h6>' +
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
                        url: "operaciones.php?op=13",
                        type: 'POST',
                        data: {Placa: placa,Id: $('#txtVehiculo').val()},
                        success: function (r) {
                            if(r==true){
                                Cargar_Datos();
                                Cargar_Datos_vehiculo();
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
