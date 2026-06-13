<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    /**
     * Muestra la pagina principal de la aplicacion.
     */
    public function home(): View
    {
        return view('welcome');
    }
}
