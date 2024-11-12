<?php

class Producto
{
        #region Atributos
        public $tipo;
        public $nombre;
        public $stock;
        public $precio;
        #endregion
    
        #region Constructor
        public function __construct($tipo,$nombre,$stock,$precio)
        {
            $this -> tipo = $tipo;
            $this -> nombre = $nombre;
            $this -> stock = $stock;
            $this -> precio = $precio;
        }
        #endregion
}