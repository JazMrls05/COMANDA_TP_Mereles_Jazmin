<?php

class Mesa 
{
    public $codigoMesa;
    public $estado;
    

    public function __construct($codigoMesa, $estado = null)
    {
        $this->codigoMesa = $codigoMesa;
        if ($estado=== '') 
        {
            $this->estado = "Con cliente esperando pedido"; 
        } 
        else 
        {
            $this->estado = $estado;
        }

    }
}