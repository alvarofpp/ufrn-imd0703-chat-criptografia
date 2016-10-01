<?php

/**
 * Created by PhpStorm.
 * User: alvarofpp
 * Date: 09/09/16
 * Time: 21:05
 */
class SDES
{
    var $P10, $P8; // Permutações
    var $PI, $PI_1; // Permutações Inicial e Final
    var $EP, $P4; // Permutações da função ep
    var $S0, $S1; // Matrizes
    var $bits_file_array; // Array com os bits
    var $subkeys; // Array com as chaves
    var $key; // Chave

    function __construct()
    {
        // Inicializar as variaveis
        // Permutações para geração de keys
        $this->P10 = array(1 => 3, 2 => 5, 3 => 2, 4 => 7, 5 => 4, 6 => 10, 7 => 1, 8 => 9, 9 => 8, 10 => 6);
        $this->P8 = array(1 => 6, 2 => 3, 3 => 7, 4 => 4, 5 => 8, 6 => 5, 7 => 10, 8 => 9);

        // Permutações para cifragem
        $this->PI = array(1 => 2, 2 => 6, 3 => 3, 4 => 1, 5 => 4, 6 => 8, 7 => 5, 8 => 7);
        $this->PI_1 = array(1 => 4, 2 => 1, 3 => 3, 4 => 5, 5 => 7, 6 => 2, 7 => 8, 8 => 6);

        // Permutações da função ep
        $this->EP = array(1 => 4, 2 => 1, 3 => 2, 4 => 3, 5 => 2, 6 => 3, 7 => 4, 8 => 1);
        $this->P4 = array(1 => 2, 2 => 4, 3 => 3, 4 => 1);

        // Matrizes
        $this->S0 = array(
            0 => array(1, 0, 3, 2),
            1 => array(3, 2, 1, 0),
            2 => array(0, 2, 1, 3),
            3 => array(3, 1, 3, 2));
        $this->S1 = array(
            0 => array(0, 1, 2, 3),
            1 => array(2, 0, 1, 3),
            2 => array(3, 0, 1, 0),
            3 => array(2, 1, 0, 3));

        // Keys
        $this->subkeys = array();
    }

    // Realiza a criptação do arquivo
    public function s_des()
    {
        $texto_saida = '';
        foreach ($this->bits_file_array as $byte) {
            $new_byte = $this->permutacao($byte, $this->PI); // Permutação Inicial
            $byte_div = str_split($new_byte, 4); // Divide em 2 blocos com 4 bits

            // Primeira sequência
            $byte_right = $this->f($byte_div[1], $this->subkeys[0]);
            $byte_div[0] = implode('', $this->op_xor(str_split($byte_right), str_split($byte_div[0])));
            list($byte_div[1], $byte_div[0]) = array($byte_div[0], $byte_div[1]); // SW

            // Segunda sequência
            $byte_right = $this->f($byte_div[1], $this->subkeys[1]);
            $byte_div[0] = implode('', $this->op_xor(str_split($byte_right), str_split($byte_div[0])));
            $final_byte = $this->permutacao(implode("", $byte_div), $this->PI_1); // Permutação Final
            $texto_saida = $texto_saida . chr(bindec($final_byte));
        }
        return $texto_saida;
    }

    // Realiza a decriptação do arquivo
    public function s_des_1()
    {
        $texto_saida = '';
        foreach ($this->bits_file_array as $byte) {
            $new_byte = $this->permutacao($byte, $this->PI); // Permutação Inicial
            $byte_div = str_split($new_byte, 4); // Divide em 2 blocos com 4 bits

            // Primeira sequência
            $byte_right = $this->f($byte_div[1], $this->subkeys[1]);
            $byte_div[0] = implode('', $this->op_xor(str_split($byte_right), str_split($byte_div[0])));
            list($byte_div[1], $byte_div[0]) = array($byte_div[0], $byte_div[1]); // SW

            // Segunda sequência
            $byte_right = $this->f($byte_div[1], $this->subkeys[0]);
            $byte_div[0] = implode('', $this->op_xor(str_split($byte_right), str_split($byte_div[0])));
            $final_byte = $this->permutacao(implode("", $byte_div), $this->PI_1); // Permutação Final
            $texto_saida = $texto_saida . chr(bindec($final_byte));
        }
        return $texto_saida;
    }

    /*
     * $bits_right: função F com o lado direito do byte
     * $subkey: subkey que será usado para as operações
     * Função f do S-DES (vide slide)
     */
    public function f($bits_right, $subkey)
    {
        $bits_right = $this->permutacao($bits_right, $this->EP);
        $bits_right_xor = implode('', $this->op_xor(str_split($bits_right), $subkey));
        $s = str_split($bits_right_xor, 4);
        $ss = $this->s($s[0], 0) . $this->s($s[1], 1);
        $ss = $this->permutacao($ss, $this->P4);
        return $ss;
    }

    /*
     * $array0, $array1: arrays que serão comparaddos
     * Serve para realizar a operação XOR entre arrays, retorna o array de bits comparados
     */
    public function op_xor($array0, $array1)
    {
        $array = array();
        for ($i = 0; $i < sizeof($array0); $i++) {
            $array[$i] = ($array0[$i] xor $array1[$i]) ? '1' : '0';
        }
        return $array;
    }

    /*
     * $s: 4 bits
     * $matriz: matriz que será consultada
     * Recebe 4 bits e encontra o valor na matriz, retorna 2 bits
     */
    public function s($s, $matriz)
    {
        $s = str_split($s);
        // Posições na matriz
        $l = bindec($s[0] . $s[3]); // Linha
        $c = bindec($s[1] . $s[2]); // Coluna
        // Valores na matriz
        $valor = 0;
        switch ($matriz) {
            case 0:
                $valor = $this->S0[$l][$c];
                break;
            case 1:
                $valor = $this->S1[$l][$c];
                break;
        }
        $valor = str_pad(decbin($valor), 2, 0, STR_PAD_LEFT); // 2 bits
        return $valor;
    }

    /*
     * $bloco: É o bloco de bits
     * $p_valor: É o valor da permutação (10 ou 8)
     * Essa função irá realizar a permutação do bloco de acordo com o $p_valor definido
     */
    public function permutacao($bloco, $array_permutacao)
    {
        $bits = str_split($bloco);
        $novo_bloco = array();
        // Muda as posições
        foreach ($array_permutacao as $p) {
            array_push($novo_bloco, $bits[--$p]);
        }
        return implode($novo_bloco);
    }

    /*
     * $bloco: É o bloco de bits
     * $num: É o número de casas que será deslocado para a esquerda
     * Essa função irá realizar a locomoção de casas do bloco enviado para a esquerda
     */
    public function ls($bloco, $num)
    {
        $len = strlen($bloco);
        $new_bloco = array();
        foreach (str_split($bloco) as $key => $value) {
            $posicao = ($key - $num < 0) ? $len + ($key - $num) : ($key - $num);
            $new_bloco[$posicao] = $value;
        }

        ksort($new_bloco);
        return implode($new_bloco);
    }

    // Pega os bytes do arquivo escolhido
    public function getFileBits($texto)
    {
        $bits = '';
        $caracteres = str_split($texto);
        foreach ($caracteres as $caracter) {
            $ascii = ord($caracter); // Decimal
            $binary = decbin($ascii); // Binário
            $byte = str_pad($binary, 8, 0, STR_PAD_LEFT); // Byte
            $bits = $bits . $byte;
        }
        $this->bits_file_array = str_split($bits, 8);
    }

    // Cria as subkeys de acordo com a key definida
    public function key_generation()
    {
        $key_10 = $this->permutacao($this->key, $this->P10);
        $key_10_div = str_split($key_10, 5); // Divide em 2 blocos com 5 bits

        // Realiza a locomoção
        $key_10_div[0] = $this->ls($key_10_div[0], 1);
        $key_10_div[1] = $this->ls($key_10_div[1], 1);
        $key_10 = implode($key_10_div); // Junta tudo
        $key_8 = $this->permutacao($key_10, $this->P8);
        array_push($this->subkeys, $key_8); // Salva a key

        $key_10_div = str_split($key_10, 5);
        // Realiza a locomoção
        $key_10_div[0] = $this->ls($key_10_div[0], 2);
        $key_10_div[1] = $this->ls($key_10_div[1], 2);
        $key_10 = implode($key_10_div); // Junta tudo
        $key_8 = $this->permutacao($key_10, $this->P8);
        array_push($this->subkeys, $key_8); // Salva a key
    }
}

?>