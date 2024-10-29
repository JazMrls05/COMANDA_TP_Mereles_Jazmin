<?php

class Producto
{
        #region Atributos
        public $id;
        public $tipo;
        public $nombre;
        public $stock;
        public $precio;
        public $tiempoPreparacion;
        #endregion
    
        #region Constructor
        public function __construct($tipo,$nombre,$stock,
        $precio,$tiempoPreparacion,$id=null)
        {
            $this -> tipo = $tipo;
            $this -> nombre = $nombre;
            $this -> stock = $stock;
            $this -> precio = $precio;
            $this -> tiempoPreparacion = $tiempoPreparacion;
            $this -> id = $id;
        }
        #endregion
}