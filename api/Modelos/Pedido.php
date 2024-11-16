<?php

class Pedido
{
    public $codigo;
    public $estado;
    public $codigoMesa;
    public $cliente; // nombre para identificar al cliente
    public $tipo;
    public $nombre;
    public $precioFinal;


    public function __construct($codigo,$estado,$codigoMesa,$cliente, $tipo, $nombre, $precioFinal)
    {
        $this->codigo = $codigo;
        $this->estado = $estado;
        $this->codigoMesa = $codigoMesa;
        $this->cliente = $cliente;
        $this->tipo = $tipo;
        $this->nombre = $nombre;
        $this->precioFinal = $precioFinal;
    }
}