<?php
require_once __DIR__ . '/../../DB/AccesoDatos.php';

class Usuario 
{
    #region Atributos
    public $sector;
    public $nombre;
    public $apellido;
    public $clave;
    public $id;
    public $fechaIngreso;
    #endregion

    #region Constructor
    public function __construct($nombre,$apellido,$clave,$sector = null,$fechaIngreso= null,$id = null)
    {
        $this -> nombre = $nombre;
        $this -> apellido = $apellido;
        $this -> clave = $clave;
        $this -> sector = $sector;
        if ($fechaIngreso === '') 
        {
            $this->fechaIngreso = date('d-m-Y'); 
        } 
        else 
        {
            $this->fechaIngreso = $fechaIngreso;
        }
        $this -> id = $id;
    }
    #endregion

}

?>