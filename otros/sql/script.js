$(document).ready(function () {
    $('.cont-carga').addClass('d-none');
    $('.select2bs4').select2({
        theme: 'bootstrap4'
    });
    f_get_menu();
    f_get_data();
});

function f_get_menu() {
    f_carga('sidebar');
    $.ajax({
        url: 'operaciones.php?op=1',
        success: function (r) {
            $('#sidebar').html(r);
            $(".lib-sub1 > a").click(function () {
                if ($(this).parent().find("> ul").length > 0) {
                    if ($(this).parent().find("> ul").is(':hidden')) {
                        $(".lib-sub1 > ul").slideUp();
                        $(".lib-sub1 > ul > li > ul").slideUp();
                        $(this).parent().find("> ul").slideDown();
                    }
                } else {
                    $(".lib-sub1 > ul").slideUp();
                    $(".lib-sub1 > ul > li > ul").slideUp();
                }
            })
            $(".lib-sub1 > ul > li > a").click(function () {
                if ($(this).parent().find("> ul").is(':hidden')) {
                    $(".lib-sub1 > ul > li > ul").slideUp();
                    $(this).parent().find("> ul").slideDown();
                }
            })
        }
    });
}

function f_get_data() {
    f_carga('card-data');
    $.ajax({
        url: 'operaciones.php?op=2',
        success: function (r) {
            $('#card-data').html(r);
        }
    });
}

function f_insert_consulta(op) {
    $.ajax({
        url: 'operaciones.php?op=5',
        type: 'POST',
        data: {
            Opcion: op
        },
        success: function (r) {
            $('#txtCont').html(r);
        }
    });
}

function f_select(variable, op) {
    $.ajax({
        url: 'operaciones.php?op=3',
        type: 'POST',
        data: {
            Variable: variable,
            Opcion: op
        },
        success: function (r) {
            if (op != 3) f_get_data();
        }
    });
}


function f_get_consulta() {
    if ($("#txtCont").val() == "") {
        Swal.fire({
            icon: 'error',
            title: 'Campo vacio',
        })
        return;
    }
    cons = $("#txtCont").val();
    f_carga('cont-result');
    $.ajax({
        url: 'operaciones.php?op=4',
        type: 'POST',
        data: {
            MYSQL: cons
        },
        success: function (r) {
            $("#cont-result").html(r);
            confi_tabla("table-data");
        }
    });
}

function f_update(id) {
    $.ajax({
        url: 'operaciones.php?op=6',
        type: 'POST',
        data: {
            Id: id
        },
        success: function (r) {
            $("#txtCont").val(r);
        }
    });
}