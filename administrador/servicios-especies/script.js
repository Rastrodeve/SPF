$(document).ready(function () {
    $('.cont-carga').addClass('d-none');
    Cargar_Datos();
});

//Restriciones
    function soloNumeros_Decimales(e) {
        var key = window.Event ? e.which : e.keyCode;
        return (key >= 48 && key <= 57 || key == 46);
    }
    function f_restrincion(e) {
        var key = window.event ? e.which : e.keyCode;
        if (key < 48 || key > 57) {
            e.preventDefault();
        }
    }
    function f_restrincion_1() {
        var key = window.event ? event.which : event.keyCode;
        if (key < 65 || key > 122) {
            event.preventDefault();
        } else {
            if (key > 90 && key < 97) {
                event.preventDefault();
            } 
        }   
    }
//Funciones de carga de datos
    function Cargar_Datos() {
        f_carga('cont-datos');
        $.ajax({
            url: 'operaciones.php?op=1',
            success: function (r) {
                $('#cont-datos').html(r);
            }
        });
    }
    function Cargar_Datos_table(id) {
        f_carga('conttable-'+id);
        $.ajax({
            type : 'POST',
            data : { Id:id},
            url: 'operaciones.php?op=15',
            success: function (r) {
                $('#conttable-'+id).html(''+r);
            }
        });
    }
    function get_data_new_especie() {
        f_carga('modal-content');
        $.ajax({
            url: 'operaciones.php?op=2',
            success: function (r) {
                $('#modal-content').html(r);
                $('.select2bs4').select2();
                $('.input_disablecopypaste').bind('paste', function (e) {
                    e.preventDefault();
                });
                document.getElementById("file-upload").onchange = function (e) {
                    // Creamos el objeto de la clase FileReader
                    let reader = new FileReader();
                
                    // Leemos el archivo subido y se lo pasamos a nuestro fileReader
                    reader.readAsDataURL(e.target.files[0]);
                
                    // Le decimos que cuando este listo ejecute el código interno
                    reader.onload = function () {
                        let preview = document.getElementById('preview'),
                            image = document.createElement('img');
                        button = document.createElement('button');
                        br = document.createElement('br');
                
                        image.src = reader.result;
                        image.width = 100;
                        image.height = 100;
                        button.classList.add("btn");
                        button.classList.add("btn-danger");
                        button.classList.add("mt-2");
                        button.id = 'btn-limpiar';
                        button.innerHTML = '<i class="fas fa-times-circle"></i>';
                        preview.innerHTML = '';
                        preview.append(image);
                        preview.append(br);
                        preview.append(button);

                        $("#btn-limpiar").click(function () {
                            $("#file-upload").val("");
                            $("#preview").html("");
                        })
                    };
                }
            }
        });
    }
    function get_data_img(id) {
        f_carga('modalN-content');
        $.ajax({
            type: 'POST',
            data: {Id: id},
            url: 'operaciones.php?op=6',
            success: function (r) {
                $('#modalN-content').html(r);
                $("#cont-img").click(function () {
                    $("#file-img-new").click(); 
                });
                document.getElementById("file-img-new").onchange = function (e) {
                    // Creamos el objeto de la clase FileReader
                    let reader = new FileReader();
                    // Leemos el archivo subido y se lo pasamos a nuestro fileReader
                    reader.readAsDataURL(e.target.files[0]);
                    // Le decimos que cuando este listo ejecute el código interno
                    reader.onload = function () {
                        let preview = document.getElementById('cont-img'),
                            image = document.createElement('img');
                        image.src = reader.result;
                        image.setAttribute('width', '100%');
                        preview.innerHTML = '';
                        preview.append(image);
                    };
                }
            }
        });
    }
    function get_data_edit_especie(id) {
        f_carga('modal-content');
        $.ajax({
            type: 'POST',
            data: {Id: id},
            url: 'operaciones.php?op=8',
            success: function (r) {
                $('#modal-content').html(r);
                $('.select2bs4').select2();
                $('.input_disablecopypaste').bind('paste', function (e) {
                    e.preventDefault();
                });
            }
        });
    }
    function get_data_edit_corralaje(id) {
        f_carga('modal-content');
        $.ajax({
            type: 'POST',
            data: {Id: id},
            url: 'operaciones.php?op=10',
            success: function (r) {
                $('#modal-content').html(r);
                $('.select2bs4').select2();
                $('.input_disablecopypaste').bind('paste', function (e) {
                    e.preventDefault();
                });
            }
        });
    }
    function get_data_new_servicio(id) {
        f_carga('modal-content');
        $.ajax({
            type: 'POST',
            data: {Id: id},
            url: 'operaciones.php?op=12',
            success: function (r) {
                $('#modal-content').html(r);
                $('.select2bs4').select2();
                $('.input_disablecopypaste').bind('paste', function (e) {
                    e.preventDefault();
                });
            }
        });
    }
    function get_data_update_servicio(id) {
        f_carga('modal-content');
        $.ajax({
            type: 'POST',
            data: {Id: id},
            url: 'operaciones.php?op=16',
            success: function (r) {
                $('#modal-content').html(r);
                $('.select2bs4').select2();
                $('.input_disablecopypaste').bind('paste', function (e) {
                    e.preventDefault();
                });
            }
        });
    }
//Funciones de insertar
    function f_insert_new_especie() {
        corralaje = $('input:radio[name=radio]:checked').val();//0 desactivado y 1 activado
        mensaje_corralaje='';
        if (corralaje==0) {
            datos = ["#txtEscpecie","#txtLetraEspecie","#txtHorasEstancia"];
            mensaje_corralaje ='Desactivado';
        }else if (corralaje==1){
            datos = ["#txtEscpecie","#txtLetraEspecie","#txtHorasEstancia","#txtHorasCorralaje","#txtTaza"];
            mensaje_corralaje ='Activado';
        }else{
            Swal.fire({
                icon: 'error',
                html: '<h4>8276633</h4>',
            });
        }
        if (comprobar_vacios(datos)) {
            var formData = new FormData();
            c_yupak = $("#slcYupack").val();// Depende si el estado del corralaje esta en activado o desactivado
            if (corralaje == 1 && c_yupak == 'NULL') {
                $("#slcYupack").addClass("is-invalid");
                Swal.fire({
                    icon: 'error',
                    html: '<h6>Seleccione un servicio de yupak</h6>',
                });
                return;
            }else{
                $("#slcYupack").removeClass("is-invalid");
            }
            img = $("#file-upload").val();
            mensaje_img = '';
            if (img=='') mensaje_img = 'Imagen NO seleccionada';
            else{
                var files = $('#file-upload')[0].files[0];
                mensaje_img = 'Imagen seleccionada';
                formData.append('file',files);
            }
            especie = $("#txtEscpecie").val();
            letra = $("#txtLetraEspecie").val();
            horas_estancia = $("#txtHorasEstancia").val();
            horas_corralaje = $("#txtHorasCorralaje").val();// Depende si el estado del corralaje esta en activado o desactivado
            taza = $("#txtTaza").val();// Depende si el estado del corralaje esta en activado o desactivado
            d_yupak = $("#slcYupack option[value='"+c_yupak+"']").html();// Depende si el estado del corralaje esta en activado o desactivado
            ganado = $("#slcGanado").val();
            minutos = $("#slcMinutos").val();
            segundos = $("#slcSegundos").val();
            minutos_corralaje = $("#slcMinutos_corralaje").val();
            segundos_corralaje= $("#slcSegundos_corralaje").val();
            detalle= $("#slcDetalle").val();
            mensaje_activado = '';
            if (corralaje==1)mensaje_activado = ''+
            '<h6><label>Tiempo corralaje:</label> ' + horas_corralaje + ':'+minutos_corralaje+':'+segundos_corralaje+'</h6>' +
            '<h6><label>Taza:</label> ' +taza + ' $</h6>' +
            '<h6><label>Servicio Yupak:</label> '+c_yupak +' - ' + d_yupak + ' </h6>' +
            '';
            mensaje = '' +
                '<h5><b>Esta ingresando una nueva especie:</b></h5>' +
                '<h6><label>Especie: </label> ' + especie + '</h6>' +
                '<h6><label>Tipo de ganado: </label> ' + $("#slcGanado option[value='"+ganado+"']").html() + '</h6>' +
                '<h6><label>Letra: </label> ' + letra + '</h6>' +
                '<h6><label>Estancia mínima:</label> ' + horas_estancia + ':'+minutos+':'+segundos+'</h6>' +
                '<h6><label>Corralaje:</label> ' + mensaje_corralaje + ' </h6>' +mensaje_activado +
                '<h6><label>Detalle:</label> ' + $("#slcDetalle option[value='"+detalle+"']").html()+ '</h6>' +
                '<h6><label>'+mensaje_img+'</label></h6>' +
                '';
                formData.append('Especie',especie);
                formData.append('Letra',letra);
                formData.append('Ganado',ganado);
                formData.append('HorasEstancia',horas_estancia);
                formData.append('MinutosEstancia',minutos);
                formData.append('SegundosEstancia',segundos);
                formData.append('Corralaje',corralaje);
                formData.append('HorasCorralaje',horas_corralaje);
                formData.append('MinutosCorralaje',minutos_corralaje);
                formData.append('SegundosCorralaje',segundos_corralaje);
                formData.append('Taza',taza);
                formData.append('CodigoYupak',c_yupak);
                formData.append('Detalle',detalle);
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
                        data: formData,
                        contentType: false,
                        processData: false,
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
    function f_new_servicio() {
        datos = ["#txtTaza","#txtServicio"];
        if (comprobar_vacios(datos)) {
            servicio =$("#txtServicio").val();
            precio =$("#txtTaza").val();
            c_yupak = $("#slcYupack").val();
            d_yupak = $("#slcYupack option[value='"+c_yupak+"']").html();
            if (c_yupak == 'NULL') {
                $("#slcYupack").addClass("is-invalid");
                Swal.fire({
                    icon: 'error',
                    html: '<h6>Seleccione un servicio de yupak</h6>',
                });
                return;
            }else{
                $("#slcYupack").removeClass("is-invalid");
            }
            mensaje = '' +
                '<h5><b>Esta ingresando una nuevo servicio:</b></h5>' +
                '<h6><label>Nombre del Servicio: </label> ' + servicio + '</h6>' +
                '<h6><label>Precio: </label> ' + precio + '</h6>' +
                '<h6><label>Servicio Yupak:</label> '+c_yupak +' - ' + d_yupak + ' </h6>' +
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
                        data: {Id : $("#txtIdEspecie").val(), Servicio : servicio, Taza : precio, CodigoYupak : c_yupak , Observaciones : $("#txtObservacion").val()},
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
//Funciones de actualizar estado
    function f_estado_especie(id,estado) {
        if (estado == 1) {
            ht = "<h4>¿ESTA SEGURO QUE DESEA DESACTIVAR LA ESPECIE?</h4><h6>Una vez desactivada la especie no podra ser seleccionada</h6>";
        }
        if (estado == 0) {
            ht = "<h4>¿ESTA SEGURO QUE DESEA ACTIVAR LA ESPECIE?</h4><h6>Una vez activada la especie podra ser seleccionada</h6>";
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
    function f_detalle_especie(id,estado) {
        if (estado == 1) {
            ht = "<h4>¿ESTA SEGURO QUE <b>SI</b> DESEA DETALLAR ESTA ESPECIE?</h4><h6>El detalle indivudal de las guias de procesos para esta especie, deberán ser detalladas individualmente por animal para que se muestre en la orden de producción </h6>";
        }
        if (estado == 0) {
            ht = "<h4>¿ESTA SEGURO QUE <b>NO</b> DESEA DETALLAR ESTA ESPECIE?</h4><h6>Las guías de procesos no necesitaran ser detalladas para aperecer en la orden de producción</h6>";
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
                    url: "operaciones.php?op=5",
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
    function f_estado_servivio(estado, id,especie) {
        if (estado == 0) {
            ht = "<h4>¿ESTA SEGURO QUE DESEA  ACTIVAR EL SERVICIO ?</h4>";
        }
        if (estado == 1) {
            ht = "<h4>¿ESTA SEGURO QUE DESEA  DESACTIVAR EL SERVICIO ?</h4>";
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
                    url: "operaciones.php?op=14",
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
// Funciones de actualizar datos
    function f_update_img() {
        if ($("#file-img-new").val()=='') {
            Swal.fire({
                icon: 'warning',
                html: '<h6>Ninguna imagen seleccionada</h6>',
            });
        }else {
            Swal.fire({
                title: '¿Esta seguro que desea cambiar la imagen?',
                icon: 'warning',
                showCancelButton: false,
                showDenyButton: true,
                confirmButtonColor: '#3085d6',
                denyButtonColor: '#d33',
                confirmButtonText: 'Si, Continuar',
                denyButtonText: 'No, Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    var files = $('#file-img-new')[0].files[0];
                    formData.append('file',files);
                    formData.append('Id',$("#txtIdEspecie").val());
                    $.ajax({
                        url: 'operaciones.php?op=7',
                        type: 'post',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if(response==true){
                                Cargar_Datos();
                                $("#btnCerrar").click();
                                $('#modalN-content').html('');
                                Swal.fire({
                                    icon: 'success',
                                    html: '<h4>Datos Actualizados</h4>',
                                });
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    html: '<h4><b>Error: </b> ' +response + '</h4>',
                                });
                            }
                        }
                    });
                }
            });
        }
    }
    function f_update_especie() {
        datos = ["#txtEscpecie","#txtLetraEspecie","#txtHorasEstancia"];
        if (comprobar_vacios(datos)) {
            var formData = new FormData();
            especie = $("#txtEscpecie").val();
            letra = $("#txtLetraEspecie").val();
            horas_estancia = $("#txtHorasEstancia").val();
            ganado = $("#slcGanado").val();
            minutos = $("#slcMinutos").val();
            segundos = $("#slcSegundos").val();
            mensaje = '' +
                '<h5><b>Esta actualizado una especie:</b></h5>' +
                '<h6><label>Nuevo nombre de especie: </label> ' + especie + '</h6>' +
                '<h6><label>Nuevo tipo de ganado: </label> ' + $("#slcGanado option[value='"+ganado+"']").html() + '</h6>' +
                '<h6><label>Nueva Letra: </label> ' + letra + '</h6>' +
                '<h6><label>Estancia mínima:</label> ' + horas_estancia + ':'+minutos+':'+segundos+'</h6>' +
                '';
                formData.append('Especie',especie);
                formData.append('Letra',letra);
                formData.append('Ganado',ganado);
                formData.append('HorasEstancia',horas_estancia);
                formData.append('MinutosEstancia',minutos);
                formData.append('SegundosEstancia',segundos);
                formData.append('Id',$("#txtIdEspecie").val());
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
                        data: formData,
                        contentType: false,
                        processData: false,
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
    function f_update_corralaje() {
        corralaje = $('input:radio[name=radio]:checked').val();//0 desactivado y 1 activado
        console.log(corralaje);
        mensaje_corralaje='';
        if (corralaje==0) {
            datos = [];
            mensaje_corralaje ='Desactivado';
        }else if (corralaje==1){
            datos = ["#txtHorasCorralaje","#txtTaza"];
            mensaje_corralaje ='Activado';
        }else{
            Swal.fire({
                icon: 'error',
                html: '<h4>8276633</h4>',
            });
            return;
        }
        if (comprobar_vacios(datos)) {
            var formData = new FormData();
            c_yupak = $("#slcYupack").val();// Depende si el estado del corralaje esta en activado o desactivado
            if (corralaje == 1 && c_yupak == 'NULL') {
                $("#slcYupack").addClass("is-invalid");
                Swal.fire({
                    icon: 'error',
                    html: '<h6>Seleccione un servicio de yupak</h6>',
                });
                return;
            }else{
                $("#slcYupack").removeClass("is-invalid");
            }
            horas_corralaje = $("#txtHorasCorralaje").val();// Depende si el estado del corralaje esta en activado o desactivado
            taza = $("#txtTaza").val();// Depende si el estado del corralaje esta en activado o desactivado
            d_yupak = $("#slcYupack option[value='"+c_yupak+"']").html();// Depende si el estado del corralaje esta en activado o desactivado
            minutos_corralaje = $("#slcMinutos_corralaje").val();
            segundos_corralaje= $("#slcSegundos_corralaje").val();
            mensaje_activado = '';
            if (corralaje==1)mensaje_activado = ''+
            '<h6><label>Tiempo corralaje:</label> ' + horas_corralaje + ':'+minutos_corralaje+':'+segundos_corralaje+'</h6>' +
            '<h6><label>Taza:</label> ' +taza + ' $</h6>' +
            '<h6><label>Servicio Yupak:</label> '+c_yupak +' - ' + d_yupak + ' </h6>' +
            '';
            mensaje = '' +
                '<h5><b>Esta editando el corralaje de una especie:</b></h5>' +
                '<h6><label>Corralaje:</label> ' + mensaje_corralaje + ' </h6>' +mensaje_activado +
                '';
                formData.append('Corralaje',corralaje);
                formData.append('HorasCorralaje',horas_corralaje);
                formData.append('MinutosCorralaje',minutos_corralaje);
                formData.append('SegundosCorralaje',segundos_corralaje);
                formData.append('Taza',taza);
                formData.append('CodigoYupak',c_yupak);
                formData.append('Id',$("#txtIdEspecie").val());
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
                        data: formData,
                        contentType: false,
                        processData: false,
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
    function f_update_servicio() {
        datos = ["#txtTaza","#txtServicio"];
        if (comprobar_vacios(datos)) {
            servicio =$("#txtServicio").val();
            especie =$("#txtEspecie").val();
            precio =$("#txtTaza").val();
            c_yupak = $("#slcYupack").val();
            d_yupak = $("#slcYupack option[value='"+c_yupak+"']").html();
            if (c_yupak == 'NULL') {
                $("#slcYupack").addClass("is-invalid");
                Swal.fire({
                    icon: 'error',
                    html: '<h6>Seleccione un servicio de yupak</h6>',
                });
                return;
            }else{
                $("#slcYupack").removeClass("is-invalid");
            }
            mensaje = '' +
                '<h5><b>Esta actualizando un servicio:</b></h5>' +
                '<h6><label>Nuevo nombre del Servicio: </label> ' + servicio + '</h6>' +
                '<h6><label>Nuevo Precio: </label> ' + precio + '</h6>' +
                '<h6><label>Nuevo servicio Yupak:</label> '+c_yupak +' - ' + d_yupak + ' </h6>' +
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
                        url: "operaciones.php?op=17",
                        type: 'POST',
                        data: {Id : $("#txtIdServicio").val(), Servicio : servicio, Taza : precio, CodigoYupak : c_yupak , Observaciones : $("#txtObservacion").val()},
                        success: function (r) {
                            if(r==true){
                                Cargar_Datos_table(especie);
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
// Otras funciones
    function select_yupak() {
        if ($("#slcYupack").val()=="NULL")$("#spnCodigo").html('');
        else $("#spnCodigo").html($("#slcYupack").val());
    }
    function open_ac() {
        $("#cont-active").slideDown();
    }
    function close_ac() {
        $("#cont-active").slideUp();
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