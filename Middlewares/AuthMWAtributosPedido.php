<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class AuthMWAt_pedido // Ingresé los datos correspondientes del pedido? para la solicitud INSERT
{
    /*
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

            $codigo = $parametros['codigo']; 
            $codigoMesa = $parametros['codigoMesa'];
            //$cliente = $parametros['cliente'];
            $sector = $parametros['sector'];
            $tipo = $parametros['tipo'];
            $nombre = $parametros['nombre']; 
            $precioFinal = $parametros['precioFinal']; 

            $error = false;
            $mensajeError = [];

            $coincidenciaCod_pedido = AccesoDatos::selectLike($response, 'pedidos', 'codigo', "P-$codigo");
            $coincidenciaCod_mesa = AccesoDatos::selectLike($response, 'mesas', 'codigoMesa', "M-$codigoMesa");

            #region Código
            $patron1 = "/[a-zA-Z]/"; // hay letras?
            $patron2 = "/\d/"; // hay numeros? (digitos)

            if(!(preg_match($patron1, $codigo) && preg_match($patron2, $codigo))) 
            {
                $mensajeError[] = "El codigo NO es alfanumérico";
                $error = true;
            }

            if((strlen($codigo) != 4) || $codigo == '')
            {
                $mensajeError[] = "Debe ingresar 4 caracteres.";
                $error = true;
            }
            elseif(!empty($coincidenciaCod_pedido) || $codigo = '')
            {
                $mensajeError[] = "Codigo no disponible!";
                $error = true;
            }
            #endregion

            #region CodigoMesa
            if(empty($coincidenciaCod_mesa))
            {
                $mensajeError[] = "La mesa solicitada no existe";
                $error = true;
            }
            
            #endregion

            #region Precio
            if($precioFinal <= 0)
            {
                $mensajeError[] = "El precio debe ser mayor a 0";
                $error = true;
            }
            #endregion


        ////////
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