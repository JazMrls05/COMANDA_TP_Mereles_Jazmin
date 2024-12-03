<?php
require_once __DIR__ . '/../Modelos/Encuesta.php';
require_once __DIR__ . '/../DB/AccesoDatos.php';
class EncuestaController
{
    public function Guardar($request, $response)
    {
        $datos = $request->getParsedBody();
        $fechaActual = date('Y-m-d');

        $encuesta = new Encuesta($datos['codigoMesa'], $datos['codigoPedido'], $datos['puntajeMesa'], $datos['puntajeRestaurante'], 
        $datos['puntajeMozo'],$datos['puntajeCocinero'], $datos['comentario'],);

        $array_datos = [$encuesta->codigoMesa, $encuesta->codigoPedido, $encuesta->puntajeMesa,$encuesta->puntajeRestaurante,
        $encuesta->puntajeMozo,$encuesta->puntajeCocinero,$encuesta->comentario, $fechaActual];

        $tabla = 'encuestas';
        $array_encabezados = ['codigoMesa','codigoPedido','puntajeMesa','puntajeRestaurante', 'puntajeMozo','puntajeCocinero','comentario', 'fecha'];
        
        if (AccesoDatos::insert($response, $tabla, $array_encabezados, $array_datos))
        {
            $response->getBody()->write(json_encode(["mensaje" => "Encuesta cargada exitosamente"]));
        }
        else
        {
            $response->getBody()->write(json_encode(["mensaje" => "Hubo un problema al cargar la encuesta "]));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function VerMejoresComentarios($request,$response)
    {
        $listaComentarios = AccesoDatos::promedioYmejorElemento($response);
        $response->getBody()->write(json_encode(["Mejores comentarios" => $listaComentarios]));    
        return $response->withHeader('Content-Type', 'application/json');
    }
}