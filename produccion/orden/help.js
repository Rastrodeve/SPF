$("#bnt-help").click(function () {
    console.log("Prueba");
    $.ajax({
        url: 'operaciones_help.php?op=1',
        data: {
            Numero: $("#txtNumeroGuiaHelp").val()
        },
        type: 'POST',
        success: function (r) {
            if (r == true) {
                window.location.href = "../../PDF/index.php";
            }else{
                Swal.fire({
                    icon: 'error',
                    html: '<h4>'+ r +'</h4>',
                });
            }
        }
    });
})