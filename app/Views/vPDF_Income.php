<div class="row">
    <div class="col-7">
        <h4 class="pt-4">Laporan Income</h4>
        <?php
        $tempDt = new DateTime(session('cache')->dtStart);
        $dtStartStr = $tempDt->format('d M y');
        $tempDt = new DateTime(session('cache')->dtEnd);
        $dtEndStr = $tempDt->format('d M y');
        ?>
        <p>Tanggal, <?= $dtStartStr ?> - <?= $dtEndStr ?></p>
    </div>
</div>

<?php
$myLevel = session('level');
function frmtTD(float $xVal, bool $reverse = false, bool $isNoColor = false)
{
    $xCol = ($isNoColor ? '' : ($xVal * ($reverse ? -1 : 1) >= 0 ? 'green' : 'red'));
    $xStrVal = number_format($xVal, 2, '.', ',');
    return "<td class='align-middle text-right' style='color: $xCol'> <p class='text-xs'> $xStrVal</p> </td>";
}

?>

<div class="card-body px-0 pt-0 pb-2">
    <div class="table-responsive p-0 datatable-minimal p-3">
        <table border=1 style="font-size: x-small;">
            <thead style="background-color: silver;">
                <?php if ($fltrTrans->Expand) { ?>
                    <tr>
                        <th rowspan="2">#</th>
                        <th rowspan="2">Member</th>
                        <th colspan="5">Agent</th>
                        <th colspan="5">Member</th>
                        <th rowspan="2"> Total Comp</th>
                    </tr>
                    <tr>
                        <th>Turnover</th>
                        <!-- <th>Nominal Play</th> -->
                        <th>Win</th>
                        <th>Cashback</th>
                        <th>Total</th>

                        <th>Turnover</th>
                        <!-- <th>Nominal Play</th> -->
                        <th>Win</th>
                        <th>Cashback</th>
                        <th>Total</th>
                    </tr>

                <?php } else { ?>
                    <tr>
                        <th>#</th>
                        <th>Member</th>
                        <th>Turnover</th>
                        <!-- <th>Nominal Play</th> -->
                        <th>Win</th>
                        <th>Cashback</th>
                        <th>Total</th>

                        <?php if ($myLevel >= 1 && $myLevel <= 3) { ?>
                            <th>Total Comp</th>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </thead>

            <tbody>
                <?php $i = 1;
                foreach ($dt as $q) { ?>
                    <tr>
                        <td class="align-middle text-center">
                            <p class="text-xs">
                                <?= $i++ ?>
                            </p>
                        </td>
                        <td class="align-middle text-center">
                            <p class="text-s font-weight-bold"><?= $q->Username ?></p>
                        </td>
                        <?= frmtTD($q->TurnOver) ?>
                        <!-- frmtTD($q->NominalPlay) -->
                        <?= frmtTD($q->Win) ?>
                        <?= frmtTD($q->Cashback) ?>
                        <?= frmtTD($q->Total) ?>


                        <?php if ($fltrTrans->Expand) { ?>
                            <?= frmtTD($q->TurnOverD) ?>
                            <!-- frmtTD($q->NominalPlayD) -->
                            <?= frmtTD($q->WinD) ?>
                            <?= frmtTD($q->CashbackD) ?>
                            <?= frmtTD($q->TotalD) ?>
                        <?php } ?>

                        <?php if ($myLevel >= 1 && $myLevel <= 3) { ?>
                            <?= frmtTD($q->TotalComp) ?>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class="text-center">
                    <th colspan="2">TOTAL</th>
                    <?php
                    $TurnOver = array_sum(array_column($dt, 'TurnOver'));
                    $NominalPlay = array_sum(array_column($dt, 'NominalPlay'));
                    $Win = array_sum(array_column($dt, 'Win'));
                    $Cashback = array_sum(array_column($dt, 'Cashback'));
                    $Total = array_sum(array_column($dt, 'Total'));
                    $TurnOverD = array_sum(array_column($dt, 'TurnOverD'));
                    $NominalPlayD = array_sum(array_column($dt, 'NominalPlayD'));
                    $WinD = array_sum(array_column($dt, 'WinD'));
                    $CashbackD = array_sum(array_column($dt, 'CashbackD'));
                    $TotalD = array_sum(array_column($dt, 'TotalD'));
                    $Komisi = array_sum(array_column($dt, 'Komisi'));

                    $TotalComp = array_sum(array_column($dt, 'TotalComp'));
                    ?>

                    <th style="color: <?= ($TurnOver >= 0 ? 'green' : 'red') ?> "><?= number_format($TurnOver, 2, '.', ',') ?></th>
                    <th style="color: <?= ($NominalPlay >= 0 ? 'green' : 'red') ?> "><?= number_format($NominalPlay, 2, '.', ',') ?></th>
                    <th style="color: <?= ($Win >= 0 ? 'green' : 'red') ?> "><?= number_format($Win, 2, '.', ',') ?></th>
                    <th style="color: <?= ($Cashback >= 0 ? 'green' : 'red') ?> "><?= number_format($Cashback, 2, '.', ',') ?></th>
                    <th style="color: <?= ($Total >= 0 ? 'green' : 'red') ?> "><?= number_format($Total, 2, '.', ',') ?></th>
                    <?php if ($fltrTrans->Expand) { ?>
                        <th style="color: <?= ($TurnOverD >= 0 ? 'green' : 'red') ?> "><?= number_format($TurnOverD, 2, '.', ',') ?></th>
                        <th style="color: <?= ($NominalPlayD >= 0 ? 'green' : 'red') ?> "><?= number_format($NominalPlayD, 2, '.', ',') ?></th>
                        <th style="color: <?= ($WinD >= 0 ? 'green' : 'red') ?> "><?= number_format($WinD, 2, '.', ',') ?></th>
                        <th style="color: <?= ($CashbackD >= 0 ? 'green' : 'red') ?> "><?= number_format($CashbackD, 2, '.', ',') ?></th>
                        <th style="color: <?= ($TotalD >= 0 ? 'green' : 'red') ?> "><?= number_format($TotalD, 2, '.', ',') ?></th>
                    <?php } ?>

                    <?php if ($myLevel >= 1 && $myLevel <= 3) { ?>
                        <th style="color: <?= ($TotalComp >= 0 ? 'green' : 'red') ?> "><?= number_format($TotalComp, 2, '.', ',') ?></th>
                    <?php } ?>
                </tr>
            </tfoot>
        </table>
    </div>
</div>