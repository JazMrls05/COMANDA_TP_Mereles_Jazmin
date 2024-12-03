<?php

require_once __DIR__ . '/../Modelos/Comanda.php';
require_once __DIR__ . '/../DB/AccesoDatos.php';

class ComandaController
{
    public function Guardar($request, $response, $args)
    {
        $cantidad = $args['cantidadComandas'];
        $tabla = 'comandas';
        $array_datos = [0];
        $array_encabezados = ['importeTotal'];

        $i = 0;

        if($cantidad > 5)
        {
            $response->getBody()->write(json_encode(["Error" => "No se pueden hacer mas de 5 comandas a la vez"]));
        }
        else
        {
            while ($i < $cantidad)
            {
                if (AccesoDatos::insert($response, $tabla, $array_encabezados, $array_datos))
                {
                    $response->getBody()->write(json_encode(["Comanda" => "cargada exitosamente"]));
                }
                else
                {
                    $response->getBody()->write(json_encode(["Comanda" => "Hubo un problema al cargar la comanda"]));
                }
                $i++;
            }
        }

        
        return $response->withHeader('Content-Type', 'application/json');
    }

    
}