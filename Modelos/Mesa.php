<?php

class Mesa 
{
    public $codigoMesa;
    public $cantidadPersonas; // podría ser de 2, 4, 5 o 6. Tambien será la cantidad de pedidos de personas diferentes q va a poder tomar
    public $estado;

    public function __construct($codigoMesa, $cantidadPersonas, $estado)
    {
        $this->codigoMesa = $codigoMesa;
        $this->cantidadPersonas = $cantidadPersonas;
        $this->estado = $estado;

    }
}