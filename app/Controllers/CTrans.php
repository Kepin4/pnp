<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Base_model;
use App\Helpers;
use App\Libraries\func;
use DateTime;
use stdClass;
use Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

class CTrans extends Controller
{
    public $CTools;
    public function __construct()
    {
        $this->CTools = new CTools();
        $qry = new Base_model();
        if (!session()->has('Comp')) {
            $CTools = new CTools();
            $CTools->getComp();
        }
    }

    public function index()
    {
        return redirect()->to("../CTrans/Transaction");
    }

    public function Transaction()
    {
        $qry = new Base_model;
        $xJam = new DateTime($qry->getWaktu());
        $myID = session('idUser');
        $dtStart = $xJam->format("Y-m-d");
        $dtEnd = $xJam->format("Y-m-d");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dtStart = $this->request->getPost('dtStart');
            $dtEnd = $this->request->getPost('dtEnd');
            session()->setFlashdata('fltrTrans', (object)  array('dtStart' => $dtStart, 'dtEnd' => $dtEnd));
            header("Location: /CTrans/Transaction");
            exit();
        }

        if (session()->has('fltrTrans')) {
            $dtFilter = session('fltrTrans');
            $dtStart = $dtFilter->dtStart;
            $dtEnd = $dtFilter->dtEnd;
        }
        $whr = "";
        if (!(session('level') >= 1 && session('level') <= 3)) {
            $whr = "AND iduser = '{$myID}'";
        }

        $xKali = 1;
        if (session('level') >= 1 && session('level') <= 3) {
            $xKali = -1;
        }

        $str = "SELECT 
                    t.id,
                    t.notrans,
                    t.tanggal,
                    t.iduser,
                    u.username,
                    t.jenistrans,
                    jt.jenis,
                    t.keterangan,
                    (t.amount * $xKali) amount,
                    ((t.amount * t.cashback) * $xKali) cashback,
                    (t.total * $xKali) total
                FROM ttrans t
                    LEFT JOIN tuser u ON t.iduser = u.id
                    LEFT JOIN tjenistrans jt ON t.jenistrans = jt.id
                WHERE t.status = 5
                    AND t.jenistrans <> 7 
                    AND DATE(t.tanggal) BETWEEN '{$dtStart}' AND '{$dtEnd}' {$whr}
                ORDER BY t.id DESC";
        $data['dtTrans'] = $qry->use($str);
        $data['anyData'] = !empty($data['dtTrans']);
        $data['fltrTrans'] = (object) array('dtStart' => $dtStart, 'dtEnd' => $dtEnd);
        echo view("vHead");
        echo view("vMenu");
        echo view("vTrans", $data);
        echo view("vFooter");
    }

    public function NewTransaction($idUser = 0)
    {
        $qry = new Base_model;
        if (!(session('level') >= 1 && session('level') <= 3)) {
            session()->setFlashdata('alert', "3|No Access!");
            return redirect()->to("../CTrans/Transaction");
        }

        $data['idUser'] = $idUser;
        $data['xKodeReq'] = "";
        $data['xNominal'] = 0;

        $str = "SELECT id, username FROM tuser WHERE status = 5";
        $data['dtUser'] = $qry->use($str);

        $str = "SELECT id, jenis FROM tjenistrans";
        $data['dtJenisTrans'] = $qry->use($str);

        session()->setFlashdata('cache');
        echo view("vHead");
        echo view("vMenu");
        echo view("vNewTrans", $data);
        echo view("vFooter");
    }

    public function SaveTransaction()
    {

        if (!session()->has('AllowSave')) {
            session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba lagi!");
            return redirect()->to('/CTrans/NewTransaction');
        }
        session()->remove('AllowSave');

        if (!(session('level') >= 1 && session('level') <= 3)) {
            session()->setflashdata('alert', '3|Unauthorized User, ADMIN only!');
            return redirect()->to('/CData/User');
        }

        $qry = new Base_model;
        $xJam = new DateTime($qry->getWaktu());
        $myID = session('idUser');
        $IDAccount = $this->request->getPost("selAccount");
        $IDJenisTrans = $this->request->getPost("selJenisTrans");
        $Keterangan = $this->request->getPost("txtDesc");
        $Amount = $this->request->getPost("txtNominal");
        $xKodeReq = $this->request->getPost("txtKodeReq");

        // $Cashback = session('cashback');
        // $CashbackAmount = $Amount * $Cashback / 100;
        // $Total = $Amount + $Cashback;

        $Total = $Amount;
        $str = "SELECT sum(amount) saldo FROM tsaldo WHERE iduser = '{$IDAccount}'";
        $dtUser = $qry->use($str);
        $SaldoUser = reset($dtUser)->saldo;

        if ($IDJenisTrans != 1 && $Total > $SaldoUser) {
            session()->setFlashdata('alert', "3|Saldo User tidak cukup!");
            return redirect()->To('/CTrans/NewTransaction');
        }

        $dtJenisTrans = $qry->usefirst("SELECT kodedepan, ismin FROM tjenistrans WHERE id = {$IDJenisTrans}");
        $KodeDepan = $dtJenisTrans->kodedepan ?? '';
        $xIsMin = (bool) $dtJenisTrans->ismin ?? false;
        $Amount *=  $xIsMin ? -1 : 1;
        $qry->db->transStart();
        $Notrans = func::getKey("ttrans", "notrans", $KodeDepan, $xJam, true, true);
        $dtTrans = array(
            'notrans' => $Notrans,
            'noref' => $xKodeReq,
            'jenistrans' => $IDJenisTrans,
            'iduser' => $IDAccount,
            'tanggal' => $xJam->format('Y-m-d H:i:s'),
            'tanggalperiode' => $xJam->format('Y-m-d H:i:s'),
            'keterangan' => $Keterangan,
            'amount' => $Amount,
            'total' => $Total,
            'status' => 5,
            'inputby' => $myID,
            'inputdate' => $xJam->format('Y-m-d H:i:s')
        );

        $rslt = $qry->ins("ttrans", $dtTrans);
        if (!$rslt) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba lagi!");
            return redirect()->to('/CTrans/NewTransaction');
        }

        $rslt = $qry->ins("tsaldo",  array(
            'notrans' => $Notrans,
            'tanggal' => $xJam->format('Y-m-d H:i:s'),
            'iduser' => $IDAccount,
            'amount' => $Amount,
            'inputby' => $myID,
            'inputdate' =>  $xJam->format('Y-m-d H:i:s')
        ));
        if (!$rslt) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba lagi!");
            return redirect()->to('/CTrans/NewTransaction');
        }

        if ($xKodeReq != "") {
            if ($IDJenisTrans == 1) {
                $rslt = $qry->upd("treqtopup", array('status' => 5, 'updateby' => $myID, 'updatedate' => $xJam->format('Y-m-d H:i:s')), array('kodereq' => $xKodeReq));
            } elseif ($IDJenisTrans == 4) {
                $rslt = $qry->upd("treqwithdraw", array('status' => 5, 'updateby' => $myID, 'updatedate' => $xJam->format('Y-m-d H:i:s')), array('kodereq' => $xKodeReq));
            }

            if (!$rslt) {
                $qry->db->transRollback();
                session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba lagi!");
                return redirect()->to('/CTrans/NewTransaction');
            }
        }


        $qry->db->transComplete();
        if ($qry->db->transStatus() === FALSE) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba lagi!");
            return redirect()->to('/CTrans/NewTransaction');
        }

        session()->setFlashdata('alert', "5|Berhasil Menambahkan Transaksi {$Notrans}, {$Keterangan}!");
        return redirect()->to('/CTrans/TransactionSuccess');
    }
    public function TransactionSuccess()
    {
        session()->setFlashdata('alert', session()->getFlashdata('alert'));
        return redirect()->to('/CTrans/Transaction');
    }


    public function getUsername($id = 0): string
    {
        $qry = new Base_model();
        $str = "SELECT id, username FROM tuser WHERE id = '{$id}'";
        return ($qry->usefirst($str)->username ?? "");
    }
    public function ShiftReport()
    {
        if (!(session('level') >= 1 && session('level') <= 3)) {
            session()->setflashdata('alert', '3|Unauthorized User, ADMIN only!');
            return redirect()->to('../CData/User');
        }

        $qry = new Base_model();
        $xJam = new DateTime($qry->getWaktu());

        $dtStart = $this->request->getPost('dtStart');
        $dtEnd = $this->request->getPost('dtEnd');
        if (!$dtStart) {
            $dtStart = $xJam->format("Y-m-d");
        }
        if (!$dtEnd) {
            $dtEnd = $xJam->format("Y-m-d");
        }

        $str = "SELECT * FROM tshift WHERE isclose AND date(inputdate) BETWEEN '$dtStart' AND '$dtEnd' ORDER BY 1 DESC";
        $dtShift = $qry->use($str);

        $str = "SELECT idshift, total FROM tplacement";
        $dtSesi = $qry->use($str);

        $str = "SELECT h.idshift, SUM(d.amount) amount, SUM(d.amount * d.kali * d.cashback) cashback, SUM(d.total) total FROM twind d LEFT JOIN twin h ON h.id = d.idwin GROUP BY h.idshift";
        $dtWin = $qry->use($str);

        $dt = [];
        foreach ($dtShift as $q) {
            $obj = new stdClass;
            $obj->IDShift = $q->id;
            $obj->IDInputBy = $q->inputby;
            $obj->InputBy = $this->getUsername($q->inputby);
            $obj->InputDate = $q->inputdate;
            $obj->IDCloseBy = $q->updateby;
            $obj->CloseBy = $this->getUsername($q->updateby);
            $obj->CloseDate = $q->updatedate;

            $qSesi = array_filter($dtSesi, function ($x) use ($q) {
                return $x->idshift == $q->id;
            });

            $qWin = array_filter($dtWin, function ($x) use ($q) {
                return $x->idshift == $q->id;
            });

            $obj->Periode = count($qSesi);
            $obj->TotalPlacement = array_sum(array_column($qSesi, 'total'));
            $obj->NominalWin =  array_sum(array_column($qWin, 'amount'));
            $obj->NominalCashback =  array_sum(array_column($qWin, 'cashback'));
            $obj->TotalWin =  array_sum(array_column($qWin, 'total'));
            $obj->Total = $obj->TotalWin + $obj->NominalCashback - $obj->TotalPlacement;
            $obj->TotalComp = $obj->TotalPlacement -  $obj->TotalWin - $obj->NominalCashback;
            $dt[] =  $obj;
        }


        session()->setFlashdata('cache', (object) array('dtStart' => $dtStart, 'dtEnd' => $dtEnd));
        $data['dt'] = $dt;
        echo view('vHead');
        echo view('vMenu');
        echo view('vShiftReport', $data);
        echo view('vFooter');
    }

    public function DetailTransaction($xNotrans)
    {
        $qry = new Base_model();
        $str = "SELECT t.notrans, t.noref, t.tanggal, t.iduser, t.keterangan, t.amount, t.cashback, t.total, t.jenistrans, u.username, jt.jenis FROM ttrans t LEFT JOIN tuser u ON t.iduser = u.id LEFT JOIN tjenistrans jt ON t.jenistrans = jt.id WHERE t.notrans = '{$xNotrans}';";
        $dtTrans = $qry->usefirst($str);
        if (!$dtTrans) {
            session()->setFlashdata('alert', "3|Data Transaksi tidak ditemukan!");
            return redirect()->to('/CTrans/Transaction');
        }

        $IDShift = 0;
        $Periode = 0;
        $WinNum = 0;
        if ($dtTrans->jenistrans == 3) {
            $str = "SELECT idshift, idsesi FROM tplacement WHERE notrans = '{$xNotrans}'";
            $qPlacement = $qry->usefirst($str);
            if ($qPlacement) {
                $IDShift = $qPlacement->idshift;
                $IDSesi = $qPlacement->idsesi;
                $str = "SELECT count(1) periode FROM tsesi WHERE id <= $IDSesi AND idshift = $IDShift";
                $Periode =  $qry->usefirst($str)->periode ?? 0;

                $str = "SELECT number FROM twin WHERE status = 5 AND idshift = $IDShift AND sesi = $IDSesi";
                $WinNum =   $qry->usefirst($str)->number ?? 0;
            }
        } elseif ($dtTrans->jenistrans == 6) {
            $str = "SELECT noref FROM ttrans WHERE notrans = '$xNotrans'";
            $Noref = $qry->usefirst($str)->noref ?? '';
            if ($Noref == '') {
                session()->setFlashdata('alert', "3|Data Transaksi tidak ditemukan!");
                return redirect()->to('/CTrans/Transaction');
            }
            $str = "SELECT idshift, idsesi FROM tplacement WHERE notrans = '{$Noref}'";
            $qPlacement = $qry->usefirst($str);
            if ($qPlacement) {
                $IDShift = $qPlacement->idshift;
                $IDSesi = $qPlacement->idsesi;
                $str = "SELECT count(1) periode FROM tsesi WHERE id <= $IDSesi AND idshift = $IDShift";
                $Periode =  $qry->usefirst($str)->periode ?? 0;

                $str = "SELECT number FROM twin WHERE status = 5 AND idshift = $IDShift AND sesi = $IDSesi";
                $WinNum =   $qry->usefirst($str)->number ?? 0;
            }
        }

        // Handle multiple notrans for JenisTrans = 1 (Topup)
        if ($dtTrans->jenistrans == 1) {
            // Get all notrans with the same noref (for multi-payment topups)
            $str = "SELECT notrans FROM ttrans WHERE noref = (SELECT noref FROM ttrans WHERE notrans = '{$xNotrans}') AND jenistrans = 1 AND status = 5";
            $allNotrans = $qry->use($str);

            if (!empty($allNotrans)) {
                $notransList = array_column($allNotrans, 'notrans');
                $notransString = "'" . implode("','", $notransList) . "'";
                $str = "SELECT amount FROM tsaldo WHERE notrans IN ({$notransString})";
            } else {
                $str = "SELECT amount FROM tsaldo WHERE notrans = '{$xNotrans}'";
            }
        } else {
            $str = "SELECT amount FROM tsaldo WHERE notrans = '{$xNotrans}'";
        }
        $dtSaldo = $qry->use($str);

        $str = "SELECT status FROM tplacement WHERE notrans = '{$xNotrans}'";
        $qStatus = $qry->usefirst($str)->status ?? 1;

        $str = "SELECT notrans FROM ttrans WHERE noref = '{$xNotrans}' AND jenistrans = 6";
        $qNotransWin = $qry->usefirst($str)->notrans ?? '';

        $data['idUser'] = $dtTrans->iduser;
        $data['TotalSaldo'] = array_sum(array_column((array) $dtSaldo, 'amount'));
        $data['dtSaldo'] = $dtSaldo;

        $data['IDShift'] = $IDShift;
        $data['Periode'] = $Periode;
        $data['WinNum'] = $WinNum;

        $data['qStatus'] = $qStatus;
        $data['qNotransWin'] = $qNotransWin;
        $data['qTrns'] = $dtTrans;
        echo view('vHead');
        echo view('vMenu');
        echo view('vDetailTransaction', $data);
        echo view('vFooter');
    }

    public function DetailTransactionRef($xNoref)
    {
        $qry = new Base_model();
        $str = "SELECT notrans FROM ttrans WHERE noref = '{$xNoref}' ORDER BY id ASC;";
        $dtTrans = $qry->use($str);
        if (empty($dtTrans)) {
            session()->setFlashdata('alert', "3|Data Transaksi tidak ditemukan!");
            return redirect()->to('/CTrans/Transaction');
        }

        // Get all transaction numbers for this reference
        $allNotrans = array_column($dtTrans, 'notrans');
        $notransString = implode(',', $allNotrans);

        // Store the transaction numbers in session for the detail view
        session()->setFlashdata('multi_notrans', $notransString);

        // Use the first transaction for the detail view
        $firstNotrans = $dtTrans[0]->notrans;
        return redirect()->to('../CTrans/DetailTransaction/' . $firstNotrans);
    }

    public function RequestTopUp()
    {
        $qry = new Base_model();
        $xJam = new DateTime($qry->getWaktu());
        $xAmount  = $this->request->getPost('txtNominal');
        $xKodeReq = "";

        $xListChar = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $xListLen = strlen($xListChar);

        $xBool = true;
        while ($xBool) {
            $xKodeReq = '';
            for ($i = 0; $i < 15; $i++) {
                $xKodeReq .= $xListChar[rand(0, $xListLen - 1)];
            }

            $str = "SELECT 1 FROM treqtopup WHERE kodereq = '{$xKodeReq}'";
            $xBool = !empty($qry->use($str));
        }

        $dtIns = array(
            'kodereq' => $xKodeReq,
            'iduser' => session('idUser'),
            'amount' => $xAmount,
            'tanggal' => $xJam->format('Y-m-d H:i:s'),
            'status' => 1
        );

        $rslt = $qry->ins('treqtopup', $dtIns);
        if (!$rslt) {
            session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahka Coba Lagi atau Kontak Admin Secara Manual!.');
            return redirect()->to('CBase/Profile');
        }


        $waUrl = 'https://wa.me/' . urlencode(session('Comp')->nohp) . '?text=' .
            urlencode(
                '-----===| Request-Topup |===-----' . "\n\n" .
                    'Kode Request = ' . $xKodeReq . "\n" .
                    'Account ID = ' . session('idUser') . "\n" .
                    'Username = ' . session('username') . "\n" .
                    'Nominal = ' . $xAmount . "\n\n" .
                    base_url('../../CTrans/ProcessReqTopUp/') . $xKodeReq
            );

        session()->setFlashdata('ReqTopup', $waUrl);
        session()->setFlashdata('alert', '5|Berhasil Request TopUp.');
        return redirect()->to('CBase/Profile');
    }


    public function RequestWithdraw()
    {
        $qry = new Base_model();
        $xJam = new DateTime($qry->getWaktu());
        $myID = session('idUser');
        $xAmount  = $this->request->getPost('txtNominal');
        $xNoRef  = $this->request->getPost('txtNoref') ?? "";
        $Saldo = $this->CTools->getSaldo($myID);

        if ($xAmount > $Saldo) {
            session()->setFlashdata('alert', '3|Saldo Tidak Mencukupi!');
            return redirect()->to('CBase/Profile');
        }

        $xKodeReq = "";
        $xListChar = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $xListLen = strlen($xListChar);

        $xBool = true;
        while ($xBool) {
            $xKodeReq = '';
            for ($i = 0; $i < 15; $i++) {
                $xKodeReq .= $xListChar[rand(0, $xListLen - 1)];
            }

            $str = "SELECT 1 FROM treqwithdraw WHERE kodereq = '{$xKodeReq}'";
            $xBool = !empty($qry->use($str));
        }

        $dtIns = array(
            'kodereq' => $xKodeReq,
            'noref' => $xNoRef,
            'iduser' => $myID,
            'amount' => $xAmount,
            'tanggal' => $xJam->format('Y-m-d H:i:s'),
            'status' => 1
        );

        $rslt = $qry->ins('treqwithdraw', $dtIns);
        if (!$rslt) {
            session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahka Coba Lagi atau Kontak Admin Secara Manual!.');
            return redirect()->to('CBase/Profile');
        }


        $waUrl = 'https://wa.me/' . urlencode(session('Comp')->nohp) . '?text=' .
            urlencode(
                '-----===| Request-Withdraw |===-----' . "\n\n" .
                    'Kode Request = ' . $xKodeReq . "\n" .
                    'Bank = ' . $xNoRef . "\n" .
                    'Account ID = ' . $myID . "\n" .
                    'Username = ' . session('username') . "\n" .
                    'Nominal = ' . $xAmount . "\n\n" .
                    base_url('../../CTrans/ProcessReqWithdraw/') . $xKodeReq
            );

        session()->setFlashdata('ReqTopup', $waUrl);
        session()->setFlashdata('alert', '5|Berhasil Request Withdraw.');
        return redirect()->to('CBase/Profile');
    }


    public function ProcessReqTopUp($xKodeReq)
    {
        if (!(session('level') >= 1 && session('level') <= 3)) {
            session()->setFlashdata('alert', '3|Unauthorized User Access, Only For Admin!');
            return redirect()->to('CBase/Profile');
        }

        $qry = new Base_model();
        // Get topup request with paid amount from database
        $str = "SELECT r.id, r.iduser, r.amount, COALESCE(SUM(t.amount), 0) as paid_amount
                FROM treqtopup r 
                LEFT JOIN ttrans t ON r.kodereq = t.noref AND t.jenistrans = 1 AND t.status = 5
                WHERE r.kodereq = '{$xKodeReq}' AND (r.status = 1 OR r.status = 4)
                GROUP BY r.id, r.iduser, r.amount";
        $dtReqTopup = $qry->use($str);
        $ReqTopup = reset($dtReqTopup);

        if (empty($ReqTopup)) {
            session()->setFlashdata('alert', '3|Request Topup not Found!');
            return redirect()->to('CTrans/Transaction');
        }

        $dtCache = array(
            'idUser' => $ReqTopup->iduser,
            'Username' => $this->CTools->getUsername($ReqTopup->iduser),
            'JenisTrans' => 1,
            'KodeReq' => $xKodeReq,
            'Nominal' => $ReqTopup->amount,
            'PaidAmount' => $ReqTopup->paid_amount,
        );
        session()->setFlashdata('cache',  $dtCache);


        $str = "SELECT id, username FROM tuser WHERE status = 5";
        $data['dtUser'] = $qry->use($str);

        $str = "SELECT id, jenis FROM tjenistrans";
        $data['dtJenisTrans'] = $qry->use($str);

        echo view("vHead");
        echo view("vMenu");
        echo view("vNewTopupTrans", $data);
        echo view("vFooter");
    }


    public function ProcessReqWithdraw($xKodeReq)
    {
        $myLevel = session('level');
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            session()->setFlashdata('alert', '3|Unauthorized User Access, Only For Admin!');
            return redirect()->to('../CBase/Profile');
        }

        $qry = new Base_model();
        $str = "SELECT id, iduser, amount FROM treqwithdraw WHERE kodereq = '{$xKodeReq}' AND status = 1";
        $dtReqTopup = $qry->use($str);
        $ReqTopup = reset($dtReqTopup);

        if (empty($ReqTopup)) {
            session()->setFlashdata('alert', '3|Request Withdraw not Found!');
            return redirect()->to('CTrans/Transaction');
        }

        $dtCache = array(
            'idUser' => $ReqTopup->iduser,
            'Username' => $this->CTools->getUsername($ReqTopup->iduser),
            'JenisTrans' => 4,
            'KodeReq' => $xKodeReq,
            'Nominal' => $ReqTopup->amount,
        );
        session()->setFlashdata('cache',  $dtCache);

        $str = "SELECT id, username FROM tuser WHERE status = 5";
        $data['dtUser'] = $qry->use($str);

        $str = "SELECT id, jenis FROM tjenistrans";
        $data['dtJenisTrans'] = $qry->use($str);

        echo view("vHead");
        echo view("vMenu");
        echo view("vNewTrans", $data);
        echo view("vFooter");
    }

    public function RefuseReqTopup($KodeReq)
    {
        $myID = session('idUser');
        $myLevel = session('level');
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            session()->setFlashdata('alert', "3|Unauthorized User, Admin Only!");
            return redirect()->to('../CTrans/TopupRequest');
        }

        $qry = new Base_model();
        $xJam = new DateTime($qry->getWaktu());
        if (!$qry->upd('treqtopup', array('status' => 8, 'updateby' => $myID, 'updatedate' => $xJam->format('Y-m-d H:i:s')), array('kodereq' => $KodeReq))) {
            session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba Kembali!");
            return redirect()->to('../CTrans/TopupRequest');
        }

        session()->setFlashdata('alert', "5|Berhasil Refuse Topup!");
        return redirect()->to('../CTrans/TopupRequest');
    }

    public function RefuseReqWithdraw($KodeReq)
    {
        $myID = session('idUser');
        $myLevel = session('level');
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            session()->setFlashdata('alert', "3|Unauthorized User, Admin Only!");
            return redirect()->to('../CTrans/WithdrawRequest');
        }

        $qry = new Base_model();
        $xJam = new DateTime($qry->getWaktu());
        if (!$qry->upd('treqwithdraw', array('status' => 8, 'updateby' => $myID, 'updatedate' => $xJam->format('Y-m-d H:i:s')), array('kodereq' => $KodeReq))) {
            session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba Kembali!");
            return redirect()->to('../CTrans/WithdrawRequest');
        }

        session()->setFlashdata('alert', "5|Berhasil Refuse Topup!");
        return redirect()->to('../CTrans/WithdrawRequest');
    }

    public function TopupRequest()
    {
        $qry = new Base_model();
        $xJam = new Datetime($qry->getWaktu());

        $myID = session('idUser');
        $myLevel = session('level');

        $dtStart = $this->request->getPost('dtStart');
        $dtEnd = $this->request->getPost('dtEnd');
        $chkRequest = $this->request->getPost('chkRequest');
        $dtStart = !$dtStart ? $xJam->format("Y-m-d") : $dtStart;
        $dtEnd = !$dtEnd ? $xJam->format("Y-m-d") : $dtEnd;
        $xWhr = "";
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            $xWhr = "AND r.iduser = '{$myID}'";
        }

        session()->setFlashdata('cache', (object) array('dtStart' => $dtStart, 'dtEnd' => $dtEnd, 'chkRequest' => $chkRequest));
        $str = "SELECT r.kodereq, u.username, r.iduser, r.amount, r.tanggal, r.status, r.updateby, r.updatedate, 
                       COALESCE(SUM(t.amount), 0) as paid_amount
                FROM treqtopup r 
                INNER JOIN tuser u ON r.iduser = u.id 
                LEFT JOIN ttrans t ON r.kodereq = t.noref AND t.jenistrans = 1 AND t.status = 5
                WHERE date(r.tanggal) BETWEEN '$dtStart' AND '$dtEnd' {$xWhr} 
                GROUP BY r.kodereq, u.username, r.iduser, r.amount, r.tanggal, r.status, r.updateby, r.updatedate
                ORDER BY r.id DESC";
        if ($chkRequest) {
            $str = "SELECT r.kodereq, u.username, r.iduser, r.amount, r.tanggal, r.status, r.updateby, r.updatedate,
                           COALESCE(SUM(t.amount), 0) as paid_amount
                    FROM treqtopup r 
                    INNER JOIN tuser u ON r.iduser = u.id 
                    LEFT JOIN ttrans t ON r.kodereq = t.noref AND t.jenistrans = 1 AND t.status = 5
                    WHERE r.status = 1 {$xWhr} 
                    GROUP BY r.kodereq, u.username, r.iduser, r.amount, r.tanggal, r.status, r.updateby, r.updatedate
                    ORDER BY r.id DESC";
        }
        $data['dtReqTopup'] = $qry->use($str);
        echo view('vHead');
        echo view('vMenu');
        echo view('vTopupReq', $data);
        echo view('vFooter');
    }

    public function ajaxTopupRequests()
    {
        $qry = new Base_model();
        $xJam = new Datetime($qry->getWaktu());

        $myID = session('idUser');
        $myLevel = session('level');

        // Get filter parameters
        $dtStart = $this->request->getGet('dtStart');
        $dtEnd = $this->request->getGet('dtEnd');
        $status = $this->request->getGet('status');
        $chkRequest = $this->request->getGet('chkRequest');

        // Set default dates if not provided
        $dtStart = !$dtStart ? $xJam->format("Y-m-d") : $dtStart;
        $dtEnd = !$dtEnd ? $xJam->format("Y-m-d") : $dtEnd;

        // Build WHERE clause for user permissions
        $xWhr = "";
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            $xWhr = "AND r.iduser = '{$myID}'";
        }

        // Build WHERE clause for status filter
        $statusWhr = "";
        if (!empty($status)) {
            $statusWhr = "AND r.status = '{$status}'";
        }

        try {
            // Build the main query
            $str = "SELECT r.kodereq, u.username, r.iduser, r.amount, r.tanggal, r.status, r.updateby, r.updatedate,
                           COALESCE(SUM(t.amount), 0) as paid_amount,
                           CASE 
                               WHEN r.status = 5 THEN (SELECT username FROM tuser WHERE id = r.updateby)
                               ELSE ''
                           END as topup_by,
                           CASE 
                               WHEN r.status = 8 THEN (SELECT username FROM tuser WHERE id = r.updateby)
                               ELSE ''
                           END as refused_by,
                           CASE 
                               WHEN r.status IN (5, 8) THEN r.updatedate
                               ELSE NULL
                           END as update_time
                    FROM treqtopup r 
                    INNER JOIN tuser u ON r.iduser = u.id 
                    LEFT JOIN ttrans t ON r.kodereq = t.noref AND t.jenistrans = 1 AND t.status = 5
                    WHERE 1=1 {$xWhr} {$statusWhr}";

            // Apply date or request filter
            if ($chkRequest == 'on') {
                $str .= " AND r.status = 1";
            } else {
                $str .= " AND date(r.tanggal) BETWEEN '{$dtStart}' AND '{$dtEnd}'";
            }

            $str .= " GROUP BY r.kodereq, u.username, r.iduser, r.amount, r.tanggal, r.status, r.updateby, r.updatedate ORDER BY r.id DESC";

            $dtReqTopup = $qry->use($str);

            // Calculate summary
            $totalAmount = 0;
            $totalPaid = 0;
            if (!empty($dtReqTopup)) {
                $totalAmount = array_sum(array_column($dtReqTopup, 'amount'));
                $totalPaid = array_sum(array_column($dtReqTopup, 'paid_amount'));
            }

            // Prepare response
            $response = [
                'success' => true,
                'data' => $dtReqTopup,
                'summary' => [
                    'total_amount' => $totalAmount,
                    'total_paid' => $totalPaid,
                    'total_records' => count($dtReqTopup)
                ]
            ];

            return $this->response->setJSON($response);
        } catch (Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading data: ' . $e->getMessage()
            ]);
        }
    }

    public function ajaxWithdrawRequests()
    {
        $qry = new Base_model();
        $xJam = new Datetime($qry->getWaktu());

        $myID = session('idUser');
        $myLevel = session('level');

        // Get filter parameters
        $dtStart = $this->request->getGet('dtStart');
        $dtEnd = $this->request->getGet('dtEnd');
        $status = $this->request->getGet('status');
        $chkRequest = $this->request->getGet('chkRequest');

        // Set default dates if not provided
        $dtStart = !$dtStart ? $xJam->format("Y-m-d") : $dtStart;
        $dtEnd = !$dtEnd ? $xJam->format("Y-m-d") : $dtEnd;

        // Build WHERE clause for user permissions
        $xWhr = "";
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            $xWhr = "AND r.iduser = '{$myID}'";
        }

        // Build WHERE clause for status filter
        $statusWhr = "";
        if (!empty($status)) {
            $statusWhr = "AND r.status = '{$status}'";
        }

        try {
            // Build the main query
            $str = "SELECT r.kodereq, r.noref, u.username, r.iduser, r.amount, r.tanggal, r.status, r.updateby, r.updatedate,
                           CASE 
                               WHEN r.status = 5 THEN (SELECT username FROM tuser WHERE id = r.updateby)
                               ELSE ''
                           END as withdraw_by,
                           CASE 
                               WHEN r.status = 8 THEN (SELECT username FROM tuser WHERE id = r.updateby)
                               ELSE ''
                           END as refused_by,
                           CASE 
                               WHEN r.status IN (5, 8) THEN r.updatedate
                               ELSE NULL
                           END as update_time
                    FROM treqwithdraw r 
                    INNER JOIN tuser u ON r.iduser = u.id 
                    WHERE 1=1 {$xWhr} {$statusWhr}";

            // Apply date or request filter
            if ($chkRequest == 'on') {
                $str .= " AND r.status = 1";
            } else {
                $str .= " AND date(r.tanggal) BETWEEN '{$dtStart}' AND '{$dtEnd}'";
            }

            $str .= " ORDER BY r.id DESC";

            $dtReqWithdraw = $qry->use($str);

            // Calculate summary
            $totalAmount = 0;
            if (!empty($dtReqWithdraw)) {
                $approvedRequests = array_filter($dtReqWithdraw, function ($item) {
                    return $item->status == 5;
                });
                $totalAmount = array_sum(array_column($approvedRequests, 'amount'));
            }

            // Prepare response
            $response = [
                'success' => true,
                'data' => $dtReqWithdraw,
                'summary' => [
                    'total_amount' => $totalAmount,
                    'total_records' => count($dtReqWithdraw)
                ]
            ];

            return $this->response->setJSON($response);
        } catch (Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading data: ' . $e->getMessage()
            ]);
        }
    }


    public function WithdrawRequest()
    {
        $qry = new Base_model();
        $xJam = new Datetime($qry->getWaktu());

        $myID = session('idUser');
        $myLevel = session('level');

        $dtStart = $this->request->getPost('dtStart');
        $dtEnd = $this->request->getPost('dtEnd');
        $chkRequest = $this->request->getPost('chkRequest');
        $dtStart = !$dtStart ? $xJam->format("Y-m-d") : $dtStart;
        $dtEnd = !$dtEnd ? $xJam->format("Y-m-d") : $dtEnd;

        $xWhr = "";
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            $xWhr = "AND r.iduser = '{$myID}'";
        }

        session()->setFlashdata('cache', (object) array('dtStart' => $dtStart, 'dtEnd' => $dtEnd, 'chkRequest' => $chkRequest));
        $str = "SELECT r.kodereq, r.noref, u.username, r.iduser, r.amount, r.tanggal, r.status, r.updateby, r.updatedate FROM treqwithdraw r INNER JOIN tuser u ON r.iduser = u.id WHERE date(tanggal) BETWEEN '$dtStart' AND '$dtEnd' {$xWhr} ORDER BY r.id DESC";
        if ($chkRequest) {
            $str = "SELECT r.kodereq, r.noref, u.username, r.iduser, r.amount, r.tanggal, r.status, r.updateby, r.updatedate FROM treqwithdraw r INNER JOIN tuser u ON r.iduser = u.id WHERE r.status = 1 {$xWhr} ORDER BY r.id DESC";
        }
        $data['dtReqTopup'] = $qry->use($str);
        echo view('vHead');
        echo view('vMenu');
        echo view('vWithdrawReq', $data);
        echo view('vFooter');
    }

    public function DetailShift($id)
    {
        $myLevel = session('level');
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            session()->setFlashdata('alert', '3|No Access!');
            return redirect()->to('../CBase');
        }

        $qry = new Base_model();

        $str = "SELECT id, number, jamselesai, jamupdate, inputby, inputdate, updateby FROM tsesi WHERE idshift = '{$id}'";
        $dtSesi = $qry->use($str);
        $Count = count($dtSesi);

        $str = "SELECT w.sesi, SUM(wd.total) amount, SUM(wd.amount * wd.kali * wd.cashback) cashback FROM twin w INNER JOIN twind wd ON w.id = wd.idwin WHERE w.idshift = '{$id}'  GROUP by sesi";
        $dtWin = $qry->use($str);

        $str = "SELECT idsesi, sum(total) total FROM tplacement WHERE idshift = '{$id}' AND status = '5' GROUP by idsesi";
        $dtPlacement = $qry->use($str);

        $dt = [];
        foreach ($dtSesi as $q) {
            $obj = new stdClass;
            $obj->Sesi = $q->id;
            $obj->Number = $q->number;
            $obj->StartBy = $this->CTools->getUsername($q->inputby);
            $obj->StartDate = $q->inputdate;
            $obj->CloseBy =  $this->CTools->getUsername($q->updateby);
            $obj->CloseDate = $q->jamupdate;

            $qWin = array_filter($dtWin, function ($x) use ($q) {
                return $x->sesi == $q->id;
            });

            $qPlacement = array_filter($dtPlacement, function ($x) use ($q) {
                return $x->idsesi == $q->id;
            });
            $obj->TotalWin = reset($qWin)->amount ?? 0;
            $obj->TotalCashback = reset($qWin)->cashback ?? 0;
            $obj->TotalPlacement = reset($qPlacement)->total ?? 0;
            $obj->Total = $obj->TotalWin + $obj->TotalCashback - $obj->TotalPlacement;
            $dt[] = $obj;
        }

        $data['dtDetail'] = $dt;
        echo view('vHead');
        echo view('vMenu');
        echo view('vDetailShift', $data);
        echo view('vFooter');
    }

    public function ListNumber()
    {
        $myLevel = session('level');
        if (!($myLevel >= 1 &&  $myLevel <= 3)) {
            session()->setFlashdata('alert', '3|Unauthorized User, Admin Only!');
            return redirect()->to('../CBase/');
        }

        $dtStart =  '';
        $dtEnd =  '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dtStart = $this->request->getPost('dtStart');
            $dtEnd = $this->request->getPost('dtEnd');
            session()->setFlashData('fltr', (object)  array('dtStart' => $dtStart, 'dtEnd' => $dtEnd));
            header("Location: /CTrans/ListNumber");
            exit();
        }

        if (session()->has('fltr')) {
            $dtFilter = session('fltr');
            $dtStart = $dtFilter->dtStart;
            $dtEnd = $dtFilter->dtEnd;
        }

        $qry = new Base_model();
        $xJam = new Datetime($qry->getWaktu());

        if ($dtStart == '') {
            $dtStart = $xJam->format('Y-m-d');
        }
        if ($dtEnd == '') {
            $dtEnd = $xJam->format('Y-m-d');
        }

        if ($dtStart > $dtEnd) {
            session()->setFlashdata('alert', '3|Format Tanggal Salah!');
            return redirect()->to('../CTrans/ListNumber');
        }

        $str = "SELECT id FROM tshift WHERE date(inputdate) BETWEEN '{$dtStart}' AND '{$dtEnd}'";
        $IDShift = $qry->use($str);
        $IDShiftStr = implode(', ', array_column($IDShift, 'id'));

        $dtData = [];
        if ($IDShift) {
            $str = "SELECT p.idshift, d.alias, COUNT(1) xCount, SUM(amount) amount FROM tplacement p INNER JOIN tplacementd d ON p.id = d.idplacement WHERE NOT d.isrefuse AND p.idshift IN ({$IDShiftStr}) GROUP BY p.idshift, d.alias";
            $dtNumber = $qry->use($str);

            foreach ($IDShift as $qID) {
                for ($i = 1; $i <= 24; $i++) {
                    $qNum = array_filter($dtNumber, function ($x) use ($i) {
                        return $x->alias == $i;
                    });
                    $q = reset($qNum);

                    $obj = new stdClass;
                    $obj->Shift = $qID->id;
                    $obj->Number = $i;
                    $obj->TotalPasang = $q->xCount ?? 0;
                    $obj->TotalNominal = $q->amount ?? 0;
                    $dtData[] = $obj;
                }
                foreach (['Besar', 'Kecil', 'Ganjil', 'Genap'] as $i) {
                    $qNum = array_filter($dtNumber, function ($x) use ($i) {
                        return $x->alias == $i;
                    });
                    $q = reset($qNum);

                    $obj = new stdClass;
                    $obj->Shift = $qID->id;
                    $obj->Number = $i;
                    $obj->TotalPasang = $q->xCount ?? 0;
                    $obj->TotalNominal = $q->amount ?? 0;
                    $dtData[] = $obj;
                }
            }
        }

        $data['dtNum'] = $dtData;
        $data['fltr'] = (object) array('dtStart' => $dtStart, 'dtEnd' => $dtEnd);
        echo view('vHead');
        echo view('vMenu');
        echo view('vListNumber', $data);
        echo view('vFooter');
    }

    public function PlacementReport()
    {
        $qry = new Base_model();
        $xJam = new Datetime($qry->getWaktu());
        $myLevel = session('level');
        $myID = session('idUser');


        $dtStart = $xJam->format("Y-m-d");
        $dtEnd = $xJam->format("Y-m-d");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dtStart = $this->request->getPost('dtStart');
            $dtEnd = $this->request->getPost('dtEnd');
            session()->setFlashdata('fltrTrans', (object)  array('dtStart' => $dtStart, 'dtEnd' => $dtEnd));
            header("Location: /CTrans/PlacementReport");
            exit();
        }

        if (session()->has('fltrTrans')) {
            $dtFilter = session('fltrTrans');
            $dtStart = $dtFilter->dtStart;
            $dtEnd = $dtFilter->dtEnd;
        }



        $whr = '';
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            $whr = "AND iduser = '{$myID}'";
        }

        $str = "SELECT notrans, noref, iduser, SUM(amount) amount, SUM(amount * cashback) cashback, jenistrans FROM ttrans WHERE status = 5 AND noref <> '' GROUP BY notrans, noref";
        $dtRef = $qry->use($str);

        $str = "SELECT wd.notrans, w.number, SUM(wd.total) amount, SUM(wd.total * wd.cashback) cashback FROM twin w INNER JOIN twind wd ON w.id = wd.idwin GROUP BY wd.notrans";
        $dtWinNum = $qry->use($str);

        $str = "SELECT p.notrans, p.total, COUNT(1) xCount, MIN(status) status FROM tplacement p INNER JOIN tplacementd d ON p.id = d.idplacement GROUP BY notrans;";
        $dtPlacement = $qry->use($str);

        $dtData = [];
        $str = "SELECT notrans, tanggal, iduser, amount FROM ttrans WHERE jenistrans = 3 {$whr} AND date(tanggalperiode) BETWEEN '{$dtStart}' AND '{$dtEnd}' AND status = 5 ORDER BY id DESC";
        foreach ($qry->use($str) as $q) {
            $obj = new stdClass;
            $obj->notrans = $q->notrans;
            $obj->tanggal = $q->tanggal;
            $obj->username = $this->CTools->getUsername($q->iduser);
            $obj->amount = $q->amount;

            $qWin = array_filter($dtWinNum, function ($x) use ($q) {
                return $x->notrans == $q->notrans;
            });

            $dtPlcmnt = array_filter($dtPlacement, function ($x) use ($q) {
                return $x->notrans == $q->notrans;
            });
            if (!$dtPlcmnt) {
                continue;
            }
            $qPlacement = reset($dtPlcmnt);
            $obj->TotalWin = array_sum(array_column($qWin, 'amount'));
            $obj->TotalPlacement = $qPlacement->xCount ?? 0;
            $obj->TotalNominalPlacement = $qPlacement->total ?? 0;
            $obj->TotalNominalCashback =  (float) array_sum(array_column($qWin, 'cashback'));
            $obj->StatusWinLoss = ($qWin ? 'Win' : ($qPlacement->status == 1 ? "On Placement" : "Lose"));
            $obj->TotalAkhir = ($obj->TotalWin + $obj->TotalNominalCashback) - $obj->TotalNominalPlacement;
            $dtData[] = $obj;
        }

        $data['dt'] = $dtData;
        $data['fltrTrans'] = (object) array('dtStart' => $dtStart, 'dtEnd' => $dtEnd);
        echo view('vHead');
        echo view('vMenu');
        echo view('vPlacementReport', $data);
        echo view('vFooter');
    }

    public function IncomeReport()
    {
        $qry = new Base_model();
        $xJam = new Datetime($qry->getWaktu());
        $myLevel = session('level');
        $myID = session('idUser');

        $dtStart = $xJam->format("Y-m-d");
        $dtEnd = $xJam->format("Y-m-d");
        $txtID = 0;
        $txtIsNoAgent = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dtStart = $this->request->getPost('dtStart');
            $dtEnd = $this->request->getPost('dtEnd');
            $txtID = $this->request->getPost('txtID');
            $txtIsNoAgent = $this->request->getPost('txtIsNoAgent');
            session()->setFlashdata('fltrTrans', (object)  array('dtStart' => $dtStart, 'dtEnd' => $dtEnd, 'txtID' => $txtID, 'txtIsNoAgent' => $txtIsNoAgent));
            header("Location: /CTrans/IncomeReport");
            exit();
        }

        if (session()->has('fltrTrans')) {
            $dtFilter = session('fltrTrans');
            $dtStart = $dtFilter->dtStart;
            $dtEnd = $dtFilter->dtEnd;
            $txtID = $dtFilter->txtID;
            $txtIsNoAgent = $dtFilter->txtIsNoAgent;
        }

        $whrID = " AND idatasan = '$myID'";
        if ($myLevel >= 1 && $myLevel <= 3) {
            $whrID = "AND level = 4";
        } elseif ($myLevel == 4) {
            $whrID = " AND idatasan = '$myID'";
        } else {
            $whrID = " AND id = '$myID'";
        }


        $xWBawahan = false;
        if (($myLevel >= 1 && $myLevel <= 3) && (($txtID == 0 || $txtID == "") && !$txtIsNoAgent)) {
            $xWBawahan = true;
        }

        if (!($txtID == 0 || $txtID == "") && !$txtIsNoAgent) {
            $whrID = " AND idatasan = '$txtID'";
        }

        if ($txtIsNoAgent) {
            $whrID .= " AND level = 5";
        }

        $str = "SELECT id, username, level, idatasan FROM tuser WHERE status = 5 AND level > 3 $whrID";
        $dtUser = $qry->use($str);

        $str = "SELECT
                    h.iduser,
                    SUM(d.amount) total,
                    SUM(d.amount * (IF(d.isbanding, 2, 22)) * d.cashback) cashback
                FROM tplacement h
                    INNER JOIN tplacementd d ON h.id = d.idplacement
                    INNER JOIN ttrans t ON h.notrans = t.notrans AND t.jenistrans = 3
                WHERE h.status = 5
                    AND DATE(t.tanggalperiode) BETWEEN '$dtStart' AND '$dtEnd'
                GROUP BY h.iduser;";
        $dtPlacement = $qry->use($str);

        $str = "SELECT notrans, iduser, SUM(amount) amount, SUM(total * cashback) cashback, SUM(total) total FROM twind WHERE date(tanggal) BETWEEN '$dtStart' AND '$dtEnd' GROUP BY notrans";
        $dtWin = $qry->use($str);

        $str = "SELECT iduser, amount FROM ttrans WHERE jenistrans = 7 AND status = 5 AND date(inputdate) BETWEEN '$dtStart' AND '$dtEnd'";
        $dtKomisi = $qry->use($str);

        $dt = [];
        foreach ($dtUser as $q) {
            $qPlacementDTotal = 0;
            $qPlacementDCashback = 0;
            $qWinD = 0;
            $qNominalPlayD = 0;


            $tPlacement = array_filter($dtPlacement, function ($x) use ($q) {
                return $x->iduser == $q->id;
            });
            $qPlacement = reset($tPlacement);

            $tWin =  array_filter($dtWin, function ($x) use ($q) {
                return $x->iduser == $q->id;
            });
            $qWin = array_sum(array_column($tWin, "total"));
            $qNominalPlay = array_sum(array_column($tWin, "amount"));
            $qCashback = array_sum(array_column($tWin, "cashback"));

            $obj = new stdClass;
            $obj->idUser = $q->id;
            $obj->Username = $q->username;
            $obj->Level = $q->level;
            $obj->idAtasan = $q->idatasan;
            $obj->TurnOver = ($qPlacement->total ?? 0);
            $obj->Cashback = $qCashback;
            $obj->NominalPlay = $qNominalPlay;
            $obj->Win = $qWin;
            $obj->Komisi = 0;
            $obj->isNoAgent = false;

            $obj->TurnOverD = 0;
            $obj->CashbackD = 0;
            $obj->NominalPlayD = 0;
            $obj->WinD = 0;


            if ($q->level == 4) {
                $str = "SELECT id, username, level, idatasan FROM tuser WHERE status = 5 AND level = 5 AND idatasan = '$q->id'";
                $dtUserB = $qry->use($str);

                $qNominalPlayD = 0;
                foreach ($dtUserB as $q2) {
                    $tPlacementD = array_filter($dtPlacement, function ($x) use ($q2) {
                        return $x->iduser == $q2->id;
                    });
                    $qPlacementDTotal += array_sum(array_column($tPlacementD, "total"));

                    $tWinD =  array_filter($dtWin, function ($x) use ($q2) {
                        return $x->iduser == $q2->id;
                    });

                    $NewWin = array_sum(array_column($tWinD, "total"));
                    $NewNomPlay = array_sum(array_column($tWinD, "amount"));
                    $qWinD += $NewWin;
                    $qNominalPlayD += $NewNomPlay;
                    $qPlacementDCashback +=  array_sum(array_column($tWinD, "cashback"));
                }

                $obj->TurnOverD = $qPlacementDTotal;
                $obj->CashbackD = $qPlacementDCashback;
                $obj->NominalPlayD = $qNominalPlayD;
                $obj->WinD = $qWinD;
            }


            // if ($q->level == 5) {
            //     $tKomisi =  array_filter($dtKomisi, function ($x) use ($q) {
            //         return $x->iduser == $q->idatasan;
            //     });
            //     $qKomisi = array_sum(array_column($tKomisi, "amount"));
            //     $obj->Komisi = $qKomisi;
            // } elseif ($q->level == 4) {
            //     $tKomisi =  array_filter($dtKomisi, function ($x) use ($q) {
            //         return $x->iduser == $q->id;
            //     });
            //     $qKomisi = array_sum(array_column($tKomisi, "amount"));
            //     $obj->Komisi = $qKomisi;
            // }

            $obj->Total = ($obj->Win + $obj->Cashback) - $obj->TurnOver;
            $obj->TotalD = ($obj->WinD + $obj->CashbackD) - $obj->TurnOverD;
            $obj->TotalComp = ($obj->Total + $obj->TotalD) * -1;
            if (!($obj->TurnOverD + $obj->TotalComp == 0)) {
                $dt[] = $obj;
            }
        }

        if ($xWBawahan) {
            $qPlacementDTotal = 0;
            $qPlacementDCashback = 0;
            $qWinD = 0;

            $obj = new stdClass;
            $obj->idUser = 0;
            $obj->Username = "NO AGENT";
            $obj->Level = 4;
            $obj->idAtasan = 0;
            $obj->TurnOver = 0;
            $obj->Cashback = 0;
            $obj->NominalPlay = 0;
            $obj->Win = 0;
            $obj->Komisi = 0;
            $obj->isNoAgent = true;

            $str = "SELECT id, username, level, idatasan FROM tuser WHERE status = 5 AND level = 5 AND idatasan = 0";
            $dtUserB = $qry->use($str);

            $qNominalPlayD = 0;
            foreach ($dtUserB as $q2) {
                $tPlacementD = array_filter($dtPlacement, function ($x) use ($q2) {
                    return $x->iduser == $q2->id;
                });
                $qPlacementDTotal += array_sum(array_column($tPlacementD, "total"));

                $tWinD =  array_filter($dtWin, function ($x) use ($q2) {
                    return $x->iduser == $q2->id;
                });

                $qWinD += array_sum(array_column($tWinD, "total"));
                $qNominalPlayD +=  array_sum(array_column($tWinD, "amount"));
                $qPlacementDCashback += array_sum(array_column($tWinD, "cashback"));
            }

            $obj->TurnOverD = $qPlacementDTotal;
            $obj->CashbackD = $qPlacementDCashback;
            $obj->NominalPlayD = $qNominalPlayD;
            $obj->WinD = $qWinD;

            $obj->Total = ($obj->Win + $obj->Cashback) - $obj->TurnOver;
            $obj->TotalD = ($obj->WinD + $obj->CashbackD) - $obj->TurnOverD;
            $obj->TotalComp = ($obj->Total + $obj->TotalD) * -1;
            $dt[] = $obj;
        }

        $data['dt'] = $dt;
        $data['fltrTrans'] = (object) array('dtStart' => $dtStart, 'dtEnd' => $dtEnd, 'txtID' => $txtID, 'Expand' => $xWBawahan, 'txtIsNoAgent' => $txtIsNoAgent);
        echo view('vHead');
        echo view('vMenu');
        echo view('vIncomeReport', $data);
        echo view('vFooter');
    }


    public function CommissionReport()
    {
        helper(['fHelp']);
        $qry = new Base_model();
        $xJam = new Datetime($qry->getWaktu());
        $myLevel = session('level');
        $myID = session('idUser');

        $cbPeriode = 'All';
        $txtID = 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cbPeriode = $this->request->getPost('cbPeriode');
            $txtID = $this->request->getPost('txtID');
            session()->setFlashdata('fltrTrans', (object)  array('cbPeriode' => $cbPeriode, 'txtID' => $txtID));
            header("Location: /CTrans/CommissionReport");
            exit();
        }

        if (session()->has('fltrTrans')) {
            $dtFilter = session('fltrTrans');
            $cbPeriode = $dtFilter->cbPeriode;
            $txtID = $dtFilter->txtID;
        }


        $vAgent = false;
        if (($myLevel >= 1 && $myLevel <= 3) && ($txtID == 0 || $txtID == "")) {
            $vAgent = true;
        }

        $str = "SELECT id, username, level, idatasan FROM tuser WHERE status = 5";
        $dtUser = $qry->use($str);

        $whrPeriode = ($cbPeriode == 'All' ? "" : "AND DATE_FORMAT(tanggal, '%y%m') = '$cbPeriode'");
        $str = "SELECT notrans, noref, iduser, jenistrans, SUM(amount) amount FROM ttrans WHERE status = 5 AND jenistrans IN (3, 6, 7) $whrPeriode GROUP BY notrans, noref, jenistrans, iduser";
        $dtTrans = $qry->use($str);

        $str = "SELECT iduser, notrans, SUM(amount) amount, kali, SUM(total * cashback) cashback, SUM(total) total FROM twind WHERE TRUE $whrPeriode GROUP BY notrans";
        $dtWin = $qry->use($str);

        $dt = [];
        $tUser = array_filter($dtUser, function ($x) use ($txtID, $myLevel, $myID) {
            if (!($txtID == 0 || $txtID == "")) {
                return $x->idatasan == $txtID;
            } elseif ($myLevel >= 1 && $myLevel <= 3) {
                return $x->level == 4;
            } elseif ($myLevel == 4) {
                return $x->idatasan == $myID;
            } else {
                return $x->id == $myID;
            }
        });

        if ($vAgent) {
            foreach ($tUser as $qUser) {
                $dtUserB = array_filter($dtUser, function ($x) use ($qUser) {
                    return $x->idatasan == $qUser->id;
                });

                $dtTransB = array_filter($dtTrans, function ($x) use ($dtUserB) {
                    return array_filter($dtUserB, function ($xx) use ($x) {
                        return $xx->id == $x->iduser;
                    });
                });

                $xtKomisi = 0;
                foreach ($dtUserB as $qUserB) {
                    $xtTurnOver = $xtWin = 0;

                    $dtTurnOver = array_filter($dtTransB, function ($x) use ($qUserB) {
                        return $x->jenistrans == 3 && $x->iduser == $qUserB->id;
                    });
                    $xtTurnOver += (array_sum(array_column($dtTurnOver, 'amount')) * -1);

                    $dtWin = array_filter($dtTransB, function ($x) use ($qUserB) {
                        return $x->jenistrans == 6 && $x->iduser == $qUserB->id;
                    });
                    $xtWin += array_sum(array_column($dtWin, 'amount'));

                    $dtKomisi = array_filter($dtTrans, function ($x) use ($dtTransB, $qUserB) {
                        return array_filter($dtTransB, function ($xx) use ($x, $qUserB) {
                            return  $x->jenistrans == 7 && $xx->iduser == $x->noref && $xx->iduser == $qUserB->id;
                        });
                    });
                    $xtKomisi += array_sum(array_column($dtKomisi, 'amount'));
                }

                $obj = new stdClass;
                $obj->idUser = $qUser->id;
                $obj->Username = $qUser->username;
                $obj->Level = $qUser->level;
                $obj->Komisi = $xtKomisi;
                $dt[] = $obj;
            }
        } else {
            foreach ($tUser as $qUser) {
                $dtTransA = array_filter($dtTrans, function ($x) use ($qUser) {
                    return $x->iduser == $qUser->id;
                });
                $dtTurnOver = array_filter($dtTransA, function ($x) use ($qUser) {
                    return $x->jenistrans == 3 && $x->iduser == $qUser->id;
                });
                $TurnOver = array_sum(array_column($dtTurnOver, 'amount'));

                $dtWin = array_filter($dtWin, function ($x) use ($qUser) {
                    return $x->iduser == $qUser->id;
                });
                $Win = array_sum(array_column($dtWin, 'total'));
                $NominalPlay = array_sum(array_column($dtWin, 'amount'));
                $Cashback = array_sum(array_column($dtWin, 'cashback'));

                $dtKomisi = array_filter($dtTrans, function ($x) use ($dtTransA, $qUser) {
                    return array_filter($dtTransA, function ($xx) use ($x, $qUser) {
                        return  $x->jenistrans == 7 && $xx->iduser == $x->noref && $xx->iduser = $qUser->id;
                    });
                });
                $xtKomisi = array_sum(array_column($dtKomisi, 'amount'));

                $obj = new stdClass;
                $obj->idUser = $qUser->id;
                $obj->Username = $qUser->username;
                $obj->Level = $qUser->level;
                $obj->TurnOver = ($TurnOver) * -1;
                $obj->NominalPlay = $NominalPlay;
                $obj->Win = $Win;
                $obj->Cashback = $Cashback;
                $obj->Total = ($obj->Win + $obj->Cashback) - $obj->TurnOver;
                $obj->TotalComp = $obj->Total * -1;
                $obj->Komisi = $xtKomisi;

                $dt[] = $obj;
            }
        }


        $str = "SELECT DATE_FORMAT(tanggal, '%y%m') Periode FROM ttrans WHERE status = 5 AND jenistrans = 7 GROUP BY Periode";
        $data['dtPeriode'] = $qry->use($str);
        $data['vAgent'] = $vAgent;
        $data['dt'] = $dt;
        $data['fltrTrans'] = (object) array('cbPeriode' => $cbPeriode, 'txtID' => $txtID, 'Expand' => $vAgent);
        echo view('vHead');
        echo view('vMenu');
        echo view('vComissionReport', $data);
        echo view('vFooter');
    }

    public function PrintTransaction($xNotrans)
    {
        $qry = new Base_model();
        $str = "SELECT t.notrans, t.noref, t.tanggal, t.iduser, t.keterangan, t.amount, t.cashback, t.total, t.jenistrans, u.username, jt.jenis FROM ttrans t LEFT JOIN tuser u ON t.iduser = u.id LEFT JOIN tjenistrans jt ON t.jenistrans = jt.id WHERE t.notrans = '{$xNotrans}';";
        $dtTrans = $qry->usefirst($str);
        if (!$dtTrans) {
            session()->setFlashdata('alert', "3|Data Transaksi tidak ditemukan!");
            return redirect()->to('/CTrans/PlacementReport');
        }

        $str = "SELECT amount FROM tsaldo WHERE notrans = '{$xNotrans}'";
        $dtSaldo = $qry->use($str);

        $str = "SELECT status FROM tplacement WHERE notrans = '{$xNotrans}'";
        $qStatus = $qry->usefirst($str)->status ?? 1;

        $str = "SELECT notrans FROM ttrans WHERE noref = '{$xNotrans}'";
        $qNotransWin = $qry->usefirst($str)->notrans ?? '';

        $data['idUser'] = $dtTrans->iduser;
        $data['TotalSaldo'] = array_sum(array_column((array) $dtSaldo, 'amount'));
        $data['dtSaldo'] = $dtSaldo;

        $data['qStatus'] = $qStatus;
        $data['qNotransWin'] = $qNotransWin;
        $data['qTrans'] = $dtTrans;
        $html = view('vPrintTransaction', $data);

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'potrait');
        $dompdf->render();
        $dompdf->stream("Transaction_{$xNotrans}.pdf", array("Attachment" => false));
        $pdfContent = $dompdf->output();
        return $this->response->setContentType('application/pdf')->setBody($pdfContent);
    }

    public function DetailPlacement($xNotrans)
    {
        $qry = new Base_model();
        $str = "SELECT t.notrans, t.noref, t.tanggal, t.iduser, t.keterangan, t.amount, t.cashback, t.total, t.jenistrans, u.username, jt.jenis FROM ttrans t LEFT JOIN tuser u ON t.iduser = u.id LEFT JOIN tjenistrans jt ON t.jenistrans = jt.id WHERE t.notrans = '{$xNotrans}';";
        $dtTrans = $qry->usefirst($str);
        if (!$dtTrans) {
            session()->setFlashdata('alert', "3|Data Transaksi tidak ditemukan!");
            return redirect()->to('/CTrans/Transaction');
        }

        $IDShift = 0;
        $Periode = 0;
        $WinNum = 0;
        $str = "SELECT idshift, idsesi FROM tplacement WHERE notrans = '{$xNotrans}'";
        $qPlacement = $qry->usefirst($str);
        if ($qPlacement) {
            $IDShift = $qPlacement->idshift;
            $IDSesi = $qPlacement->idsesi;
            $str = "SELECT count(1) periode FROM tsesi WHERE id <= $IDSesi AND idshift = $IDShift";
            $Periode =  $qry->usefirst($str)->periode ?? 0;

            $str = "SELECT number FROM twin WHERE status = 5 AND idshift = $IDShift AND sesi = $IDSesi";
            $WinNum =   $qry->usefirst($str)->number ?? 0;
        }

        $str = "SELECT status FROM tplacement WHERE notrans = '$xNotrans'";
        $data['qStatus'] = $qry->usefirst($str)->status ?? 1;

        $str = "SELECT notrans FROM ttrans WHERE noref = '{$xNotrans}' AND jenistrans = 6";
        $data['qNotransWin'] = $qry->usefirst($str)->notrans ?? '';

        $str = "SELECT amount FROM tsaldo WHERE notrans = '{$xNotrans}'";
        $dtSaldo = $qry->use($str);

        $str = "SELECT pd.alias, sum(pd.amount) amount FROM tplacement p INNER JOIN tplacementd pd ON p.id = pd.idplacement  WHERE p.notrans = '$xNotrans' GROUP BY pd.alias";
        $dtNum = $qry->use($str);

        $str = "SELECT w.number, wd.total, wd.cashback FROM twin w INNER JOIN twind wd ON w.id = wd.idwin  WHERE wd.notrans = '$xNotrans'";
        $dtWinNum = $qry->usefirst($str);

        $dtNumber = [];
        foreach ($dtNum as $q) {
            $obj = new stdClass;
            $obj->Number = $q->alias;
            $obj->Nominal = $q->amount ?? 0;
            $obj->Win = 0;
            $obj->Cashback = 0;
            $xNum = ($dtWinNum->number ?? 0);
            if ($xNum == $q->alias) {
                $obj->Win = $dtWinNum->total ?? 0;
                $obj->Cashback = $dtWinNum->total * $dtWinNum->cashback ?? 0;
            }

            if ((($xNum >= 1 && $xNum <= 12) && $q->alias == "Kecil") || (($xNum >= 13 && $xNum <= 24) && $q->alias == "Besar") || (($xNum % 2 == 0) && $q->alias == "Genap") || (($xNum % 2 != 0) && $q->alias == "Ganjil")) {
                $obj->Win = $dtWinNum->total ?? 0;
                $obj->Cashback = ($dtWinNum->total ?? 0) * ($dtWinNum->cashback ?? 0);
            }

            $obj->Total = ($obj->Win + $obj->Cashback) - $obj->Nominal;
            $dtNumber[] = $obj;
        }

        $data['TotalSaldo'] = array_sum(array_column((array) $dtSaldo, 'amount'));
        $data['IDShift'] = $IDShift;
        $data['Periode'] = $Periode;
        $data['WinNum'] = $WinNum;
        $data['qTrns'] = $dtTrans;
        $data['dtNumber'] = $dtNumber;

        echo view('vHead');
        echo view('vMenu');
        echo view('vDetailPlacement', $data);
        echo view('vFooter');
    }

    public function SaveMultiPaymentTopUp()
    {
        if (!session()->has('AllowSave')) {
            session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba lagi!");
            return redirect()->to('/CTrans/NewTransaction');
        }
        session()->remove('AllowSave');

        if (!(session('level') >= 1 && session('level') <= 3)) {
            session()->setflashdata('alert', '3|Unauthorized User, ADMIN only!');
            return redirect()->to('/CData/User');
        }

        $qry = new Base_model;
        $xJam = new DateTime($qry->getWaktu());
        $myID = session('idUser');
        $IDAccount = session('cache')['idUser'] ?? 0;
        $IDJenisTrans = session('cache')['JenisTrans'] ?? 1;
        $Keterangan = $this->request->getPost("txtDesc");
        $xKodeReq = $this->request->getPost("txtKodeReq");
        $totalTopupAmount = $this->request->getPost("txtTotalTopupAmount");

        // Get payment arrays
        $paymentMethods = $this->request->getPost("payment_method");
        $paymentAmounts = $this->request->getPost("payment_amount");

        if (empty($paymentMethods) || empty($paymentAmounts)) {
            session()->setFlashdata('alert', "3|Payment details are required!");
            return redirect()->to('/CTrans/NewTransaction');
        }

        // Calculate total paid amount (new payments only)
        $newPaymentAmount = array_sum($paymentAmounts);

        // Get existing paid amount from database
        $str = "SELECT COALESCE(SUM(t.amount), 0) as existing_paid
                FROM ttrans t 
                WHERE t.noref = '{$xKodeReq}' AND t.jenistrans = 1 AND t.status = 5";
        $existingPaidResult = $qry->usefirst($str);
        $existingPaidAmount = $existingPaidResult->existing_paid ?? 0;

        // Total paid amount = existing + new payments
        $totalPaidAmount = $existingPaidAmount + $newPaymentAmount;

        $qry->db->transStart();

        // Create individual transactions for each payment
        $allTransSuccess = true;
        foreach ($paymentMethods as $index => $paymentMethod) {
            $amount = $paymentAmounts[$index];
            if ($amount <= 0) continue;

            $dtJenisTrans = $qry->usefirst("SELECT kodedepan, ismin FROM tjenistrans WHERE id = {$IDJenisTrans}");
            $KodeDepan = $dtJenisTrans->kodedepan ?? '';
            $xIsMin = (bool) $dtJenisTrans->ismin ?? false;
            $finalAmount = $amount * ($xIsMin ? -1 : 1);

            $Notrans = func::getKey("ttrans", "notrans", $KodeDepan, $xJam, true, true);
            $paymentMethodName = $this->getPaymentMethodName($paymentMethod);

            $dtTrans = array(
                'notrans' => $Notrans,
                'noref' => $xKodeReq,
                'jenistrans' => $IDJenisTrans,
                'iduser' => $IDAccount,
                'tanggal' => $xJam->format('Y-m-d H:i:s'),
                'tanggalperiode' => $xJam->format('Y-m-d H:i:s'),
                'keterangan' => $Keterangan . " - " . $paymentMethodName,
                'amount' => $finalAmount,
                'total' => $amount,
                'status' => 5,
                'inputby' => $myID,
                'inputdate' => $xJam->format('Y-m-d H:i:s')
            );

            $rslt = $qry->ins("ttrans", $dtTrans);
            if (!$rslt) {
                $allTransSuccess = false;
                break;
            }

            $rslt = $qry->ins("tsaldo",  array(
                'notrans' => $Notrans,
                'tanggal' => $xJam->format('Y-m-d H:i:s'),
                'iduser' => $IDAccount,
                'amount' => $finalAmount,
                'inputby' => $myID,
                'inputdate' =>  $xJam->format('Y-m-d H:i:s')
            ));
            if (!$rslt) {
                $allTransSuccess = false;
                break;
            }
        }

        if (!$allTransSuccess) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba lagi!");
            return redirect()->to('/CTrans/NewTransaction');
        }

        // Update request status based on payment completion
        if ($xKodeReq != "") {
            $newStatus = ($totalPaidAmount >= $totalTopupAmount) ? 5 : 4;

            if ($IDJenisTrans == 1) {
                $rslt = $qry->upd("treqtopup", array('status' => $newStatus, 'updateby' => $myID, 'updatedate' => $xJam->format('Y-m-d H:i:s')), array('kodereq' => $xKodeReq));
            } elseif ($IDJenisTrans == 4) {
                $rslt = $qry->upd("treqwithdraw", array('status' => $newStatus, 'updateby' => $myID, 'updatedate' => $xJam->format('Y-m-d H:i:s')), array('kodereq' => $xKodeReq));
            }

            if (!$rslt) {
                $qry->db->transRollback();
                session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba lagi!");
                return redirect()->to('/CTrans/NewTransaction');
            }
        }

        $qry->db->transComplete();
        if ($qry->db->transStatus() === FALSE) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba lagi!");
            return redirect()->to('/CTrans/NewTransaction');
        }

        $statusMessage = ($totalPaidAmount >= $totalTopupAmount) ? "Completed" : "Partial Payment";
        session()->setFlashdata('alert', "5|Berhasil Menambahkan Multi-Payment Topup - {$statusMessage}!");
        return redirect()->to('/CTrans/TransactionSuccess');
    }

    private function getPaymentMethodName($methodId)
    {
        switch ($methodId) {
            case 1:
                return "Cash";
            case 2:
                return "Bank Transfer";
            case 3:
                return "E-Wallet";
            default:
                return "Unknown";
        }
    }
}
