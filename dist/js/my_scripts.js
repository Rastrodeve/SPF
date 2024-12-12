function f_carga(id) {
    var carga = '<div class="spinner-border " role="status">'
    '<span class="sr-only">Espere...</span>' +
    '</div>';
    $("#" + id).html(carga);
}



$("#btn-full-scream").click(function () {
    var doc = window.document;
    var docEl = doc.documentElement;

    var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
    var cancelFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen || doc.msExitFullscreen;

    if(!doc.fullscreenElement && !doc.mozFullScreenElement && !doc.webkitFullscreenElement && !doc.msFullscreenElement) {
        requestFullScreen.call(docEl);
    }
    else {
        cancelFullScreen.call(doc);
    }
})


function confi_tabla(table_id) {
    $("#" + table_id).DataTable({
        "responsive": true,
        "autoWidth": false,
        "language": {
            "emptyTable": "No hay información",
            "info": "Mostrando de _START_ a _END_ de _TOTAL_ Resultados",
            "infoEmpty": "Mostrando 0 a 0 de 0 Resultados",
            "infoFiltered": "(Filtrado de _MAX_ total Resultados)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Resultados",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "SIN RESULTADOS ENCONTRADOS",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            },
        },
    });
}