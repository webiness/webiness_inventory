<?php
$lang = WsLocalize::getLang();
setlocale(LC_ALL, $lang,
    $lang.'_'.strtoupper($lang),
    $lang.'_'.strtoupper($lang).'.utf8'
);

$cur_date = strftime("%x");
?>

<div class="uk-modal" id="add_product"></div>

<div class="uk-grid">
    <div class="uk-width-1-1">
    
        <h1>
            <?php
                echo WsLocalize::msg('Inventory Summary')
                    .WsLocalize::msg(' on ').$cur_date;
            ?>
        </h1>

        <div class="uk-container-responsive">
        <table class="uk-table uk-table-hover uk-table-striped">
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
                    <th class="uk-text-right">
                        <?php echo WsLocalize::msg('purchased'); ?>
                    </th>
                    <th class="uk-text-right">
                        <?php echo WsLocalize::msg('sold'); ?>
                    </th>
                    <th class="uk-text-right">
                        <?php echo WsLocalize::msg('dismission'); ?>
                    </th>
                    <th class="uk-text-right">
                        <?php echo WsLocalize::msg('minimum'); ?>
                    </th>
                    <th class="uk-text-right">
                        <?php echo WsLocalize::msg('quantity'); ?>
                    </th>
                    <th></th>
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
                        echo '<tr class="uk-text-danger">';
                    } else if ($total == $min_qnty) {
                        echo '<tr class="uk-text-warning">';
                    } else {
                        echo '<tr>';
                    }
                    echo '<td>'.$product['barcode'].'</td>';
                    echo '<td>'.$product['name'].'</td>';
                    echo '<td>'.$product['pos'].'</td>';

                    if ($purchase == intval($purchase)) {
                        echo '<td class="uk-text-right">'
                            .number_format($purchase, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="uk-text-right">'
                            .number_format($purchase, 3, ',', '.').'</td>';
                    }

                    if ($sale == intval($sale)) {
                        echo '<td class="uk-text-right">'
                            .number_format($sale, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="uk-text-right">'
                            .number_format($sale, 3, ',', '.').'</td>';
                    }

                    if ($dismission == intval($dismission)) {
                        echo '<td class="uk-text-right">'
                            .number_format($dismission, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="uk-text-right">'
                            .number_format($dismission, 3, ',', '.').'</td>';
                    }

                    if ($min_qnty == intval($min_qnty)) {
                        echo '<td class="uk-text-right">'
                            .number_format($min_qnty, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="uk-text-right">'
                            .number_format($min_qnty, 3, ',', '.').'</td>';
                    }

                    if ($total == intval($total)) {
                        echo '<td class="uk-text-right"><strong>'
                            .number_format($total, 0, ',', '.')
                            .'</strong>'
                            .' '.$product['uom'].'</td>';
                    } else {
                        echo '<td class="uk-text-right"><strong>'
                            .number_format($total, 3, ',', '.')
                            .'</strong>'
                            .' '.$product['uom'].'</td>';
                    }

                    if ($total < $min_qnty) {
                        echo '<td>'
                            .'<a class="uk-button uk-button-success no-print"'
                                .' data-uk-modal="{target:\'#add_product\''
                                .', center:true}"'
                                .' onclick="addProduct('.$product['id'].')" >'
                                .WsLocalize::msg('order/buy now')
                            .'</a>'
                            .'</td>';
                    } else {
                        echo '<td></td>';
                    }

                    echo '</tr>';
                }
            ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    function addProduct(id)
    {
        $.ajax({
            type: "POST",
            url: "<?php echo WsUrl::link('product', 'add_product')?>",
            data: {
                id: id
            },
            error: function (request, status, error) {
                alert(request.responseText);
            },
            cache: false
        }).done(function(result) {
            $("#add_product").html(result);
        }).fail(function() {
            alert("Sorry. Server unavailable.");
        });

        return false;
    }
</script>
