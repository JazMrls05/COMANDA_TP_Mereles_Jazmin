<?php

class Encuesta
{
    public $codigoMesa;
    public $codigoPedido;
    public $puntajeMesa;
    public $puntajeRestaurante;
    public $puntajeMozo;
    public $puntajeCocinero;
    public $comentario;

    public function __construct($codigoMesa,$codigoPedido,$puntajeMesa,$puntajeRestaurante,
    $puntajeMozo,$puntajeCocinero,$comentario)
    {
        $this->codigoMesa = $codigoMesa;
        $this->codigoPedido = $codigoPedido;
        $this->puntajeMesa = $puntajeMesa;
        $this->puntajeRestaurante = $puntajeRestaurante;
        $this->puntajeMozo = $puntajeMozo;
        $this->puntajeCocinero = $puntajeCocinero;
        if(strlen($comentario) > 66)
        {
            throw new Exception("El comentario debe tener menos de 66 caracteres.");
        }
        else
        {
            $this->comentario = $comentario;
        }
        
    }

}