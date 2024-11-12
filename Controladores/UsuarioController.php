<?php

require_once __DIR__ . '/../Modelos/Personal/Usuario.php'; 

class UsuarioController
{

    #region INSERT
    public function Guardar($request, $response)
    {
        $parsear_datos = $request->getParsedBody();

        $usuario = new Usuario($parsear_datos['perfil'],$parsear_datos['nombre'], 
        $parsear_datos['clave'],$parsear_datos['sector'],$parsear_datos['fechaIngreso']);

        $array_datos = [$usuario->perfil,$usuario->nombre,$usuario->clave,$usuario->sector, $usuario->fechaIngreso];

        $tabla = 'usuarios';
        $array_encabezados = ['perfil','nombre', 'clave','sector','fechaIngreso'];
        
        if (AccesoDatos::insert($response, $tabla, $array_encabezados, $array_datos))
        {
            $response->getBody()->write(json_encode(["mensaje" => "Usuario cargado exitosamente"]));
        }
        else
        {
            $response->getBody()->write(json_encode(["mensaje" => "Hubo un problema al cargar el usuario"]));
        }

        return $response;
    }

    public function GuardarDesdeCSV($request, $response)
    {
        $tabla = 'usuarios';
        $nombreArchivo = 'usuario.csv';
        
        if (AccesoDatos::insertDesdeCSV($response, $tabla, $nombreArchivo))
        {
            $response->getBody()->write(json_encode(["mensaje" => "Usuario cargado exitosamente a $nombreArchivo"]));
        }
        else
        {
            $response->getBody()->write(json_encode(["mensaje" => "Hubo un problema al cargar el usuario a $nombreArchivo"]));
        }

        return $response;
    }

    #endregion

    #region SELECT
    public function VerTodos($request, $response)
    {
        $lista_usuarios = AccesoDatos::selectAll($response, "usuarios");
        $response->getBody()->write(json_encode(["Usuarios" => $lista_usuarios], JSON_PRETTY_PRINT));
        return $response;
    }

    public function VerPorID($request,$response,$args)
    {
        $id = $args['id'];
        $usuario = AccesoDatos::selectCriterioINT($response, "usuarios", 'id', $id);
        $response->getBody()->write(json_encode(["Usuario" => $usuario], JSON_PRETTY_PRINT));
        return $response;
    }

    public function VerPorSector($request,$response,$args)
    {
        $sector = $args['sector'];
        $usuario = AccesoDatos::selectCriterioSTR($response, "usuarios", 'sector',$sector);
        $response->getBody()->write(json_encode(["Usuario" => $usuario], JSON_PRETTY_PRINT));
        return $response;
    }

    #endregion

    #region DELETE
    public function EliminarPorID($request,$response,$args)
    {
        $id = $args['id'];
        AccesoDatos::deleteID($response, "usuarios", $id);
        $response->getBody()->write(json_encode(["Usuario $id" => "Fue eliminado exitosamente"], JSON_PRETTY_PRINT));
        return $response;
    }
    #endregion



}

?>