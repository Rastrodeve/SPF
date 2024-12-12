$(document).ready(function () {

    
});
$('#mostrar_contrasena').click(function () {
    if ($('#mostrar_contrasena').is(':checked')) {
        $('#pass_new').attr('type', 'text');
    } else {
        $('#pass_new').attr('type', 'password');
    }
});
$("#form-restore").submit(function (event) {
    pass = $('#pass_new').val();
    if (pass=="") {
        Swal.fire({
            icon: 'error',
            html: '<h4>Ingrese una nueva contraseña</h4>',
        });
    }else{
        Swal.fire({
            title: '¿Esta seguró que sea cambiar la contraseña?',
            showCancelButton: true,
            confirmButtonText: 'SI, CONTINUAR',
            cancelButtonText: 'NO, CANCELAR',
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $.ajax({
                    url: 'operaciones.php?op=1',
                    data: {
                        Password: pass
                    },
                    type: 'POST',
                    success: function (r) {
                        if (r == true) {
                            window.location.href="../perfil/";
                        } else {
                            Swal.fire({
                                icon: 'error',
                                html: '<h4>'+r+'</h4>',
                            });
                        }
                    }
                });
            }
        });
    }
    event.preventDefault();
});

