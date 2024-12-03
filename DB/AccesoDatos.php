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
            return $sentencia->FetchAll(PDO::FETCH_ASSOC);
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

    public static function selectCriterioSTR_AND($response, $tabla, $nombreDato ,$datoVal,$nombreDato2, $datoVal2) 
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "SELECT * FROM $tabla WHERE $nombreDato = :$nombreDato AND $nombreDato2 = :$nombreDato2";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia->bindParam(":$nombreDato", $datoVal, PDO::PARAM_STR);
            $sentencia->bindParam(":$nombreDato2", $datoVal2, PDO::PARAM_STR);
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
            $consulta = "SELECT * FROM $tabla WHERE $dato LIKE :patron";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia->bindValue(':patron', $patron, PDO::PARAM_STR);
            $sentencia-> execute();
            return $sentencia-> FetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    public static function selectColumna($response, $columna, $tabla)
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "SELECT $columna FROM $tabla";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia-> execute();
            return $sentencia-> FetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    public static function selectColumnaWhere($response, $columna, $tabla, $nombreDato, $signoWhere, $valorDato)
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "SELECT $columna FROM $tabla WHERE $nombreDato $signoWhere '$valorDato'";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia-> execute();
            return $sentencia-> FetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    public static function selectMayorCantidad($response, $columna, $tabla, $elemento)
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "SELECT $columna FROM $tabla ORDER BY $elemento DESC LIMIT 1";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia->execute();
            return $sentencia->FetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    public static function promedioYmejorElemento($response)
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "SELECT codigoPedido, comentario,
            (puntajeMesa + puntajeRestaurante + puntajeMozo + puntajeCocinero) / 4 AS promedio
            FROM encuestas
            ORDER BY promedio DESC 
            LIMIT 3";
            $sentencia = $accesoDatos->_pdo->prepare($consulta);
            $sentencia->execute();
            return $sentencia->FetchAll(PDO::FETCH_ASSOC);
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
            $consulta = "UPDATE $tabla SET $datoAsetear = '$valorSeteo' WHERE $datoWhere $signoWhere '$valorWhere'";
            $consultaPreparada = $accesoDatos->_pdo->prepare($consulta);
            $consultaPreparada->execute();
            return true;
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    public static function SumarCantidad($response, $tabla, $codigoMesa)
    {
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "UPDATE $tabla SET usoMesa = usoMesa + 1 WHERE codigoMesa = ?";
            $consultaPreparada = $accesoDatos->_pdo->prepare($consulta);
            $consultaPreparada->bindParam(1, $codigoMesa, PDO::PARAM_STR);
            $consultaPreparada->execute();
            return true;
        }
        catch(PDOException $e)
        {
            return $response->getBody()->write(json_encode(["Error" => $e->getMessage()]));
        }
    }

    public static function updateImporteComanda($request, $response)
    {
        $codigoMesa = $request->getAttribute('codigoMesa');
        try
        {
            $accesoDatos = new AccesoDatos();
            $consulta = "UPDATE comandas SET importeTotal = (
                SELECT SUM(precioFinal)
                FROM pedidos
                WHERE pedidos.idComanda = comandas.id
                AND pedidos.codigoMesa = :codigoMesa
            )";
            $consultaPreparada = $accesoDatos->_pdo->prepare($consulta);
            $consultaPreparada->bindParam(':codigoMesa', $codigoMesa, PDO::PARAM_STR);
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