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

            $coincidencia = AccesoDatos::selectLike($response, 'usuarios', 'nombre', "$nombre");
            $sectoresValidos = ['cocina','barra','choperas','salon'];


            if($perfil != 'empleado' || $perfil == '')
            {
                $mensajeError[] = "Tipo de perfil inválido.";
                $error = true;
            }

            if (!empty($coincidencia) || $nombre == '')
            {
                $mensajeError[] = "Nombre no disponible!";
                $error = true;
            }
            elseif(strlen($nombre > 20))
            {
                $mensajeError[] = "Nombre muy largo! Debe tener menos de 35 caracteres";
                $error = true;
            }

            if(strlen($clave) < 8)
            {
                $mensajeError[] = "La clave debe tener por lo menos 8 caracteres";
                $error = true;
            }

            if(!(in_array($sector,$sectoresValidos)))
            {
                $mensajeError[] = "Sector inválido. Debe ser 'cocina', 'barra', 'choperas' o 'salon' ";
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