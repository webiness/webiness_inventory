<?php
    $purchase = $totals['purchase'];
    $sale = $totals['sale'];
    $dismission = $totals['dismission'];
    $purchase_draft = $totals['purchase_draft'];
    $sale_draft = $totals['sale_draft'];
    $dismission_draft = $totals['dismission_draft'];
    $quantitymin = floatval($product_model->quantitymin);

    // set locale for date and time representation
    $lang = substr(
        filter_input(
            INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE'
        ), 0,2
    );
    setlocale(LC_ALL, $lang,
        $lang.'_'.strtoupper($lang),
        $lang.'_'.strtoupper($lang).'.utf8'
    );
 ?>
<br/>
<br/>

<div class="row">
    <div class="column column-10 column-offset-1">
        <h2 class="text-primary">
            <?php
                echo $product_model->product_name
                    .' ('.$product_model->category_id.')';
            ?>
        </h2>
    </div>
</div>

<br/>

<div class="row">
    <div class="column column-6 column-offset-1">
        <?php
        if (file_exists(WsROOT.'/runtime/ItemModel/'.$product_model->picture)) {
        ?>
        <img style="float: left; margin: 10px;"
            src="<?php
                echo WsSERVER_ROOT.'/runtime/ItemModel/'.$product_model->picture;
            ?>"/>
        <?php
        }
        ?>
        <p class="text-left">
            <?php echo $product_model->description; ?>
            <br/>
            <br/>
            <?php echo $product_model->declaration; ?>
        </p>
    </div>

    <div class="column column-4 text-right no-print">
        <input type="button" class="error" onclick="window.close()"
            value="<?php echo WsLocalize::msg('close window'); ?>"/>
    </div>
</div>

<br/>

<div class="row">
    <div class="column column-6 column-offset-1">
        <strong><?php echo WsLocalize::msg('Product position: '); ?></strong>
        <?php echo $product_model->pos; ?>
    </div>
</div>

<br/>

<div class="row">
    <div class="column column-3 column-offset-1">
        <strong><?php echo WsLocalize::msg('Minimum quantity: '); ?></strong>
        <?php echo $product_model->quantitymin.' '.$product_model->uom; ?>
    </div>

    <div class="column column-3">
        <strong><?php echo WsLocalize::msg('Current quantity: '); ?></strong>
        <?php
            $total = $purchase - $sale - $dismission;
            if ($total > $quantitymin) {
                $class = 'text-success';
            } else if ($total == $quantitymin) {
                $class = 'text-primary';
            } else {
                $class = 'text-error';
            }
        ?>
        <span class="<?php echo $class; ?>">
            <?php echo $total.' '.$product_model->uom; ?>
        </span>
    </div>
</div>

<br/>
<div class="row">
    <div class="column column-3 column-offset-1">
        <strong><?php echo WsLocalize::msg('Purchased: '); ?></strong>
        <?php
            if ($purchase == intval($purchase)) {
                echo number_format($purchase, 0, ',', '.');
            } else {
                echo number_format($purchase, 3, ',', '.');
            }
            echo ' '.$product_model->uom;
        ?>
    </div>

    <div class="column column-3">
        <strong><?php echo WsLocalize::msg('Sold: '); ?></strong>
        <?php
            if ($sale == intval($sale)) {
                echo number_format($sale, 0, ',', '.');
            } else {
                echo number_format($sale, 3, ',', '.');
            }
            echo ' '.$product_model->uom;
        ?>
    </div>

    <div class="column column-3">
        <strong><?php echo WsLocalize::msg('Dismission: '); ?></strong>
        <?php
            if ($dismission == intval($dismission)) {
                echo number_format($dismission, 0, ',', '.');
            } else {
                echo number_format($dismission, 3, ',', '.');
            }
            echo ' '.$product_model->uom;
        ?>
    </div>
</div>

<br/>
<div class="row">
    <div class="column column-3 column-offset-1">
        <strong><?php echo WsLocalize::msg('Ordered: '); ?></strong>
        <?php
            if ($purchase_draft == intval($purchase_draft)) {
                echo number_format($purchase_draft, 0, ',', '.');
            } else {
                echo number_format($purchase_draft, 3, ',', '.');
            }
            echo ' '.$product_model->uom;
        ?>
    </div>

    <div class="column column-3">
        <strong><?php echo WsLocalize::msg('Proposed for sale: '); ?></strong>
        <?php
            if ($sale_draft == intval($sale_draft)) {
                echo number_format($sale_draft, 0, ',', '.');
            } else {
                echo number_format($sale_draft, 3, ',', '.');
            }
            echo ' '.$product_model->uom;
        ?>
    </div>

    <div class="column column-3">
        <strong><?php echo WsLocalize::msg('Proposed for dismission: '); ?></strong>
        <?php
            if ($dismission_draft == intval($dismission_draft)) {
                echo number_format($dismission_draft, 0, ',', '.');
            } else {
                echo number_format($dismission_draft, 3, ',', '.');
            }
            echo ' '.$product_model->uom;
        ?>
    </div>
</div>

<br/>
<br class="page-break"/>
<div class="row">
    <div class="column column-10 column-offset-1">

        <div class="row grid-header">
            <div class="row">
                <div class="column column-6 text-left text-error">
                    <div class="grid-title">
                        <?php
                            echo WsLocalize::msg('Purchases')
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <table class="grid">
            <thead>
                <tr class="ws_tr">
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('document'); ?>
                    </th>
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('date'); ?>
                    </th>
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('partner'); ?>
                    </th>
                    <th class="ws_th text-right">
                        <?php echo WsLocalize::msg('discount'); ?>
                    </th>
                    <th class="ws_th text-right">
                        <?php echo WsLocalize::msg('quantity'); ?>
                    </th>
            </thead>
            <tbody>
            <?php
                foreach ($product_purchase as $i) {
                    $discount = floatval($i['discount']);
                    $quantity = floatval($i['quantity']);
                    $date = strftime('%x', strtotime($i['document_date']));

                    echo '<tr class="ws_tr">';
                    echo '<td class="ws_td">'.$i['document_id'].'</td>';
                    echo '<td class="ws_td">'.$date.'</td>';
                    echo '<td class="ws_td">'.$i['partner_name'].'</td>';

                    if ($discount == intval($discount)) {
                        echo '<td class="ws_td text-right">'
                            .number_format($discount, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="ws_td text-right">'
                            .number_format($discount, 3, ',', '.').'</td>';
                    }

                    if ($quantity == intval($quantity)) {
                        echo '<td class="ws_td text-right">'
                            .number_format($quantity, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="ws_td text-right">'
                            .number_format($quantity, 3, ',', '.').'</td>';
                    }

                    echo '</tr>';
                }
            ?>
            </tbody>
        </table>
    </div>
</div>

<br class="page-break"/>
<div class="row">
    <div class="column column-10 column-offset-1">

        <div class="row grid-header">
            <div class="row">
                <div class="column column-6 text-left text-error">
                    <div class="grid-title">
                        <?php
                            echo WsLocalize::msg('Sales')
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <table class="grid">
            <thead>
                <tr class="ws_tr">
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('document'); ?>
                    </th>
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('date'); ?>
                    </th>
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('partner'); ?>
                    </th>
                    <th class="ws_th text-right">
                        <?php echo WsLocalize::msg('discount'); ?>
                    </th>
                    <th class="ws_th text-right">
                        <?php echo WsLocalize::msg('quantity'); ?>
                    </th>
            </thead>
            <tbody>
            <?php
                foreach ($product_sale as $i) {
                    $discount = floatval($i['discount']);
                    $quantity = floatval($i['quantity']);
                    $date = strftime('%x', strtotime($i['document_date']));

                    echo '<tr class="ws_tr">';
                    echo '<td class="ws_td">'.$i['document_id'].'</td>';
                    echo '<td class="ws_td">'.$date.'</td>';
                    echo '<td class="ws_td">'.$i['partner_name'].'</td>';

                    if ($discount == intval($discount)) {
                        echo '<td class="ws_td text-right">'
                            .number_format($discount, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="ws_td text-right">'
                            .number_format($discount, 3, ',', '.').'</td>';
                    }

                    if ($quantity == intval($quantity)) {
                        echo '<td class="ws_td text-right">'
                            .number_format($quantity, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="ws_td text-right">'
                            .number_format($quantity, 3, ',', '.').'</td>';
                    }

                    echo '</tr>';
                }
            ?>
            </tbody>
        </table>
    </div>
</div>
