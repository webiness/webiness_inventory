<?php
$lang = WsLocalize::getLang();
setlocale(LC_ALL, $lang,
    $lang.'_'.strtoupper($lang),
    $lang.'_'.strtoupper($lang).'.utf8'
);

$cur_date = strftime("%x");
?>

<br/>
<br/>

<div class="row">
    <div class="column column-10 column-offset-1">

        <div class="row grid-header">
            <div class="row">
                <div class="column column-6 text-left text-error">
                    <div class="grid-title">
                        <?php
                            echo WsLocalize::msg('Inventory Summary')
                                .WsLocalize::msg(' on ').$cur_date;
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <table class="grid">
            <thead>
                <tr class="ws_tr">
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('barcode'); ?>
                    </th>
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('product name'); ?>
                    </th>
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('position'); ?>
                    </th>
                    <th class="ws_th text-right">
                        <?php echo WsLocalize::msg('purchased'); ?>
                    </th>
                    <th class="ws_th text-right">
                        <?php echo WsLocalize::msg('sold'); ?>
                    </th>
                    <th class="ws_th text-right">
                        <?php echo WsLocalize::msg('dismission'); ?>
                    </th>
                    <th class="ws_th text-right">
                        <?php echo WsLocalize::msg('minimum'); ?>
                    </th>
                    <th class="ws_th text-right">
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

                    echo '<tr class="ws_tr">';
                    echo '<td class="ws_td">'.$product['barcode'].'</td>';
                    echo '<td class="ws_td">'.$product['name'].'</td>';
                    echo '<td class="ws_td">'.$product['pos'].'</td>';

                    if ($purchase == intval($purchase)) {
                        echo '<td class="ws_td text-right">'
                            .number_format($purchase, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="ws_td text-right">'
                            .number_format($purchase, 3, ',', '.').'</td>';
                    }

                    if ($sale == intval($sale)) {
                        echo '<td class="ws_td text-right">'
                            .number_format($sale, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="ws_td text-right">'
                            .number_format($sale, 3, ',', '.').'</td>';
                    }

                    if ($dismission == intval($dismission)) {
                        echo '<td class="ws_td text-right">'
                            .number_format($dismission, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="ws_td text-right">'
                            .number_format($dismission, 3, ',', '.').'</td>';
                    }

                    if ($min_qnty == intval($min_qnty)) {
                        echo '<td class="ws_td text-right">'
                            .number_format($min_qnty, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="ws_td text-right">'
                            .number_format($min_qnty, 3, ',', '.').'</td>';
                    }

                    if ($total == intval($total)) {
                        echo '<td class="ws_td text-right"><strong>'
                            .number_format($total, 0, ',', '.')
                            .'</strong>'
                            .' '.$product['uom'].'</td>';
                    } else {
                        echo '<td class="ws_td text-right"><strong>'
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
