<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class AuthMWAt_mesa // Ingresé los datos correspondientes de la mesa? para la solicitud INSERT
{
    public function __invoke(Request $request, RequestHandler $handler): ResponseMW
    {   
        try
        {
            $response = new ResponseMW();
            $parametros = $request->getParsedBody();

            $codigoMesa = $parametros['codigoMesa'];
            $cantidadPersonas = $parametros['cantidadPersonas'];
            $error = false;
            $mensajeError = [];

            $coincidencia = AccesoDatos::selectLike($response, 'mesas', 'codigoMesa', "'$codigoMesa'");


            if((strlen($codigoMesa) != 4) || $codigoMesa == '')
            {
                $mensajeError[] = "Debe ingresar 4 caracteres.";
                $error = true;
            }
            elseif (!empty($coincidencia))
            {
                $mensajeError[] = "Codigo no disponible!";
                $error = true;
            }
            elseif(ctype_alnum($codigoMesa) == false)
            {
                $mensajeError[] = "El codigo debe ser alfanumérico";
                $error = true;
            }

            if($cantidadPersonas > 5 && $cantidadPersonas < 2)
            {
                $mensajeError[] = "La cantidad de personas en una mesas puede ser de 2 a 6";
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