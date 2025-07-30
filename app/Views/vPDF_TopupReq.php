<h4 class="pt-4">Topup Request List</h4>
<?php
$tempDt = new DateTime(session('cache')->dtStart);
$dtStartStr = $tempDt->format('d M y');
$tempDt = new DateTime(session('cache')->dtEnd);
$dtEndStr = $tempDt->format('d M y');
?>
<p>Tanggal, <?= $dtStartStr ?> - <?= $dtEndStr ?></p>


<table border="1" style="font-size: small;">
    <thead>
        <tr>
            <th>#</th>
            <th>Kode Request</th>
            <th>Member</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Status</th>
            <th>Topup By</th>
            <th>Refused By</th>
            <th>Update Time</th>
        </tr>
    </thead>
    <tbody>
        <?php

        use App\Controllers\CTools;

        $CTools = new CTools();

        $i = 1;
        foreach ($dtReqTopup as $q) { ?>
            <tr>
                <td style="width: auto; padding-left: 10px; padding-right: 10px;"><?= $i++ ?></td>
                <td><?= $q->kodereq ?></td>
                <td><?= $q->username ?></td>
                <td style="text-align: right;"><?= number_format($q->amount, 2, ',', '.'); ?></td>
                <td><?php $xTgl = new Datetime($q->tanggal);
                    echo $xTgl->format('Y-M-d') . '<br>';
                    echo $xTgl->format('H:i:s');
                    ?></td>
                <td><?= $q->status == 5 ? 'Success' : ($q->status == 8 ? 'Refused' : 'Request')  ?></td>
                <td><?= ($q->status == 5 ? $CTools->getUsername($q->updateby) : "") ?></td>
                <td><?= ($q->status == 8 ? $CTools->getUsername($q->updateby) : "") ?></td>
                <td><?php if ($q->status == 5 ||  $q->status == 8) {
                        $xTgl = new Datetime($q->updatedate);
                        echo $xTgl->format('Y-M-d') . '<br>';
                        echo $xTgl->format('H:i:s');
                    } ?></td>

            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <?php
        $Amount = array_sum(array_column($dtReqTopup, 'amount'));
        ?>
        <tr>
            <th class="text-center" colspan="3">Total</th>
            <th style="text-align: right;"><?= number_format($Amount, 2, ',', '.'); ?></th>
            <th colspan="6"></th>
        </tr>
    </tfoot>
</table>