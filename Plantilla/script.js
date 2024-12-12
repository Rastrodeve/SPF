function f_carga(id) {
    var carga = '<div class="spinner-border " role="status">'
    '<span class="sr-only">Espere...</span>' +
    '</div>';
    $("#" + id).html(carga);
}

function f_menu(id){
    $.ajax({
        url:'../FilePHP/menu.php',
        data:{Id_Menu : id},
        type:'POST',
        success : function(r) { 
            if (r=='Vacio') {
                window.location.href = "../../menuprocesos.php";
            }else $('#navbarCollapse').html(r);  
            
        }
    });
}