<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Base_model;
use App\Libraries\func;
use App\Controllers\CTools;
use Datetime;
use stdClass;

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CExport extends Controller
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
        return redirect()->to('../CBase');
    }


    public function PlacementReport($Type = "")
    {
        if ($Type == "") {
            return redirect()->back();
        }
        $qry = new Base_model();
        $myLevel = session('level');
        $myID = session('idUser');

        $dt = $this->request->getPost();
        if (!$dt) {
            session()->setFlashdata('alert', '3|Please ReSubmit Request!');
            return redirect()->to('../CTrans/PlacementReport');
        }

        $xJam = new Datetime($qry->getWaktu());
        $dtStart = $xJam->format("Y-m-d");
        $dtEnd = $xJam->format("Y-m-d");

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
        $str = "SELECT notrans, tanggal, iduser, amount FROM ttrans WHERE jenistrans = 3 {$whr} AND date(tanggal) BETWEEN '{$dtStart}' AND '{$dtEnd}' AND status = 5 ORDER BY id DESC";
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

        if (!$dtData) {
            session()->setFlashdata('alert', '3|No Data!');
            return redirect()->back();
        }

        session()->setFlashdata('cache', (object) array('dtStart' => $dtStart, 'dtEnd' => $dtEnd));

        if ($Type == "PDF") {
            $data['dt'] = $dtData;
            $dompdf = new Dompdf();
            $dompdf->loadHtml(view('vPDF_PlacementReport', $data));
            $dompdf->setPaper('A4', 'potrait');
            $dompdf->render();
            $dompdf->stream("Placement_Report.pdf", array("Attachment" => false));
            $pdfContent = $dompdf->output();
            return $this->response->setContentType('application/pdf')->setBody($pdfContent);
        } elseif ($Type == "Excel") {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->mergeCells('A1:J1');
            $sheet->setCellValue('A1', 'Placement Report List');
            $sheet->getStyle('A1')->getFont()->setBold(true);

            if ($myLevel >= 1 && $myLevel <= 3) {
                $sheet->mergeCells('A2:J2');
            } else {
                $sheet->mergeCells('A2:I2');
            }
            $sheet->setCellValue('A2', "Tanggal, $dtStart - $dtEnd");

            $sheet->setCellValue('A3', "#");
            $sheet->setCellValue('B3', "Notrans");
            $sheet->setCellValue('C3', "Date");
            $sheet->setCellValue('D3', "Member");
            $sheet->setCellValue('E3', "Status");
            $sheet->setCellValue('F3', "Turnover");
            $sheet->setCellValue('G3', "Total Win");
            $sheet->setCellValue('H3', "Total Cashback");
            $sheet->setCellValue('I3', "Total Amount");
            if ($myLevel >= 1 && $myLevel <= 3) {
                $sheet->setCellValue('J3', "Total Comp");
            }

            $xRow = 4;
            foreach ($dtData as $q) {
                $sheet->setCellValue("A$xRow", $xRow - 3);
                $sheet->setCellValue("B$xRow", $q->notrans);
                $sheet->setCellValue("C$xRow", $q->tanggal);
                $sheet->setCellValue("D$xRow", $q->username);
                $sheet->setCellValue("E$xRow", $q->StatusWinLoss);
                $sheet->setCellValue("F$xRow", $q->TotalNominalPlacement);
                $sheet->setCellValue("G$xRow", $q->TotalWin);
                $sheet->setCellValue("H$xRow", $q->TotalNominalCashback);
                $sheet->setCellValue("I$xRow", $q->TotalAkhir);
                if ($myLevel >= 1 && $myLevel <= 3) {
                    $sheet->setCellValue("J$xRow", $q->TotalAkhir * -1);
                }
                $xRow++;
            }

            $sheet->mergeCells("A$xRow:E$xRow");
            $sheet->getStyle("A$xRow")->getFont()->setBold(true);
            $sheet->getStyle("F$xRow")->getFont()->setBold(true);
            $sheet->getStyle("G$xRow")->getFont()->setBold(true);
            $sheet->getStyle("H$xRow")->getFont()->setBold(true);
            $sheet->getStyle("I$xRow")->getFont()->setBold(true);
            $sheet->getStyle("A$xRow")->getAlignment()->setHorizontal('center');

            $TotalNominalPlacement = array_sum(array_column($dtData, 'TotalNominalPlacement'));
            $TotalWin = array_sum(array_column($dtData, 'TotalWin'));
            $TotalNominalCashback = array_sum(array_column($dtData, 'TotalNominalCashback'));
            $TotalAkhir = array_sum(array_column($dtData, 'TotalAkhir'));
            $sheet->setCellValue("A$xRow", "TOTAL");
            $sheet->setCellValue("F$xRow", $TotalNominalPlacement);
            $sheet->setCellValue("G$xRow", $TotalWin);
            $sheet->setCellValue("H$xRow", $TotalNominalCashback);
            $sheet->setCellValue("I$xRow", $TotalAkhir);
            if ($myLevel >= 1 && $myLevel <= 3) {
                $sheet->getStyle("J$xRow")->getFont()->setBold(true);
                $sheet->setCellValue("J$xRow", $TotalAkhir * -1);
            }


            foreach (range('A', 'J') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $styleArray = [
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'inside' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            if ($myLevel >= 1 && $myLevel <= 3) {
                $sheet->getStyle("A3:J$xRow")->applyFromArray($styleArray);
            } else {
                $sheet->getStyle("A3:I$xRow")->applyFromArray($styleArray);
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="PlacementReport.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');

            echo "<script> window.close(); </script>";
        }
    }

    public function IncomeReport($Type = "")
    {
        $qry = new Base_model();
        $xJam = new Datetime($qry->getWaktu());
        $myLevel = session('level');
        $myID = session('idUser');

        $dtStart = $xJam->format("Y-m-d");
        $dtEnd = $xJam->format("Y-m-d");
        $txtID = 0;
        $txtIsNoAgent = false;

        $dtStart = $this->request->getPost('dtStart');
        $dtEnd = $this->request->getPost('dtEnd');
        $txtID = $this->request->getPost('txtID');
        $txtIsNoAgent = $this->request->getPost('txtIsNoAgent');
        session()->setFlashdata('cache', (object) array('dtStart' => $dtStart, 'dtEnd' => $dtEnd));

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

        $str = "SELECT h.iduser, SUM(d.amount) total, SUM(d.amount * (IF(d.isbanding, 2, 22)) * d.cashback) cashback FROM tplacement h LEFT JOIN tplacementd d ON h.id = d.idplacement WHERE h.status = 5 AND date(h.inputdate) BETWEEN '$dtStart' AND '$dtEnd' GROUP BY h.iduser;";
        $dtPlacement = $qry->use($str);

        $str = "SELECT notrans, iduser, SUM(amount) amount, SUM(total * cashback ) cashback, SUM(total) total FROM twind WHERE date(tanggal) BETWEEN '$dtStart' AND '$dtEnd' GROUP BY iduser";
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

                $test = array();
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

            foreach ($dtUserB as $q2) {
                $tPlacementD = array_filter($dtPlacement, function ($x) use ($q2) {
                    return $x->iduser == $q2->id;
                });
                $qPlacementDTotal += array_sum(array_column($tPlacementD, "total"));

                $tWinD =  array_filter($dtWin, function ($x) use ($q2) {
                    return $x->iduser == $q2->id;
                });

                $qWin = array_sum(array_column($tWin, "total"));
                $qNominalPlay = array_sum(array_column($tWin, "amount"));
                $qCashback = array_sum(array_column($tWin, "cashback"));

                $qWinD += array_sum(array_column($tWinD, "total"));
                $qNominalPlayD +=  array_sum(array_column($tWin, "amount"));
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

        $data['fltrTrans'] = (object) array('dtStart' => $dtStart, 'dtEnd' => $dtEnd, 'txtID' => $txtID, 'Expand' => $xWBawahan, 'txtIsNoAgent' => $txtIsNoAgent);
        if ($Type == "PDF") {
            $data['dt'] = $dt;
            $dompdf = new Dompdf();
            $dompdf->loadHtml(view('vPDF_Income', $data));
            $dompdf->setPaper('A4', 'potrait');
            $dompdf->render();
            $dompdf->stream("Income_Report.pdf", array("Attachment" => false));
            $pdfContent = $dompdf->output();
            return $this->response->setContentType('application/pdf')->setBody($pdfContent);
        } elseif ($Type == "Excel") {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $isComp =  $myLevel >= 1 && $myLevel <= 3;
            $xColA = $xWBawahan ? "L" : "G";
            $xColB = $xWBawahan ? "M" : "H";
            $LastCol = !$isComp ? $xColA : $xColB;

            $sheet->mergeCells("A1:{$LastCol}1");
            $sheet->setCellValue('A1', 'Laporan Income');
            $sheet->getStyle('A1')->getFont()->setBold(true);
            $sheet->mergeCells("A2:{$LastCol}2");
            $sheet->setCellValue('A2', "Tanggal, $dtStart - $dtEnd");


            $sheet->setCellValue('A3', "#");
            $sheet->mergeCells("A3:A4");

            $sheet->setCellValue('B3', "Member");
            $sheet->mergeCells("B3:B4");

            $sheet->getStyle("C3")->getAlignment()->setHorizontal('center');
            $sheet->setCellValue('C3', ($xWBawahan ? "Agent" : "Member"));
            $sheet->mergeCells("C3:G3");

            $sheet->setCellValue('C4', "TurnOver");
            $sheet->setCellValue('D4', "Nominal Play");
            $sheet->setCellValue('E4', "Win");
            $sheet->setCellValue('F4', "Cashback");
            $sheet->setCellValue('G4', "Total");

            if ($xWBawahan) {
                $sheet->getStyle("H3")->getAlignment()->setHorizontal('center');
                $sheet->setCellValue('H3', "Member");
                $sheet->mergeCells("H3:L3");

                $sheet->setCellValue('H4', "TurnOver");
                $sheet->setCellValue('I4', "Nominal Play");
                $sheet->setCellValue('J4', "Win");
                $sheet->setCellValue('K4', "Cashback");
                $sheet->setCellValue('L4', "Total");
            }

            if ($isComp) {
                $sheet->setCellValue($LastCol . "3", "Total Comp");
                $sheet->mergeCells($LastCol . "3:" . $LastCol . "4");
            }

            $xRow = 5;
            foreach ($dt as $q) {
                $sheet->setCellValue("A$xRow", $xRow - 4);
                $sheet->setCellValue("B$xRow", $q->Username);
                $sheet->setCellValue("C$xRow", $q->TurnOver);
                $sheet->setCellValue("D$xRow", $q->NominalPlay);
                $sheet->setCellValue("E$xRow", $q->Win);
                $sheet->setCellValue("F$xRow", $q->Cashback);
                $sheet->setCellValue("G$xRow", $q->Total);

                if ($xWBawahan) {
                    $sheet->setCellValue("H$xRow", $q->TurnOverD);
                    $sheet->setCellValue("I$xRow", $q->NominalPlayD);
                    $sheet->setCellValue("J$xRow", $q->WinD);
                    $sheet->setCellValue("K$xRow", $q->CashbackD);
                    $sheet->setCellValue("L$xRow", $q->TotalD);
                }

                if ($isComp) {
                    $sheet->setCellValue("{$LastCol}$xRow", $q->TotalComp);
                }
                $xRow++;
            }

            $sheet->mergeCells("A$xRow:B$xRow");
            $sheet->getStyle("A{$xRow}:J{$xRow}")->getFont()->setBold(true);
            $sheet->getStyle("A$xRow")->getAlignment()->setHorizontal('center');

            $TotalTurnOver = array_sum(array_column($dt, 'TurnOver'));
            $TotalNominalPlay = array_sum(array_column($dt, 'NominalPlay'));
            $TotalWin = array_sum(array_column($dt, 'Win'));
            $TotalCashback = array_sum(array_column($dt, 'Cashback'));
            $TotalTotal = array_sum(array_column($dt, 'Total'));
            if ($xWBawahan) {
                $TotalTurnOverD = array_sum(array_column($dt, "TurnOverD"));
                $TotalNominalPlayD = array_sum(array_column($dt, "NominalPlayD"));
                $TotalWinD = array_sum(array_column($dt, "WinD"));
                $TotalCashbackD = array_sum(array_column($dt, "CashbackD"));
                $TotalTotalD = array_sum(array_column($dt, "TotalD"));
            }
            $TotalTotalComp = array_sum(array_column($dt, "TotalComp"));

            $sheet->setCellValue("A$xRow", "TOTAL");
            $sheet->setCellValue("C$xRow", $TotalTurnOver);
            $sheet->setCellValue("D$xRow", $TotalNominalPlay);
            $sheet->setCellValue("E$xRow", $TotalWin);
            $sheet->setCellValue("F$xRow", $TotalCashback);
            $sheet->setCellValue("G$xRow", $TotalTotal);
            if ($xWBawahan) {
                $sheet->setCellValue("H$xRow", $TotalTurnOverD);
                $sheet->setCellValue("I$xRow", $TotalNominalPlayD);
                $sheet->setCellValue("J$xRow", $TotalWinD);
                $sheet->setCellValue("K$xRow", $TotalCashbackD);
                $sheet->setCellValue("L$xRow", $TotalTotalD);
            }

            if ($isComp) {
                $sheet->getStyle("{$LastCol}{$xRow}")->getFont()->setBold(true);
                $sheet->setCellValue("{$LastCol}{$xRow}", $TotalTotalComp);
            }

            foreach (range('A', $LastCol) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $styleArray = [
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'inside' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $sheet->getStyle("A3:{$LastCol}$xRow")->applyFromArray($styleArray);


            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="LaporanIncome.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');

            echo "<script> window.close(); </script>";
        }
    }

    public function TransactionReport($Type = "")
    {
        if ($Type == "") {
            return redirect()->back();
        }

        $myLevel = session('level');
        $qry = new Base_model;
        $xJam = new DateTime($qry->getWaktu());
        $myID = session('idUser');
        $dtStart = $xJam->format("Y-m-d");
        $dtEnd = $xJam->format("Y-m-d");

        $dtStart = $this->request->getPost('dtStart');
        $dtEnd = $this->request->getPost('dtEnd');
        session()->setFlashdata('cache', (object) array('dtStart' => $dtStart, 'dtEnd' => $dtEnd));

        $whr = "";
        if (!(session('level') >= 1 && session('level') <= 3)) {
            $whr = "AND iduser = '{$myID}'";
        }

        $xKali = 1;
        if (session('level') >= 1 && session('level') <= 3) {
            $xKali = -1;
        }

        $str = "SELECT t.id, t.notrans, t.tanggal, t.iduser, u.username, t.jenistrans, jt.jenis, t.keterangan, (t.amount * $xKali) amount, ((t.amount * t.cashback) * $xKali) cashback, (t.total * $xKali) total FROM ttrans t LEFT JOIN tuser u ON t.iduser = u.id LEFT JOIN tjenistrans jt ON t.jenistrans = jt.id WHERE t.status = 5 AND t.jenistrans <> 7 AND DATE(t.tanggal) BETWEEN '{$dtStart}' AND '{$dtEnd}' {$whr} ORDER BY t.id DESC";
        $dtTrans = $qry->use($str);

        if (!$dtTrans) {
            session()->setFlashdata('alert', '3|No Data!');
            return redirect()->back();
        }

        if ($Type == "PDF") {
            $data['dtTrans'] = $dtTrans;
            $data['anyData'] = !empty($data['dtTrans']);
            $data['fltrTrans'] = (object) array('dtStart' => $dtStart, 'dtEnd' => $dtEnd);

            $dompdf = new Dompdf();
            $dompdf->loadHtml(view('vPDF_Trans', $data));
            $dompdf->setPaper('A4', 'potrait');
            $dompdf->render();
            $dompdf->stream("Laporan_Invoice.pdf", array("Attachment" => false));
            $pdfContent = $dompdf->output();
            return $this->response->setContentType('application/pdf')->setBody($pdfContent);
        } elseif ($Type == "Excel") {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $isComp =  $myLevel >= 1 && $myLevel <= 3;

            $sheet->mergeCells("A1:I1");
            $sheet->setCellValue('A1', 'Laporan Invoice');
            $sheet->getStyle('A1')->getFont()->setBold(true);
            $sheet->mergeCells("A2:I2");
            $sheet->setCellValue('A2', "Tanggal, $dtStart - $dtEnd");

            $sheet->setCellValue('A3', "#");
            $sheet->setCellValue('B3', "Notrans");
            $sheet->setCellValue('C3', "Date");
            $sheet->setCellValue('D3', "Member");
            $sheet->setCellValue('E3', "Type");
            $sheet->setCellValue('F3', "Description");
            $sheet->setCellValue('G3', "Amount");
            $sheet->setCellValue('H3', "Cashback");
            $sheet->setCellValue('I3', "Total Amount");

            $xRow = 4;
            foreach ($dtTrans as $q) {
                $sheet->setCellValue("A$xRow", $xRow - 3);
                $sheet->setCellValue("B$xRow", $q->notrans);
                $sheet->setCellValue("C$xRow", $q->tanggal);
                $sheet->setCellValue("D$xRow", $q->username);
                $sheet->setCellValue("E$xRow", $q->jenis);
                $sheet->setCellValue("F$xRow", $q->keterangan);
                $sheet->setCellValue("G$xRow", $q->amount);
                $sheet->setCellValue("H$xRow", $q->cashback);
                $sheet->setCellValue("I$xRow", $q->total);
                $xRow++;
            }

            $sheet->mergeCells("A$xRow:F$xRow");
            $sheet->getStyle("A{$xRow}:J{$xRow}")->getFont()->setBold(true);
            $sheet->getStyle("A$xRow")->getAlignment()->setHorizontal('center');

            $tWONetral = array_filter($dtTrans, function ($q) {
                return !($q->jenis == "Topup" || $q->jenis == 'Withdraw');
            });
            $amount = array_sum(array_column($dtTrans, 'amount'));
            $amountwonetral = array_sum(array_column($tWONetral, 'amount'));
            $cashback = array_sum(array_column($dtTrans, 'cashback'));
            $total = array_sum(array_column($dtTrans, 'total'));
            $totalwonetral = array_sum(array_column($tWONetral, 'total'));

            $sheet->setCellValue("A$xRow", "TOTAL");
            $sheet->setCellValue("G$xRow", $amountwonetral);
            $sheet->setCellValue("H$xRow", $cashback);
            $sheet->setCellValue("I$xRow", $totalwonetral);

            foreach (range('A', 'I') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $styleArray = [
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'inside' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $sheet->getStyle("A3:I$xRow")->applyFromArray($styleArray);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="TransactionReport.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');

            echo "<script> window.close(); </script>";
        }
    }

    public function ShiftReport($Type = "")
    {
        if (!(session('level') >= 1 && session('level') <= 3)) {
            session()->setflashdata('alert', '3|Unauthorized User, ADMIN only!');
            return redirect()->to('../CData/User');
        }

        if ($Type == "") {
            return redirect()->back();
        }
        $myLevel = session('level');

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
            $obj->InputBy = $this->CTools->getUsername($q->inputby);
            $obj->InputDate = $q->inputdate;
            $obj->IDCloseBy = $q->updateby;
            $obj->CloseBy = $this->CTools->getUsername($q->updateby);
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

        if (!$dt) {
            session()->setFlashdata('alert', '3|No Data!');
            return redirect()->back();
        }
        session()->setFlashdata('cache', (object) array('dtStart' => $dtStart, 'dtEnd' => $dtEnd));

        if ($Type == "PDF") {
            $data['dt'] = $dt;
            $dompdf = new Dompdf();
            $dompdf->loadHtml(view('vPDF_ShiftReport', $data));
            $dompdf->setPaper('A4', 'potrait');
            $dompdf->render();
            $dompdf->stream("Shift_Report.pdf", array("Attachment" => false));
            $pdfContent = $dompdf->output();
            return $this->response->setContentType('application/pdf')->setBody($pdfContent);
        } elseif ($Type == "Excel") {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $LastCol = $myLevel >= 1 && $myLevel <= 3 ? "M" : "L";

            $sheet->mergeCells("A1:{$LastCol}1");
            $sheet->setCellValue('A1', 'Shift Report List');
            $sheet->getStyle('A1')->getFont()->setBold(true);
            $sheet->mergeCells("A2:{$LastCol}2");
            $sheet->setCellValue('A2', "Tanggal, $dtStart - $dtEnd");

            $sheet->setCellValue('A3', "#");
            $sheet->setCellValue('B3', "ID Shift");
            $sheet->setCellValue('C3', "Total Periode");
            $sheet->setCellValue('D3', "Started By");
            $sheet->setCellValue('E3', "Start Date");
            $sheet->setCellValue('F3', "Ended by");
            $sheet->setCellValue('G3', "End Date");
            $sheet->setCellValue('H3', "Total Amount");
            $sheet->setCellValue('I3', "Nominal Win");
            $sheet->setCellValue('J3', "Cashback");
            $sheet->setCellValue('K3', "Total Win");
            $sheet->setCellValue('L3', "Total");
            if ($myLevel >= 1 && $myLevel <= 3) {
                $sheet->setCellValue('M3', "Total Comp");
            }

            $xRow = 4;
            foreach ($dt as $q) {
                $sheet->setCellValue("A$xRow", $xRow - 3);
                $sheet->setCellValue("B$xRow", $q->IDShift);
                $sheet->setCellValue("C$xRow", $q->Periode);
                $sheet->setCellValue("D$xRow", $q->InputBy);
                $sheet->setCellValue("E$xRow", $q->InputDate);
                $sheet->setCellValue("F$xRow", $q->CloseBy);
                $sheet->setCellValue("G$xRow", $q->CloseDate);
                $sheet->setCellValue("H$xRow", $q->TotalPlacement);
                $sheet->setCellValue("I$xRow", $q->NominalWin);
                $sheet->setCellValue("J$xRow", $q->NominalCashback);
                $sheet->setCellValue("K$xRow", $q->TotalWin);
                $sheet->setCellValue("L$xRow", $q->Total);
                if ($myLevel >= 1 && $myLevel <= 3) {
                    $sheet->setCellValue("M$xRow", $q->Total * -1);
                }
                $xRow++;
            }

            $sheet->mergeCells("A$xRow:G$xRow");
            $sheet->getStyle("A{$xRow}:M{$xRow}")->getFont()->setBold(true);
            $sheet->getStyle("A$xRow")->getAlignment()->setHorizontal('center');

            $TotalPlacement = array_sum(array_column($dt, 'TotalPlacement'));
            $NominalWin = array_sum(array_column($dt, 'NominalWin'));
            $NominalCashback = array_sum(array_column($dt, 'NominalCashback'));
            $TotalWin = array_sum(array_column($dt, 'TotalWin'));
            $Total = array_sum(array_column($dt, 'Total'));

            $sheet->setCellValue("A$xRow", "TOTAL");
            $sheet->setCellValue("H$xRow", $TotalPlacement);
            $sheet->setCellValue("I$xRow", $NominalWin);
            $sheet->setCellValue("J$xRow", $NominalCashback);
            $sheet->setCellValue("K$xRow", $TotalWin);
            $sheet->setCellValue("L$xRow", $Total);

            if ($myLevel >= 1 && $myLevel <= 3) {
                $sheet->getStyle("M$xRow")->getFont()->setBold(true);
                $sheet->setCellValue("M$xRow", $Total * -1);
            }


            foreach (range('A', 'M') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $styleArray = [
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'inside' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $sheet->getStyle("A3:{$LastCol}$xRow")->applyFromArray($styleArray);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="ShiftReport.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');

            echo "<script> window.close(); </script>";
        }
    }

    public function TopupRequest($Type = "")
    {
        if ($Type == "") {
            return redirect()->back();
        }
        $qry = new Base_model();
        $xJam = new Datetime($qry->getWaktu());

        $myID = session('idUser');
        $myLevel = session('level');

        $dtStart = $this->request->getPost('dtStart');
        $dtEnd = $this->request->getPost('dtEnd');
        $dtStart = !$dtStart ? $xJam->format("Y-m-d") : $dtStart;
        $dtEnd = !$dtEnd ? $xJam->format("Y-m-d") : $dtEnd;

        $xWhr = "";
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            $xWhr = "AND r.iduser = '{$myID}'";
        }

        session()->setFlashdata('cache', (object) array('dtStart' => $dtStart, 'dtEnd' => $dtEnd));
        $str = "SELECT r.kodereq, u.username, r.iduser, r.amount, r.tanggal, r.status, r.updateby, r.updatedate FROM treqtopup r INNER JOIN tuser u ON r.iduser = u.id WHERE date(tanggal) BETWEEN '$dtStart' AND '$dtEnd' {$xWhr} ORDER BY r.id DESC";
        $dtReq = $qry->use($str);
        if (!$dtReq) {
            session()->setFlashdata('alert', '3|No Data!');
            return redirect()->back();
        }

        if ($Type == "PDF") {
            $data['dtReqTopup'] = $dtReq;
            $dompdf = new Dompdf();
            $dompdf->loadHtml(view('vPDF_TopupReq', $data));
            $dompdf->setPaper('A4', 'potrait');
            $dompdf->render();
            $dompdf->stream("TopUpRequest.pdf", array("Attachment" => false));
            $pdfContent = $dompdf->output();
            return $this->response->setContentType('application/pdf')->setBody($pdfContent);
        } elseif ($Type == "Excel") {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->mergeCells("A1:I1");
            $sheet->setCellValue('A1', 'Topup Request List');
            $sheet->getStyle('A1')->getFont()->setBold(true);
            $sheet->mergeCells("A2:I2");
            $sheet->setCellValue('A2', "Tanggal, $dtStart - $dtEnd");

            $sheet->setCellValue('A3', "#");
            $sheet->setCellValue('B3', "Kode Request");
            $sheet->setCellValue('C3', "Member");
            $sheet->setCellValue('D3', "Amount");
            $sheet->setCellValue('E3', "Date");
            $sheet->setCellValue('F3', "Status");
            $sheet->setCellValue('G3', "TopUp By");
            $sheet->setCellValue('H3', "Refused By");
            $sheet->setCellValue('I3', "Update Time");

            $xRow = 4;
            foreach ($dtReq as $q) {
                $sheet->setCellValue("A$xRow", $xRow - 3);
                $sheet->setCellValue("B$xRow", $q->kodereq);
                $sheet->setCellValue("C$xRow", $q->username);
                $sheet->setCellValue("D$xRow", $q->amount);
                $sheet->setCellValue("E$xRow", $q->tanggal);
                $sheet->setCellValue("F$xRow", $q->status == 5 ? 'Success' : ($q->status == 8 ? 'Refused' : 'Request'));
                $sheet->setCellValue("G$xRow", ($q->status == 5 ? $this->CTools->getUsername($q->updateby) : ""));
                $sheet->setCellValue("H$xRow", ($q->status == 8 ? $this->CTools->getUsername($q->updateby) : ""));
                $sheet->setCellValue("I$xRow", ($q->status == 5 ||  $q->status == 8) ? $q->updatedate : '');
                $xRow++;
            }

            $sheet->mergeCells("A$xRow:C$xRow");
            $sheet->mergeCells("E$xRow:I$xRow");
            $sheet->getStyle("A{$xRow}:H{$xRow}")->getFont()->setBold(true);
            $sheet->getStyle("A$xRow")->getAlignment()->setHorizontal('center');

            $Amount = array_sum(array_column($dtReq, 'amount'));

            $sheet->setCellValue("A$xRow", "TOTAL");
            $sheet->setCellValue("D$xRow", $Amount);

            foreach (range('A', 'I') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $styleArray = [
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'inside' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $sheet->getStyle("A3:I$xRow")->applyFromArray($styleArray);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="TopupRequest.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');

            echo "<script> window.close(); </script>";
        }
    }

    public function WithdrawRequest($Type = "")
    {
        if ($Type == "") {
            return redirect()->back();
        }
        $qry = new Base_model();
        $xJam = new Datetime($qry->getWaktu());

        $myID = session('idUser');
        $myLevel = session('level');

        $dtStart = $this->request->getPost('dtStart');
        $dtEnd = $this->request->getPost('dtEnd');
        $dtStart = !$dtStart ? $xJam->format("Y-m-d") : $dtStart;
        $dtEnd = !$dtEnd ? $xJam->format("Y-m-d") : $dtEnd;

        $xWhr = "";
        if (!($myLevel >= 1 && $myLevel <= 3)) {
            $xWhr = "AND r.iduser = '{$myID}'";
        }

        session()->setFlashdata('cache', (object) array('dtStart' => $dtStart, 'dtEnd' => $dtEnd));
        $str = "SELECT r.kodereq, u.username, r.iduser, r.amount, r.tanggal, r.status, r.updateby, r.updatedate FROM treqwithdraw r INNER JOIN tuser u ON r.iduser = u.id WHERE date(tanggal) BETWEEN '$dtStart' AND '$dtEnd' {$xWhr} ORDER BY r.id DESC";
        $dtReq = $qry->use($str);
        if (!$dtReq) {
            session()->setFlashdata('alert', '3|No Data!');
            return redirect()->back();
        }

        if ($Type == "PDF") {
            $data['dtReqWithdraw'] = $dtReq;
            $dompdf = new Dompdf();
            $dompdf->loadHtml(view('vPDF_WithdrawReq', $data));
            $dompdf->setPaper('A4', 'potrait');
            $dompdf->render();
            $dompdf->stream("WithdrawRequest.pdf", array("Attachment" => false));
            $pdfContent = $dompdf->output();
            return $this->response->setContentType('application/pdf')->setBody($pdfContent);
        } elseif ($Type == "Excel") {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->mergeCells("A1:I1");
            $sheet->setCellValue('A1', 'Withdraw Request List');
            $sheet->getStyle('A1')->getFont()->setBold(true);
            $sheet->mergeCells("A2:I2");
            $sheet->setCellValue('A2', "Tanggal, $dtStart - $dtEnd");

            $sheet->setCellValue('A3', "#");
            $sheet->setCellValue('B3', "Kode Request");
            $sheet->setCellValue('C3', "Member");
            $sheet->setCellValue('D3', "Amount");
            $sheet->setCellValue('E3', "Date");
            $sheet->setCellValue('F3', "Status");
            $sheet->setCellValue('G3', "Withdraw By");
            $sheet->setCellValue('H3', "Refused By");
            $sheet->setCellValue('I3', "Update Time");

            $xRow = 4;
            foreach ($dtReq as $q) {
                $sheet->setCellValue("A$xRow", $xRow - 3);
                $sheet->setCellValue("B$xRow", $q->kodereq);
                $sheet->setCellValue("C$xRow", $q->username);
                $sheet->setCellValue("D$xRow", $q->amount);
                $sheet->setCellValue("E$xRow", $q->tanggal);
                $sheet->setCellValue("F$xRow", $q->status == 5 ? 'Success' : ($q->status == 8 ? 'Refused' : 'Request'));
                $sheet->setCellValue("G$xRow", ($q->status == 5 ? $this->CTools->getUsername($q->updateby) : ""));
                $sheet->setCellValue("H$xRow", ($q->status == 8 ? $this->CTools->getUsername($q->updateby) : ""));
                $sheet->setCellValue("I$xRow", ($q->status == 5 ||  $q->status == 8) ? $q->updatedate : '');
                $xRow++;
            }

            $sheet->mergeCells("A$xRow:C$xRow");
            $sheet->mergeCells("E$xRow:I$xRow");
            $sheet->getStyle("A{$xRow}:H{$xRow}")->getFont()->setBold(true);
            $sheet->getStyle("A$xRow")->getAlignment()->setHorizontal('center');

            $Amount = array_sum(array_column($dtReq, 'amount'));

            $sheet->setCellValue("A$xRow", "TOTAL");
            $sheet->setCellValue("D$xRow", $Amount);

            foreach (range('A', 'I') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $styleArray = [
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'inside' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ];
            $sheet->getStyle("A3:I$xRow")->applyFromArray($styleArray);
            ob_end_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="WithdrawRequest.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');

            echo "<script> window.close(); </script>";
        }
    }
}
