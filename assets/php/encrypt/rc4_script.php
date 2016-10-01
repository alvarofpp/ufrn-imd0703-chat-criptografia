<?php

/**
 * Created by PhpStorm.
 * User: alvarofpp
 * Date: 09/09/16
 * Time: 21:05
 */
class RC4
{
    var $S, $S2, $T, $C; // Array com os bits
    var $bits_file_array; // Array com os bits
    var $key; // Chave
    var $pseudo_random_key; // Chave

    function __construct()
    {
        $this->S = array();
        $this->S2 = array();
        $this->T = array();
        $this->C = array();
        $this->pseudo_random_key = array();
    }

    public function rc(){
        $this->inicializacao_vetores(); // INICIALIZA OS VETORES
        $this->permutacao(); // PERMUTAÇÃO INICIAL
        $this->geracao_fluxo(); // GERAÇÃO DE FLUXO
        $this->realizar_xor();
        $texto = '';
        foreach ($this->C as $c){
            $texto = $texto . chr($c);
        }
        return $texto;
    }

    // Inicialização dos vetores
    public function inicializacao_vetores()
    {
        $key = str_split($this->key);
        $length_key = sizeof($key);
        $c = 0;
        for ($i = 0; $i < 256; $i++) {
            $this->S[$i] = $i; // inicializando o vetor S
            $this->T[$i] = $key[$c]; // inicializando o vetor S
            $c = ($c + 1) == $length_key ? 0 : $c + 1;
        }
    }

    // Permutação Inicial
    public function permutacao()
    {
        $j = 0;
        for ($i = 0; $i < 256; $i++) {
            $j = ($j + $this->S[$i] + ord($this->T[$i])) % 256; // MOD
            list($this->S[$i], $this->S[$j]) = array($this->S[$j], $this->S[$i]); // SW
        }
    }

    // Geração de Fluxo
    public function geracao_fluxo()
    {
        $i = 0;
        $j = 0;
        $size = sizeof($this->bits_file_array);
        while ($size > 0) {
            $i = ($i + 1) % 256;
            $j = ($j + $this->S[$i]) % 256;
            list($this->S[$i], $this->S[$j]) = array($this->S[$j], $this->S[$i]); // SW
            $k = $this->S[($this->S[$i] + $this->S[$j]) % 256];
            $this->pseudo_random_key[] = $k;
            $size--;
        }
    }

    public function realizar_xor()
    {
        $size = sizeof($this->bits_file_array);
        for ($i = 0; $i < $size; $i++) {
            $byte = str_pad(decbin($this->pseudo_random_key[$i]), 8, 0, STR_PAD_LEFT);
            $array0 = str_split($byte);
            $array1 = str_split($this->bits_file_array[$i]);
            $num = bindec(implode('', $this->op_xor($array0, $array1)));
            $this->C[] = $num;
        }
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
}

?>