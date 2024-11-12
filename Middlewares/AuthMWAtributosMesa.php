<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class AuthMWAt_mesa // Ingresé los datos correspondientes de la mesa? para la solicitud INSERT
{
    /*
    * Hay que verificar que:
    * la cantidad de personas para cada mesa sea entre 2 y 6.
    * el código de la mesa sea de 5 caracteres y alfanumérico.
    */
}