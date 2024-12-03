<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;
require_once __DIR__ . '/../DB/AccesoDatos.php';
require_once __DIR__ . '/../Utils/AuthJWT.php';

class AuthMWLogin // Mi usuario y clave ingresados, existen?
{
    public function __invoke(Request $request, RequestHandler $handler): ResponseMW
    {   
        $response = new ResponseMW();
        $parametros = $request->getParsedBody();

        $perfil = $parametros['perfilAcceso'];
        $nombre = $parametros['nombreAcceso'];
        $clave = $parametros['claveAcceso'];

        $listaUsuarios = AccesoDatos::selectAll($response,'usuarios');

        $payload = json_encode(array("Error" => "Usuario o contraseña incorrecta."));

        foreach($listaUsuarios as $usuario)
        {
            if($perfil == $usuario['perfil'] && $nombre == $usuario['nombre'] &&
            $clave == $usuario['clave'])
            {
                $datos = array(
                    'idEmpleado' => $usuario['id'],
                    'perfil' => $perfil,
                    'nombre' => $nombre,
                    'clave' => $clave,
                    'sector' => $usuario['sector'],
                    'estado' => $usuario['estado']
                );
                $token = AuthJWT::crearToken($datos);

                $payload = json_encode(array("Login" => "Logueado exitoso!",
                "Token" => "Bearer ". $token));
                break;
            }
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}