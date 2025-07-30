<div class="row">
    <div class="col-7">
        <h4 class="pt-4">Laporan Invoice</h4>
        <?php
        $tempDt = new DateTime(session('cache')->dtStart);
        $dtStartStr = $tempDt->format('d M y');
        $tempDt = new DateTime(session('cache')->dtEnd);
        $dtEndStr = $tempDt->format('d M y');
        ?>
        <p>Tanggal, <?= $dtStartStr ?> - <?= $dtEndStr ?></p>
    </div>
</div>



<div class="card-body px-0 pt-0 pb-2">
    <div class="table-responsive p-0 datatable-minimal p-3">
        <table border=1 style="font-size: x-small;">
            <thead style="background-color: silver;">
                <tr>
                    <th>#</th>
                    <th>Notrans</th>
                    <th>Date</th>
                    <th>Member</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Cashback</th>
                    <th>Total Amount</th>
                </tr>
            </thead>

            <tbody>
                <?php $i = 1;
                foreach ($dtTrans as $q) { ?>
                    <tr style="height: min-content;">
                        <td>
                            <p style="width: auto; padding-left: 10px; padding-right: 10px;"> <?= $i++ ?> </p>
                        </td>
                        <td>
                            <p style=" font-weight: bold;"><?= $q->notrans ?></p>
                        </td>
                        <td style="width: 100px;">
                            <p style=" font-weight: bold;">
                                <?php $xDt = new DateTime($q->tanggal);
                                echo $xDt->format('Y-M-d') . "<br>";
                                echo $xDt->format('h:i:s A');
                                ?>
                            </p>
                        </td>
                        <td>
                            <p style="width: auto; padding-left: 10px; padding-right: 10px;"><?= $q->username ?></p>
                        </td>
                        <td>
                            <p style="width: auto; padding-left: 10px; padding-right: 10px;"><?= $q->jenis ?></p>
                        </td>
                        <td>
                            <p style="width: auto; padding-left: 10px; padding-right: 10px;"><?= $q->keterangan ?></p>
                        </td>
                        <td>
                            <?php $xAmount = $q->jenis == 'Topup' || $q->jenis == 'Withdraw' ? abs($q->amount) : $q->amount ?>
                            <p class="text-xs" style="color: <?= ($q->jenis == 'Topup' || $q->jenis == 'Withdraw' ? '' : ($q->amount >= 0 ? 'green' : 'red')) ?>"><?= number_format($xAmount, 2, '.', ',') ?></p>
                        </td>
                        <td>
                            <p style="width: auto; padding-left: 10px; padding-right: 10px; text-align: right; color: <?= ($q->cashback >= 0 ? 'green' : 'red') ?>"><?= number_format($q->cashback, 2, '.', ',') ?></p>
                        </td>
                        <td>
                            <?php $xTotal = $q->jenis == 'Topup' || $q->jenis == 'Withdraw' ? abs($q->total) : $q->total ?>
                            <p class="text-xs" style="color: <?= ($q->jenis == 'Topup' || $q->jenis == 'Withdraw' ? '' : ($q->total >= 0 ? 'green' : 'red')) ?>"><?= number_format($xTotal, 2, '.', ',') ?></p>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6">TOTAL</td>
                        <?php
                        $tWONetral = array_filter($dtTrans, function ($q) {
                            return !($q->jenis == "Topup" || $q->jenis == 'Withdraw');
                        });

                        $amount = array_sum(array_column($dtTrans, 'amount'));
                        $amountwonetral = array_sum(array_column($tWONetral, 'amount'));
                        $cashback = array_sum(array_column($dtTrans, 'cashback'));
                        $total = array_sum(array_column($dtTrans, 'total'));
                        $totalwonetral = array_sum(array_column($tWONetral, 'total'));
                        ?>
                    <th style="color: <?= ($amountwonetral >= 0 ? 'green' : 'red')  ?>"><?= number_format($amountwonetral, 2, '.', ',') ?></td>
                    <th style="color: <?= ($cashback >= 0 ? 'green' : 'red')  ?>"><?= number_format($cashback, 2, '.', ',') ?></td>
                    <th style="color: <?= ($totalwonetral >= 0 ? 'green' : 'red')  ?>"><?= number_format($totalwonetral, 2, '.', ',') ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>