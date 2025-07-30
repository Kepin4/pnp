<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Base_model;
use App\Controllers\CTools;
use App\Libraries\func;
use DateTime;

class CBase extends Controller
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
        // $myLevel = session('level');
        // if (($myLevel >= 1 && $myLevel <= 3)) {
        return redirect()->to("../CBase/Dashboard");
        // } else {
        //     return redirect()->to("../CBase/Profile");
        // }
    }

    public function NewTopup()
    {
        session()->setFlashdata('newTopup', 'true');
        return redirect()->to('/CBase/Profile');
    }

    public function NewWithdraw()
    {
        session()->setFlashdata('newWithDraw', 'true');
        return redirect()->to('/CBase/Profile');
    }

    public function Dashboard()
    {
        $myLevel = session('level');
        // if (!($myLevel >= 1 && $myLevel <= 3)) {
        //     return redirect()->to("../CBase");
        // }

        $qry = new Base_model();
        $Now = $qry->getWaktu();
        $xJam = new DateTime($Now);
        $xYear = date('Y', strtotime($Now));
        $xMonth = date('m', strtotime($Now));


        $str = "SELECT informasi, caramain FROM tcomp;";
        $xComp = $qry->usefirst($str);
        $data['Informasi'] = $xComp->informasi ?? "";
        $data['CaraMain'] = $xComp->caramain ?? "";

        $xJamFormat = $xJam->format('Y-m-d');
        $str = "SELECT jenistrans, SUM(amount) Val FROM ttrans WHERE date(tanggal) = '{$xJamFormat}' GROUP BY jenistrans";
        $dtTrans = $qry->use($str);

        $i = 0;
        foreach (['Topup', 'Sales', 'Placement', 'Withdraw', 'CheckIn', 'Prize'] as $q) {
            $i++;
            $qDt = array_filter($dtTrans, function ($x) use ($i) {
                return $x->jenistrans == $i;
            });
            $data[$q] = func::NumNULL(reset($qDt)->Val ?? 0);
        }

        $str = "SELECT SUM(amount * cashback) Val FROM ttrans WHERE date(tanggal) = '{$xJamFormat}'";
        $dtCashback = $qry->usefirst($str);

        $data['Cashback'] = $dtCashback->Val ?? 0;
        $data['name'] = session('username');
        echo view("vHead");
        echo view("vMenu");
        echo view("vDashboard", $data);
        echo view("vFooter");
    }

    public function Profile($id = 0)
    {
        $qry = new Base_model();
        $Tools = new CTools();
        if (!(session('level') >= 1 && session('level') <= 3)) {
            $id = session('idUser');
        }

        $dtSaldo = $qry->use("SELECT SUM(amount) saldo FROM tsaldo WHERE iduser = '{$id}'");
        $dtTrans = $qry->use("SELECT SUM(amount) Amount FROM ttrans WHERE iduser = '{$id}' AND jenistrans = 1");
        $dtPlay = $qry->use("SELECT SUM(amount) Amount FROM ttrans WHERE iduser = '{$id}' AND jenistrans = 3");
        $dtAllWin = $qry->use("SELECT month(tanggal) month, SUM(amount) Amount, SUM(cashback) Cashback FROM ttrans WHERE iduser= '{$id}' GROUP BY month(tanggal)");

        $totalWin = 0;
        $totalCashBack = 0;
        $dtWin = array_fill(1, 12, 0);
        foreach ($dtAllWin as $q) {
            $month = (int) $q->month;
            $dtWin[$month] = func::NumNull($q->Amount);
            $totalWin = func::NumNull($q->Amount);
            $totalCashBack = func::NumNull($q->Cashback);
        }

        $data['name'] = $Tools->getUsername($id);
        $data['account_id'] = $id;
        $data['level_name'] = session('level');
        $data['saldo'] = func::NumNull(reset($dtSaldo)->saldo);

        $data['totalWin'] = (float) $totalWin;
        $data['cashback'] = (float) $totalCashBack * 100;
        $data['topup'] = (float) func::NumNull(reset($dtTrans)->Amount);
        $data['play'] = (float) func::NumNull(reset($dtPlay)->Amount) * -1;
        $data['play'] = $data['play'] == -0 ? 0 : $data['play'];
        $data['win'] = $dtWin;
        $data['transaction'] = (object) [];

        echo view("vHead");
        echo view("vMenu");
        echo view("vProfile", $data);
        echo view("vFooter");
    }

    public function Stream()
    {
        echo view("vHead");
        echo view("vMenu");
        echo view("vStream");
        echo view("vFooter");
    }
}
