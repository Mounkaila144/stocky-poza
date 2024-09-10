<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Models\Product;

class EtiquettesController extends Controller
{
    public function generatePDF()
    {
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes
        // Nombre de produits par lot
        $perPage = 120;
        // Obtenez le nombre total de produits
        $totalProducts = Product::count();

        // Récupérer tous les produits
        $products = Product::take($totalProducts)->get();

        // Générer un seul PDF avec tous les produits
        $pdf = PDF::loadView('etiquettes', compact('products'));

        // Retourner le PDF en tant que téléchargement
        return $pdf->stream('etiquettes.pdf');
    }
    public function printSelected($ids)
    {
        $idsArray = explode(',', $ids);
        $products = Product::whereIn('id', $idsArray)->get();

        $pdf = PDF::loadView('etiquettes', compact('products'));
        return $pdf->stream('etiquettes.pdf');
    }
}
