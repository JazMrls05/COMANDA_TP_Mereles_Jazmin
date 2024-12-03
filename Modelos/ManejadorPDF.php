<?php
require_once __DIR__ . "/../PDF_COSAS/fpdf.php";
class ManejadorPDF
{
    static function descargarProductos($request, $response)
    {

        $AccesoDatos = new AccesoDatos();
        $listaProductos = $AccesoDatos->selectAll($response, 'productos');
        $productos = [];

        foreach($listaProductos as $producto)
        {
            $productos[] = [
            'Tipo' => $producto["tipo"], 
            'Sector' => $producto["sector"],
            'Nombre' => $producto["nombre"], 
            'Precio' => $producto["precio"], 
            'Stock' => $producto["stock"]
            ];
        }

        $pdf = new FPDF();
        
        $pdf->AddPage(); 
        
        $pdf->SetFont("Arial", 'B', 12);

        $pdf->Cell(200, 10, "Productos de la tienda", 1, 1, 'C');

        $pdf->Ln(10);

        $pdf->SetFont("Arial", 'B', 10);
        $pdf->Cell(50, 10, "Tipo", 1, 0, 'C');
        $pdf->Cell(50, 10, "Sector", 1, 0, 'C');
        $pdf->Cell(50, 10, "Nombre", 1, 0, 'C');
        $pdf->Cell(50, 10, "Precio", 1, 0, 'C');
        $pdf->Cell(50, 10, "Stock", 1, 1, 'C');

        foreach ($productos as $producto) {
            $pdf->Cell(50, 10, $producto['Tipo'], 1, 0, 'C');
            $pdf->Cell(50, 10, $producto['Sector'], 1, 0, 'C');
            $pdf->Cell(50, 10, $producto['Nombre'], 1, 0, 'C');
            $pdf->Cell(50, 10, "$" . $producto['Precio'], 1, 0, 'C');
            $pdf->Cell(50, 10, $producto['Stock'], 1, 1, 'C');
        }

        $pdf->Output('I', 'productos.pdf'); 
        
        $response->getBody()->write(json_encode(["Mensaje" => "Se ha creado el PDF"], JSON_PRETTY_PRINT));

        return $response;
        
    }
}