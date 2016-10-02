<html>
<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link rel="stylesheet" href="assets/css/chat.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <script src="assets/js/jquery-2.2.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</head>
<body>
<?php
include('assets/php/database/chat_criptografia.php');

// Arquivo JSON com as configurações (tipo de criptogafia e chave)
$json_file = file_get_contents('assets/php/database/config.json');
$json = json_decode($json_file, true);

// Cria objeto da classe chatCriptografia, salvando o tipo de criptografia e a chave usada
$cript = new chatCriptografia($json['encryption'], $json['key']);
$cript->import('assets/php/encrypt/s_des_script.php');
$cript->import('assets/php/encrypt/rc4_script.php');
?>
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <b>Criptografia:</b> <?php echo $cript->encryption; ?><br/>
            <b>Key:</b> <?php echo $cript->key; ?>
        </div>
        <fieldset>
            <legend>Mudança de senha por esteganografia e cifra de data</legend>
            <div class="col-md-4">
                <form method="get" action="assets/py/esteganografia_py/trocar_senha_processo.php">
                    <label for="c">Criptografia:</label><br/>
                    <input type="radio" name="c"
                           value="s_des" <?php echo ($cript->encryption == 's_des') ? 'checked' : ''; ?>/> S-DES<br/>
                    <input type="radio" name="c"
                           value="rc4" <?php echo ($cript->encryption == 'rc4') ? 'checked' : ''; ?>/> RC4<br/>
                    <input type="submit" value="Trocar Senha"/>
                </form>
            </div>
            <div class="col-md-4">
                <form method="post" action="assets/py/esteganografia_py/trocar_senha_validar.php"
                      enctype="multipart/form-data">
                    <label for="image">Imagem para validar nova senha:</label><br/>
                    <input type="file" name="image" required/><br/>
                    <label for="data">Data:</label><br/>
                    <input type="text" name="data" placeholder="07/07/1822" required/><br/>
                    <input type="submit" value="Trocar Senha"/>
                </form>
            </div>
        </fieldset>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-comment"></span> Chat
                </div>
                <div class="panel-body">
                    <ul class="chat" id="chat">
                        <?php
                        session_start(); // Inicia sessão
                        if (isset($_POST)) $_SESSION['username'] = $_POST['username']; // Salva o nick do usuário se estiver vindo pelo formulário
                        if (!isset($_SESSION['username'])) header("Location: index.html"); // Verifica se alguém está tentando acessar a página sem ter logado antes

                        $contador = 0;
                        $ponteiro = fopen('assets/php/database/chat.txt', "r"); // Abre o arquivo

                        // Ler o arquivo até chegar no fim
                        while (!feof($ponteiro)) {
                            $linha = fgets($ponteiro, 4096); // Ler uma linha do arquivo e avanço o ponteiro
                            // Verifica se é o final do arquivo
                            $final = (feof($ponteiro)) ? 1 : 0;
                            if ($final == 0) {
                                $linha = substr($linha, 0, strlen($linha) - 1); // Se não, retira o último caractere que é "\n"
                            }

                            // Esse IF serve para evitar imprimir algo caso não tenha nada no arquivo
                            if (strlen($linha) > 1) {
                                $contador++; // Se tiver, aumenta o contador

                                $texto = $cript->action('d', $linha); // Decripta
                                $chat = explode(";;;", $texto); // Divide o texto decriptado

                                // Imprime o texto de duas formas, caso seja o usuário proprietário ou não
                                if ($chat[0] == $_SESSION['username']) {
                                    ?>
                                    <li class="right clearfix"><span class="chat-img pull-right">
                                        <img src="http://placehold.it/50/FA6F57/fff&text=ME" alt="User Avatar"
                                             class="img-circle"/>
                                    </span>
                                        <div class="chat-body clearfix">
                                            <div class="header">
                                                <small class=" text-muted"><span
                                                        class="glyphicon glyphicon-time"></span><?php echo $chat[1]; ?>
                                                </small>
                                                <strong class="pull-right primary-font"><?php echo $chat[0]; ?></strong>
                                            </div>
                                            <p><b><?php echo $chat[2]; ?></b></p>
                                        </div>
                                    </li>
                                    <?php
                                } else {
                                    ?>
                                    <li class="left clearfix"><span class="chat-img pull-left">
                                        <img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar"
                                             class="img-circle"/>
                                    </span>
                                        <div class="chat-body clearfix">
                                            <div class="header">
                                                <strong class="primary-font"><?php echo $chat[0]; ?></strong>
                                                <small class="pull-right text-muted">
                                                    <span
                                                        class="glyphicon glyphicon-time"></span><?php echo $chat[1]; ?>
                                                </small>
                                            </div>
                                            <p><b><?php echo $chat[2]; ?></b></p>
                                        </div>
                                    </li>
                                    <?php
                                }
                            }
                        }
                        fclose($ponteiro); // Fecha o ponteiro do arquivo
                        ?>
                    </ul>
                </div>
                <div class="panel-footer">
                    <div class="input-group">
                        <input id="btn-input" type="text" class="form-control input-sm"
                               placeholder="Type your message here..."/>
                        <span class="input-group-btn">
                            <button class="btn btn-warning btn-sm" id="btn-chat">
                                Send</button>
                        </span>
                    </div>
                    <input type="hidden" id="username" value="<?php echo $_POST['username']; ?>"/>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <div class="row">
        <fieldset>
            <legend>Diffie-Hellman</legend>
            <?php
            $dh_file = file_get_contents('assets/php/encrypt/dh.json');
            $dh = json_decode($dh_file, true);

            foreach ($dh as $key => $value) {
                switch ($key) {
                    case 'config':
                        break;
                    case 'users':
                        // Se não tiver um número definido para o Diffie-Hellman
                        if (!isset($dh[$key][$_SESSION['username']])) {
                            $dh[$key][$_SESSION['username']] = 1;
                        }
                        break;
                }
            }
            ?>
            <div class="col-md-4">
                <form method="get" action="assets/php/encrypt/dh_change_num.php">
                    <label for="num">Número:</label><br/>
                    <input type="input" name="num" value="<?php echo $dh['users']['alvarofpp']; ?>" required/><br/>
                    <input type="submit" value="Trocar número"/>
                </form>
            </div>
            <div class="col-md-4">
                <form method="post" action="assets/php/encrypt/dh_init.php"
                      enctype="multipart/form-data">
                    <label for="image">q:</label><br/>
                    <input type="input" name="q" value="<?php echo $dh['config']['q']; ?>" required/><br/>
                    <label for="image">a:</label><br/>
                    <input type="input" name="a" value="<?php echo $dh['config']['a']; ?>" required/><br/>
                    <input type="submit" value="Realizar algoritmo Diffie-Hellman"/>
                </form>
            </div>
        </fieldset>
    </div>
</div>


<script>
    var contador = <?php echo $contador; ?>;
    var json = <?php echo json_encode($json); ?>;

    // A cada 1 segundo, verifica se tem mensagem nova
    setInterval(function () {
        $.post("assets/php/database/chat_verificar.php", {
            'contador': contador,
            'json': json
        }, function (data) {
        }).success(function (data) {
            if (data.length > 0) {
                // Se tiver mensagem nova, ele trabalha sobre o texto retorno e o transforma em JSON
                var json = JSON.parse(data);
                json = json.replace("\n", '');
                json = JSON.parse(json);

                $('#chat').append('<li class="left clearfix"><span class="chat-img pull-left">'
                    + '<img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-circle"/>'
                    + '</span>'
                    + '<div class="chat-body clearfix">'
                    + '<div class="header">'
                    + '<strong class="primary-font">' + json[0].username + '</strong>'
                    + '<small class="pull-right text-muted">'
                    + '<span class="glyphicon glyphicon-time"></span>' + json[0].data_hora
                    + '</small>'
                    + '</div>'
                    + '<p><b>'
                    + json[0].mensagem
                    + '</b></p>'
                    + '</div>'
                    + '</li>');
                contador++;
                console.log("Mensagem recebida!");
            }
        }).error(function () {
            alert("error na coleta de mensagens");
        });
        return true;
    }, 1000);
</script>
<script src="assets/js/chat.js"></script>
</body>
</html>