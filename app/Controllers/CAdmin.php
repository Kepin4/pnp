<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Base_model;
use App\Libraries\func;
use DateTime;
use stdClass;

class CAdmin extends Controller
{
    public $dtUser = [];
    public function __construct()
    {
        $qry = new Base_model();
        if (!session()->has('Comp')) {
            $CTools = new CTools();
            $CTools->getComp();
        }
    }

    public function Setting()
    {
        $qry = new Base_model();
        $str = "SELECT amount FROM trecnum";
        $dtRecNum = [];
        $i = 1;
        foreach ($qry->use($str) as $q) {
            $dtRecNum[$i++] = $q->amount;
        }

        $str = "SELECT kodedepan FROM tjenistrans";
        $dtJenisTrans = $qry->use($str);
        $KodeDepan = [];
        $xI = 0;
        foreach (['Topup', 'Sales', 'Placement', 'Withdraw', 'CheckIn', 'Prize', 'Commission'] as $q) {
            $KodeDepan[$q] = $dtJenisTrans[$xI++]->kodedepan;
        }

        if (session('cache')->dtStart ?? false) {
            session()->remove('cache');
        }
        $str =  "SELECT id, username FROM tuser";
        $data['dtUser'] = $qry->use($str);

        $str = "SELECT informasi, caramain FROM tcomp;";
        $dtComp = $qry->usefirst($str);
        $xInformasi = $dtComp->informasi ?? "";
        $xCaraMain = $dtComp->caramain ?? "";

        $data['Informasi'] = $xInformasi;
        $data['CaraMain'] = $xCaraMain;
        $data['KodeDepan'] = $KodeDepan;
        $data['qRecNum'] = $dtRecNum;
        echo view("vHead");
        echo view("vMenu");
        echo view("vSetting", $data);
        echo view("vFooter");
    }

    public function SaveSetting()
    {
        $qry = new Base_model();
        $xJam = new Datetime($qry->getWaktu());
        $myID = session('idUser');

        // Input sanitization and validation
        $Nama = trim($this->request->getPost('txtnama'));
        $NoHp = trim($this->request->getPost('txtnohp'));
        $defTimer = (int) $this->request->getPost('txtDefTimer');
        $PlacementTime = (int) $this->request->getPost('txtPlacementTime');
        $isWizard = $this->request->getPost('chkWizard') == 'on' ? 1 : 0;
        $UserWizard = trim($this->request->getPost('UserWizard'));
        $limitsaldo = (float) $this->request->getPost('txtLimitSaldo');
        $activationKey = trim($this->request->getPost('txtActivationKey'));

        // Additional validation
        if ($defTimer <= 0) {
            session()->setFlashData('alert', '3|Default Timer harus lebih dari 0!');
            return redirect()->to('../CAdmin/Setting');
        }
        if ($PlacementTime <= 0) {
            session()->setFlashData('alert', '3|Placement Time harus lebih dari 0!');
            return redirect()->to('../CAdmin/Setting');
        }
        if ($limitsaldo < 0) {
            session()->setFlashData('alert', '3|Limit Saldo tidak boleh negatif!');
            return redirect()->to('../CAdmin/Setting');
        }

        // Auto-generate activation key if empty
        if (empty($activationKey) && session('idUser') == 1) {
            $randomWords = [
                'burger',
                'pizza',
                'coffee',
                'cake',
                'icecream',
                'sandwich',
                'cookie',
                'donut',
                'car',
                'phone',
                'computer',
                'book',
                'chair',
                'table',
                'lamp',
                'clock',
                'cat',
                'dog',
                'bird',
                'fish',
                'lion',
                'tiger',
                'bear',
                'rabbit',
                'sun',
                'moon',
                'star',
                'house',
                'garden',
                'flower',
                'tree',
                'ocean',
                'music',
                'dance',
                'movie',
                'game',
                'sport',
                'travel',
                'beach',
                'mountain',
                'apple',
                'banana',
                'orange',
                'grape',
                'cherry',
                'mango',
                'peach',
                'berry'
            ];
            $activationKey = $randomWords[array_rand($randomWords)];
        }

        $RecNum = [];
        for ($i = 1; $i <= 5; $i++) {
            $xVal = (float) $this->request->getPost("txtRecNum{$i}");
            $RecNum[$i] = $xVal;
        }

        $str = "SELECT kodedepan FROM tjenistrans";
        $dtJenisTrans = $qry->use($str);
        $KodeDepan = [];
        $xI = 0;
        foreach (['Topup', 'Sales', 'Placement', 'Withdraw', 'CheckIn', 'Prize', 'Commission'] as $q) {
            $KodeDepan[$q] = $dtJenisTrans[$xI]->kodedepan;
            $xI++;
        }

        // Use parameterized query to prevent SQL injection
        $idWizard = 0;
        if (!empty($UserWizard)) {
            $str = "SELECT id FROM tuser WHERE username = ?";
            $result = $qry->db->query($str, [$UserWizard])->getRow();
            $idWizard = $result->id ?? 0;
        }

        session()->setFlashData('cache',  (object) array('Nama' => $Nama, 'NoHp' => $NoHp, 'defTimer' => $defTimer, 'RecNum' => $RecNum, 'KodeDepan' => $KodeDepan, 'PlacementTime' => $PlacementTime, 'isWizard' => $isWizard, 'idWizard' => $idWizard, 'limitsaldo' => $limitsaldo));

        if (!(session('level') >= 1 && session('level') <= 3)) {
            session()->setFlashData('alert', '3|Unauthorized User, ADMIN only!');
            return redirect()->to('/CBase/Profile');
        }

        if (!$Nama) {
            session()->setFlashData('alert', '3|Harap isi Nama!');
            return redirect()->to('../CAdmin/Setting');
        }
        if (!$NoHp) {
            session()->setFlashData('alert', '3|Harap isi Nomor HP!');
            return redirect()->to('../CAdmin/Setting');
        }

        $qry->db->transBegin();

        // Prepare update data
        $updateData = array(
            'nama' => $Nama,
            'nohp' => $NoHp,
            'defaulttimer' => $defTimer,
            'placementtime' => $PlacementTime,
            'chkwizard' => $isWizard,
            'idwizard' => $idWizard,
            'limitsaldo' => $limitsaldo
        );

        // Add activation key if user is admin and key is provided
        if (session('idUser') == 1 && !empty($activationKey)) {

            $dataActiveKey = array(
                'kode' => $activationKey,
                'inputby' => $myID,
                'inputdate' => $xJam->format('Y-m-d H:i:s')
            );

            if (!$qry->ins('tblactivekey', $dataActiveKey)) {
                $qry->db->transRollback();
                session()->setFlashData('alert', "3|Terjadi Kesalahan Update Activation Key, {$activationKey}!");
                return redirect()->to('../CAdmin/Setting');
            }
        }

        if (!$qry->upd('tcomp', $updateData)) {
            $qry->db->transRollback();
            session()->setFlashData('alert', '3|Terjadi Kesalahan Update Data Company!');
            return redirect()->to('../CAdmin/Setting');
        }

        if (!$qry->del('trecnum')) {
            $qry->db->transRollback();
            session()->setFlashData('alert', '3|Terjadi Kesalahan!');
            return redirect()->to('../CAdmin/Setting');
        };

        foreach ($RecNum as $q) {
            if ($q != 0 && ($q < 10 ||  $q > $limitsaldo)) {
                session()->setFlashData('alert', "3|Nominal Recommend tidak boleh dibawah 10 atau lebih dari $limitsaldo!");
                return redirect()->to('../CAdmin/Setting');
            }

            $dtRecNum = array(
                'amount' => $q,
                'inputby' => $myID,
                'inputdate' => $xJam->format('Y-m-d H:i:s')
            );

            if (!$qry->ins('trecnum', $dtRecNum)) {
                $qry->db->transRollback();
                session()->setFlashData('alert', '3|Terjadi Kesalahan Update Recommend Number!');
                return redirect()->to('../CAdmin/Setting');
            };
        }

        $xI = 1;
        foreach (['Topup', 'Sales', 'Placement', 'Withdraw', 'CheckIn', 'Prize', 'Commission'] as $q) {
            $xKodeDepan = trim($this->request->getPost("txtKodeDepan{$q}"));
            
            // Validate KodeDepan input
            if (empty($xKodeDepan)) {
                $qry->db->transRollback();
                session()->setFlashData('alert', "3|Kode Depan {$q} tidak boleh kosong!");
                return redirect()->to('../CAdmin/Setting');
            }
            
            // Sanitize KodeDepan - only allow alphanumeric characters
            if (!preg_match('/^[A-Za-z0-9]+$/', $xKodeDepan)) {
                $qry->db->transRollback();
                session()->setFlashData('alert', "3|Kode Depan {$q} hanya boleh berisi huruf dan angka!");
                return redirect()->to('../CAdmin/Setting');
            }

            if (!$qry->upd('tjenistrans', array('kodedepan' => $xKodeDepan), array('id' => $xI++))) {
                $qry->db->transRollback();
                session()->setFlashData('alert', "3|Terjadi Kesalahan Update Kode Depan {$q}!");
                return redirect()->to('../CAdmin/Setting');
            }
        }



        $qry->db->transComplete();
        if ($qry->db->transStatus() === FALSE) {
            $qry->db->transRollback();
            session()->setFlashData('alert', '3|Terjadi Kesalahan!');
            return redirect()->to('../CAdmin/Setting');
        }

        $CTools = new CTools();
        $CTools->getComp();

        // Set success message with activation key if it was generated
        if (session('idUser') == 1 && !empty($activationKey)) {
            session()->setFlashData('alert', "5|Data Berhasil diUpdate! Activation Key: $activationKey");
            return redirect()->to('../CAdmin/Setting');
        }

        return redirect()->to('../CAdmin/SuccessSaveSetting');
    }

    public function SuccessSaveSetting()
    {
        session()->setFlashData('alert', '5|Data Berhasil diUpdate!');
        return redirect()->to('../CAdmin/Setting');
    }

    public function ResetLimitKomisi()
    {
        $qry = new Base_model();
        $qry->del('tdatekomisi');
        session()->setFlashData('alert', '5|Limit Komisi Berhasil diHapus!');
        return redirect()->to('../CAdmin/Setting');
    }

    public function DisableUser($id = 0)
    {
        $qry = new Base_model();
        $myLevel = session('level');
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            session()->setFlashData('alert', '3|No Access!');
            return redirect()->to('../CBase');
        }

        if ($id == 0) {
            session()->setFlashData('alert', '3|Harap pilih User!');
            return redirect()->to('../CData/User');
        }
        $str = "SELECT idshift, id  FROM tsesi WHERE status = 1;";
        $dtID = $qry->usefirst($str);
        $xIDShift = $dtID->idshift ?? 0;
        $xIDSesi = $dtID->id ?? 0;

        $str = "SELECT 1 FROM tplacement WHERE idshift = '$xIDShift' AND idsesi = '$xIDSesi' AND iduser = '$id'";
        $dtAny = $qry->usefirst($str);
        if ($dtAny) {
            session()->setFlashData('alert', '3|User Sedang on Placement!');
            return redirect()->to('../CData/User');
        }

        $str = "SELECT IFNULL(SUM(amount), 0) amount FROM tsaldo WHERE iduser = '$id'";
        $dtSaldo = $qry->usefirst($str)->amount;
        if ($dtSaldo != 0) {
            session()->setFlashData('alert', '3|User masih memiliki sisa Saldo!');
            return redirect()->to('../CData/User');
        }

        if (!($qry->upd('tuser', array('status' => 8), array('id' => $id)))) {
            session()->setFlashData('alert', '3|Terjadi Kesalahan!');
            return redirect()->to('../CData/User');
        }

        session()->setFlashData('alert', '5|Berhasil Disable User!');
        return redirect()->to('../CData/User');
    }

    public function ReActiveUser($id = 0)
    {
        $qry = new Base_model();
        $myLevel = session('level');
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            session()->setFlashData('alert', '3|No Access!');
            return redirect()->to('../CBase');
        }

        if ($id == 0) {
            session()->setFlashData('alert', '3|Harap pilih User!');
            return redirect()->to('../CData/User');
        }

        if (!($qry->upd('tuser', array('status' => 5), array('id' => $id)))) {
            session()->setFlashData('alert', '3|Terjadi Kesalahan!');
            return redirect()->to('../CData/User');
        }

        session()->setFlashData('alert', '5|Berhasil ReActive User!');
        return redirect()->to('../CData/User');
    }

    public function SaveInformasi()
    {
        $qry = new Base_model();
        $xJam = new Datetime($qry->getWaktu());
        $myID = session('idUser');
        $myLevel = session('level');

        if (!($myLevel >= 1 && $myLevel <= 3)) {
            session()->setFlashData('alert', '3|No Access!');
            return redirect()->to('../CBase');
        }

        $xInformasi = $this->request->getPost('meInformasi');
        $xCaraMain = $this->request->getPost('meCaraMain');

        if (!($qry->upd('tcomp', array('informasi' => $xInformasi, 'caramain' => $xCaraMain)))) {
            session()->setFlashData('alert', '3|Terjadi Kesalahan!');
            return redirect()->to('../CAdmin/Setting');
        }

        session()->setFlashData('alert', '5|Berhasil Update Informasi!');
        return redirect()->to('../CAdmin/Setting');
    }

    public function ResetData()
    {
        $myLevel = session('level');
        if ($myLevel != 1) {
            session()->setFlashData('alert', '3|No Access!');
            return redirect()->to('../CBase');
        }

        $str = "TRUNCATE TABLE tplacement;
                TRUNCATE TABLE tplacementd;
                TRUNCATE TABLE twin;
                TRUNCATE TABLE twind;
                TRUNCATE TABLE treqtopup;
                TRUNCATE TABLE treqwithdraw;
                TRUNCATE TABLE tsaldo;
                TRUNCATE TABLE tsesi;
                TRUNCATE TABLE tshift;
                TRUNCATE TABLE ttrans;
                TRUNCATE TABLE tdatekomisi;";
        $qry = new Base_model();
        $qry->trunc($str);

        session()->setFlashData('alert', '5|Berhasil Membersihkan Data!');
        return redirect()->to('../CAdmin/Setting');
    }
}
