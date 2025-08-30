<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Base_model;
use App\Libraries\func;
use DateTime;
use DateInterval;

class CNumber extends Controller
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
        return redirect()->to('/CNumber/Number');
    }

    public function Number()
    {
        $qry = new Base_model();
        $myID = session('idUser');
        $myLevel = session('level');

        $str = "SELECT id FROM tshift WHERE NOT isclose";
        $idShift = $qry->usefirst($str)->id ?? 0;

        if ($idShift == 0) {
            session()->setFlashdata('alert', '3|Tidak ada Shift!');
            if (!($myLevel >= 1 && $myLevel <= 3)) {
                // return redirect()->to('../CBase');
            }
            // return redirect()->to('../CNumber/SetNumber');

            $str = "SELECT id FROM tshift WHERE isclose ORDER BY id DESC LIMIT 1";
            $idShift = $qry->usefirst($str)->id ?? 0;
        }

        $countWin = $qry->usefirst("SELECT count(1) count FROM twin WHERE status = 5 AND idshift = $idShift")->count ?? 0;
        $floorWin = floor($countWin / 50);
        $offsetWin = ($floorWin == 0 ? 0 : ($floorWin - 1) * 50);
        $offsetWin2 = ($floorWin * 50);



        $dtAllWin = $qry->use("SELECT number FROM twin WHERE status = 5 AND idshift = $idShift LIMIT 50 OFFSET {$offsetWin}");
        if ($floorWin > 0) {
            $dtAllWin2 = $qry->use("SELECT number FROM twin WHERE status = 5 AND idshift = $idShift LIMIT 50 OFFSET {$offsetWin2}");
        }
        $dtWin = array_fill(1, 50, ['Val' => 0, 'isLast' => false]);

        $i = 50;
        foreach ($dtAllWin as $q) {
            if ($i <= 0) break;
            $dtWin[$i--]['Val'] = func::NumNull($q->number);
            $dtWin[$i]['isLast'] = false;
        }

        if ($floorWin > 0) {
            $i = 50;
            foreach ($dtAllWin2 as $q) {
                if ($i <= 0) break;
                $dtWin[$i--]['Val'] = func::NumNull($q->number);
            }
            $dtWin[++$i]['isLast'] = true;
            $i--;

            if ($i >= 1) {
                $xSisa = $i % 10;
                if ($xSisa > 0) {
                    for ($j = $xSisa; $j != 0; $j--) {
                        if ($i <= 0) break;
                        $dtWin[$i--]['Val'] = 0;
                        $dtWin[$i]['isLast'] = false;
                    }
                }
            }
        } else {
            $dtWin[++$i]['isLast'] = true;
        }


        $str = "SELECT idshift, jamselesai, number FROM tsesi WHERE status = 5 AND idshift = $idShift";
        $data['dtNum'] = $qry->use($str);


        $data['histWin'] = $dtWin;
        echo view('vHead');
        echo view('vMenu');
        echo view('vNumber', $data);
        echo view('vFooter');
    }





    public function StartShift()
    {
        $myID = session('idUser');
        $myLevel = session('level');

        if (!($myLevel >= 1 && $myLevel <= 3)) {
            session()->setFlashdata('alert', '3|Tidak Memiliki Akses!');
            return redirect()->to('../CNumber/Number');
        }

        $qry = new Base_model();
        $xJam = new DateTime($qry->getWaktu());

        $str = "SELECT 1 FROM tshift WHERE NOT isclose";
        if ($qry->usefirst($str)) {
            session()->setFlashdata('alert', '3|Sudah Mulai Shift!');
            return redirect()->to('../CNumber/SetNumber');
        }

        $qry->db->transBegin();
        $idShift = (int) $qry->insID('tshift', array('inputby' => $myID, 'inputdate' => $xJam->format('Y-m-d H:i:s')));
        if ($idShift === 0) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', '3|Terjadi Kesalahan Start Shift!');
            return redirect()->to('../CNumber/SetNumber');
        }

        $Timer = session('Comp')->PlacementTime ?? 3;
        $jamSelesai = $xJam->add(new DateInterval("PT{$Timer}S"));
        $dtSesi = array(
            'idShift' => $idShift,
            'jamselesai' => $jamSelesai->format('Y-m-d H:i:s'),
            'status' => 1,
            'inputby' => $myID,
            'inputdate' => $xJam->format('Y-m-d H:i:s')
        );
        if (!$qry->ins('tsesi', $dtSesi)) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', '3|Terjadi Kesalahan Start Shift!');
            return redirect()->to('../CNumber/SetNumber');
        }

        $qry->db->transComplete();
        if ($qry->db->transStatus() === FALSE) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', '3|Terjadi Kesalahan Start Shift!');
            return redirect()->to('../CNumber/SetNumber');
        }

        session()->setFlashdata('alert', '5|Berhasil Open Shift!');
        return redirect()->to('../CNumber/SetNumber');
    }

    public function CloseShift()
    {
        $myID = session('idUser');
        $myLevel = session('level');

        if (!($myLevel >= 1 && $myLevel <= 3)) {
            session()->setFlashdata('alert', '3|Tidak Memiliki Akses!');
            return redirect()->to('../CNumber/Number');
        }

        $qry = new Base_model();
        $xJam = new DateTime($qry->getWaktu());

        $str = "SELECT 1 FROM tshift WHERE NOT isclose";
        if (!$qry->usefirst($str)) {
            session()->setFlashdata('alert', '3|Tidak ada Shift Aktif!');
            return redirect()->to('../CNumber/SetNumber');
        }

        if (!$qry->upd('tshift', array('isclose' => 1, 'updateby' => $myID, 'updatedate' => $xJam->format('Y-m-d H:i:s')), array('isclose' => 0))) {
            session()->setFlashdata('alert', '3|Terjadi Kesalahan Closing Shift!');
            return redirect()->to('../CNumber/SetNumber');
        }

        if (!$qry->del('tsesi', array('number' => 0, 'status' => 1))) {
            session()->setFlashdata('alert', '3|Terjadi Kesalahan Closing Shift!');
            return redirect()->to('../CNumber/SetNumber');
        }

        session()->setFlashdata('alert', '5|Berhasil Closing Shift!');
        return redirect()->to('../CNumber/Number');
    }

    public function SetNumber()
    {
        $qry = new Base_model();
        $myID = session('idUser');
        $myLevel = session('level');

        if (!($myLevel >= 1 && $myLevel <= 3)) {
            session()->setFlashdata('alert', '3|Tidak Memiliki Akses!');
            return redirect()->to('../CNumber/Number');
        }

        $str = "SELECT id FROM tshift WHERE NOT isclose";
        $idShift = $qry->usefirst($str)->id ?? 0;

        if ($idShift == 0) {
            $str = "SELECT id FROM tshift WHERE isclose ORDER BY id DESC LIMIT 1";
            $idShift = $qry->usefirst($str)->id ?? 0;
        }


        $countWin = $qry->usefirst("SELECT count(1) count FROM twin WHERE status = 5 AND idshift = $idShift")->count ?? 0;
        $floorWin = floor($countWin / 50);
        $offsetWin = ($floorWin == 0 ? 0 : ($floorWin - 1) * 50);
        $offsetWin2 = ($floorWin * 50);

        $dtAllWin = $qry->use("SELECT number FROM twin WHERE status = 5 AND idshift = $idShift LIMIT 50 OFFSET {$offsetWin}");
        if ($floorWin > 0) {
            $dtAllWin2 = $qry->use("SELECT number FROM twin WHERE status = 5 AND idshift = $idShift LIMIT 50 OFFSET {$offsetWin2}");
        }
        $dtWin = array_fill(1, 50, ['Val' => 0, 'isLast' => false]);

        $i = 50;
        foreach ($dtAllWin as $q) {
            if ($i <= 0) break;
            $dtWin[$i--]['Val'] = func::NumNull($q->number);
            $dtWin[$i]['isLast'] = false;
        }

        if ($floorWin > 0) {
            $i = 50;
            foreach ($dtAllWin2 as $q) {
                if ($i <= 0) break;
                $dtWin[$i--]['Val'] = func::NumNull($q->number);
            }
            $dtWin[++$i]['isLast'] = true;
            $i--;

            if ($i >= 1) {
                $xSisa = $i % 10;
                if ($xSisa > 0) {
                    for ($j = $xSisa; $j != 0; $j--) {
                        if ($i <= 0) break;
                        $dtWin[$i--]['Val'] = 0;
                        $dtWin[$i]['isLast'] = false;
                    }
                }
            }
        } else {
            $dtWin[++$i]['isLast'] = true;
        }



        $idShift = 0;
        $str = "SELECT id FROM tshift WHERE NOT isclose";
        $dtShift = $qry->usefirst($str);
        if ($dtShift) {
            $idShift = $dtShift->id;
        }

        $str = "SELECT count(1) periode FROM tsesi WHERE idshift = '{$idShift}' AND status <> 8";
        $xPeriode = $qry->usefirst($str)->periode ?? 0;
        $data['histWin'] = $dtWin;
        $data['idShift'] = $idShift;
        $data['SesiCount'] = $xPeriode;

        echo view('vHead');
        echo view('vMenu');
        echo view('vSetNumber', $data);
        if (!$dtShift) {
            echo view('vStartShiftAlert');
        }
        echo view('vFooter');
    }




    public function UpdateNum()
    {
        $myID = session('idUser');
        $myLevel = session('level');

        if (!($myLevel >= 1 && $myLevel <= 3)) {
            session()->setFlashdata('alert', '3|Tidak Memiliki Akses!');
            return redirect()->to('../CNumber/Number');
        }

        if (!(session('xAllowSave') === true || session('xAllowSave') === 1)) {
            session()->setFlashdata('alert', '3|Mohon ReUpdate!');
            return redirect()->to('../CNumber/SetNumber');
        }
        session()->remove('xAllowSave');


        $qry = new Base_model();
        $xJam = new DateTime($qry->getWaktu());
        $Num = (int) $this->request->getPost("txtUpdNum");
        $xLastSesi = $this->request->getPost("chkIsLast");

        $str = "SELECT kodedepan FROM tjenistrans WHERE jenis = 'PNP Prize'";
        $KodeDepan = ($qry->usefirst($str)->kodedepan ?? "");
        $Notrans = func::getKey('ttrans', 'notrans', $KodeDepan,  $xJam, true, true);

        $str = "SELECT id FROM tshift WHERE NOT isclose";
        $idShift = func::NumNull($qry->usefirst($str)->id);
        if ($idShift == 0) {
            session()->setFlashdata('alert', '3|Tidak ada Shift, pastikan sudah mulai Shift!');
            return redirect()->to('../CNumber/SetNumber');
        }

        $str = "SELECT id, jamselesai FROM tsesi WHERE status = 1";
        $qSesi = $qry->usefirst($str);
        if (!$qSesi) {
            session()->setFlashdata('alert', '3|Tidak ada Sesi, pastikan sudah mulai Sesi!');
            return redirect()->to('../CNumber/Number');
        }

        $idSesi = $qSesi->id;
        $JamSelesai = new DateTime($qSesi->jamselesai);

        if ($JamSelesai > $xJam) {
            session()->setFlashdata('alert', '3|Sesi tidak dapat ditutup, Harap menunggu!');
            return redirect()->to('../CNumber/SetNumber');
        }

        if ($Num < 1 || $Num > 24) {
            session()->setFlashdata('alert', '3|Harap isi Angka yang Valid (1-24) !');
            return redirect()->to('../CNumber/SetNumber');
        }

        $qry->db->transStart();
        if (!$qry->upd('tsesi', array('number' => $Num, 'jamupdate' => $xJam->format('Y-m-d H:i:s'), 'status' => 5, 'updateby' => $myID, 'updatedate' => $xJam->format('Y-m-d H:i:s')), array('id' => $idSesi))) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahkan dicoba lagi !');
            return redirect()->to('../CNumber/SetNumber');
        }

        $idWin = $qry->insID('twin', array('idshift' => $idShift, 'sesi' => $idSesi, 'number' => $Num, 'status' => 5, 'inputby' => session('idUser'), 'inputdate' => $xJam->format('Y-m-d H:i:s')));
        if ($idWin == 0) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahkan coba lagi!');
            return redirect()->to('../CNumber/SetNumber');
        }


        $str = "SELECT inputdate FROM tshift WHERE id = {$idShift};";
        $tglPeriode = new DateTime($qry->usefirst($str)->inputdate ?? $xJam);

        $str = "SELECT chkwizard, idwizard FROM tcomp";
        $dtComp = $qry->usefirst($str);
        if ($dtComp->chkwizard == 1) {
            if ($dtComp->idwizard <> 0) {
                $str = "SELECT d.id FROM tplacement h LEFT JOIN tplacementd d ON h.id = d.idplacement WHERE NOT d.isbanding AND h.iduser = '$dtComp->idwizard' AND h.status = 1";
                $dtPlcWzrd = $qry->use(query: $str);
                $cWizard = count($dtPlcWzrd);
                if ($cWizard > 0) {
                    $str = "SELECT 1 FROM tplacement h LEFT JOIN tplacementd d ON h.id = d.idplacement WHERE NOT d.isbanding AND h.iduser = '$dtComp->idwizard' AND h.status = 1 AND d.number = $Num";
                    $qSameNum = $qry->use($str);
                    if (!$qSameNum) {
                        $xIndexWizard = mt_rand(0, $cWizard - 1);
                        $idPlacementWizard = $dtPlcWzrd[$xIndexWizard]->id ?? 0;
                        if (!($qry->upd('tplacementd', array('number' => $Num, 'alias' => $Num), array('id' => $idPlacementWizard)))) {
                            $qry->db->transRollback();
                            session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahkan coba lagi!');
                            return redirect()->to('../CNumber/SetNumber');
                        }
                    }
                }
            }
        }


        $str = "SELECT p.notrans, p.iduser, p.inputdate tanggal, p.total, pd.amount, pd.cashback 
                FROM tplacement p 
                INNER JOIN tplacementd pd ON p.id = pd.idplacement 
                WHERE NOT pd.isbanding AND p.status = 1 AND pd.number = {$Num}";
        $dtPlcmnt = $qry->use($str);

        if ($dtPlcmnt) {
            $dtWinD = array();
            $dtSaldo = array();
            foreach ($dtPlcmnt as $q) {
                $dtWinD[] = array(
                    'idwin' => $idWin,
                    'iduser' => $q->iduser,
                    'notrans' => $q->notrans,
                    'tanggal' => $q->tanggal,
                    'amount' => $q->amount,
                    'kali' => 22,
                    'cashback' => $q->cashback,
                    'total' => $q->amount * 22,
                );

                $dtSaldo[] = array(
                    'notrans' => $Notrans,
                    'tanggal' => $xJam->format('Y-m-d H:i:s'),
                    'iduser' => $q->iduser,
                    'amount' => $q->amount * 22,
                    'inputby' => $myID,
                    'inputdate' => $xJam->format('Y-m-d H:i:s')
                );

                $dtSaldoCashBack[] = array(
                    'notrans' => $Notrans,
                    'tanggal' => $xJam->format('Y-m-d H:i:s'),
                    'iduser' => $q->iduser,
                    'amount' => $q->amount * 22 * $q->cashback,
                    'inputby' => $myID,
                    'inputdate' => $xJam->format('Y-m-d H:i:s')
                );

                $dtTrans = array(
                    'notrans' => $Notrans,
                    'noref' => $q->notrans,
                    'jenistrans' => 6,
                    'iduser' => $q->iduser,
                    'tanggal' => $xJam->format('Y-m-d H:i:s'),
                    'tanggalperiode' => $tglPeriode->format('Y-m-d H:i:s'),
                    'keterangan' => "WIN Number {$Num}",
                    'amount' => $q->amount * 22,
                    'cashback' => $q->cashback,
                    'total' => ($q->amount * 22) + ($q->amount * 22 * $q->cashback),
                    'status' => 5,
                    'inputby' => $myID,
                    'inputdate' => $xJam->format('Y-m-d H:i:s')
                );

                if (!$qry->ins('ttrans', $dtTrans)) {
                    $qry->db->transRollback();
                    session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahkan coba lagi!');
                    return redirect()->to('../CNumber/SetNumber');
                }
            }

            if (!$qry->insbulk('twind', $dtWinD)) {
                $qry->db->transRollback();
                session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahkan coba lagi!');
                return redirect()->to('../CNumber/SetNumber');
            }
            if (!$qry->insbulk('tsaldo', $dtSaldo)) {
                $qry->db->transRollback();
                session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahkan coba lagi!');
                return redirect()->to('../CNumber/SetNumber');
            }
            if (!$qry->insbulk('tsaldo', $dtSaldoCashBack)) {
                $qry->db->transRollback();
                session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahkan coba lagi!');
                return redirect()->to('../CNumber/SetNumber');
            }
        }

        //BANDING

        $xWhrBanding = [];

        if ($Num >= 1 && $Num <= 12) {
            $xWhrBanding[] = "Kecil";
        } else if ($Num >= 13 && $Num <= 24) {
            $xWhrBanding[] = "Besar";
        }

        if ($Num % 2 == 0) {
            $xWhrBanding[] = "Genap";
        } else {
            $xWhrBanding[] = "Ganjil";
        }

        $strBanding = join("', '", $xWhrBanding);

        $Notrans = func::getKey('ttrans', 'notrans', $KodeDepan,  $xJam, true, true);
        $str = "SELECT p.notrans, p.iduser, p.inputdate tanggal, p.total, pd.amount, pd.cashback, pd.alias
                FROM tplacement p 
                INNER JOIN tplacementd pd ON p.id = pd.idplacement 
                WHERE pd.isbanding AND p.status = 1 AND pd.alias IN ('$strBanding')";
        $dtPlcmnt = $qry->use($str);

        if ($dtPlcmnt) {
            $dtWinD = array();
            $dtSaldo = array();
            foreach ($dtPlcmnt as $q) {
                $dtWinD2[] = array(
                    'idwin' => $idWin,
                    'iduser' => $q->iduser,
                    'notrans' => $q->notrans,
                    'tanggal' => $q->tanggal,
                    'amount' => $q->amount,
                    'kali' => 2,
                    'cashback' => $q->cashback,
                    'total' => $q->amount * 2,
                );

                $dtSaldo2[] = array(
                    'notrans' => $Notrans,
                    'tanggal' => $xJam->format('Y-m-d H:i:s'),
                    'iduser' => $q->iduser,
                    'amount' => $q->amount * 2,
                    'inputby' => $myID,
                    'inputdate' => $xJam->format('Y-m-d H:i:s')
                );

                $dtSaldoCashBack2[] = array(
                    'notrans' => $Notrans,
                    'tanggal' => $xJam->format('Y-m-d H:i:s'),
                    'iduser' => $q->iduser,
                    'amount' => $q->amount * 2 * $q->cashback,
                    'inputby' => $myID,
                    'inputdate' => $xJam->format('Y-m-d H:i:s')
                );

                $dtTrans2[] = array(
                    'notrans' => $Notrans,
                    'noref' => $q->notrans,
                    'jenistrans' => 6,
                    'iduser' => $q->iduser,
                    'tanggal' => $xJam->format('Y-m-d H:i:s'),
                    'tanggalperiode' => $tglPeriode->format('Y-m-d H:i:s'),
                    'keterangan' => "WIN Number $Num - ($q->alias)",
                    'amount' => $q->amount * 2,
                    'cashback' => $q->cashback,
                    'total' => ($q->amount * 2) + ($q->amount * 2 * $q->cashback),
                    'status' => 5,
                    'inputby' => $myID,
                    'inputdate' => $xJam->format('Y-m-d H:i:s')
                );
            }

            if (!$qry->insbulk('twind', $dtWinD2)) {
                $qry->db->transRollback();
                session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahkan coba lagi!');
                return redirect()->to('../CNumber/SetNumber');
            }
            if (!$qry->insbulk('tsaldo', $dtSaldo2)) {
                $qry->db->transRollback();
                session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahkan coba lagi!');
                return redirect()->to('../CNumber/SetNumber');
            }
            if (!$qry->insbulk('tsaldo', $dtSaldoCashBack2)) {
                $qry->db->transRollback();
                session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahkan coba lagi!');
                return redirect()->to('../CNumber/SetNumber');
            }
            if (!$qry->insbulk('ttrans', $dtTrans2)) {
                $qry->db->transRollback();
                session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahkan coba lagi!');
                return redirect()->to('../CNumber/SetNumber');
            }
        }

        $str = "SELECT id, idatasan, komisi FROM tuser;";
        $dtUser = $qry->use($str);

        if ($xJam->format('d') == '01') {
            $xNowPeriod = $xJam->format('ym');
            $str = "SELECT 1 xAny FROM tdatekomisi WHERE DATE_FORMAT(tanggal,'%y%m') = '$xNowPeriod'";
            $xAny = $qry->usefirst($str);
            if (($xAny->xAny ?? 0) == 0) {
                $str = "SELECT tanggal FROM tdatekomisi ORDER BY id DESC LIMIT 1";
                $dtLast = $qry->usefirst($str);
                $dtLastStr = '0001-01-01 00:00:01';
                if ($dtLast) {
                    $tLast = new Datetime($dtLast->tanggal);
                    $dtLastStr = $tLast->format('Y-m-d H:i:s');
                }

                $str = "SELECT p.iduser, p.inputdate tanggal, SUM(p.total) total, SUM(pd.amount) amount, SUM(pd.cashback) cashback
                        FROM tplacement p 
                        INNER JOIN tplacementd pd ON p.id = pd.idplacement 
                        WHERE inputdate > '$dtLastStr'
                        GROUP BY p.iduser";
                $dtLosePlcmnt = $qry->use($str);

                if ($dtLosePlcmnt) {
                    $str = "SELECT iduser, SUM(total) total, SUM(cashback * total) cashback FROM twind WHERE tanggal > '$dtLastStr' GROUP BY iduser";
                    $dtWinKom = $qry->use($str);
                    $str = "SELECT kodedepan FROM tjenistrans WHERE jenis = 'Commission';";
                    $xKodeDepanKomisi = $qry->usefirst($str)->kodedepan ??  'CMS';

                    foreach ($dtLosePlcmnt as $q) {
                        $tUser = array_filter((array) $dtUser, function ($x) use ($q) {
                            return $x->id == $q->iduser;
                        });
                        $qUser = reset($tUser);

                        $tAtasan = array_filter((array)$dtUser, function ($x) use ($qUser) {
                            return $x->id == $qUser->idatasan;
                        });
                        $qAtasan = reset($tAtasan);

                        $tWinKom = array_filter($dtWinKom, function ($x) use ($q) {
                            return $x->iduser == $q->iduser;
                        });
                        $qWinKom = reset($tWinKom);
                        $xWin = ($qWinKom->total ?? 0) + ($qWinKom->cashback ?? 0);


                        $xVal = (($q->amount ?? 0) - $xWin) * (($qAtasan->komisi ?? 0) / 100);
                        $xKomisi = ((($q->amount ?? 0) - $xWin) > 0) ? $xVal : 0;

                        if ($xKomisi > 0) {
                            $NotransKomisi = func::getKey('ttrans', 'notrans', $xKodeDepanKomisi, $xJam, true, true);
                            $dtKomisi = array(
                                'notrans' => $NotransKomisi,
                                'noref' => $q->iduser,
                                'jenistrans' => 7,
                                'iduser' => $qUser->idatasan,
                                'tanggal' => $xJam->format('Y-m-d H:i:s'),
                                'tanggalperiode' => $tglPeriode->format('Y-m-d H:i:s'),
                                'keterangan' => 'Commission User',
                                'amount' => $xKomisi,
                                'total' => $xKomisi,
                                'status' => 5,
                                'inputby' => $myID,
                                'inputdate' => $xJam->format('Y-m-d H:i:s'),
                            );

                            $dtSaldoKomisi = array(
                                'notrans' => $NotransKomisi,
                                'tanggal' => $xJam->format('Y-m-d H:i:s'),
                                'iduser' => $qUser->idatasan ?? 0,
                                'amount' => $xKomisi,
                                'inputby' => $myID,
                                'inputdate' => $xJam->format('Y-m-d H:i:s')
                            );

                            if (!$qry->ins('ttrans', $dtKomisi)) {
                                $qry->db->transRollback();
                                session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahkan dicoba lagi !');
                                return redirect()->to('../CNumber/SetNumber');
                            };

                            if (!$qry->ins('tsaldo', $dtSaldoKomisi)) {
                                $qry->db->transRollback();
                                session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahkan coba lagi!');
                                return redirect()->to('../CNumber/SetNumber');
                            }
                        }
                    }
                }

                if (!$qry->ins('tdatekomisi', array('tanggal' => $xJam->format('Y-m-d H:i:s'), 'inputby' => $myID, 'inputdate' => $xJam->format('Y-m-d H:i:s')))) {
                    $qry->db->transRollback();
                    session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahkan dicoba lagi !');
                    return redirect()->to('../CNumber/SetNumber');
                };
            }
        }



        if (!$qry->upd('tplacement', array('status' => 5, 'updateby' => session('idUser'), 'updatedate' => $xJam->format('Y-m-d H:i:s')), array('status' => 1))) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', '3|Terjadi Kesalahan, Silahkan dicoba lagi !');
            return redirect()->to('../CNumber/SetNumber');
        }

        if ($xLastSesi == 'false') {
            $Timer = session('Comp')->PlacementTime ?? 3;
            $jamSelesai = $xJam->add(new DateInterval("PT{$Timer}S"));
            $dtIns = array(
                'idshift' => $idShift,
                'number' => 0,
                'jamselesai' => $jamSelesai->format('Y-m-d H:i:s'),
                'status' => 1,
                'inputby' => session('idUser'),
                'inputdate' => $xJam->format('Y-m-d H:i:s')
            );
            if (!$qry->ins('tsesi', $dtIns)) {
                $qry->db->transRollback();
                session()->setFlashdata('alert', '3|Tidak ada Shift aktif!');
                return redirect()->to('../CNumber/SetNumber');
            }
        } else {
            $str = "SELECT 1 FROM tshift WHERE NOT isclose";
            if (!$qry->usefirst($str)) {
                $qry->db->transRollback();
                session()->setFlashdata('alert', '3|Tidak ada Shift Aktif!');
                return redirect()->to('../CNumber/SetNumber');
            }

            if (!$qry->upd('tshift', array('isclose' => 1, 'updateby' => $myID, 'updatedate' => $xJam->format('Y-m-d H:i:s')), array('isclose' => 0))) {
                $qry->db->transRollback();
                session()->setFlashdata('alert', '3|Terjadi Kesalahan Closing Shift!');
                return redirect()->to('../CNumber/SetNumber');
            }
        }



        $qry->db->transComplete();
        if ($qry->db->transStatus() === FALSE) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba lagi!");
            return redirect()->to('/CNumber/SetNumber');
        }

        session()->setFlashdata('alert', "5|Success UPDATE NUMBER!");
        fSendSignal('ReportListNumber', 'ReportShift', 'ReportTransaction', 'ReportPlacement', "ReportIncome", 'ReportCommission', 'ReportInvoice');
        return redirect()->to('/CNumber/SetNumber');
    }


    public function UndoLastNumber()
    {
        $myID = session('idUser');
        $myLevel = session('level');

        if (!($myLevel >= 1 && $myLevel <= 3)) {
            return json_encode(['success' => false, 'message' => 'Tidak Memiliki Akses!']);
        }

        $qry = new Base_model();
        $xJam = new DateTime($qry->getWaktu());


        $str = "SELECT id FROM tshift WHERE NOT isclose";
        $idShift = func::NumNull($qry->usefirst($str)->id);
        if ($idShift == 0) {
            return json_encode(['success' => false, 'message' => 'Tidak ada Shift, pastikan sudah mulai Shift!']);
        }

        $str = "SELECT status FROM tsesi WHERE idshift = $idShift AND status != 1 ORDER BY id DESC LIMIT 1";
        $qCekSesi = $qry->usefirst($str)->status ?? 0;
        if ($qCekSesi == 8) {
            return json_encode(['success' => false, 'message' => 'Sudah tidak dapat Undo!']);
        }

        $str = "SELECT id, jamselesai FROM tsesi WHERE status = 5 AND idshift = $idShift ORDER BY id DESC LIMIT 1";
        $qSesi = $qry->usefirst($str);
        if (!$qSesi) {
            return json_encode(['success' => false, 'message' => 'Tidak ada Sesi yang bisa di Undo!']);
        }

        $idSesi = $qSesi->id;

        $idWin = $qry->usefirst("SELECT id FROM twin WHERE sesi = $idSesi AND idshift = $idShift AND status = 5 ORDER BY id DESC LIMIT 1")->id ?? 0;

        $qry->db->transStart();
        if (!$qry->del('twin', array('idshift' => $idShift, 'sesi' => $idSesi))) {
            $qry->db->transRollback();
            return json_encode(['success' => false, 'message' => 'Terjadi Kesalahan pada Hapus Win, Silahkan dicoba lagi !']);
        }
        if (!$qry->del('twind', array('idwin' => $idWin))) {
            $qry->db->transRollback();
            return json_encode(['success' => false, 'message' => 'Terjadi Kesalahan pada Hapus Detail Win, Silahkan dicoba lagi !']);
        }
        if (!$qry->del('tsaldo', array('notrans' => function ($builder) use ($idSesi) {
            $builder->select('notrans')->from('ttrans')->where(['jenistrans' => 6, 'noref' => $idSesi]);
        }))) {
            $qry->db->transRollback();
            return json_encode(['success' => false, 'message' => 'Terjadi Kesalahan pada Hapus Saldo, Silahkan dicoba lagi !']);
        }
        if (!$qry->del('ttrans', array('jenistrans' => 6, 'noref' => $idSesi))) {
            $qry->db->transRollback();
            return json_encode(['success' => false, 'message' => 'Terjadi Kesalahan pada Hapus Trans, Silahkan dicoba lagi !']);
        }
        if (!$qry->del('ttrans', array('jenistrans' => 7, 'noref' => $idSesi))) {
            $qry->db->transRollback();
            return json_encode(['success' => false, 'message' => 'Terjadi Kesalahan pada Hapus Trans, Silahkan dicoba lagi !']);
        }
        if (!$qry->upd('tsesi', array('number' => 0, 'jamupdate' => null, 'status' => 8, 'updateby' => $myID, 'updatedate' => $xJam->format('Y-m-d H:i:s')), array('id' => $idSesi))) {
            $qry->db->transRollback();
            return json_encode(['success' => false, 'message' => 'Terjadi Kesalahan pada Hapus Sesi, Silahkan dicoba lagi !']);
        }
        $qry->db->transComplete();
        if ($qry->db->transStatus() === FALSE) {
            $qry->db->transRollback();
            return json_encode(['success' => false, 'message' => 'Terjadi Kesalahan, Silahkan Coba lagi!']);
        }
        session()->setFlashdata('alert', "5|Success UNDO LAST NUMBER!");
        return json_encode(['success' => true]);
    }
}
