<?php

class CifraData
{
    var $data; // Array com a data (dia, mês, ano)
    var $texto;
    var $contador_auxiliar;

    function __construct($data, $texto)
    {
        $data = str_replace('/', '', $data); // Retira os /
        $this->data = str_split($data); // Transforma em um array com cada indice sendo um caractere
        $this->texto = $texto; // Atribui valor
        $this->contador_auxiliar = 0; // Atribui valor
    }

    // Realiza a cifragem pela data
    public function cifradata($escolha)
    {
        $texto = '';
        $letras = str_split($this->texto); // Transforma em um array com as letras
        // Percorre o array anteriormente criado
        foreach ($letras as $key => $value) {
            $ascii = ord($value); // ASCII
            // Se for cifragem ou se for decifragem
            if ($escolha == 'c') {
                $ascii = $ascii + $this->data[$this->contador_auxiliar];
                if ($ascii > 255) {
                    $ascii = $ascii - 255;
                }
            } else {
                $ascii = $ascii - $this->data[$this->contador_auxiliar];
                if ($ascii < 0) {
                    $ascii = 255 + $ascii;
                }
            }
            // Verificia contador, se volta ao inicio do indice do array ou se avança
            if ($this->contador_auxiliar >= count($this->data) - 1) {
                $this->contador_auxiliar = 0;
            } else {
                $this->contador_auxiliar++;
            }
            $texto .= chr($ascii); // Transforma em caracter e coloca no texto final
        }
        return $texto;
    }
}

?>