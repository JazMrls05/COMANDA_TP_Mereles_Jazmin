<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class AuthMWAt_producto // Ingresé los datos correspondientes del producto? para la solicitud INSERT
{
    public function __invoke(Request $request, RequestHandler $handler): ResponseMW
    {   
        try
        {
            $response = new ResponseMW();
            $parametros = $request->getParsedBody();

            $tipo = $parametros['tipo'];
            $sector = $parametros['sector'];
            $nombre = $parametros['nombre'];
            $precio = $parametros['precio'];
            $stock = $parametros['stock'];
            $error = false;

            $coincidencia = AccesoDatos::selectLike($response, 'productos', 'nombre', "'$nombre'");

            if($tipo != 'bebida' && $tipo != 'comida' || $tipo == '')
            {
                $mensajeError = json_encode(array("Error" => "Tipo invalido. Debe ser 'comida' o 'bebida'"));
                $error = true;
            }

            if($sector != 'cocina' && $sector != 'barra' && $sector != 'choperas' || $sector == '')
            {
                $mensajeError = json_encode(array("Error" => "Sector invalido!"));
                $error = true;
            }
            elseif($sector == 'cocina' && ($tipo == 'bebida'))
            {
                $mensajeError = json_encode(array("Error" => "El sector no coincide con el tipo de comida"));
                $error = true;
            }
            elseif(($sector == 'choperas' || $sector == 'barra') && ($tipo == 'comida'))
            {
                $mensajeError = json_encode(array("Error" => "El sector no coincide con el tipo de comida"));
                $error = true;
            }

            if(!empty($coincidencia) || $nombre = '')
            {
                $mensajeError = json_encode(array("Error" => "Codigo no disponible!"));
                $error = true;
            }

            if($precio <= 0 || $stock <= 0)
            {
                $mensajeError = json_encode(array("Error" => "El precio y el stock deben ser mayor a 0"));
                $error = true;
            }

        
            if($error == true)
            {
                throw new Exception(json_encode(["Error"=> $mensajeError]));
            }

            return $handler->handle($request); // todo ok
        }
        catch(Exception $e)
        {
            $payload = json_encode(['Error' => $e->getMessage]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

}