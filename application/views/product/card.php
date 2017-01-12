<?php
    $purchase = $totals['purchase'];
    $sale = $totals['sale'];
    $dismission = $totals['dismission'];
    $purchase_draft = $totals['purchase_draft'];
    $sale_draft = $totals['sale_draft'];
    $dismission_draft = $totals['dismission_draft'];
    $quantitymin = floatval($product_model->quantitymin);

    // set locale for date and time representation
    $lang = WsLocalize::getLang();
    setlocale(LC_ALL, $lang,
        $lang.'_'.strtoupper($lang),
        $lang.'_'.strtoupper($lang).'.utf8'
    );
 ?>

<div class="uk-grid">
    <div class="uk-width-1-1">
        <h1 class="uk-text-center">
            <?php
                echo $product_model->product_name
                    .' ('.$product_model->category_id.')';
            ?>
        </h1>
    </div>
</div>

<div class="uk-grid">
    <div class="uk-width-small-1-1 uk-width-medium-4-5">
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
        <?php echo $product_model->description; ?>
        <br/>
        <br/>
        <?php echo $product_model->declaration; ?>
    </div>

    <div class="uk-width-small-1-1 uk-width-medium-1-5">
        <input type="button" class="uk-button uk-button-danger uk-text-right"
            onclick="window.close()"
            value="<?php echo WsLocalize::msg('close window'); ?>"/>
    </div>
</div>


<div class="uk-grid">
    <div class="col-sm-12 col-md-6 col-md-offset-1">
        <strong><?php echo WsLocalize::msg('Product position: '); ?></strong>
        <?php echo $product_model->pos; ?>
    </div>
</div>

<div class="uk-grid uk-grid-small">
    <div class="uk-width-small-1-1 uk-width-medium-1-2">
        <strong><?php echo WsLocalize::msg('Minimum quantity: '); ?></strong>
        <?php echo $product_model->quantitymin.' '.$product_model->uom; ?>
    </div>

    <div class="uk-width-small-1-1 uk-width-medium-1-2">
        <strong><?php echo WsLocalize::msg('Current quantity: '); ?></strong>
        <?php
            $total = $purchase - $sale - $dismission;
            if ($total > $quantitymin) {
                $class = 'uk-text-success';
            } else if ($total == $quantitymin) {
                $class = 'uk-text-warning';
            } else {
                $class = 'uk-text-danger';
            }
        ?>
        <span class="<?php echo $class; ?>">
            <?php echo $total.' '.$product_model->uom; ?>
        </span>
    </div>
</div>

<div class="uk-grid uk-grid-small">
    <div class="uk-width-small-1-1 uk-width-medium-1-3">
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

    <div class="uk-width-small-1-1 uk-width-medium-1-3">
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

    <div class="uk-width-small-1-1 uk-width-medium-1-3">
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

<div class="uk-grid uk-grid-small">
    <div class="uk-width-small-1-1 uk-width-medium-1-3">
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

    <div class="uk-width-small-1-1 uk-width-medium-1-3">
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

    <div class="uk-width-small-1-1 uk-width-medium-1-3">
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

<br class="page-break"/>
<div class="uk-grid">
    <div class="uk-width-1-1">
        <h3>
            <?php
                echo WsLocalize::msg('Purchases')
            ?>
        </h3>

        <div class="uk-responsive-container">
        <table class="uk-table uk-table-hover uk-table-striped">
            <thead>
                <tr>
                    <th>
                        <?php echo WsLocalize::msg('document'); ?>
                    </th>
                    <th>
                        <?php echo WsLocalize::msg('date'); ?>
                    </th>
                    <th>
                        <?php echo WsLocalize::msg('partner'); ?>
                    </th>
                    <th class="uk-text-right">
                        <?php echo WsLocalize::msg('discount'); ?>
                    </th>
                    <th class="uk-text-right">
                        <?php echo WsLocalize::msg('quantity'); ?>
                    </th>
            </thead>
            <tbody>
            <?php
                foreach ($product_purchase as $i) {
                    $discount = floatval($i['discount']);
                    $quantity = floatval($i['quantity']);
                    $date = strftime('%x', strtotime($i['document_date']));

                    echo '<tr>';
                    echo '<td>'.$i['document_id'].'</td>';
                    echo '<td>'.$date.'</td>';
                    echo '<td>'.$i['partner_name'].'</td>';

                    if ($discount == intval($discount)) {
                        echo '<td class="uk-text-right">'
                            .number_format($discount, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="uk-text-right">'
                            .number_format($discount, 3, ',', '.').'</td>';
                    }

                    if ($quantity == intval($quantity)) {
                        echo '<td class="uk-text-right">'
                            .number_format($quantity, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="uk-text-right">'
                            .number_format($quantity, 3, ',', '.').'</td>';
                    }

                    echo '</tr>';
                }
            ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<br class="page-break"/>
<div class="uk-grid">
    <div class="uk-width-1-1">
        <h3>
            <?php
                echo WsLocalize::msg('Sales')
            ?>
        </h3>

        <div class="uk-responsive-container">
        <table class="uk-table uk-table-hover uk-table-striped">
            <thead>
                <tr>
                    <th>
                        <?php echo WsLocalize::msg('document'); ?>
                    </th>
                    <th>
                        <?php echo WsLocalize::msg('date'); ?>
                    </th>
                    <th>
                        <?php echo WsLocalize::msg('partner'); ?>
                    </th>
                    <th class="text-right">
                        <?php echo WsLocalize::msg('discount'); ?>
                    </th>
                    <th class="text-right">
                        <?php echo WsLocalize::msg('quantity'); ?>
                    </th>
            </thead>
            <tbody>
            <?php
                foreach ($product_sale as $i) {
                    $discount = floatval($i['discount']);
                    $quantity = floatval($i['quantity']);
                    $date = strftime('%x', strtotime($i['document_date']));

                    echo '<tr>';
                    echo '<td>'.$i['document_id'].'</td>';
                    echo '<td>'.$date.'</td>';
                    echo '<td>'.$i['partner_name'].'</td>';

                    if ($discount == intval($discount)) {
                        echo '<td class="uk-text-right">'
                            .number_format($discount, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="uk-text-right">'
                            .number_format($discount, 3, ',', '.').'</td>';
                    }

                    if ($quantity == intval($quantity)) {
                        echo '<td class="uk-text-right">'
                            .number_format($quantity, 0, ',', '.').'</td>';
                    } else {
                        echo '<td class="uk-text-right">'
                            .number_format($quantity, 3, ',', '.').'</td>';
                    }

                    echo '</tr>';
                }
            ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
