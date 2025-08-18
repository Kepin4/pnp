<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Base_model;
use App\Libraries\func;
use Datetime;

class CPlay extends Controller
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
        if (session('level') >= 1 && session('level') <= 3) {
            return redirect()->to("../CBase/Dashboard");
        } else {
            return redirect()->to("../CPlay/Play");
        }
    }

    public function Play()
    {
        if (session('level') >= 1 && session('level') <= 3) {
            return redirect()->to("../CBase/Dashboard");
        }




        $qry = new Base_model();
        $id = session('idUser');

        $str = "SELECT id FROM tshift WHERe NOT isclose";
        $IDShift = $qry->usefirst($str)->id ?? 0;
        $dtCountWin = $qry->use("SELECT count(1) count FROM twin WHERE status = 5 AND idshift = $IDShift");
        $countWin = reset($dtCountWin)->count;
        $floorWin = floor($countWin / 50);

        $offsetWin = ($floorWin == 0 ? 0 : ($floorWin - 1) * 50);
        $offsetWin2 = ($floorWin * 50);


        $dtAllWin = $qry->use("SELECT number FROM twin WHERE status = 5 AND idshift = $IDShift LIMIT 50 OFFSET {$offsetWin}");
        if ($floorWin > 0) {
            $dtAllWin2 = $qry->use("SELECT number FROM twin WHERE status = 5 AND idshift = $IDShift LIMIT 50 OFFSET {$offsetWin2}");
        }
        $dtAllReq = $qry->use("SELECT amount FROM trecnum LIMIT 5");
        $dtWin = array_fill(1, 50, ['Val' => 0, 'isLast' => false]);
        $dtReq = array_fill(1, 5, 0);

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

        $i = 1;
        foreach ($dtAllReq as $q) {
            $dtReq[$i++] = func::NumNull($q->amount);
        }

        $Started = true;
        $str = "SELECT 1 FROM tshift WHERE NOT isclose";
        if (!$qry->usefirst($str)) {
            $Started = false;
        }

        $data['histWin'] = $dtWin;
        $data['Req'] = $dtReq;

        echo view("vHead");
        echo view("vMenu");
        if (!$Started) {
            echo view('vStillClosedAlert');
        }
        session()->set('xAllowSave', true);
        echo view("vPlay", $data);
        echo view("vFooter");
    }

    public function SavePlay()
    {
        $qry = new Base_model();
        $CTools = new CTools();
        $xJam = new DateTime($qry->getWaktu());
        $myID = session('idUser');
        $myCashback = session('cashback');
        if (!isset($_SESSION['xAllowSave'])) {
            session()->setFlashdata('alert', "3| Terjadi Kesalahan, Mohon Pastikan apakah Placement terinput dengan benar!");
            return redirect()->to('../CPlay/Play');
        }
        session()->remove('xAllowSave');

        $TotalPlay = 0;
        for ($i = 1; $i <= 24; $i++) {
            $xVal[$i] = (object) [];
            $xVal[$i]->Num = $i;
            $xVal[$i]->Nominal = (int) $this->request->getPost("txtVal{$i}");
            $TotalPlay = (float) $xVal[$i]->Nominal;
        }


        $str = "SELECT sum(amount) amount FROM tsaldo WHERE iduser = '{$myID}'";
        $mySaldo =  func::NumNull($qry->usefirst($str)->amount);

        // Validasi
        $str = "SELECT id, idshift, jamselesai FROM tsesi WHERE status = 1";
        $dtJamSelesai = $qry->usefirst($str);

        if (!$dtJamSelesai) {
            session()->setFlashdata('alert', "1|Sesi Belum dibuka, harap bersabar!");
            return redirect()->to('/CPlay/Play');
        }

        $JamSelesai = new DateTime($dtJamSelesai->jamselesai);
        if (!$JamSelesai) {
            session()->setFlashdata('alert', "3|Terjadi Kesalahan pada System!");
            return redirect()->to('/CPlay/Play');
        }

        if ($xJam > $JamSelesai) {
            session()->setFlashdata('alert', "1|Sesi sudah TerClose, Harap Menunggu Sesi Selanjutnya!");
            return redirect()->to('/CPlay/Play');
        }


        $idSesi = $dtJamSelesai->id;
        $idShift = $dtJamSelesai->idshift;

        $str = "SELECT inputdate FROM tshift WHERE id = {$idShift};";
        $tglPeriode = new DateTime($qry->usefirst($str)->inputdate ?? $xJam);

        $dtPlacement = array_filter($xVal, function ($x) {
            return $x->Nominal !== 0;
        });

        $Total = array_sum(array_column($dtPlacement, 'Nominal')); // Yang terRefuse tetap terhitung
        if ($Total <= 0) {
            session()->setFlashdata('alert', "3|Harap isi Nominal!");
            return redirect()->to('/CPlay/Play');
        }

        //Proses Save
        $qry->db->transStart();

        $xNotrans = func::getKey("tplacement", "notrans", "PL", $xJam, true, true);
        $dtHeader = array(
            'notrans' => $xNotrans,
            'idshift' => $idShift,
            'idsesi' => $idSesi,
            'iduser' => $myID,
            'total' => $Total,
            'cashback' => $myCashback,
            'status' => 1,
            'inputby' => $myID,
            'inputdate' => $xJam->format('Y-m-d H:i:s')
        );
        // SAVE PLACEMENT TRANS
        $HeaderID = $qry->insID("tplacement", $dtHeader);

        $Total = 0;
        $dtRefusePlacement = [];
        $saldoTerpakai = $CTools->getSisaMaxSaldo();

        // Get user's placement limit and current total
        $str = "SELECT limitplacement FROM tuser WHERE id = $myID";
        $userLimit = $qry->usefirst($str)->limitplacement ?? 0;
        
        // Get current session's total placement for this user
        $str = "SELECT IFNULL(SUM(total), 0) as currentTotal FROM tplacement WHERE id <> $HeaderID AND idshift = '{$idShift}' AND idsesi = '{$idSesi}' AND iduser = '{$myID}' AND status = 1";
        $currentTotal = $qry->usefirst($str)->currentTotal ?? 0;
        
        $acceptedTotal = 0; // Track accepted placements in this transaction

        foreach ($dtPlacement as $q) {
            $dtDetail = array(
                'idplacement' => $HeaderID,
                'number' => $q->Num,
                'alias' => $q->Num,
                'amount' => $q->Nominal,
                'cashback' => $myCashback,
                'isrefuse' => 0
            );

            if ($mySaldo < $q->Nominal) {
                $xObj = new \stdClass;
                $xObj->keterangan = "Saldo Tidak Mencukupi!";
                $xObj->value = $q;
                $dtRefusePlacement[] = $xObj;
                continue;
            }

            // Check individual placement limit
            if ($userLimit > 0 && ($currentTotal + $acceptedTotal + $q->Nominal) > $userLimit) {
                $xObj = new \stdClass;
                $remaining = $userLimit - ($currentTotal + $acceptedTotal);
                $xObj->keterangan = "Placement melebihi limit! Limit: " . number_format($userLimit, 2, ',', '.') . ", Sisa: " . number_format($remaining, 2, ',', '.') . ", Placement #{$q->Num}: " . number_format($q->Nominal, 2, ',', '.');
                $xObj->value = $q;
                $dtRefusePlacement[] = $xObj;
                continue;
            }

            $saldoTerpakai += $q->Nominal;
            $limitSaldo = (session('Comp')->limitsaldo ?? 0);
            if ($saldoTerpakai > $limitSaldo) {
                $xObj = new \stdClass;
                $xBefore = $saldoTerpakai - $q->Nominal;
                $xObj->keterangan = "Saldo Telah Mencapai Limit!, Sisa Saldo = $xBefore/$limitSaldo";
                $xObj->value = $q;
                $dtRefusePlacement[] = $xObj;
                continue;
            }

            // Accept this placement and update running total (only after all checks pass)
            $acceptedTotal += $q->Nominal;
            $mySaldo -= $q->Nominal;
            $Total += $q->Nominal;
            
            // SAVE PLACEMENT DETAIL TRANS
            $DetailID = $qry->insID("tplacementd", $dtDetail);


            // ADD SALDO USER
            $dtSaldo = array(
                'notrans' => $xNotrans,
                'tanggal' => $xJam->format('Y-m-d H:i:s'),
                'iduser' => $myID,
                'amount' => $q->Nominal * -1,
                'inputby' => $myID,
                'inputdate' => $xJam->format('Y-m-d H:i:s')
            );
            $qry->ins("tsaldo", $dtSaldo);
        }

        if (!$qry->upd('tplacement', array('total' => $Total), array('id' => $HeaderID))) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba lagi!");
            return redirect()->to('/CPlay/Play');
        }

        $TotalAkhir = $Total * -1;
        $qry->ins("ttrans", array(
            'notrans' => $xNotrans,
            'jenistrans' => 3,
            'iduser' => $myID,
            'tanggal' => $xJam->format('Y-m-d H:i:s'),
            'tanggalperiode' => $tglPeriode->format('Y-m-d H:i:s'),
            'keterangan' => 'Placement',
            'amount' => $Total * -1,
            'cashback' => 0,
            'total' => $TotalAkhir,
            'status' => 5,
            'inputby' => $myID,
            'inputdate' => $xJam->format('Y-m-d H:i:s')
        ));

        $qry->db->transComplete();
        if ($qry->db->transStatus() === FALSE) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba lagi!");
            return redirect()->to('/CPlay/Play');
        }

        if ($dtRefusePlacement) {
            session()->setFlashdata('refused', $dtRefusePlacement);
            return redirect()->to('CPlay/RefusedSavePlay');
        }

        return redirect()->to('CPlay/SuccessSavePlay');
    }

    public function SavePlayBanding()
    {
        $qry = new Base_model();
        $xJam = new DateTime($qry->getWaktu());
        $myID = session('idUser');
        $myCashback = session('cashback');

        if (!session('xAllowSave')) {
            return redirect()->to('../CPlay/Play');
        }
        session()->remove('xAllowSave');


        $xVal = [];
        foreach (["BESAR", "KECIL", "GENAP", "GANJIL"] as $q) {
            $xVal[$q] = new \stdClass();
            $xVal[$q]->Num =  ucfirst(strtolower($q));
            $xVal[$q]->Nominal = (int) $this->request->getPost("txtVal$q");
        }

        $str = "SELECT sum(amount) amount FROM tsaldo WHERE iduser = '{$myID}'";
        $mySaldo =  func::NumNull($qry->usefirst($str)->amount);

        // Validasi
        $str = "SELECT id, idshift, jamselesai FROM tsesi WHERE status = 1";
        $dtJamSelesai = $qry->usefirst($str);

        if (!$dtJamSelesai) {
            session()->setFlashdata('alert', "1|Sesi Belum dibuka, harap bersabar!");
            return redirect()->to('/CPlay/Play');
        }

        $JamSelesai = new DateTime($dtJamSelesai->jamselesai);
        if (!$JamSelesai) {
            session()->setFlashdata('alert', "3|Terjadi Kesalahan pada System!");
            return redirect()->to('/CPlay/Play');
        }

        if ($xJam > $JamSelesai) {
            session()->setFlashdata('alert', "1|Sesi sudah TerClose, Harap Menunggu Sesi Selanjutnya!");
            return redirect()->to('/CPlay/Play');
        }


        $idSesi = $dtJamSelesai->id;
        $idShift = $dtJamSelesai->idshift;

        $dtPlacement = array_filter((array) $xVal, function ($x) {
            return $x->Nominal !== 0;
        });

        $Total = array_sum(array_column($dtPlacement, 'Nominal'));
        if ($Total <= 0) {
            session()->setFlashdata('alert', "3|Harap isi Nominal!");
            return redirect()->to('/CPlay/Play');
        }

        //Proses Save
        $qry->db->transStart();

        $xNotrans = func::getKey("tplacement", "notrans", "PL", $xJam, true, true);
        $dtHeader = array(
            'notrans' => $xNotrans,
            'idshift' => $idShift,
            'idsesi' => $idSesi,
            'iduser' => $myID,
            'total' => $Total,
            'cashback' => $myCashback,
            'status' => 1,
            'inputby' => $myID,
            'inputdate' => $xJam->format('Y-m-d H:i:s')
        );

        // SAVE PLACEMENT TRANS
        $HeaderID = $qry->insID("tplacement", $dtHeader);
        $Total = 0;
        $dtRefusePlacement = [];
        foreach ($dtPlacement as $q) {
            $dtDetail = array(
                'idplacement' => $HeaderID,
                'number' => 0,
                'alias' =>  $q->Num,
                'amount' => $q->Nominal,
                'cashback' => $myCashback,
                'isbanding' => 1,
                'isrefuse' => 0
            );

            if ($mySaldo < $q->Nominal) {
                $dtRefusePlacement[] = $q;
                continue;
            }

            $mySaldo -= $q->Nominal;
            $Total += $q->Nominal;
            // SAVE PLACEMENT DETAIL TRANS
            $DetailID = $qry->insID("tplacementd", $dtDetail);


            // ADD SALDO USER
            $dtSaldo = array(
                'notrans' => $xNotrans,
                'tanggal' => $xJam->format('Y-m-d H:i:s'),
                'iduser' => $myID,
                'amount' => $q->Nominal * -1,
                'inputby' => $myID,
                'inputdate' => $xJam->format('Y-m-d H:i:s')
            );
            $qry->ins("tsaldo", $dtSaldo);
        }

        if (!$qry->upd('tplacement', array('total' => $Total), array('id' => $HeaderID))) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba lagi!");
            return redirect()->to('/CPlay/Play');
        }

        $TotalAkhir = $Total * -1;
        $qry->ins("ttrans", array(
            'notrans' => $xNotrans,
            'jenistrans' => 3,
            'iduser' => $myID,
            'tanggal' => $xJam->format('Y-m-d H:i:s'),
            'keterangan' => 'Placement',
            'amount' => $Total * -1,
            'cashback' => 0,
            'total' => $TotalAkhir,
            'status' => 5,
            'inputby' => $myID,
            'inputdate' => $xJam->format('Y-m-d H:i:s')
        ));

        $qry->db->transComplete();
        if ($qry->db->transStatus() === FALSE) {
            $qry->db->transRollback();
            session()->setFlashdata('alert', "3|Terjadi Kesalahan, Silahkan Coba lagi!");
            return redirect()->to('/CPlay/Play');
        }

        if ($dtRefusePlacement) {
            session()->setFlashdata('refused', $dtRefusePlacement);
            return redirect()->to('CPlay/RefusedSavePlay');
        }

        return redirect()->to('CPlay/SuccessSavePlay');
    }

    public function SavePlayGroup()
    {
        $xVal = [];
        for ($i = 1; $i <= 24; $i++) {
            $xVal[$i] = (object) [];
            $xVal[$i]->Num = $i;
            $xVal[$i]->Nominal = 0;
        }

        foreach (['Genap', 'Ganjil', 'Besar', 'Kecil'] as $q) {
            $xNom = (float) $this->request->getPost("txtVal{$q}");
            if ($xNom == 0) {
                continue;
            }

            if ($q === 'Genap') {
                for ($i = 1; $i <= 24; $i++) {
                    if ($i % 2 == 0) {
                        $xVal[$i]->Num = $i;
                        $xVal[$i]->Nominal += $xNom;
                    }
                }
            } elseif ($q === 'Ganjil') {
                for ($i = 1; $i <= 24; $i++) {
                    if ($i % 2 != 0) {
                        $xVal[$i]->Num = $i;
                        $xVal[$i]->Nominal += $xNom;
                    }
                }
            } elseif ($q === 'Besar') {
                for ($i = 13; $i <= 24; $i++) {
                    $xVal[$i]->Num = $i;
                    $xVal[$i]->Nominal += $xNom;
                }
            } elseif ($q === 'Kecil') {
                for ($i = 1; $i <= 12; $i++) {
                    $xVal[$i]->Num = $i;
                    $xVal[$i]->Nominal += $xNom;
                }
            }
        }

        $postData = [];
        foreach ($xVal as $val) {
            $postData["txtVal{$val->Num}"] = $val->Nominal;
        }

        $this->request->setGlobal('post', $postData);
        return $this->SavePlay();
    }



    public function SuccessSavePlay()
    {
        session()->setFlashdata('alert', "5|Transaksi Berhasil disimpan!");
        return redirect()->to('/CPlay/Play');
    }
    public function RefusedSavePlay()
    {
        session()->setFlashdata('refused', session()->getFlashdata('refused'));
        return redirect()->to('/CPlay/Play');
    }

    public function UpdSesi()
    {
        $xAdmin = session('Admin');
        if (!isset($xAdmin)) {
            session()->setFlashdata('alert', "3|No Access!");
            return redirect()->to('/CPlay/Play');
        };
    }

    public function getTimer()
    {
        $qry = new Base_model();
        $xJam = new DateTime($qry->getWaktu());

        $str = "SELECT jamselesai FROM tsesi WHERE status = 1";
        $dtJam = $qry->usefirst($str);

        if (!$dtJam) {
            return json_encode(["status" => "NO SESSION"]);
        }

        $JamSelesai = new DateTime($dtJam->jamselesai);
        $dt = [
            "JamSelesai" => $JamSelesai->format('Y-m-d H:i:s'),
            "JamSekarang" => $xJam->format('Y-m-d H:i:s')
        ];

        return json_encode($dt);
    }

    public function getSaldo($id = 0): float
    {
        $qry = new Base_model();
        $myLevel = session('level');
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            $id = session('idUser');
        }
        return func::NumNULL($qry->usefirst("SELECT SUM(amount) saldo FROM tsaldo WHERE iduser = {$id}")->saldo);
    }
}
