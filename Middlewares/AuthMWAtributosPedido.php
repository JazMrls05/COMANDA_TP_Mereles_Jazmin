<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class AuthMWAt_pedido // Ingresé los datos correspondientes del pedido? para la solicitud INSERT
{
    public function __invoke(Request $request, RequestHandler $handler): ResponseMW
    {   
        try
        {
            $response = new ResponseMW();
            $parametros = $request->getParsedBody();

            $codigo = $parametros['codigo']; 
            $codigoMesa = $parametros['codigoMesa'];
            $sector = $parametros['sector'];
            $nombre = $parametros['nombre']; 
            $precioFinal = $parametros['precioFinal']; 

            $error = false;
            $mensajeError = [];

            $coincidenciaCod_pedido = AccesoDatos::selectLike($response, 'pedidos', 'codigo', "P-$codigo");
            $coincidenciaCod_mesa = AccesoDatos::selectLike($response, 'mesas', 'codigoMesa', "M-$codigoMesa");

            $estadoMesa = AccesoDatos::selectColumnaWhere($response, 'estado', 'mesas', 'codigoMesa', '=', $codigoMesa);
            $mesa = $estadoMesa[0];

            if($mesa['estado'] == 'Cerrada')
            {
                $mensajeError[] = "La mesa $codigoMesa está cerrada, usar otra mesa";
                $error = true;
            }

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