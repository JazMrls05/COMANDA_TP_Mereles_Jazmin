<?php
require_once __DIR__ . '/../DB/AccesoDatos.php';

class SerializadoraCSV
{

    // [tipo,sector, nombre, precio, stock]
    public function LeerDB($request, $response)
    {
        $accesoDatos = new AccesoDatos();
        $lista = $accesoDatos::selectAll($response,'productos');
        try
        {
            $archivo = fopen("productos.csv", 'w+');
            if($archivo === false)
            {
                $response->getBody()->write(json_encode(["Error" => "No se pudo abrir el archivo"]));
            }
            $encabezados = ['tipo','sector','nombre','precio','stock'];
            
            fputcsv($archivo, $encabezados);

            foreach($lista as $producto)
            {
                $listaCSV[] = [$producto['tipo'],$producto['sector'],$producto['nombre'], $producto['precio'], $producto['stock']];
            }

            foreach($listaCSV as $linea)
            {
                fputcsv($archivo, $linea);
            }
            
            $response->getBody()->write(json_encode(["Exito" => "archivo creado"]));

        }
        catch(Exception $e)
        {
            $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
            
        }
        finally 
        {
            fclose($archivo);
            return $response;
        }
    }

    function GuardarDatos($request, $response)
    {
        try{

            $archivo = fopen("productos.csv", 'w+');
            $datos = $request->getParsedBody();
            $nombre = $datos['nombre'];
            $tipo = $datos['tipo'];
            $sector = $datos['sector'];
            $precio = intval($datos['precio']);
            $stock = intval($datos['stock']);

            $productoNuevo = ["tipo" => $tipo, "sector" => $sector,"nombre" => $nombre, "precio" => $precio, "stock" => $stock];
            $listaProductosCSV = self::LeerDatos();
            $modificado = false;
            
            
            if ($archivo === false) 
            {
                $response->getBody()->write(json_encode(["Error" => "No se pudo abrir el archivo"]));
            }
            $encabezados = ['tipo','sector','nombre','precio','stock'];
            
            fputcsv($archivo, $encabezados);
            
            foreach ($listaProductosCSV as &$producto) 
            {
                if($producto['nombre'] == $nombre)
                {
                    $producto['stock'] += $stock;
                    $producto['precio'] = $precio;
                    AccesoDatos::update($response, 'productos', 'stock', $producto['stock'], 'nombre', '=', $nombre);
                    AccesoDatos::update($response, 'productos', 'precio', $producto['precio'], 'nombre', '=', $nombre);
                    $modificado = true;
                    break;
                }
            }

            if($modificado == false)
            {
                $listaProductosCSV[] = $productoNuevo;
                AccesoDatos::insert($response, 'productos', $encabezados, [$productoNuevo['tipo'], $productoNuevo['sector'], $productoNuevo['nombre'], $productoNuevo['precio'], $productoNuevo['stock']]);
            }
            
            $listaProductosCSV = array_map(function($producto){
                return [$producto['tipo'],$producto['sector'],$producto['nombre'], $producto['precio'], $producto['stock']];
            }, $listaProductosCSV);

            foreach ($listaProductosCSV as $linea) 
            {
                fputcsv($archivo, $linea); 
            }

            $response->getBody()->write(json_encode(["Archivo" => "guardado exitoso"]));

            return $response;
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
        }
        finally
        {
            fclose($archivo);
        }
    }

    function LeerDatos()
    {
        try
        {
            $rutaArchivo = $_FILES['productosCSV']['tmp_name'];
            $archivo = fopen($rutaArchivo, "r");

            $encabezado = fgetcsv($archivo);
            while (($linea = fgetcsv($archivo)) !== FALSE) 
            {
                $tipo = $linea[0];
                $sector = $linea[1];
                $nombre = $linea[2]; 
                $precio = $linea[3]; 
                $stock = $linea[4];  

                $producto = ["tipo" => $tipo, "sector" => $sector,"nombre" => $nombre, "precio" => $precio, "stock" => $stock];
                $listaProductos[] = $producto;
            }

            return $listaProductos;
        }
        catch(Exception $e)
        {
            echo $e->getMessage(). " No se pudo abrir el archivo.";
        }
        finally
        {
            fclose($archivo);
        }
    }

    function leerCSV()
    {
        try
        {
            $rutaArchivo = __DIR__ . '/../productos.csv';
            $archivo = fopen($rutaArchivo, "r");

            $encabezado = fgetcsv($archivo);
            while (($linea = fgetcsv($archivo)) !== FALSE) 
            {
                $tipo = $linea[0];
                $sector = $linea[1];
                $nombre = $linea[2]; 
                $precio = $linea[3]; 
                $stock = $linea[4];  

                $producto = ["tipo" => $tipo, "sector" => $sector,"nombre" => $nombre, "precio" => $precio, "stock" => $stock];
                $listaProductos[] = $producto;
            }

            return $listaProductos;
        }
        catch(Exception $e)
        {
            echo $e->getMessage(). " No se pudo abrir el archivo.";
        }
        finally
        {
            fclose($archivo);
        }
    }

}