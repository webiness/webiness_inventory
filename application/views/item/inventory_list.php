<?php
$lang = substr(filter_input(
    INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING), 0,2);
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
                            echo WsLocalize::msg('Inventory List')
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
                        <?php echo WsLocalize::msg('item name'); ?>
                    </th>
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('position'); ?>
                    </th>
                    <th class="ws_th text-right">
                        <?php echo WsLocalize::msg('entered'); ?>
                    </th>
                    <th class="ws_th text-right">
                        <?php echo WsLocalize::msg('issued'); ?>
                    </th>
                    <th class="ws_th text-right">
                        <?php echo WsLocalize::msg('sold'); ?>
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
                foreach ($items as $item) {
                    $entrance = floatval($item['entrance']);
                    $issue = floatval($item['issue']);
                    $sale = floatval($item['sale']);
                    $min_qnty = floatval($item['min_qnty']);

                    $total = $entrance - $issue - $sale;

                    echo '<tr class="ws_tr">';
                    echo '<td class="ws_td">'.$item['barcode'].'</td>';
                    echo '<td class="ws_td">'.$item['name'].'</td>';
                    echo '<td class="ws_td">'.$item['pos'].'</td>';

                    if ($entrance == intval($entrance)) {
                        echo '<td class="ws_td text-right">'
                            .number_format($entrance, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="ws_td text-right">'
                            .number_format($entrance, 3, ',', '.').'</td>';
                    }

                    if ($issue == intval($issue)) {
                        echo '<td class="ws_td text-right">'
                            .number_format($issue, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="ws_td text-right">'
                            .number_format($issue, 3, ',', '.').'</td>';
                    }

                    if ($sale == intval($sale)) {
                        echo '<td class="ws_td text-right">'
                            .number_format($sale, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="ws_td text-right">'
                            .number_format($sale, 3, ',', '.').'</td>';
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
                            .' '.$item['uom'].'</td>';
                    } else {
                        echo '<td class="ws_td text-right"><strong>'
                            .number_format($total, 3, ',', '.')
                            .'</strong>'
                            .' '.$item['uom'].'</td>';
                    }

                    echo '</tr>';
                }
            ?>
            </tbody>
        </table>
    </div>
</div>
