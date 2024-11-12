<?php

require_once __DIR__ . '/../Modelos/Pedido.php';
require_once __DIR__ . '/../DB/AccesoDatos.php';

class PedidoController
{
    #region INSERT
    public function Guardar($request, $response)
    {
        $parsear_datos = $request->getParsedBody();

        $pedido = new Pedido($parsear_datos['codigo'],$parsear_datos['estado'], $parsear_datos['codigoMesa'],
        $parsear_datos['sector'],$parsear_datos['tipo'],$parsear_datos['nombre'], $parsear_datos['precioTotal']);

        $array_datos = [$pedido->codigo, $pedido->estado,  $pedido->codigoMesa,
        $pedido->sector,$pedido->tipo, $pedido->nombre, $pedido->precioTotal];

        $tabla = 'pedidos';
        $array_encabezados = ['codigo','estado','codigoMesa', 'sector', 'tipo', 'nombre','precioTotal'];
        
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

    public function GuardarDesdeCSV($request, $response)
    {
        $tabla = 'pedidos';
        $nombreArchivo = 'pedido.csv';
        
        if (AccesoDatos::insertDesdeCSV($response, $tabla, $nombreArchivo))
        {
            $response->getBody()->write(json_encode(["mensaje" => "Pedido cargado exitosamente a $nombreArchivo"]));
        }
        else
        {
            $response->getBody()->write(json_encode(["mensaje" => "Hubo un problema al cargar el pedido a $nombreArchivo"]));
        }

        return $response;
    }
    #endregion

    #region SELECT
    public function VerTodos($request, $response)
    {
        $lista_pedidos = AccesoDatos::selectAll($response, "pedidos");
        $response->getBody()->write(json_encode(["Pedidos" => $lista_pedidos], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerPorCodigo($request, $response, $args)
    {
        $codigo = $args['codigo'];
        $lista_pedidos = AccesoDatos::selectCriterioSTR($response, "pedidos", 'codigo', $codigo);
        $response->getBody()->write(json_encode(["Pedidos" => $lista_pedidos], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function VistaCliente($request, $response)
    {
        return false;
    }
    #endregion

    #region UPDATE
    public static function ModificarEstado($request, $response, $args)
    {
        $estado = $args['estado'];
        $codigo = $args['codigo'];
        AccesoDatos::update($response, 'pedidos', 'estado', $estado, 'codigo', '=', $codigo);
        $response->getBody()->write(json_encode(["Pedido $codigo" => "Ha cambiado de estado"], JSON_PRETTY_PRINT));
        return $response;
    }
    #endregion
    
    public static function AgregarTiempoPreparacion($request, $response, $args)
    {
        $tiempo = intval($args['tiempo']);
        $codigo = $args['codigo'];
        AccesoDatos::update($response, 'pedidos', 'tiempoPreparacion', $tiempo, 'codigo', '=', $codigo);
        $response->getBody()->write(json_encode(["Pedido $codigo" => "Le agregaron un tiempo de $tiempo minutos aproximadamente..."], JSON_PRETTY_PRINT));
        return $response;
    }

    #region DELETE
    public function EliminarPorCodigo($request,$response,$args)
    {
        $codigo = $args['codigo'];
        AccesoDatos::deleteCodigo($response, "pedidos", "codigo", $codigo);
        $response->getBody()->write(json_encode(["Pedido $codigo" => "Fue eliminado exitosamente"], JSON_PRETTY_PRINT));
        return $response;
    }
    #endregion

}