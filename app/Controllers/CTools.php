<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Base_model;
use App\Libraries\func;
use CodeIgniter\CLI\Console;
use DateTime;
use stdClass;

class CTools extends Controller
{
    public $dtUser = [];

    public function __construct()
    {
        if (!$this->dtUser) {
            $this->readUser();
        }
    }

    public function getComp()
    {
        $qry = new Base_model();
        $dtComp = $qry->use("SELECT c.nama, c.logo, c.nohp, c.defaulttimer, c.placementtime, c.chkwizard, c.idwizard, IFNULL(u.username, '') userWizard, c.limitsaldo FROM tcomp c LEFT JOIN tuser u ON c.idwizard = u.id");
        $Comp = reset($dtComp);
        session()->set('Comp', (object) array('nama' => $Comp->nama, 'logo' => $Comp->logo, 'nohp' => $Comp->nohp, 'defTimer' => $Comp->defaulttimer, 'PlacementTime' => $Comp->placementtime, 'isWizard' => $Comp->chkwizard, 'idWizard' => $Comp->idwizard, 'userWizard' => $Comp->userWizard, 'limitsaldo' => $Comp->limitsaldo));
    }

    public function getSisaMaxSaldo($idUser = 0): float
    {
        $qry = new Base_model();
        if ($idUser == 0) {
            $idUser = session('idUser');
        }

        $activeSession = $qry->usefirst("SELECT id FROM tsesi WHERE status = 1");
        if ($activeSession) {
            $dtSaldo = $qry->usefirst("SELECT IFNULL(SUM(amount), 0) amount FROM tplacementd d LEFT JOIN tplacement h ON h.id = d.idplacement WHERE h.idsesi = $activeSession->id AND h.iduser = $idUser;");
            return (float) $dtSaldo->amount;
        } else {
            return 0;
        }
    }

    private function readUser()
    {
        $qry = new Base_model();
        $str = "SELECT id, username, level, cashback, status, inputby, inputdate FROM tuser";
        foreach ($qry->use($str) as $q) {
            $obj = new stdClass;
            $obj->IDUser = $q->id;
            $obj->Username = $q->username;
            $obj->Level = $q->level;
            $obj->Cashback = $q->cashback;
            $obj->Status = $q->status;
            $obj->InputBy = $q->inputby;
            $obj->InputDate = $q->inputdate;
            $this->dtUser[] = $obj;
        }
    }

    public function getUsername($id)
    {
        if (!$this->dtUser) {
            $this->readUser();
        }

        $dtUser = array_filter($this->dtUser, function ($x) use ($id) {
            return $x->IDUser == $id;
        });

        if (!$dtUser) {
            return "";
        }
        $qUser = reset($dtUser);
        return $qUser->Username;
    }

    public function getSaldo($id = 0, $xJam = '0001-01-01 00:00:00'): float
    {
        $qry = new Base_model();
        $myLevel = session('level');
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            $id = session('idUser');
        }

        $xWhr = "";
        if ($xJam != '0001-01-01 00:00:00') {
            $xWhr = "AND tanggal < '{$xJam}'";
        } elseif (ctype_lower($xJam) == 'now') {
            $xDtJam = new DateTime($qry->getWaktu());
            $xJam = $xDtJam->format("Y-m-d H:i:s");
            $xWhr = "AND tanggal < '{$xJam}'";
        }
        return func::NumNULL($qry->usefirst("SELECT SUM(amount) saldo FROM tsaldo WHERE iduser = {$id} {$xWhr}")->saldo);
    }

    public function getDataUser($id = 0)
    {
        $qry = new Base_model();
        $myLevel = session('level');
        if ($id == 0) {
            $id = session('idUser');
        }

        $str = "SELECT IFNULL(SUM(amount), 0) AS amount FROM tsaldo WHERE iduser = '{$id}'";
        $qSaldo = $qry->usefirst($str);
        $str = "SELECT username, level, cashback, idatasan, komisi, maxcashback FROM tuser WHERE id = '{$id}'";
        $qUser = $qry->usefirst($str);
        $qUser->Saldo = (float) $qSaldo->amount;
        $qUser->LevelString = ($qUser->level == 1 ? "Maintenance" : ($qUser->level == 2 ? "Admin" : ($qUser->level == 3 ? "Kasir" : ($qUser->level == 4  ? "Agent" : ($qUser->level == 5  ? "VIP" : "Unknown Level")))));
        return json_encode($qUser);
    }

    public function getLevelString($level = 0)
    {
        return ($level == 1 ? "Maintenance" : ($level == 2 ? "Admin" : ($level == 3 ? "Kasir" : ($level == 4  ? "Agent" : ($level == 5  ? "VIP" : "Unknown Level")))));
    }
    public function getNotif()
    {
        $qry = new Base_model();
        $RecTopup = '';
        $RecWithdraw = '';

        $str = "SELECT 1 FROM treqtopup WHERE status = 1";
        if ($qry->use($str)) {
            $RecTopup = '*';
        }

        $str = "SELECT 1 FROM treqwithdraw WHERE status = 1";
        if ($qry->use($str)) {
            $RecWithdraw = '*';
        }

        return (object) array('Topup' => $RecTopup, 'Withdraw' => $RecWithdraw);
    }

    public function cekActivationKey($key)
    {
        if (!$key) {
            return json_encode(['status' => 500, 'message' => 'No Key Found']);
        }

        $qry = new Base_model();
        $activeKey = $qry->usefirst('SELECT kode FROM tblactivekey ORDER BY id DESC LIMIT 1')->kode;

        if ($activeKey != $key) {
            return json_encode([
                'status' => 500,
                'message' => 'Wrong Activation Key!'
            ]);
        }

        return json_encode([
            'status' => 200,
            'message' => 'Activation Key Correct!'
        ]);
    }

    public function getUserPassword()
    {
        $idUser = $_GET['iduser'];
        $key = $_GET['key'];

        if (!$idUser) {
            return json_encode(['status' => 500, 'message' => 'ID User Found']);
        } elseif (!$key) {
            return json_encode(['status' => 500, 'message' => 'No Key Found']);
        }

        $qry = new Base_model();
        $activeKey = $qry->usefirst('SELECT kode FROM tblactivekey ORDER BY id DESC LIMIT 1')->kode;

        if ($activeKey != $key) {
            return json_encode([
                'status' => 500,
                'message' => 'Wrong Activation Key!'
            ]);
        }

        $Password = $qry->usefirst("SELECT password FROM tuser WHERE id = {$idUser}")->password;
        return json_encode([
            'status' => 200,
            'message' => 'Activation Key Correct!',
            'data' => ['password' => $Password]
        ]);
    }

    public function regenerateActivationKey()
    {
        if ((session('idUser') ?? 0) == 0) {
            return json_encode(['status' => 400, 'message' => 'Unauthorized User!']);
        }
        $qry = new Base_model();
        $key = $this->generateRandomKey();
        $res = $qry->ins('tblactivekey', ['kode' => $key, 'inputby' => session('username')]);
        if ($res < 0) {
            return json_encode(['status' => 500, 'message' => 'Something Wrong Happend!']);
        }
        return json_encode(['status' => 200, 'message' => 'Generate Activation Key Success!', 'data' => ['key' => $key]]);
    }

    function generateRandomKey($length = 15)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
