<h4>Shift Report</h4>
<?php
$tempDt = new DateTime(session('cache')->dtStart);
$dtStartStr = $tempDt->format('d M y');
$tempDt = new DateTime(session('cache')->dtEnd);
$dtEndStr = $tempDt->format('d M y');
?>
<p>Tanggal, <?= $dtStartStr ?> - <?= $dtEndStr ?></p>

<table border="1" style="font-size: x-small;">
    <thead>
        <tr>
            <th>#</th>
            <th>ID Shift</th>
            <th>Total Periode</th>
            <th>Started By</th>
            <th>Started Date</th>
            <th>Ended By</th>
            <th>Ended Date</th>
            <th>Total Amount</th>
            <th>Nominal Win</th>
            <th>CashBack</th>
            <th>Total Win</th>
            <th>Total</th>
            <th>Total Comp</th>
        </tr>
    </thead>

    <tbody>
        <?php $i = 1;
        foreach ($dt as $q) { ?>
            <tr>
                <td>
                    <p style="width: auto; padding-left: 10px; padding-right: 10px;"><?= $i++ ?></p>
                </td>
                <td>
                    <p style="width: auto; padding-left: 10px; padding-right: 10px;"><?= $q->IDShift ?></p>
                </td>
                <td>
                    <p style="width: auto; padding-left: 10px; padding-right: 10px;"><?= $q->Periode ?></p>
                </td>
                <td>
                    <p><?= $q->InputBy ?></p>
                </td>
                <td>
                    <p style="width: 70px;">
                        <?php $xDt = new DateTime($q->InputDate);
                        echo $xDt->format('Y-M-d') . "<br>";
                        echo $xDt->format('h:i:s A');
                        ?>
                    </p>
                </td>
                <td>
                    <p><?= $q->CloseBy ?></p>
                </td>
                <td>
                    <p style="width: 70px;">
                        <?php $xDt = new DateTime($q->CloseDate);
                        echo $xDt->format('Y-M-d') . "<br>";
                        echo $xDt->format('h:i:s A');
                        ?>
                    </p>
                </td>
                <td style="text-align: right;">
                    <p style="color: <?= ($q->TotalPlacement >= 0 ? 'green' : 'red') ?>"><?= number_format($q->TotalPlacement, 2, ",", '.'); ?></p>
                </td>
                <td style="text-align: right;">
                    <p style="color: <?= ($q->NominalWin >= 0 ? 'green' : 'red') ?>"><?= number_format($q->NominalWin, 2, ",", '.'); ?></p>
                </td>
                <td style="text-align: right;">
                    <p style="color: <?= ($q->NominalCashback >= 0 ? 'green' : 'red') ?>"><?= number_format($q->NominalCashback, 2, ",", '.'); ?></p>
                </td>
                <td style="text-align: right;">
                    <p style="color: <?= ($q->TotalWin >= 0 ? 'green' : 'red') ?>"><?= number_format($q->TotalWin, 2, ",", '.'); ?></p>
                </td>
                <td style="text-align: right;">
                    <p style="color: <?= ($q->Total >= 0 ? 'green' : 'red') ?>"><?= number_format($q->Total, 2, ",", '.'); ?></p>
                </td>
                <td style="text-align: right;">
                    <p style="color: <?= ($q->Total * -1 >= 0 ? 'green' : 'red') ?>"><?= number_format($q->Total * -1, 2, ",", '.'); ?></p>
                </td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="7">TOTAL</td>
                <?php
                $TotalPlacement = array_sum(array_column($dt, 'TotalPlacement'));
                $NominalWin = array_sum(array_column($dt, 'NominalWin'));
                $NominalCashback = array_sum(array_column($dt, 'NominalCashback'));
                $TotalWin = array_sum(array_column($dt, 'TotalWin'));
                $Total = array_sum(array_column($dt, 'Total'));
                $Total = array_sum(array_column($dt, 'Total'));
                ?>

            <th style="text-align: right; color: <?= ($TotalPlacement >= 0 ? 'green' : 'red') ?>"><?= number_format($TotalPlacement, 2, ',', '.') ?></td>
            <th style="text-align: right; color: <?= ($NominalWin >= 0 ? 'green' : 'red') ?>"><?= number_format($NominalWin, 2, ',', '.') ?></td>
            <th style="text-align: right; color: <?= ($NominalCashback >= 0 ? 'green' : 'red') ?>"><?= number_format($NominalCashback, 2, ',', '.') ?></td>
            <th style="text-align: right; color: <?= ($TotalWin >= 0 ? 'green' : 'red') ?>"><?= number_format($TotalWin, 2, ',', '.') ?></td>
            <th style="text-align: right; color: <?= ($Total >= 0 ? 'green' : 'red') ?>"><?= number_format($Total, 2, ',', '.') ?></td>
            <th style="text-align: right; color: <?= ($Total * -1 >= 0 ? 'green' : 'red') ?>"><?= number_format($Total * -1, 2, ',', '.')  ?></td>
        </tr>
    </tfoot>
</table>