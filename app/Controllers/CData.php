<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Base_model;
use App\Models\Bank_model;
use App\Libraries\func;
use DateTime;
use stdClass;

class CData extends Controller
{
    public function User()
    {
        $qry = new Base_model();
        $f = new CTools();
        if (!(session('level') >= 1 && session('level') <= 4)) {
            session()->setflashdata('alert', '3|Unauthorized User, ADMIN only!');
            return redirect()->to('/CBase/Profile');
        }

        $myLevel = session('level');
        $myID = session('idUser');
        $whrLevel = ($myLevel == 1 ? "" : ($myLevel == 4 ? "WHERE idatasan = '{$myID}'" : "WHERE level > '$myLevel' "));
        $str = "SELECT u.id, u.username, u.level, u.cashback, u.idatasan, u.komisi, u.status, u.kodebank, u.norek, u.namarek, b.nama as namabank FROM tuser u LEFT JOIN tbank b ON u.kodebank = b.kode $whrLevel ORDER BY u.status ASC, u.id ASC ";
        $dtUser = $qry->use($str);

        $str = "SELECT iduser, SUM(amount) amount FROM tsaldo GROUP BY iduser";
        $dtSaldo = array_column($qry->use($str), 'amount', 'iduser');

        $qBindSaldo = array_map(function ($q) use ($dtSaldo, $f) {
            return (object) [
                'id' => $q->id,
                'username' => $q->username,
                'level' => $q->level,
                'LevelString' => $f->getlevelString($q->level),
                'cashback' => $q->cashback,
                'idatasan' => $q->idatasan,
                'Atasan' => ($q->idatasan == 0 ? 'NO AGENT' : $f->getUsername($q->idatasan)),
                'komisi' => $q->komisi,
                'status' => $q->status,
                'saldo' => $dtSaldo[$q->id] ?? 0,
                'kodebank' => $q->kodebank ?? '',
                'norek' => $q->norek ?? '',
                'namarek' => $q->namarek ?? '',
                'namabank' => $q->namabank ?? ''
            ];
        }, $dtUser);

        $bankModel = new Bank_model();
        $banks = $bankModel->getBanks();

        $data = array(
            'User' => $qBindSaldo,
            'banks' => $banks
        );
        echo view("vHead");
        echo view("vMenu");
        echo view("vUser", $data);
        echo view("vFooter");
    }

    public function NewUser()
    {
        $bankModel = new Bank_model();
        $data = [
            'banks' => $bankModel->getBanks()
        ];
        
        echo view('vHead');
        echo view('vMenu');
        echo view('vNewUser', $data);
        echo view('vFooter');
    }

    public function SaveUser()
    {
        $qry = new Base_model();
        $xJam = new DateTime($qry->getWaktu());
        $myID = session('idUser');
        $myLevel = session('level');
        if (!($myLevel >= 1 && $myLevel <= 4)) {
            session()->setflashdata('alert', '3|Unauthorized User, ADMIN only!');
            return redirect()->to('../CData/User');
        }

        $dt = $this->request->getPost();
        $Username = $dt["txtUsername"];

        if (!$Username) {
            session()->setflashdata('alert', '3|Harap isi Username!');
            return redirect()->to('../CData/User');
        }

        $Username = str_replace(" ", "", $Username);
        $str = "SELECT 1 FROM tuser WHERE username = '{$Username}'";
        if ($qry->usefirst($str)) {

            session()->setflashdata('alert', '3|Username sudah digunakan!');
            return redirect()->to('../CData/User');
        }

        $dtIns = array(
            'username' => $Username,
            'password' => $dt['txtPassword'],
            'level' => $dt['selLevel'],
            'cashback' => $dt['txtCashback'],
            'idatasan' => $myLevel == 4 ? $myID : 0,
            'komisi' => $dt['txtKomisi'] ?? 0,
            'maxcashback' => $dt['txtMaxCashback'],
            'kodebank' => $dt['selBank'] ?? '',
            'norek' => $dt['txtNoRek'] ?? '',
            'namarek' => $dt['txtNamaRek'] ?? '',
            'status' => 5,
            'inputby' => $myID,
            'inputdate' => $xJam->format('Y-m-d H:i:s')
        );
        if (!$qry->ins('tuser', $dtIns)) {
            session()->setflashdata('alert', '3|Terjadi Kesalahan!');
            return redirect()->to('../CData/User');
        }

        return redirect()->to('../CData/SuccessSaveUser' . '/' . $Username);
    }
    public function SuccessSaveUser($Username = "")
    {
        session()->setflashdata('alert', "5|Berhasil Create User, {$Username}!");
        return redirect()->to('../CData/User');
    }

    public function UpdateUser()
    {
        $qry = new Base_model();
        $xJam = new DateTime($qry->getWaktu());
        $myID = session('idUser');
        $myLevel = session('level');
        if (!($myLevel >= 1 && $myLevel <= 4)) {
            session()->setflashdata('alert', '3|Unauthorized User, ADMIN only!');
            return redirect()->to('../CData/User');
        }

        $dt = $this->request->getPost();
        $Username = $dt["txtNamaUser"];
        $IDUser = $dt["txtID"];
        $str = "SELECT 1 FROM tuser WHERE username = '{$Username}' AND id <> '{$IDUser}'";
        if ($qry->usefirst($str)) {
            session()->setflashdata('alert', '3|Username sudah digunakan!');
            return redirect()->to('../CData/User');
        }

        $dtUpd = array(
            'username' => $dt['txtNamaUser'],
            'level' => $dt['cbLevel'],
            'cashback' => $dt['txtCashback'],
            'idatasan' => $myLevel == 4 ? $myID : 0,
            'komisi' => $dt['txtKomisi'] ?? 0,
            'maxcashback' => $dt['txtMaxCashback'],
            'kodebank' => $dt['selBankDetail'] ?? '',
            'norek' => $dt['txtNoRekDetail'] ?? '',
            'namarek' => $dt['txtNamaRekDetail'] ?? '',
            'status' => 5,
            'inputby' => $myID,
            'inputdate' => $xJam->format('Y-m-d H:i:s'),
            'updateby' => $myID,
            'updatedate' => $xJam->format('Y-m-d H:i:s')
        );

        if (!$qry->upd('tuser', $dtUpd, array('id' => $IDUser))) {
            session()->setflashdata('alert', '3|Terjadi Kesalahan!');
            return redirect()->to('../CData/User');
        }

        return redirect()->to('../CData/SuccessUpdateUser' . '/' . $Username);
    }


    public function ChangePass()
    {
        $qry = new Base_model();
        $xJam = new DateTime($qry->getWaktu());
        $myID = session('idUser');
        $myLevel = session('level');
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            session()->setflashdata('alert', '3|Unauthorized User, ADMIN only!');
            return redirect()->to('../CData/User');
        }

        $dt = $this->request->getPost();
        $IDUser = $dt["txtIDChangePass"];

        $dtUpd = array(
            'password' => $dt['txtPass'],
            'status' => 5,
            'updateby' => $myID,
            'updatedate' => $xJam->format('Y-m-d H:i:s')
        );
        if (!$qry->upd('tuser', $dtUpd, array('id' => $IDUser))) {
            session()->setflashdata('alert', '3|Terjadi Kesalahan!');
            return redirect()->to('../CData/User');
        }

        return redirect()->to('../CData/SuccessChangePass');
    }
    public function SuccessUpdateUser($Username = "")
    {
        session()->setflashdata('alert', "5|Berhasil Update User, {$Username}!");
        return redirect()->to('../CData/User');
    }

    public function SuccessChangePass()
    {
        session()->setflashdata('alert', '5|Berhasil Update Password User!');
        return redirect()->to('../CData/User');
    }

    public function SaveBank()
    {
        $myLevel = session('level');
        if (!($myLevel >= 1 && $myLevel <= 4)) {
            return json_encode(['status' => 403, 'message' => 'Unauthorized User, ADMIN only!']);
        }

        $dt = $this->request->getPost();
        $namaBank = $dt['namaBank'] ?? '';

        if (!$namaBank) {
            return json_encode(['status' => 400, 'message' => 'Nama Bank is required!']);
        }

        $bankModel = new Bank_model();
        
        // Check if bank already exists
        $existingBank = $bankModel->where('nama', $namaBank)->first();
        if ($existingBank) {
            return json_encode(['status' => 400, 'message' => 'Bank already exists!']);
        }

        $result = $bankModel->insertBank($namaBank);
        
        if ($result['success']) {
            return json_encode([
                'status' => 200, 
                'message' => 'Bank added successfully!',
                'data' => [
                    'kode' => $result['kode'],
                    'nama' => $namaBank
                ]
            ]);
        } else {
            return json_encode(['status' => 500, 'message' => 'Failed to add bank!']);
        }
    }
}
