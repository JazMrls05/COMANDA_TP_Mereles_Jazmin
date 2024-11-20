<?php

class Pedido
{
    public $codigo;
    public $estado;
    public $codigoMesa;
    public $cliente; 
    public $sector;
    public $nombre;
    public $precioFinal;


    public function __construct($codigo,$estado,$codigoMesa,$cliente, $sector, $nombre, $precioFinal)
    {
        $this->codigo = $codigo;
        $this->estado = $estado;
        $this->codigoMesa = $codigoMesa;
        $this->cliente = $cliente;
        $this->sector = $sector;
        $this->nombre = $nombre;
        $this->precioFinal = $precioFinal;
    }
}