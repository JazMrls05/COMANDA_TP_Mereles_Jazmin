<?php

require_once __DIR__ . '/../Modelos/Mesa.php';
require_once __DIR__ . '/../DB/AccesoDatos.php';

class MesaController
{
    #region INSERT
    public function Guardar($request, $response)
    {
        $datos = $request->getParsedBody();

        $mesa = new Mesa($datos['codigoMesa'], $datos['cantidadPersonas'], "Con cliente esperando pedido");

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

        return $response->withHeader('Content-Type', 'application/json');
    }

    #endregion
    
    #region SELECT
    public function VerTodos($request, $response)
    {
        $lista_mesas = AccesoDatos::selectAll($response, "mesas");
        $response->getBody()->write(json_encode(["Mesas" => $lista_mesas], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerEstados($request, $response)
    {
        $columnas = 'codigoMesa, estado';
        $estadosMesas = AccesoDatos::selectColumna($response, $columnas, 'mesas');

        $response->getBody()->write(json_encode(["Mesas y sus estados" => $estadosMesas], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function VerMasUsada($request, $response)
    {
        $columnas = 'codigoMesa, usoMesa';
        $mesaMasUsada = AccesoDatos::selectMayorCantidad($response,$columnas, 'mesas', 'usoMesa');
        $response->getBody()->write(json_encode(["Mesa mas usada" => $mesaMasUsada], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }
    #endregion
    
    #region UPDATE
    public static function ModificarEstado($request, $response, $args)
    {
        $codigo = $args['codigoMesa'];
        $estado = $args['estado'];
        $lista_pedidos = AccesoDatos::selectColumnaWhere($response, 'precioFinal','pedidos', 'codigoMesa' ,'=', $codigo);

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
            default:
                $response->getBody()->write(json_encode(["Error" => "Estado invalido"], JSON_PRETTY_PRINT));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function CobrarMesa($request,$response)
    {
        $codigo = $request->getAttribute('codigoMesa');
        if(AccesoDatos::update($response, 'mesas', 'estado', 'Con cliente pagando' , 'codigoMesa', '=', $codigo) &&
        AccesoDatos::updateImporteComanda($request,$response))
        {
            $response->getBody()->write(json_encode(["Mesa $codigo" => "Se esta cobrando la cuenta..."], JSON_PRETTY_PRINT));
        }
        else
        {
            $response->getBody()->write(json_encode(["Mesa $codigo" => "Algo salió mal al cobrar la cuenta..."], JSON_PRETTY_PRINT));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function CerrarMesa($request, $response)
    {
        $codigo = $request->getAttribute('codigo');
        if(AccesoDatos::update($response, 'mesas', 'estado', 'Cerrada', 'codigo', '=', $codigo) &&
        AccesoDatos::SumarCantidad($response, 'mesas', $codigo))
        {
            $response->getBody()->write(json_encode(["Mesa $codigo" => "Ha sido cerrada."], JSON_PRETTY_PRINT));
        }
        else
        {
            $response->getBody()->write(json_encode(["Error" => "Hubo un problema al intentar cerrar la mesa $codigo"], JSON_PRETTY_PRINT));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
    #endregion

    #region DELETE
    public function EliminarPorCodigo($request,$response)
    {
        $codigo = $request->getAttribute('codigo');
        AccesoDatos::deleteCodigo($response, "mesas", "codigoMesa", $codigo);
        $response->getBody()->write(json_encode(["Mesa $codigo" => "Fue eliminado exitosamente"], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }
    #endregion
}