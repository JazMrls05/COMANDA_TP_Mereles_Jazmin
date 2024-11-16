<?php
require_once __DIR__ . '/../../DB/AccesoDatos.php';

class Usuario 
{
    #region Atributos
    public $perfil; // admin, empleado
    public $sector; // administración ; cocina, barra o choperas
    public $nombre;
    public $clave;
    public $fechaIngreso;
    #endregion

    #region Constructor
    public function __construct($perfil, $nombre, $clave, $sector = null, $fechaIngreso = '')
    {
        $this -> perfil = $perfil;
        $this -> nombre = $nombre;
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