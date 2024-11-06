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

    /*
    ¿Por qué hago que el pedido tenga "dos identificaciones"?
    el "id" de la base de datos va a servir principalmente para poder hacer el "delete por id"
    y así no tener q hacer un método aparte para eliminar por código. Todos los delete van a ser por id.
    el "codigo" es aquel que va a poder tener el cliente, para poder ingresar a la app, ingresarlo junto con el 
    código de la mesa y ver el tiempo restante de su pedido.
    */

    public function __construct($codigo,$estado,$codigoMesa, $sector, $tipo, $nombre/*, $tiempoPreparacion*/)
    {
        $this->codigo = $codigo;
        $this->estado = $estado;
        $this->codigoMesa = $codigoMesa;
        $this->sector = $sector;
        $this->tipo = $tipo;
        $this->nombre = $nombre;
        //$this->tiempoPreparacion = $tiempoPreparacion;
    }
}