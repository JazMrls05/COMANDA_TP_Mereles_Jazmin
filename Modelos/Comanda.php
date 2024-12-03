<?php

class Comanda
{
    public $importeTotal; 

    public function __construct($importeTotal)
    {
        $this->importeTotal = $importeTotal;
    }
}