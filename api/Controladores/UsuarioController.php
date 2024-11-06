<?php

require_once __DIR__ . '/../Modelos/Personal/Usuario.php'; 

class UsuarioController
{

    public function Guardar($request, $response)
    {
        $parsear_datos = $request->getParsedBody();

        $usuario = new Usuario($parsear_datos['perfil'],$parsear_datos['nombre'], $parsear_datos['apellido'],
        $parsear_datos['clave'],$parsear_datos['sector'],$parsear_datos['fechaIngreso']);

        $array_datos = [$usuario->perfil,$usuario->nombre, $usuario->apellido,
        $usuario->clave,$usuario->sector, $usuario->fechaIngreso];

        $tabla = 'usuarios';
        $array_encabezados = ['perfil','nombre', 'apellido', 'clave','sector','fechaIngreso'];
        
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

    public function VerTodos($request, $response)
    {
        $lista_usuarios = AccesoDatos::selectAll($response, "usuarios");
        $response->getBody()->write(json_encode(["Usuarios" => $lista_usuarios], JSON_PRETTY_PRINT));
        return $response;
    }

    public function VerPorID($request,$response,$args)
    {
        $id = $args['id'];
        $usuario = AccesoDatos::selectID($response, "usuarios", $id);
        $response->getBody()->write(json_encode(["Usuario" => $usuario], JSON_PRETTY_PRINT));
        return $response;
    }

    public function VerPorSector($request,$response,$args)
    {
        $sector = $args['sector'];
        $usuario = AccesoDatos::selectSector($response, "usuarios", $sector);
        $response->getBody()->write(json_encode(["Usuario" => $usuario], JSON_PRETTY_PRINT));
        return $response;
    }

    public function Modificar($request, $response, $args)
    {

    }

    public function EliminarPorID($request,$response,$args)
    {
        $id = $args['id'];
        AccesoDatos::deleteID($response, "usuarios", $id);
        $response->getBody()->write(json_encode(["Usuario $id" => "Fue eliminado exitosamente"], JSON_PRETTY_PRINT));
        return $response;
    }




}

?>