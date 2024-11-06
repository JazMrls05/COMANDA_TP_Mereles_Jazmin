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

    public static function selectID($response, $tabla, $id)
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "SELECT * FROM $tabla WHERE id = :id";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            $sentencia-> execute();
            return $sentencia-> FetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    public static function selectSector($response, $tabla, $sector)
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "SELECT * FROM $tabla WHERE sector = :sector";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia->bindParam(':sector', $sector, PDO::PARAM_STR);
            $sentencia-> execute();
            return $sentencia-> FetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    public static function selectCodigo($response, $tabla, $nombreColumna, $codigo)
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "SELECT * FROM $tabla WHERE $nombreColumna = :codigo";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            $sentencia-> execute();
            return $sentencia-> FetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    public static function selectTipo($response, $tabla, $tipo)
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "SELECT * FROM $tabla WHERE tipo = :tipo";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia->bindParam(':tipo', $tipo, PDO::PARAM_STR);
            $sentencia-> execute();
            return $sentencia-> FetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

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

}