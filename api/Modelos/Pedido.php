<?php

class Pedido
{
    public $estado;
    public $id_mozo;
    public $precioTotal;
    public $tiempoTotal;

    public function __construct($estado, $id_mozo, $precioTotal, $tiempoTotal)
    {
        $this->estado = $estado;
        $this->id_mozo = $id_mozo;
        $this->precioTotal = $precioTotal;
        $this->tiempoTotal = $tiempoTotal;
    }
}