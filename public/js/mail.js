function sendRequest() {


    data = $("#registo").serialize();

    $.ajax({
        type: 'POST',
        url: 'index.php/mail/email',
        data: data,
        beforeSend: function () {
            $("#loading").html("<img src='public/images/loading.gif'>");
        },
        dataType: 'json'
    }).done(function(response) {

        $("#loading").html("");
        if(response === true) {
            bootbox.alert('Dados enviados para API!');
            $('#registo input').val("");
        } else {
            bootbox.alert('Ocorreu um erro ao se conectar com a api!');
        }

    }).fail(function(xhr, desc, err) {
        $("#loading").html("");
        bootbox.alert(xhr.responseText);
        $('#password').val("");
        console.log(xhr);
        console.log("Detalhes: " + desc + "nErro:" + err);
    });
}