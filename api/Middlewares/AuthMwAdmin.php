<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class AuthMWAdmin
{
    public function __invoke(Request $request, RequestHandler $handler): ResponseMW
    {   
        $parametros = $request->getParsedBody();

        $sector = $parametros['perfilAcceso'];
        $clave = $parametros['claveAcceso'];

        if ($sector === 'admin' && $clave === 'Socio') 
        {
            $response = $handler->handle($request);
        } 
        else 
        {
            $response = new ResponseMW();
            $payload = json_encode(array("Error" => "No sos admin; por lo tanto, no podemos autorizar tu acción."));
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}