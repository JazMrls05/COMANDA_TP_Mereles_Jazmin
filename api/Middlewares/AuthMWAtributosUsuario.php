<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

require_once __DIR__ . '/../DB/AccesoDatos.php';

class AuthMWAt_usuario // Ingresé los datos correspondientes del usuario? para la solicitud INSERT
{
    public function __invoke(Request $request, RequestHandler $handler): ResponseMW
    {   
        try
        {
            $response = new ResponseMW();
            $parametros = $request->getParsedBody();

            $perfil = $parametros['perfil'];
            $nombre = $parametros['nombre'];
            $clave = $parametros['clave'];
            $sector = $parametros['sector'];
            $fechaIngreso = $parametros['fechaIngreso'];
            $error = false;

            $coincidencia = AccesoDatos::selectLike($response, 'usuarios', 'nombre', "'$nombre'");


            if($perfil != 'empleado' || $perfil == '')
            {
                $mensajeError = json_encode(array("Error" => "Tipo de perfil inválido."));
                $error = true;
            }

            if (!empty($coincidencia) || $nombre == '')
            {
                $mensajeError = json_encode(array("Error" => "Nombre no disponible!"));
                $error = true;
            }
            elseif(strlen($nombre > 20))
            {
                $mensajeError = json_encode(array("Error" => "Nombre muy largo! Debe tener menos de 35 caracteres"));
                $error = true;
            }

            if(strlen($clave) < 8)
            {
                $mensajeError = json_encode(array("Error" => "La clave debe tener por lo menos 8 caracteres"));
                $error = true;
            }

            if(($sector != 'cocina' && $sector != 'barra' && $sector != 'choperas' && $sector != 'salon') || $sector == '')
            {
                $mensajeError = json_encode(array("Error" => "Sector inválido. Debe ser 'cocina', 'barra', 'choperas' o 'salon' "));
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