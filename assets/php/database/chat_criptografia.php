<?php

class chatCriptografia
{
    var $encryption; // Tipo de criptografia
    var $key; // Chave usada

    // O construtor já começa inicializando as variáveis com o tipo de criptografia
    // e a chave usada
    function __construct($encryption, $key)
    {
        $this->encryption = $encryption;
        $this->key = $key;
    }

    // Serve para importar os arquivos necessários para executar a classe corretamente
    public function import($include)
    {
        include($include);
    }

    // Realiza a ação de criptar ou decriptar
    public function action($action, $texto)
    {
        $return = ''; // Variável que conterá o texto de retorno
        switch ($this->encryption) {
            case 's_des':
                $sdes = new SDES();
                $sdes->key = $this->key; // CHAVE DA CONVERSA
                $sdes->key_generation(); // CHAVES
                $sdes->getFileBits($texto); // PEGAR BITS DO ARQUIVO

                switch ($action) {
                    case 'c':
                        $return = $sdes->s_des(); // SAIDA
                        break;
                    case 'd':
                        $return = $sdes->s_des_1(); // SAIDA
                        break;
                }
                break;
            case 'rc4':
                $rc = new RC4();
                $rc->getFileBits($texto); // PEGAR BITS DO ARQUIVO
                $rc->key = $this->key; // CHAVE DA CONVERSA

                $return = $rc->rc();
                break;
        }
        return $return;
    }
}

?>