<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class AuthMWAt_producto // Ingresé los datos correspondientes del producto? para la solicitud INSERT
{
    /*
    * Hay que verificar que:
    * nada sea null 
    *stock (inicial) sea mayor a 0
    * precio mayor a 0, y sea un decimal(opcional?)
    */
}