<?php

require_once __DIR__ . '/../Modelos/Producto.php';
require_once __DIR__ . '/../DB/AccesoDatos.php';

class ProductoController
{

    #region INSERT
    public function Guardar($request, $response)
    {
        $parsear_datos = $request->getParsedBody();

        $producto = new Producto($parsear_datos['tipo'],$parsear_datos['nombre'], $parsear_datos['stock'],
        $parsear_datos['precio']);

        $array_datos = [$producto->tipo, $producto->nombre, $producto->stock,
        $producto->precio];

        $tabla = 'productos';
        $array_encabezados = ['tipo', 'nombre', 'stock', 'precio'];
        
        if (AccesoDatos::insert($response, $tabla, $array_encabezados, $array_datos))
        {
            $response->getBody()->write(json_encode(["mensaje" => "Producto cargado exitosamente"]));
        }
        else
        {
            $response->getBody()->write(json_encode(["mensaje" => "Hubo un problema al cargar el producto"]));
        }

        return $response;
    }

    public function GuardarDesdeCSV($request, $response)
    {
        $tabla = 'productos';
        $nombreArchivo = 'producto.csv';
        
        if (AccesoDatos::insertDesdeCSV($response, $tabla, $nombreArchivo))
        {
            $response->getBody()->write(json_encode(["mensaje" => "Producto cargado exitosamente a $nombreArchivo"]));
        }
        else
        {
            $response->getBody()->write(json_encode(["mensaje" => "Hubo un problema al cargar el producto a $nombreArchivo"]));
        }

        return $response;
    }

    #endregion

    #region SELECT
    public function VerTodos($request, $response)
    {
        $lista_productos = AccesoDatos::selectAll($response, "productos");
        $response->getBody()->write(json_encode(["Productos" => $lista_productos], JSON_PRETTY_PRINT));
        return $response;
    }

    public function VerTodosPorTipo($request,$response,$args)
    {
        $tipo = $args['tipo'];
        if($tipo === 'comida' || $tipo === 'bebida')
        {
            $lista_productos = AccesoDatos::selectCriterioSTR($response, "productos", 'tipo', $tipo);
            $response->getBody()->write(json_encode(["Productos" => $lista_productos], JSON_PRETTY_PRINT));
        }
        else
        {
            $response->getBody()->write(json_encode(["mensaje" => "El tipo ingresado debe ser 'comida' o 'bebida' "]));
        }
        return $response;
    }
    #endregion

    #region DELETE
    public function EliminarPorID($request,$response,$args)
    {
        $id = $args['id'];
        AccesoDatos::deleteID($response, "productos", $id);
        $response->getBody()->write(json_encode(["Producto $id" => "Fue eliminado exitosamente"], JSON_PRETTY_PRINT));
        return $response;
    }
    #endregion
}