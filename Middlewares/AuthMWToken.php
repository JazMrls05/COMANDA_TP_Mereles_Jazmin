<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once __DIR__ . '/../Utils/AuthJWT.php';

class AuthMWToken // mi token existe?
{
    /**
     * 
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        
        $token = AuthJWT::ObtenerToken($request);
        try 
        {
            AutentificadorJWT::VerificarToken($token);
            $response = $handler->handle($request);
        } 
        catch (Exception $e) {
            $response = new Response();
            $payload = json_encode(array('Mensaje' => 'ERROR: Hubo un error con el TOKEN'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }


}