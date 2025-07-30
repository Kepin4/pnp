<?php

namespace App\Controllers;

use App\Models\Base_model;

class Home extends BaseController
{
    public function __construct()
    {
        $qry = new Base_model();
        if (!session()->has('Comp')) {
            $CTools = new CTools();
            $CTools->getComp();
        }
    }

    public function index()
    {
        if (session('idUser') == 0) {
            return redirect()->to('../CLogin/Login');
        }
        return redirect()->to('../CBase');
    }
}
