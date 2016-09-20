<?php
$lang = WsLocalize::getLang();
setlocale(LC_ALL, $lang,
    $lang.'_'.strtoupper($lang),
    $lang.'_'.strtoupper($lang).'.utf8'
);

$cur_date = strftime("%x");
?>

<div class="row">
    <div class="col-sm-12 col-md-10 col-md-offset-1">
    
        <h1>
            <?php
                echo WsLocalize::msg('Inventory Summary')
                    .WsLocalize::msg(' on ').$cur_date;
            ?>
        </h1>
        
        <div class="table-responsive">
        <table class="table table-bordered table-hover table-condensed">
            <thead>
                <tr>
                    <th>
                        <?php echo WsLocalize::msg('barcode'); ?>
                    </th>
                    <th>
                        <?php echo WsLocalize::msg('product name'); ?>
                    </th>
                    <th>
                        <?php echo WsLocalize::msg('position'); ?>
                    </th>
                    <th class="text-right">
                        <?php echo WsLocalize::msg('purchased'); ?>
                    </th>
                    <th class="text-right">
                        <?php echo WsLocalize::msg('sold'); ?>
                    </th>
                    <th class="text-right">
                        <?php echo WsLocalize::msg('dismission'); ?>
                    </th>
                    <th class="text-right">
                        <?php echo WsLocalize::msg('minimum'); ?>
                    </th>
                    <th class="text-right">
                        <?php echo WsLocalize::msg('quantity'); ?>
                    </th>
            </thead>
            <tbody>
            <?php
                foreach ($products as $product) {
                    $purchase = floatval($product['purchase']);
                    $sale = floatval($product['sale']);
                    $dismission = floatval($product['dismission']);
                    $min_qnty = floatval($product['min_qnty']);

                    $total = $purchase - $sale - $dismission;
                    
                    if ($total < $min_qnty) {
                        echo '<tr class="danger">';
                    } else if ($total == $min_qnty) {
                        echo '<tr class="warning">';
                    } else {
                        echo '<tr>';
                    }
                    echo '<td>'.$product['barcode'].'</td>';
                    echo '<td>'.$product['name'].'</td>';
                    echo '<td>'.$product['pos'].'</td>';

                    if ($purchase == intval($purchase)) {
                        echo '<td class="text-right">'
                            .number_format($purchase, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="text-right">'
                            .number_format($purchase, 3, ',', '.').'</td>';
                    }

                    if ($sale == intval($sale)) {
                        echo '<td class="text-right">'
                            .number_format($sale, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="text-right">'
                            .number_format($sale, 3, ',', '.').'</td>';
                    }

                    if ($dismission == intval($dismission)) {
                        echo '<td class="text-right">'
                            .number_format($dismission, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="text-right">'
                            .number_format($dismission, 3, ',', '.').'</td>';
                    }

                    if ($min_qnty == intval($min_qnty)) {
                        echo '<td class="text-right">'
                            .number_format($min_qnty, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="text-right">'
                            .number_format($min_qnty, 3, ',', '.').'</td>';
                    }

                    if ($total == intval($total)) {
                        echo '<td class="text-right"><strong>'
                            .number_format($total, 0, ',', '.')
                            .'</strong>'
                            .' '.$product['uom'].'</td>';
                    } else {
                        echo '<td class="text-right"><strong>'
                            .number_format($total, 3, ',', '.')
                            .'</strong>'
                            .' '.$product['uom'].'</td>';
                    }

                    echo '</tr>';
                }
            ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
