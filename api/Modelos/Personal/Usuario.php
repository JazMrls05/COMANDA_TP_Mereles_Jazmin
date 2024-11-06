<?php
require_once __DIR__ . '/../../DB/AccesoDatos.php';

class Usuario 
{
    #region Atributos
    public $perfil; // admin, empleado
    public $sector; // administración ; cocina, barra o choperas
    public $nombre;
    public $apellido;
    public $clave;
    public $fechaIngreso;
    #endregion

    #region Constructor
    public function __construct($perfil, $nombre, $apellido, $clave, $sector = null, $fechaIngreso= null)
    {
        $this -> perfil = $perfil;
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
    }
    #endregion

}

?>