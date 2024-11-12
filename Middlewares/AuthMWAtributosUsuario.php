<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class AuthMWAt_usuario // Ingresé los datos correspondientes del usuario? para la solicitud INSERT
{
    /*
    * Hay que verificar que el perfil sea Admin o empleado: no null 
        - En caso de ser admin. Verificar que no haya mas de 3. Los admin vendrían a ser los socios.
    * Nombre: cualquiera. no null
    * Clave: Única. no null
    * Sector: no null
    - Si es un empleado: cocina (cocinero), barra(bartender), salon(mozo), choperas(cervecero)
    - Si es un admin: administración.     
    */
}