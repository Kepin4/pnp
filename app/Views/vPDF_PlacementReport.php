<div class="container">
    <div class="row">
        <div class="col-7">
            <h4 class="pt-4">Placement Report List</h4>
            <?php
            $tempDt = new DateTime(session('cache')->dtStart);
            $dtStartStr = $tempDt->format('d M y');
            $tempDt = new DateTime(session('cache')->dtEnd);
            $dtEndStr = $tempDt->format('d M y');
            ?>
            <p>Tanggal, <?= $dtStartStr ?> - <?= $dtEndStr ?></p>
        </div>
    </div>

    <table style="color: black; font-size: x-small;" border=1>
        <thead>
            <tr style="background-color: silver; ">
                <th>#</th>
                <th>Notrans</th>
                <th style="width: 100px;">Date</th>
                <th style="padding-left: 4px; padding-right: 4px;">Member</th>
                <th style="padding-left: 4px; padding-right: 4px;">Status</th>
                <th style="padding-left: 4px; padding-right: 4px; width: 75px;">Turnover</th>
                <th style="padding-left: 4px; padding-right: 4px; width: 75px;">Total Win</th>
                <th style="padding-left: 4px; padding-right: 4px; width: 75px;">Total Cashback</th>
                <th style="padding-left: 4px; padding-right: 4px; width: 75px;">Total Amount</th>
                <?php if (session('level') >= 1 && session('level') <= 3) { ?>
                    <th style="padding-left: 4px; padding-right: 4px; width: 75px;">Total Comp</th>
                <?php } ?>
            </tr>
        </thead>

        <tbody>
            <?php $i = 1;
            foreach ($dt as $q) { ?>
                <tr style="height: min-content; ">
                    <td style="padding-left: 5px; padding-right: 5px;">
                        <p> <?= $i++ ?> </p>
                    </td>

                    <td style="padding-left: 5px; padding-right: 5px;">
                        <p style=" font-weight: bold;"><?= $q->notrans ?></p>
                    </td>

                    <td style="padding-left: 5px; padding-right: 5px;">
                        <p style=" font-weight: bold;">
                            <?php $xDt = new DateTime($q->tanggal);
                            echo $xDt->format('Y-M-d') . "<br>";
                            echo $xDt->format('h:i:s A');
                            ?>
                        </p>
                    </td>

                    <td style="padding-left: 5px; padding-right: 5px;">
                        <p><?= $q->username ?></p>
                    </td>

                    <td style="padding-left: 5px; padding-right: 5px;">
                        <p><?= $q->StatusWinLoss ?></p>
                    </td>

                    <td style="padding-left: 5px; padding-right: 5px; text-align: right;">
                        <p style="color: <?= ($q->TotalNominalPlacement >= 0 ? 'green' : 'red') ?>"><?= number_format($q->TotalNominalPlacement, 2, '.', ',') ?></p>
                    </td>
                    <td style="padding-left: 5px; padding-right: 5px; text-align: right;">
                        <p style="color: <?= ($q->TotalWin >= 0 ? 'green' : 'red') ?>"><?= number_format($q->TotalWin, 2, '.', ',') ?></p>
                    </td>
                    <td style="padding-left: 5px; padding-right: 5px; text-align: right;">
                        <p style="color: <?= ($q->TotalNominalCashback >= 0 ? 'green' : 'red') ?>"><?= number_format($q->TotalNominalCashback, 2, '.', ',') ?></p>
                    </td>
                    <td style="padding-left: 5px; padding-right: 5px; text-align: right;">
                        <p style="color: <?= ($q->TotalAkhir >= 0 ? 'green' : 'red') ?>"><?= number_format($q->TotalAkhir, 2, '.', ',') ?></p>
                    </td>
                    <?php if (session('level') >= 1 && session('level') <= 3) { ?>
                        <td style="padding-left: 5px; padding-right: 5px; text-align: right;">
                            <p style="color: <?= ($q->TotalAkhir * -1 >= 0 ? 'green' : 'red') ?>"><?= number_format($q->TotalAkhir * -1, 2, '.', ',') ?></p>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr style="height: min-content;">
                <th colspan="5">TOTAL</th>
                <?php
                $TotalPlacement = array_sum(array_column($dt, 'TotalPlacement'));
                $TotalNominalPlacement = array_sum(array_column($dt, 'TotalNominalPlacement'));
                $TotalWin = array_sum(array_column($dt, 'TotalWin'));
                $TotalNominalCashback = array_sum(array_column($dt, 'TotalNominalCashback'));
                $TotalAkhir = array_sum(array_column($dt, 'TotalAkhir'));
                ?>
                <th style="color: <?= ($TotalNominalPlacement >= 0 ? 'green' : 'red') ?>;  text-align: right;"><?= number_format($TotalNominalPlacement, 2, '.', ',') ?></th>
                <th style="color: <?= ($TotalWin >= 0 ? 'green' : 'red') ?>;  text-align: right;"><?= number_format($TotalWin, 2, '.', ',') ?></th>
                <th style="color: <?= ($TotalNominalCashback >= 0 ? 'green' : 'red') ?>;  text-align: right;"><?= number_format($TotalNominalCashback, 2, '.', ',') ?></th>
                <th style="color: <?= ($TotalAkhir >= 0 ? 'green' : 'red') ?>;  text-align: right;"><?= number_format($TotalAkhir, 2, '.', ',') ?></th>
                <?php if (session('level') >= 1 && session('level') <= 3) { ?>
                    <th style="color: <?= ($TotalAkhir * -1 >= 0 ? 'green' : 'red') ?>;  text-align: right;"><?= number_format($TotalAkhir * -1, 2, '.', ',') ?></th>
                <?php } ?>
            </tr>
        </tfoot>
    </table>
</div>