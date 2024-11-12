<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;
require_once __DIR__ . '/../Utils/AuthJWT.php';

class AuthMWUsuario // Sos admin o empleado?. Si sos empleado, de qué sector?
{
    public static function VerificarAdmin(Request $request, RequestHandler $handler): ResponseMW
    {   
        $response = new ResponseMW();
        $token = AuthJWT::ObtenerToken($request);
        $perfil = AuthJWT::ObtenerData($token)->perfil;

        if ($perfil == 'admin')
        {
            $response->getBody()->write(json_encode(array('Login' => 'Admin verificado!')));
            $response = $handler->handle($request);
        }
        else
        {
            $response->getBody()->write(json_encode(array('Error' => 'Usted no es Admin. Actividad no autorizada')));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function VerificarEmpleado(Request $request, RequestHandler $handler): ResponseMW
    {   
        $response = new ResponseMW();
        $token = AuthJWT::ObtenerToken($request);
        $perfil = AuthJWT::ObtenerData($token)->perfil;

        if ($perfil == 'empleado')
        {
            $response->getBody()->write(json_encode(array('Login' => 'Empleado verificado!')));
            $response = $handler->handle($request);
        }
        else
        {
            $response->getBody()->write(json_encode(array('Error' => 'Usted no es Empleado. Actividad no autorizada')));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }


    #region Empleados 
    public static function VerificarMozo(Request $request, RequestHandler $handler): ResponseMW
    {   
        $response = new ResponseMW();
        $token = AuthJWT::ObtenerToken($request);
        $perfil = AuthJWT::ObtenerData($token)->perfil;
        $sector = AuthJWT::ObtenerData($token)->sector;

        if ($perfil === 'empleado' && $sector === 'salon')
        {
            $response->getBody()->write(json_encode(array('Login' => 'Mozo verificado!')));
            $response = $handler->handle($request);
        }
        else
        {
            $response->getBody()->write(json_encode(array('Error' => 'Usted no es Mozo. Actividad no autorizada')));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function VerificarCocinero(Request $request, RequestHandler $handler): ResponseMW
    {   
        $response = new ResponseMW();
        $token = AuthJWT::ObtenerToken($request);
        $perfil = AuthJWT::ObtenerData($token)->perfil;
        $sector = AuthJWT::ObtenerData($token)->sector;

        if ($perfil == 'empleado' && $sector == 'cocina')
        {
            $response->getBody()->write(json_encode(array('Login' => 'Cocinero verificado!')));
            $response = $handler->handle($request);
        }
        else
        {
            $response->getBody()->write(json_encode(array('Error' => 'Usted no es Cocinero. Actividad no autorizada')));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function VerificarCervecero(Request $request, RequestHandler $handler): ResponseMW
    {   
        $response = new ResponseMW();
        $token = AuthJWT::ObtenerToken($request);
        $perfil = AuthJWT::ObtenerData($token)->perfil;
        $sector = AuthJWT::ObtenerData($token)->sector;

        if ($perfil == 'empleado' && $sector == 'choperas')
        {
            $response->getBody()->write(json_encode(array('Login' => 'Cervecero verificado!')));
            $response = $handler->handle($request);
        }
        else
        {
            $response->getBody()->write(json_encode(array('Error' => 'Usted no es Cocinero. Actividad no autorizada')));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function VerificarBartender(Request $request, RequestHandler $handler): ResponseMW
    {   
        $response = new ResponseMW();
        $token = AuthJWT::ObtenerToken($request);
        $perfil = AuthJWT::ObtenerData($token)->perfil;
        $sector = AuthJWT::ObtenerData($token)->sector;

        if ($perfil == 'empleado' && $sector == 'barra')
        {
            $response->getBody()->write(json_encode(array('Login' => 'Bartender verificado!')));
            $response = $handler->handle($request);
        }
        else
        {
            $response->getBody()->write(json_encode(array('Error' => 'Usted no es Bartender. Actividad no autorizada')));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
    #endregion



}