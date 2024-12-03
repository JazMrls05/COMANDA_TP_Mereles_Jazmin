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

            $tiposValidos = ['bebida','comida'];
            $sectoresValidos = ['cocina','barra','choperas'];
            $coincidencia = AccesoDatos::selectLike($response, 'productos', 'nombre', "'$nombre'");

            if(!(in_array($tipo, $tiposValidos)))
            {
                $mensajeError[] = "Tipo invalido. Debe ser 'comida' o 'bebida'";
                $error = true;
            }

            if(!(in_array($sector, $sectoresValidos)))
            {
                $mensajeError[] = "Sector invalido!";
                $error = true;
            }
            elseif($sector == 'cocina' && ($tipo == 'bebida'))
            {
                $mensajeError[] = "El sector no coincide con el tipo de comida";
                $error = true;
            }
            elseif(($sector == 'choperas' || $sector == 'barra') && ($tipo == 'comida'))
            {
                $mensajeError[] = "El sector no coincide con el tipo de comida";
                $error = true;
            }

            if(!empty($coincidencia) || $nombre = '')
            {
                $mensajeError[] = "Codigo no disponible!";
                $error = true;
            }

            if($precio <= 0 || $stock <= 0)
            {
                $mensajeError[] = "El precio y el stock deben ser mayor a 0";
                $error = true;
            }

        
            if($error == true)
            {
                throw new Exception(json_encode($mensajeError));
            }

            return $handler->handle($request); // todo ok
        }
        catch(Exception $e)
        {
            $mensajeError = json_decode($e->getMessage(), true);
            $payload = json_encode(["Errores" => $mensajeError]);
            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

}