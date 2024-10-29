<?php

require_once __DIR__ . '/../Modelos/Mesa.php';
require_once __DIR__ . '/../DB/AccesoDatos.php';

class MesaController
{
    public function Guardar($request, $response)
    {
        $parsear_datos = $request->getParsedBody();

        $mesa = new Mesa($parsear_datos['codigoMesa'],$parsear_datos['estado']);

        $array_datos = ["M" . $mesa->codigoMesa, $mesa->estado];

        $tabla = 'mesas';
        $array_encabezados = ['codigoMesa','estado'];
        
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
    
    public function VerTodos($request, $response)
    {
        $lista_mesas = AccesoDatos::selectAll($response, "mesas");
        $response->getBody()->write(json_encode(["Mesas" => $lista_mesas], JSON_PRETTY_PRINT));
        return $response;
    }

    public function EliminarPorCodigo($request,$response,$args)
    {
        $id = $args['id'];
        AccesoDatos::deleteID($response, "mesas", $id);
        $response->getBody()->write(json_encode(["Mesa $id" => "Fue eliminado exitosamente"], JSON_PRETTY_PRINT));
        return $response;
    }
}