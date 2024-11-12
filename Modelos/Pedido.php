<?php

class Pedido
{
    public $codigo;
    public $estado;
    public $codigoMesa;
    public $sector;
    public $tipo;
    public $nombre;
    //public $tiempoPreparacion;
    public $precioTotal;


    public function __construct($codigo,$estado,$codigoMesa, $sector, $tipo, $nombre/*, $tiempoPreparacion*/, $precioTotal)
    {
        $this->codigo = $codigo;
        $this->estado = $estado;
        $this->codigoMesa = $codigoMesa;
        $this->sector = $sector;
        $this->tipo = $tipo;
        $this->nombre = $nombre;
        //$this->tiempoPreparacion = $tiempoPreparacion;
        $this->precioTotal = $precioTotal;
    }
}