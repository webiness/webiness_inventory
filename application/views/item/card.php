<?php
    $entrance = floatval($item_qnty['entrance']);
    $sale = floatval($item_qnty['sale']);
    $issue = floatval($item_qnty['issue']);
    $quantitymin = floatval($item_model->quantitymin);

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
                echo $item_model->item_name
                    .' ('.$item_model->category_id.')';
            ?>
        </h2>
    </div>
</div>

<br/>

<div class="row">
    <div class="column column-6 column-offset-1">
        <?php
        if (file_exists(WsROOT.'/runtime/ItemModel/'.$item_model->picture)) {
        ?>
        <img style="float: left; margin: 10px;"
            src="<?php
                echo WsSERVER_ROOT.'/runtime/ItemModel/'.$item_model->picture;
            ?>"/>
        <?php
        }
        ?>
        <p class="text-left">
            <?php echo $item_model->description; ?>
            <br/>
            <br/>
            <?php echo $item_model->declaration; ?>
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
        <strong><?php echo WsLocalize::msg('Item position: '); ?></strong>
        <?php echo $item_model->pos; ?>
    </div>
</div>

<br/>

<div class="row">
    <div class="column column-3 column-offset-1">
        <strong><?php echo WsLocalize::msg('Minimum quantity: '); ?></strong>
        <?php echo $item_model->quantitymin.' '.$item_model->uom; ?>
    </div>

    <div class="column column-3">
        <strong><?php echo WsLocalize::msg('Current quantity: '); ?></strong>
        <?php
            $total = $entrance - $sale - $issue;
            if ($total > $quantitymin) {
                $class = 'text-success';
            } else if ($total == $quantitymin) {
                $class = 'text-primary';
            } else {
                $class = 'text-error';
            }
        ?>
        <span class="<?php echo $class; ?>">
            <?php echo $total.' '.$item_model->uom; ?>
        </span>
    </div>
</div>

<br/>

<div class="row">
    <div class="column column-3 column-offset-1">
        <strong><?php echo WsLocalize::msg('Entered: '); ?></strong>
        <?php
            if ($entrance == intval($entrance)) {
                echo number_format($entrance, 0, ',', '.');
            } else {
                echo number_format($entrance, 3, ',', '.');
            }
            echo ' '.$item_model->uom;
        ?>
    </div>

    <div class="column column-3">
        <strong><?php echo WsLocalize::msg('Issued: '); ?></strong>
        <?php
            if ($issue == intval($issue)) {
                echo number_format($issue, 0, ',', '.');
            } else {
                echo number_format($issue, 3, ',', '.');
            }
            echo ' '.$item_model->uom;
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
            echo ' '.$item_model->uom;
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
                            echo WsLocalize::msg('Enter')
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
                foreach ($item_enter as $i) {
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
                            echo WsLocalize::msg('Issue')
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
                foreach ($item_issue as $i) {
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
                            echo WsLocalize::msg('Sale')
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
                foreach ($item_sale as $i) {
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
