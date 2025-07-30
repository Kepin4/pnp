<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Transaction</title>
</head>

<body>
    <div style="padding: 10px; background-color: white; border-radius: 5px; border: 1px solid gray">
        <h1 style="text-align: center;">Transaction Report</h1>
        <div style="padding-left: 10px; background-color: white; border-radius: 5px; border: 1px solid gray;">
            <table>
                <h3><?= $qTrans->notrans ?></h3>
                <?php if (!$qTrans->noref == '') { ?>
                    <tr>
                        <td style="font-weight: bold;">No Referensi</td>
                        <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                        <td><?= $qTrans->noref ?></td>
                    </tr>
                <?php } ?>

                <tr>
                    <td style="font-weight: bold;">Date</td>
                    <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                    <td><?php $xTgl = new Datetime($qTrans->tanggal);
                        echo $xTgl->format('Y M d'); ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Member</td>
                    <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                    <td><?= $qTrans->username ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Description</td>
                    <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                    <td><?= $qTrans->keterangan ?></td>
                </tr>
                <?php if ($qTrans->jenistrans == '3') { ?>
                    <tr>
                        <td style="font-weight: bold;">Status</td>
                        <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                        <td><?= ($qStatus == 5 ? ($qNotransWin == '' ? 'Lose' : 'Win') : 'On Placement') ?></td>
                    </tr>

                    <?php if ($qNotransWin != '') { ?>
                        <tr>
                            <td style="font-weight: bold;">Notrans Win</td>
                            <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                            <td><?= ($qStatus == 5 ? $qNotransWin : '') ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>

                <tr>
                    <td style="font-weight: bold;">Total Amount</td>
                    <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                    <td><?= number_format($TotalSaldo, 2, '.', ',') ?></td>
                </tr>
                <tr>
                    <?php
                    $i = 1;

                    use App\Controllers\CTools;

                    $cntrl = new CTools;
                    $Saldo = $cntrl->getSaldo($idUser, $xTgl->format('Y-m-d H:i:s'));
                    $SaldoAwal = $Saldo;
                    ?>
                    <td style="font-weight: bold;">Saldo Awal</td>
                    <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                    <td><?= number_format($Saldo, 2, '.', ',') ?></td>
                </tr>
            </table>
        </div>
        <div class="table-responsive mt-4">
            <caption>
                <h2 style="border-bottom: 1px solid black">Saldo</h2>
            </caption>
            <table border="1px solid black">
                <thead>
                    <tr style="text-align: center; background-color: lightgray;">
                        <td style="width: 25px">#</td>
                        <td style="width: 100px">Income</td>
                        <td style="width: 100px">Outcome</td>
                        <td style="width: 100px">Balance</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($dtSaldo as $q) {
                        $xVal = (float) $q->amount;
                        $SaldoAwal += $xVal  ?>
                        <tr style="text-align: right;">
                            <td style="width: 25px; text-align: center"><?= $i++ ?></td>
                            <td><?= ($q->amount > 0 ? number_format($xVal, 2, '.', ',') : 0) ?></td>
                            <td><?= ($q->amount < 0 ? number_format($xVal, 2, '.', ',') : 0) ?></td>
                            <td><?= number_format($SaldoAwal, 2, '.', ',') ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>
</body>

</html>