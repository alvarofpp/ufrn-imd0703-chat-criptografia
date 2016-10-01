<?php

class CifraData
{
    var $data; // Array com a data (dia, mês, ano)
    var $texto;
    var $contador_auxiliar;

    function __construct($data, $texto)
    {
        $data = str_replace('/', '', $data);
        $this->data = str_split($data);
        $this->texto = $texto;
        $this->contador_auxiliar = 0;
    }

    public function cifradata($escolha)
    {
        $texto = '';
        $letras = str_split($this->texto);
        foreach ($letras as $key => $value) {
            $ascii = ord($value);
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
            if ($this->contador_auxiliar >= count($this->data) - 1) {
                $this->contador_auxiliar = 0;
            } else {
                $this->contador_auxiliar++;
            }
            $texto .= chr($ascii);
        }
        return $texto;
    }
}

?>