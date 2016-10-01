$(document).ready(function () {
    $("#btn-chat").click(function () {
        // Cria uma string com o username, data/horário e mensagem enviada
        var d = new Date();
        var dformat = [d.getDate(), (d.getMonth() + 1), d.getFullYear()].join('/') +
            ' ' +
            [d.getHours(), d.getMinutes(), d.getSeconds()].join(':');
        var mensagem = $('#username').val() + ';;;' + dformat + ';;;' + $('#btn-input').val();

        // Executa script PHP salvando a mensagem criptografada
        $.post("assets/php/database/chat.php", {
            'mensagem': mensagem,
            'json': json
        }, function (data) {
        }).success(function () {
            console.log("Mensagem enviada com sucesso!");
            $('#chat').append('<li class="right clearfix"><span class="chat-img pull-right">'
                + '<img src="http://placehold.it/50/FA6F57/fff&text=ME" alt="User Avatar" class="img-circle"/>'
                + '</span>'
                + '<div class="chat-body clearfix">'
                + '<div class="header">'
                + '<small class=" text-muted"><span'
                + 'class="glyphicon glyphicon-time"></span>' + dformat
                + '</small>'
                + '<strong class="pull-right primary-font">' + $('#username').val() + '</strong>'
                + '</div>'
                + '<p><b>'
                + $('#btn-input').val()
                + '</b></p>'
                + '</div>'
                + '</li>');
            contador++;
        }).error(function () {
            alert("error");
        });
    });
});