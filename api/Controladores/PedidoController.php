<?php

require_once __DIR__ . '/../Modelos/Pedido.php';
require_once __DIR__ . '/../DB/AccesoDatos.php';

class PedidoController
{
    public function Guardar($request, $response)
    {
        $parsear_datos = $request->getParsedBody();

        $pedido = new Pedido($parsear_datos['estado'],$parsear_datos['id_mozo'], $parsear_datos['precioTotal'],
        $parsear_datos['tiempoTotal']);

        $array_datos = [$pedido->estado, $pedido->id_mozo, $pedido->precioTotal,
        $pedido->tiempoTotal];

        $tabla = 'pedidos';
        $array_encabezados = ['estado', 'id_mozo', 'precioTotal', 'tiempoTotal'];
        
        if (AccesoDatos::insert($response, $tabla, $array_encabezados, $array_datos))
        {
            $response->getBody()->write(json_encode(["mensaje" => "Pedido cargado exitosamente"]));
        }
        else
        {
            $response->getBody()->write(json_encode(["mensaje" => "Hubo un problema al cargar el pedido"]));
        }

        return $response;
    }
}