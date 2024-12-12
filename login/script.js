$(document).ready(function () {
    
});

$("#form-login").submit(function (event) {
    user = $("#txtUser").val();
    pass = $("#txtPass").val();
    $.ajax({
        url: 'operaciones.php?op=1',
        data:{EPMRQ_Usuario : user,EPMRQ_Pass :pass },
        type : 'POST',
        success: function (r) {
            console.log(r);
            if (r==true) {
                window.location.href="../perfil/";
            }else{
                Swal.fire({
                    icon: 'error',
                    html: '<h4>Usuario o contraseña Incorrectas</h4>',
                });
            }
        }
    });
    event.preventDefault();
})


