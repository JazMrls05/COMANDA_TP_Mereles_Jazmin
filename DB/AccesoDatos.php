<?php

class AccesoDatos
{
    private $_pdo;

    public function __construct()
    {
        try 
        {
            $this->_pdo = new PDO("mysql:host=localhost;dbname=comanda", 'root', '');
        } 
        catch(PDOException $e) 
        {
            echo "Error: " . $e->getMessage();
            die();
        }
    }


    #region INSERT
    public static function insert($response, $tabla, $array_encabezados, $array_datos)
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $encabezados = implode(',', $array_encabezados);
            $valores = implode(',', array_fill(0, count($array_datos), '?'));
            $consulta = "INSERT INTO $tabla ($encabezados) VALUES ($valores)";
            $consultaPreparada = $accesoDatos->_pdo->prepare($consulta);
            $consultaPreparada->execute($array_datos);
            return true;
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    public static function insertDesdeCSV($response, $tabla, $nombreArchivo)
    {
        try
        {

            if (($archivo = fopen($nombreArchivo, "r")) !== false) 
            {
                $accesoDatos = new AccesoDatos();
                $encabezados = fgetcsv($archivo, 1000, ","); /* El archivo csv que pase, va a tener encabezados con los mismos nombres
                de las columnas de la respectiva tabla*/
                $valores = implode(',', array_fill(0, count($encabezados), '?'));
                $consulta = "INSERT INTO $tabla ($encabezados) VALUES ($valores)";
                $consultaPreparada = $accesoDatos->_pdo->prepare($consulta);

                while (($datos = fgetcsv($archivo, 1000, ",")) !== false)
                {
                    $consultaPreparada->execute($datos);
                }

                fclose($archivo);
                $response->getBody()->write(json_encode(["Mensaje" => "Datos cargados exitosamente"]));
            }
            else
            {
                $response->getBody()->write(json_encode(["Mensaje" => "Hubo un probelam al intentar abrir el archivo"]));
            }

        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }
    #endregion

    #region SELECT
    public static function selectAll($response, $tabla)
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "SELECT * FROM $tabla";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia->execute();
            return $sentencia->FetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    public static function selectCriterioINT($response, $tabla, $nombreDato ,$datoVal) // Por ejemplo: un ID
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "SELECT * FROM $tabla WHERE $nombreDato = :$nombreDato";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia->bindParam(":$nombreDato", $datoVal, PDO::PARAM_INT);
            $sentencia-> execute();
            return $sentencia-> FetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    public static function selectCriterioSTR($response, $tabla, $nombreDato ,$datoVal) // Por ejemplo: sector, código alfnum, tipo
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "SELECT * FROM $tabla WHERE $nombreDato = :$nombreDato";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia->bindParam(":$nombreDato", $datoVal, PDO::PARAM_STR);
            $sentencia-> execute();
            return $sentencia-> FetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    public static function selectLIKE($response, $tabla, $dato, $patron)
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "SELECT * FROM $tabla WHERE $dato LIKE $patron";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia-> execute();
            return $sentencia-> FetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }


    #endregion

    #region UPDATE
    public static function update($response, $tabla, $datoAsetear, $valorSeteo, $datoWhere, $signoWhere, $valorWhere)
    {
        try
        {
            // $signoWhere refiere al '=', '>=', '<=', '>', '<' etc..
            $accesoDatos = new AccesoDatos();
            $consulta = "UPDATE $tabla SET $datoAsetear = $valorSeteo WHERE $datoWhere $signoWhere $valorWhere";
            $consultaPreparada = $accesoDatos->_pdo->prepare($consulta);
            $consultaPreparada->execute();
            return true;
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    #endregion 

    #region DELETE
    public static function deleteID($response, $tabla, $id)
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "DELETE FROM $tabla WHERE id = :id";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            return $sentencia-> execute();
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    public static function deleteCodigo($response, $tabla, $nombreColumna, $codigo)
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "DELETE FROM $tabla WHERE $nombreColumna = :codigo";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            return $sentencia-> execute();
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    #endregion

}