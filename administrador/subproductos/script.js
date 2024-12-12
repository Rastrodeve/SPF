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
    function get_data_parentesco(id) {
        f_carga('modal-content');
        $.ajax({
            type: 'POST',
            data: {Id: id},
            url: 'operaciones.php?op=8',
            success: function (r) {
                $('#modal-content').html(r);
            }
        });
    }
    
    
//Insertar 
    function f_new_producto() {
        datos = ["#txtProducto","#txtCodigo"];
        if (comprobar_vacios(datos)) {
            producto =$("#txtProducto").val();
            codigo =$("#txtCodigo").val();
            parte = $("#slcParte").val();
            para = $("#slcSexo").val();
            mensaje = '' +
                '<h5><b>Esta ingresando un nuevo subproducto:</b></h5>' +
                '<h6><label>Nombre del Subproducto: </label> ' + producto + '</h6>' +
                '<h6><label>Código: </label> ' + codigo + '</h6>' +
                '<h6><label>Parte:</label> '+ parte + ' </h6>' +
                '<h6><label>Para:</label> '+ $("#slcSexo option[value='"+para+"']").html() + ' </h6>' +
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
                        data: {Id : $("#txtIdEspecie").val(), Producto : producto, Codigo : codigo, Parte : parte ,Para: para,Observaciones : $("#txtObservacion").val()},
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
    function f_update_parentesco() {
        subproducto = $("#txtIdProducto").val();
        if (subproducto != "") {
            cantidad = $("#cant-enfermedades").val();
            if (cantidad != "") {
                arrayDatos = [];
                for (i = 1; i <= cantidad; i++) {
                    estado = $("#chbst-" + i).val();
                    input = $("#chb-" + i).val();
                    select = 0;
                    if ($("#chb-" + i).prop('checked')) select = 1;
                    else select = 0;
                    if (select != estado) {
                        arrayDatos.push(new Array(input, estado, select));
                    }
                    // console.log("Input Valor: "+input+" Estado: "+estado+" Selecionado: "+select);
                }
                if (arrayDatos.length > 0) {
                    mensaje_permisos = '';
                    for (i = 0 ; i < arrayDatos.length; i++) {
                        value = arrayDatos[i][0];
                        if (arrayDatos[i][2]==1) mensaje_permisos += '<h6>Añadir <b>'+$("#lblch"+value).html() +'</b></h6>';
                        else mensaje_permisos += '<h6>Eliminar <b>'+$("#lblch"+value).html() +'</b></h6>';
                    }
                    mensaje = '' +
                        '<h5><b>Listado de Enfermedades:</b></h5>' +mensaje_permisos
                        '';
                    Swal.fire({
                        title: 'Verificación de la información',
                        html: mensaje,
                        icon: 'warning',
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
                                data: {
                                    SubProd: subproducto,
                                    Array: arrayDatos
                                },
                                type: 'POST',
                                success: function (r) {
                                    if (r == true) {
                                        $("#btnCerrar").click();
                                        Swal.fire({
                                            icon: 'success',
                                            html: '<h4>Datos Actualizados</h4>',
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            html: '<h4><b>Error: </b> ' + r + '</h4>',
                                        });
                                        $("#btn-action").prop('disabled', false);
                                    }
                                }
                            });
                        }
                        $("#btn-action").prop('disabled', false);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        html: '<h4>Ningun Permiso para editar</h4>',
                    });
                    $("#btn-action").prop('disabled', false);
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    html: '<h4>ERROR-86552</h4>',
                });
                $("#btn-action").prop('disabled', false);
            }
        } else {
            Swal.fire({
                icon: 'error',
                html: '<h4>ERROR-8872621</h4>',
            });
            $("#btn-action").prop('disabled', false);
        }
    }
//Cambiar estado
    function f_estado_producto(estado, id,especie) {
        if (estado == 0) {
            ht = "<h4>¿ESTA SEGURO DE ACTIVAR EL SUBPRODUCTO ?</h4>";
        }
        if (estado == 1) {
            ht = "<h4>¿ESTA SEGURO DE DESACTIVAR EL SUBPRODUCTO ?</h4>";
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
    
//Actualizar
    function f_update_producto() {
        datos = ["#txtProducto","#txtCodigo"];
            if (comprobar_vacios(datos)) {
                producto =$("#txtProducto").val();
                codigo =$("#txtCodigo").val();
                parte = $("#slcParte").val();
                para = $("#slcSexo").val();
                mensaje = '' +
                    '<h5><b>Esta ingresando un nuevo producto:</b></h5>' +
                    '<h6><label>Nombre del Producto: </label> ' + producto + '</h6>' +
                    '<h6><label>Código: </label> ' + codigo + '</h6>' +
                    '<h6><label>Parte:</label> '+ parte + ' </h6>' +
                    '<h6><label>Para:</label> '+ $("#slcSexo option[value='"+para+"']").html() + ' </h6>' +
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
                            data: {Id : $("#txtIdProducto").val(), Producto : producto, Codigo : codigo, Parte : parte ,Para: para,Observaciones : $("#txtObservacion").val()},
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
