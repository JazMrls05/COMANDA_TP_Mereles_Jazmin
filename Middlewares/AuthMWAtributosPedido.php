<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class AuthMWAt_pedido // Ingresé los datos correspondientes del pedido? para la solicitud INSERT
{
    /*
    * Hay que verificar que:
    * Codigo: alfanumérico y de 5 caracteres.
    * codigoMesa: coincida con una mesa existente, cuyo estado no sea "cerrada"
    * sector: coincida con el tipo de comida
    *nombre (de la comida): exista en la tabla de productos y coincida su tipo
    *Cliente: nombre del cliente. 

    Lógica de pedidos y clientes:
    - Si se eligió una mesa para dos por ejemplo, solo dos nombres de clientes podrán ser ingresados:
    Mesa M23rt: pide agustín, pide Franco; Jaz no puede pedir en esa mesa hasta que los clientes se hayan ido, y la mesa quede desocupada.
    se emitirá un mensaje de "mesa ocupada" en tal caso. Se podrá hacer una sugerencia de mesas disponibles o muy rebuscado?
    */

}