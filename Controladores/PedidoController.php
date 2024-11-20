<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once __DIR__ . '/../Modelos/Pedido.php';
require_once __DIR__ . '/../DB/AccesoDatos.php';
require_once __DIR__ . '/../Utils/AuthJWT.php';

class PedidoController
{
    #region INSERT
    public function Guardar($request, $response)
    {
        $parsear_datos = $request->getParsedBody();

        $pedido = new Pedido($parsear_datos['codigo'],'Pendiente', $parsear_datos['codigoMesa'],
        $parsear_datos['cliente'],$parsear_datos['sector'],$parsear_datos['nombre'], $parsear_datos['precioFinal']);

        $array_datos = ['P-' .$pedido->codigo, $pedido->estado,  $pedido->codigoMesa,
        $pedido->cliente,$pedido->sector, $pedido->nombre, $pedido->precioFinal];

        $tabla = 'pedidos';
        $array_encabezados = ['codigo','estado','codigoMesa', 'cliente','sector', 'nombre','precioFinal'];
        
        if (AccesoDatos::insert($response, $tabla, $array_encabezados, $array_datos))
        {
            if(isset($_FILES['sacarFoto']))
            {
                self::SubirFoto($request,$response,$pedido->codigoMesa, $pedido->codigo);
            }

            $response->getBody()->write(json_encode(["mensaje" => "Pedido $pedido->codigo cargado exitosamente"]));
        }
        else
        {
            $response->getBody()->write(json_encode(["mensaje" => "Hubo un problema al cargar el pedido $pedido->codigo"]));
        }
        return $response;
    }

    public static function SubirFoto($request, $response, $codigoMesa, $codigoPedido)
    {
        $carpeta_fotos = "Pedidos/Fotos/";
        $archivo = $_FILES['sacarFoto']['name'];
        $extension_archivo = pathinfo($archivo, PATHINFO_EXTENSION);

        $nombre_archivo = $codigoMesa . "_pedido_" . $codigoPedido . ".$extension_archivo";
        $ruta_destino = $carpeta_fotos . $nombre_archivo;

        if(!file_exists($carpeta_fotos))
        {
            mkdir("./Pedidos/Fotos", 0777, true);
        }

        $resultado = move_uploaded_file($_FILES['sacarFoto']['tmp_name'],$ruta_destino);

        if($resultado)
        {
            $response->getBody()->write(json_encode(["Foto" => "Foto cargada exitosamente a $ruta_destino"]));
        }
        else{
            $response->getBody()->write(json_encode(["Error" => "Ocurrió algún error al subir el fichero. No pudo cargarse<br/>"]));
        }
    }

    public function GuardarDesdeCSV($request, $response)
    {
        $tabla = 'pedidos';
        $nombreArchivo = 'pedido.csv';
        
        if (AccesoDatos::insertDesdeCSV($response, $tabla, $nombreArchivo))
        {
            $response->getBody()->write(json_encode(["mensaje" => "Pedido cargado exitosamente a $nombreArchivo"]));
        }
        else
        {
            $response->getBody()->write(json_encode(["mensaje" => "Hubo un problema al cargar el pedido a $nombreArchivo"]));
        }
        return $response;
    }

    #endregion



    #region SELECT
    public static function VerTodos($request, $response)
    {
        $lista_pedidos = AccesoDatos::selectAll($response, "pedidos");
        $response->getBody()->write(json_encode(["Pedidos" => $lista_pedidos], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function VerPorCodigo($request, $response, $args)
    {
        $codigo = $args['codigo'];
        $lista_pedidos = AccesoDatos::selectCriterioSTR($response, "pedidos", 'codigo', $codigo);
        $response->getBody()->write(json_encode(["Pedidos" => $lista_pedidos], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function VerTodosPorEstado($request, $response, $args)
    {
        $estado = $args['estado'];
        switch($estado)
        {
            case 1:
                $lista_pedidos = AccesoDatos::selectCriterioSTR($response, "pedidos", 'estado', 'Pendiente');
                $response->getBody()->write(json_encode(["Pedidos" => $lista_pedidos], JSON_PRETTY_PRINT));
                break;
            case 2: 
                $lista_pedidos = AccesoDatos::selectCriterioSTR($response, "pedidos", 'estado', 'En preparacion');
                $response->getBody()->write(json_encode(["Pedidos" => $lista_pedidos], JSON_PRETTY_PRINT));
                break;
            case 3:
                $lista_pedidos = AccesoDatos::selectCriterioSTR($response, "pedidos", 'estado', 'listo para servir');
                $response->getBody()->write(json_encode(["Pedidos" => $lista_pedidos], JSON_PRETTY_PRINT));
                break;
            case "todos":
                $lista_pedidos = AccesoDatos::selectColumna($response, 'estado', 'pedidos');
                $response->getBody()->write(json_encode(["Pedidos" => $lista_pedidos], JSON_PRETTY_PRINT));
                break;
            default:
                $response->getBody()->write(json_encode(["Error" => "Estado invalido"], JSON_PRETTY_PRINT));
        }
        return $response;
    }

    public static function VerPendientes($request, $response)
    {
        $token = AuthJWT::ObtenerToken($request);
        $sector = AuthJWT::ObtenerData($token)->sector;
        $lista_pedidos = AccesoDatos::selectCriterioSTR_AND($response, "pedidos", 'estado', 'Pendiente', 'sector', $sector);
        $response->getBody()->write(json_encode(["Pedidos" => $lista_pedidos], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function VerListosParaServir($request, $response)
    {
        $token = AuthJWT::ObtenerToken($request);
        $sector = AuthJWT::ObtenerData($token)->sector;
        $lista_pedidos = AccesoDatos::selectCriterioSTR_AND($response, "pedidos", 'estado', 'Listos para servir', 'sector', $sector);
        $response->getBody()->write(json_encode(["Pedidos" => $lista_pedidos], JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function VistaCliente($request, $response)
    {        
        $listaPedidos = AccesoDatos::selectAll($response,'pedidos');

        $datosCliente = $request->getQueryParams();
        $codigoMesa = $datosCliente['codigoMesa'];
        $codigoPedido = $datosCliente['codigoPedido'];

        foreach($listaPedidos as $pedido)
        {
            $horaEstimada = $pedido['preparacionEstimada'];
            if($codigoMesa == $pedido['codigoMesa'] && $codigoPedido == $pedido['codigo'])
            {
                if($pedido['estado'] == 'En preparacion')
                {
                    $response->getBody()->write(json_encode(["Pedido $codigo" => "Estará listo a las $horaEstimada"], JSON_PRETTY_PRINT));
                }
                else
                {
                    $response->getBody()->write(json_encode(["Pedido $codigo" => "No se esta prepando aun"], JSON_PRETTY_PRINT));
                }
                
            }
        }

        return $response;

    }

    public static function VerDemoras($request, $response)
    {
        $lista_pedidos = AccesoDatos::selectAll($response, 'pedidos');

        foreach($lista_pedidos as $pedido)
        {
            
        }
        
        return $response;
    }

    #endregion

    #region UPDATE
    public static function ModificarEstadoPreparando($request, $response, $args)
    {
        $datos = $request->getParsedBody();
        $minutos = $datos['minutosEstimados'];
        $codigo = $args['codigo'];
        $horaSeteada = false;
        $token = AuthJWT::ObtenerToken($request);
        $sector = AuthJWT::ObtenerData($token)->sector;
        $idEmpleado = AuthJWT::ObtenerData($token)->idEmpleado;

        $lista_pedidos = AccesoDatos::selectCriterioSTR($response,'pedidos','codigo', $codigo);
        
        $horaActual = new DateTime();
        $horaActual->modify("+$minutos minutes");

        $horaEstimada = $horaActual->format('H:i:s');

        foreach($lista_pedidos as $pedido)
        {
            if($pedido['sector'] == $sector)
            {
                AccesoDatos::update($response, 'pedidos', 'id_empleado', $idEmpleado, 'codigo', '=', $codigo);
                if($minutos == '')
                {
                    $response->getBody()->write(json_encode(["Error" => "Agregar hora estimada de preparacion!! - formato 00:00:00"], JSON_PRETTY_PRINT));
                }
                else
                {
                    AccesoDatos::update($response, 'pedidos', 'preparacionEstimada', $horaEstimada, 'codigo', '=', $codigo);
                    $horaSeteada = true;
                }

                if(AccesoDatos::update($response, 'pedidos', 'estado', 'En preparacion', 'codigo', '=', $codigo) && $horaSeteada == true)
                {
                    $response->getBody()->write(json_encode(["Pedido $codigo" => "Ha entrado en estado de preparacion..."], JSON_PRETTY_PRINT));
                }
                else 
                {
                    $response->getBody()->write(json_encode(["Error Pedido $codigo" => "Algo salió mal"], JSON_PRETTY_PRINT));
                }
                break;
            }
            else
            {
                $response->getBody()->write(json_encode(["Error" => "Empleado no correspondiente"], JSON_PRETTY_PRINT));
            }
        }


        
        return $response;
    }

    public static function ModificarEstadoListo($request, $response, $args)
    {
        $codigo = $args['codigo'];
        $token = AuthJWT::ObtenerToken($request);
        $sector = AuthJWT::ObtenerData($token)->sector;
        $idEmpleado = AuthJWT::ObtenerData($token)->idEmpleado;

        $lista_pedidos = AccesoDatos::selectCriterioSTR($response,'pedidos','codigo', $codigo);

        $horaEntrega = new dateTime();
        $horaEntrega = $horaEntrega->format('H:i:s');
        
        foreach($lista_pedidos as $pedido)
        {
            if($pedido['sector'] == $sector)
            {

                if(AccesoDatos::update($response, 'pedidos', 'estado', 'Listo para servir', 'codigo', '=', $codigo) && 
                AccesoDatos::update($response, 'pedidos', 'entregaFinal', $horaEntrega, 'codigo', '=', $codigo) )
                {
                    $response->getBody()->write(json_encode(["Pedido $codigo" => "Esta listo para servir!"], JSON_PRETTY_PRINT));
                }
                else 
                {
                    $response->getBody()->write(json_encode(["Error Pedido $codigo" => "Algo salió mal"], JSON_PRETTY_PRINT));
                }
                break;
            }
            else
            {
                $response->getBody()->write(json_encode(["Error" => "Empleado no correspondiente"], JSON_PRETTY_PRINT));
            }
        }


        
        return $response;
    }
    #endregion

    #region DELETE
    public function EliminarPorCodigo($request,$response,$args)
    {
        $codigo = $args['codigo'];
        AccesoDatos::deleteCodigo($response, "pedidos", "codigo", $codigo);
        $response->getBody()->write(json_encode(["Pedido $codigo" => "Fue eliminado exitosamente"], JSON_PRETTY_PRINT));
        return $response;
    }
    #endregion

}