<?php

require_once __DIR__ . '/../Modelos/Mesa.php';
require_once __DIR__ . '/../DB/AccesoDatos.php';

class MesaController
{
    #region INSERT
    public function Guardar($request, $response)
    {
        $parsear_datos = $request->getParsedBody();

        $mesa = new Mesa($parsear_datos['codigoMesa'], $parsear_datos['cantidadPersonas'], "Con cliente esperando pedido");

        $array_datos = ["M-" . $mesa->codigoMesa, $mesa->cantidadPersonas, $mesa->estado];

        $tabla = 'mesas';
        $array_encabezados = ['codigoMesa','cantidadPersonas', 'estado'];
        
        if (AccesoDatos::insert($response, $tabla, $array_encabezados, $array_datos))
        {
            $response->getBody()->write(json_encode(["mensaje" => "Mesa cargada exitosamente"]));
        }
        else
        {
            $response->getBody()->write(json_encode(["mensaje" => "Hubo un problema al cargar la mesa"]));
        }

        return $response;
    }

    public function GuardarDesdeCSV($request, $response)
    {
        $tabla = 'mesas';
        $nombreArchivo = 'mesa.csv';
        
        if (AccesoDatos::insertDesdeCSV($response, $tabla, $nombreArchivo))
        {
            $response->getBody()->write(json_encode(["mensaje" => "Mesa cargada exitosamente a $nombreArchivo"]));
        }
        else
        {
            $response->getBody()->write(json_encode(["mensaje" => "Hubo un problema al cargar la mesa a $nombreArchivo"]));
        }

        return $response;
    }
    #endregion
    
    #region SELECT
    public function VerTodos($request, $response)
    {
        $lista_mesas = AccesoDatos::selectAll($response, "mesas");
        $response->getBody()->write(json_encode(["Mesas" => $lista_mesas], JSON_PRETTY_PRINT));
        return $response;
    }

    public function VerTodosPorEstado($request, $response,$args)
    {
        $estado = $args['estado'];
        switch($estado)
        {
            case 1:
                $lista_mesas = AccesoDatos::selectCriterioSTR($response, "mesas", 'estado', 'con cliente esperando pedido');
                $response->getBody()->write(json_encode(["Mesas" => $lista_mesas], JSON_PRETTY_PRINT));
                break;
            case 2: 
                $lista_mesas = AccesoDatos::selectCriterioSTR($response, "mesas", 'estado', 'con cliente comiendo');
                $response->getBody()->write(json_encode(["Mesas" => $lista_mesas], JSON_PRETTY_PRINT));
                break;
            case 3:
                $lista_mesas = AccesoDatos::selectCriterioSTR($response, "mesas", 'estado', 'con cliente pagando');
                $response->getBody()->write(json_encode(["Mesas" => $lista_mesas], JSON_PRETTY_PRINT));
                break;
            case 4:
                $lista_mesas = AccesoDatos::selectCriterioSTR($response, "mesas", 'estado', 'cerrada');
                $response->getBody()->write(json_encode(["Mesas" => $lista_mesas], JSON_PRETTY_PRINT));
                break;
            case "todos":
                $lista_mesas = AccesoDatos::selectColumna($response, 'estado', 'mesas');
                $response->getBody()->write(json_encode(["Mesas" => $lista_mesas], JSON_PRETTY_PRINT));
                break;
            default:
                $response->getBody()->write(json_encode(["Error" => "Estado invalido"], JSON_PRETTY_PRINT));
        }
        return $response;
    }
    #endregion
    
    #region UPDATE
    public static function ModificarEstado($request, $response, $args)
    {
        $codigo = $args['codigoMesa'];
        $estado = $args['estado'];
        $lista_pedidos = AccesoDatos::selectColumnaWhere($response, 'precioFinal','pedidos', 'codigoMesa' ,'=', $codigo);
        $lista_precios = [];

        foreach($lista_pedidos as $pedido)
        {
            array_push($lista_precios, $pedido['precioFinal']);
        }
        $precioTotal = array_sum($lista_precios);

        switch($estado)
        {
            case 1:
                AccesoDatos::update($response, 'mesas', 'estado', 'Con cliente esperando pedido' , 'codigoMesa', '=', $codigo);
                $response->getBody()->write(json_encode(["Mesa $codigo" => "Ha sido modificada."], JSON_PRETTY_PRINT));
                break;
            case 2: 
                AccesoDatos::update($response, 'mesas', 'estado', 'Con cliente comiendo' , 'codigoMesa', '=', $codigo);
                $response->getBody()->write(json_encode(["Mesa $codigo" => "Los clientes estan comiendo."], JSON_PRETTY_PRINT));
                break;
            case 3:
                AccesoDatos::update($response, 'mesas', 'estado', 'Con cliente pagando' , 'codigoMesa', '=', $codigo);
                AccesoDatos::update($response, 'mesas', 'importeTotal', $precioTotal , 'codigoMesa', '=', $codigo);
                $response->getBody()->write(json_encode(["Mesa $codigo" => "Se esta cobrando la cuenta..."], JSON_PRETTY_PRINT));
                break;
            default:
                $response->getBody()->write(json_encode(["Error" => "Estado invalido"], JSON_PRETTY_PRINT));
        }
        return $response;
    }
    public static function CerrarMesa($request, $response, $args)
    {
        $codigo = $args['codigo'];
        AccesoDatos::update($response, 'mesas', 'estado', 'cerrada', 'codigo', '=', $codigo);
        $response->getBody()->write(json_encode(["Mesa $codigo" => "Ha sido cerrada."], JSON_PRETTY_PRINT));
        return $response;
    }
    #endregion

    #region DELETE
    public function EliminarPorCodigo($request,$response,$args)
    {
        $codigo = $args['codigo'];
        AccesoDatos::deleteCodigo($response, "mesas", "codigoMesa", $codigo);
        $response->getBody()->write(json_encode(["Mesa $codigo" => "Fue eliminado exitosamente"], JSON_PRETTY_PRINT));
        return $response;
    }
    #endregion
}