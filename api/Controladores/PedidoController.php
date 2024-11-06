<?php

require_once __DIR__ . '/../Modelos/Pedido.php';
require_once __DIR__ . '/../DB/AccesoDatos.php';

class PedidoController
{
    public function Guardar($request, $response)
    {
        $parsear_datos = $request->getParsedBody();

        $pedido = new Pedido($parsear_datos['codigo'],$parsear_datos['estado'], $parsear_datos['codigoMesa'],
        $parsear_datos['sector'],$parsear_datos['tipo'],$parsear_datos['nombre']);

        $array_datos = [$pedido->codigo, $pedido->estado,  $pedido->codigoMesa,
        $pedido->sector,$pedido->tipo, $pedido->nombre];

        $tabla = 'pedidos';
        $array_encabezados = ['codigo','estado','codigoMesa', 'sector', 'tipo', 'nombre'];
        
        if (AccesoDatos::insert($response, $tabla, $array_encabezados, $array_datos))
        {
            $response->getBody()->write(json_encode(["mensaje" => "Pedido $pedido->codigo cargado exitosamente"]));
        }
        else
        {
            $response->getBody()->write(json_encode(["mensaje" => "Hubo un problema al cargar el pedido $pedido->codigo"]));
        }
        return $response;
    }

    public function VerTodos($request, $response)
    {
        $lista_pedidos = AccesoDatos::selectAll($response, "pedidos");
        $response->getBody()->write(json_encode(["Pedidos" => $lista_pedidos], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerPorCodigo($request, $response, $args)
    {
        $codigo = $args['codigo'];
        $lista_pedidos = AccesoDatos::selectCodigo($response, "pedidos", "codigo", $codigo);
        $response->getBody()->write(json_encode(["Pedidos" => $lista_pedidos], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function EliminarPorCodigo($request,$response,$args)
    {
        $codigo = $args['codigo'];
        AccesoDatos::deleteCodigo($response, "pedidos", "codigo", $codigo);
        $response->getBody()->write(json_encode(["Pedido $codigo" => "Fue eliminado exitosamente"], JSON_PRETTY_PRINT));
        return $response;
    }


}