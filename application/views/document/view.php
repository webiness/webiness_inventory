<style>
    #document_title {
        font-size: 18px;
        text-align: center;
        font-weight: bold;
        padding-top: 20px;
    }
</style>

<div class="row">
    <div class="column column-10 column-offset-1">
        <?php
            if ($error !== '') {
                echo '<div class="callout error">';
                echo $error;
                echo '</div>';
            } else {
        ?>
        <table>
            <tr class="no-print">
                <td colspan=2 style="text-align: right;">
                    <input type="button" class="error" onclick="window.close()"
                        value="<?php echo WsLocalize::msg('close window'); ?>"/>
                </td>
            </tr>
            <tr>
                <td colspan=2>
                    <hr/>
                </td>
            </tr>
            <tr>
                <td valign="top">
                <?php
                    echo $company_model->company_name.'<br/>';
                    echo $company_model->address1.'<br/>';
                    if ($company_model->address2 !== null) {
                        echo $company_model->address2.'<br/>';
                    }
                    echo $company_model->zip.' '.$company_model->city.'<br/>';
                    echo $company_model->country.'<br/>';
                    echo $company_model->email.'<br/>';
                    echo $company_model->web.'<br/>';
                    echo '<br/>';
                    if ($company_model->id_number !== null) {
                        echo WsLocalize::msg('ID number: ')
                            .$company_model->id_number.'<br/>';
                    }
                    if ($company_model->tax_number !== null) {
                        echo WsLocalize::msg('tax number: ')
                        .$company_model->tax_number.'<br/>';
                    }
                    if ($company_model->iban !== null) {
                        echo WsLocalize::msg('IBAN: ')
                        .$company_model->iban.'<br/>';
                    }
                ?>
                </td>
                <td valign="top" style="text-align: right;">
                <?php
                    echo $partner_model->partner_name.'<br/>';
                    echo $partner_model->address1.'<br/>';
                    if ($partner_model->address2 !== null) {
                        echo $partner_model->address2.'<br/>';
                    }
                    echo $partner_model->region_state.'<br/>';
                    echo $partner_model->zip.' '.$partner_model->city.'<br/>';
                    echo $partner_model->country.'<br/>';
                    echo $partner_model->phone_number.'<br/>';
                    echo $partner_model->email.'<br/>';
                    echo $partner_model->web.'<br/>';
                    echo '<br/>';
                    if ($partner_model->id_number !== null) {
                        echo WsLocalize::msg('ID number: ')
                            .$partner_model->id_number.'<br/>';
                    }
                    if ($partner_model->tax_number !== null) {
                        echo WsLocalize::msg('tax number: ')
                            .$partner_model->tax_number.'<br/>';
                    }
                    if ($partner_model->iban !== null) {
                        echo WsLocalize::msg('IBAN: ')
                            .$partner_model->iban.'<br/>';
                    }
                ?>
                </td>
            </tr>

            <tr>
                <td id="document_title" colspan="2">
                <?php

                    if ($document_model->d_type == 'purchase') {
                        echo WsLocalize::msg('purchase document no: ').$id;
                    } else {
                        echo WsLocalize::msg('sale document no: ').$id;
                    }

                    if ($document_model->d_status == 'draft') {
                        echo ' (draft) ';
                    }

                    echo '<br/><br/>';
                ?>
                </td>
            </tr>

            <tr>
                <td colspan=2>
                <?php
                    $lang = WsLocalize::getLang();
                    setlocale(LC_ALL, $lang,
                        $lang.'_'.strtoupper($lang),
                        $lang.'_'.strtoupper($lang).'.utf8');

                    echo WsLocalize::msg('creation date: ');
                    echo strftime('%x', strtotime($document_model->d_date));
                    echo '<br/>';
                    echo WsLocalize::msg('the author of the document: ');
                    echo $document_model->d_user;
                ?>
                </td>
            </tr>
        </table>

        <br/>

        <table class="grid">
            <thead>
                <tr class="ws_tr">
                    <th class="ws_th">#</th>
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('product name'); ?>
                    </th>
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('price'); ?>
                    </th>
                    <?php
                    if ($document_model->d_type === 'sale') {
                    ?>
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('vat'); ?>
                    </th>
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('consumption tax'); ?>
                    </th>
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('sales tax'); ?>
                    </th>
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('sale price'); ?>
                    </th>
                    <?php
                    }
                    ?>
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('quantity'); ?>
                    </th>
                    <th class="ws_th">
                        <?php echo WsLocalize::msg('total'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                $product_no = 1;
                $total_price = 0;
                $total_vat = 0;
                $total_consumption_tax = 0;
                $total_sales_tax = 0;
                $total_total = 0;
                
                foreach ($products as $product) {
                    echo '<tr class="ws_tr">';
                    echo '<td class="ws_td">'.$product_no.'</td>';
                    echo '<td class="ws_td">'.$product['product'].'</td>';
                    // single price
                    if ($document_model->d_type === 'sale') {
                        $margin = ($product['price'] * $product['margin'])/100;
                        $price = round($product['price'] + $margin, 2);
                    } else {
                        $price = round($product['price'], 2);
                    }
                    echo '<td class="ws_td">';
                    echo number_format($price, 2, ',', '.');
                    echo '</td>';
                    $total_price += ($price * $product['quantity']);
                    
                    if ($document_model->d_type === 'sale') {
                        // vat
                        $vat = round(($price * $product['vat']) / 100, 2);
                        $total_vat += $vat * $product['quantity'];
                        echo '<td class="ws_td">';
                        echo number_format($vat, 2, ',', '.');
                        echo '</td>';
                        // consumption tax
                        $cons_tax =round(
                            ($price * $product['consumption_tax']) / 100, 2);
                        $total_consumption_tax+=$cons_tax*$product['quantity'];
                        echo '<td class="ws_td">';
                        echo number_format($cons_tax, 2, ',', '.');
                        echo '</td>';
                        // sales tax
                        $sales_tax =round(
                            ($price * $product['sales_tax']) / 100, 2);
                        $total_sales_tax += $sales_tax * $product['quantity'];
                        echo '<td class="ws_td">';
                        echo number_format($sales_tax, 2, ',', '.');
                        echo '</td>';
                        
                        $sale_price = ($price+$vat+$cons_tax+$sales_tax);
                        // sale price
                        echo '<td class="ws_td">';
                        echo number_format($sale_price, 2, ',', '.');
                        echo '</td>';
                    } else {
                        $sale_price = $product['price'];
                    }
                    
                    $total = $sale_price * $product['quantity'];
                    $total_total += $total;
                    
                    // quantity
                    echo '<td class="ws_td">';
                    echo number_format($product['quantity'], 2, ',', '.');
                    echo ' '.$product['uom'].'</td>';
                    echo '</td>';
                    // total price
                    echo '<td class="ws_td">';
                    echo number_format($total, 2, ',', '.');
                    echo '</td>';
                    echo '</tr>';
                    
                    $product_no += 1;
                }
                ?>
            </tbody>
        </table>
        
        <br/>
        
        <table class="text-right">
        <?php
        if ($document_model->d_type === 'sale') {
        ?>
            <tr>
                <td style="width: 100%"></td>
                <td style="white-space: nowrap;">
                    <?php echo WsLocalize::msg('tax base:'); ?>
                </td>
                <td style="white-space: nowrap; font-weight: bold;">
                    <?php echo number_format($total_price, 2, ',', '.'); ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100%"></td>
                <td style="white-space: nowrap;">
                    <?php echo WsLocalize::msg('vat:'); ?>
                </td>
                <td style="white-space: nowrap; font-weight: bold;">
                    <?php echo number_format($total_vat, 2, ',', '.'); ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100%"></td>
                <td style="white-space: nowrap;">
                    <?php echo WsLocalize::msg('consumption tax:'); ?>
                </td>
                <td style="white-space: nowrap; font-weight: bold;">
                    <?php
                        echo number_format($total_consumption_tax, 2, ',', '.');
                    ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100%"></td>
                <td style="white-space: nowrap;">
                    <?php echo WsLocalize::msg('sales tax:'); ?>
                </td>
                <td style="white-space: nowrap; font-weight: bold;">
                    <?php
                        echo number_format($total_sales_tax, 2, ',', '.');
                    ?>
                </td>
            </tr>
            <tr>
                <td style="width: 100%"></td>
                <td style="white-space: nowrap;">
                    <?php echo WsLocalize::msg('total:'); ?>
                </td>
                <td style="white-space: nowrap; font-weight: bold;">
                    <?php
                        echo number_format($total_total, 2, ',', '.');
                    ?>
                </td>
            </tr>
            <?php
            }   
            ?>
            <tr>
                <td style="width: 100%"></td>
                <td style="white-space: nowrap;">
                    <?php
                        echo WsLocalize::msg('discount ');
                        echo '('.$document_model->discount.'%):'; 
                    ?>
                </td>
                <td style="white-space: nowrap; font-weight: bold;">
                    <?php
                        $discount=($total_total*$document_model->discount)/100;
                        echo number_format($discount, 2, ',', '.');
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan=3><hr/></td>
            </tr>
            <tr>
                <td style="width: 100%"></td>
                <td style="white-space: nowrap; font-weight: bold;">
                    <?php
                        echo WsLocalize::msg('total:'); 
                    ?>
                </td>
                <td style="white-space: nowrap; font-weight: bold;">
                    <?php
                        $grand_total = $total_total - $discount;
                        echo number_format($grand_total, 2, ',', '.');
                    ?>
                </td>
            </tr>
        </table>

        <?php
        }
        ?>
    </div>
</div>
