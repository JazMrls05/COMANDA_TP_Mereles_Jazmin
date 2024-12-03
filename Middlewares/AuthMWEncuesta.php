<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class AuthMWEncuesta // Ingresé los datos correspondientes del producto? para la solicitud INSERT
{
    public function __invoke(Request $request, RequestHandler $handler): ResponseMW
    {   
        try
        {
            $response = new ResponseMW();
            $parametros = $request->getParsedBody();

            $codigoMesa = $parametros['codigoMesa'];
            $error = false;

            $estadoValido = ['Cerrada'];
            $mesaIngresada = AccesoDatos::selectColumnaWhere($response, 'estado', 'mesas', 'codigoMesa', '=', $codigoMesa);

            var_dump($mesaIngresada);
            if(!(in_array($mesaIngresada[0]['estado'], $estadoValido)))
            {
                $mensajeError[] = "La mesa debe estar Cerrada para realizar la encuesta";
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