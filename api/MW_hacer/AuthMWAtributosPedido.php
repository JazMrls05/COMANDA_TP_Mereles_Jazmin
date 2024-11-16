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
    *nombre (de la comida): exista en la tabla de productos y coincida su tipo
    *Cliente: nombre del cliente. 

    Lógica de pedidos y clientes:
    - Si se eligió una mesa para dos por ejemplo, solo dos nombres de clientes podrán ser ingresados:
    Mesa M23rt: pide agustín, pide Franco; Jaz no puede pedir en esa mesa hasta que los clientes se hayan ido, y la mesa quede desocupada.
    se emitirá un mensaje de "mesa ocupada" en tal caso. Se podrá hacer una sugerencia de mesas disponibles o muy rebuscado?
    */

    public function __invoke(Request $request, RequestHandler $handler): ResponseMW
    {   
        try
        {
            $response = new ResponseMW();
            $parametros = $request->getParsedBody();

            $codigo = $parametros['codigo']; //
            $estado = $parametros['estado'];
            $codigoMesa = $parametros['codigoMesa'];
            $cliente = $parametros['cliente'];
            $sector = $parametros['sector'];
            $tipo = $parametros['tipo'];
            $nombre = $parametros['nombre']; 
            $precioFinal = $parametros['precioFinal']; //
            $error = false;

            $coincidencia = AccesoDatos::selectLike($response, 'pedidos', 'codigo', "'$codigo'");

            if(IntlChar::isalnum($codigo))
            {
                $mensajeError = json_encode(array("Error" => "El codigo debe ser alfanumérico"));
                $error = true;
            }
            elseif(!empty($coincidencia) || $codigo = '')
            {
                $mensajeError = json_encode(array("Error" => "Codigo no disponible!"));
                $error = true;
            }

            if($precioFinal <= 0)
            {
                $mensajeError = json_encode(array("Error" => "El precio debe ser mayor a 0"));
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