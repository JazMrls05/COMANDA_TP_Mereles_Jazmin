<?php

class Producto
{
        #region Atributos
        public $tipo;
        public $sector;
        public $nombre;
        public $stock;
        public $precio;
        #endregion
    
        #region Constructor
        public function __construct($tipo,$sector,$nombre,$stock,$precio)
        {
            $this -> tipo = $tipo;
            $this -> sector = $sector;
            $this -> nombre = $nombre;
            $this -> stock = $stock;
            $this -> precio = $precio;
        }
        #endregion
}