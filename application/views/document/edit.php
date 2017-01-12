<div class="uk-modal-dialog">
    <a class="uk-modal-close uk-close"></a>
    <div class="uk-modal-header">
        <div class="uk-grid">
            <div class="uk-width-small-1-1 uk-width-medium-1-2">
                <h3 class="uk-text-left">
                    <?php
                    echo Wslocalize::msg('Document no: ').$id;
                    ?>
                </h3>
            </div>
            <div class="uk-width-small-1-1 uk-width-medium-1-2 uk-text-right">
                <a class="uk-button uk-button-primary" target="_blank"
                    href="<?php echo WsUrl::link('document', 'view', array(
                        'id' => $id)); ?>">
                    <?php echo WsLocalize::msg('View Document'); ?>
                </a>
            </div>
        </div>
    </div>

<div class="uk-grid">
    <div class="uk-width-1-1">

        <form id="document_form" class="uk-form uk-form-horizontal"
            action="<?php echo WsUrl::link('document', 'edit'); ?>"
            method="post">

            <input type="hidden" name="csrf"
                value="<? echo $_SESSION['ws_auth_token']; ?>"/>
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>

            <div class="uk-form-row">
                <label for="d_date" class="uk-form-label">
                    <?php echo WsLocalize::msg('document date'); ?>
                </label>
                <div class="uk-form-controls">
                    <input type="date" name="d_date" maxlength=11
                        class="webiness_datepicker uk-width-1-1"
                        value="<?php echo $d_date; ?>" />
                </div>
            </div>

            <div class="uk-form-row">
                <label for="d_partner" class="uk-form-label">
                    <?php echo WsLocalize::msg('partner'); ?>
                </label>
                <div class="uk-form-controls">
                    <select name="d_partner" class="uk-width-1-1">
                        <?php
                            foreach ($all_partners as $partner) {
                                if ($partner['partner_name'] == $d_partner) {
                                    echo '<option value="'.$partner['id']
                                        .'" selected>';
                                } else {
                                    echo '<option value="'.$partner['id'].'">';
                                }
                                echo $partner['partner_name'];
                                echo '</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>

            <div class="uk-grid uk-grid-small">
                <div class="uk-width-1-2">
                    <label for="d_type" class="uk-form-label">
                        <?php echo WsLocalize::msg('document type'); ?>
                    </label>
                    <select name="d_type" class="uk-width-1-1">
                        <option value="purchase"
                            <?php $d_type == 'purchase' ? print ' selected'
                                :false; ?>>
<?php echo WsLocalize::msg('purchase'); ?></option>
                        <option value="sale"
                            <?php $d_type == 'sale' ? print ' selected'
                                :false; ?>>
<?php echo WsLocalize::msg('sale'); ?></option>
                        <option value="dismission"
                            <?php $d_type == 'dismission' ? print ' selected'
                                :false; ?>>
<?php echo WsLocalize::msg('dismission'); ?></option>
                    </select>
                </div>
                <div class="uk-width-1-2">
                    <label for="d_status" class="uk-form-label">
                        <?php echo WsLocalize::msg('document status'); ?>
                    </label>
                    <select name="d_status" class="uk-width-1-1">
                        <option value="draft"
                            <?php
                                $d_status == 'draft' ? print ' selected'
                                    :false; ?>>
<?php echo WsLocalize::msg('draft'); ?></option>
                        <option value="approved"
                            <?php
                                $d_status == 'approved' ? print ' selected'
                                    :false; ?>>
<?php echo WsLocalize::msg('approved'); ?></option>
                    </select>
                </div>
            </div>

            <br/>

            <div class="uk-form-row">
                <label for="d_discount" class="uk-form-label">
                    <?php echo WsLocalize::msg('discount'); ?>
                </label>
                <div class="uk-form-controls">
                    <input type="number" name="discount"
                        class="uk-width-1-1 webiness_numericinput"
                        value="<?php echo $discount; ?>"/>
                </div>
            </div>

            <hr/>

            <div class="uk-grid">
                <div class="uk-width-1-1">
                    <p>
                        <input type="button" onClick="addRow('dpTable')"
                            class="uk-button uk-button-success" value="<?php
                            echo WsLocalize::msg('add product'); ?>"/>
                        <input type="button" onClick="removeRow('dpTable')"
                            class="uk-button uk-button-danger" value="<?php
                            echo WsLocalize::msg('remove product'); ?>"/>
                    </p>
                    <p class="text-primary">
                        <?php
                        echo WsLocalize::msg('(All acions apply only to entries
                                with check marked check boxes only.)'); ?>
                    </p>
                </div>
            </div>

            <div class="uk-grid">
                <div class="uk-width-1-1">
                    <div class="uk-overflow-container">
                    <table id="dpTable"
                        class="uk-table uk-table-hover uk-table-condensed">
                        <thead>
                            <tr>
                                <th>
                                </th>
                                <th>
                                    <?php echo WsLocalize::msg('product'); ?>
                                </th>
                                <th>
                                    <?php echo WsLocalize::msg('quantity'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="display: none">
                                <td class="uk-width-1-10">
                                    <input
                                        type="checkbox"
                                        name="chk[]"/>
                                </td>
                                <td class="uk-width-6-10">
                                    <select
                                        class="uk-width-1-1"
                                        name="DP_product[]">
                                        <?php
                                        foreach ($all_active_products as $product) {
                                            echo '<option value="'
                                                    .$product['id'].'">';
                                            echo $product['product_name'];
                                            echo '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td class="uk-width-3-10">
                                    <input type="number"
                                        class="webiness_numericinput uk-width-1-1"
                                        name="DP_qnty[]"/>
                                </td>
                            </tr>
                            <?php
                            for ($x=0; $x < count($DP_product); $x++) {
                            ?>
                            <tr>
                                <td class="uk-width-1-10">
                                    <input type="checkbox"
                                        name="chk[]"/>
                                </td>
                                <td class="uk-width-6-10">
                                    <select name="DP_product[]"
                                        class="uk-width-1-1">
                                        <?php
                                        foreach ($all_products as $product) {
                                            if ($DP_product[$x] == $product['id']) {
                                                echo '<option selected '
                                                    .'value="'
                                                    .$product['id'].'">';
                                            } else {
                                                echo '<option value="'
                                                    .$product['id'].'">';
                                            }
                                            echo $product['product_name'];
                                            echo '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td class="uk-width-3-10">
                                    <input type="number"
                                        class="uk-width-1-1 webiness_numericinput"
                                        name="DP_qnty[]"
                                        value="<?php
                                            echo $DP_qnty[$x]; ?>"/>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
                    
            <br/><br/>

            <button type="submit" class="uk-button uk-button-success">
                <?php echo WsLocalize::msg('Save'); ?>
            </button>
        </form>
    </div>
</div>
</div>

<script type="text/javascript">
    function addRow(tableID) {
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;

        if (rowCount < 30) {
            var row = table.insertRow(rowCount);
            row.className = "ws_tr";
            row.style = "";
            var colCount = table.rows[1].cells.length;

            for(var i=0; i < colCount; i++) {
                var newcell = row.insertCell(i);
                newcell.className = "ws_td";
                newcell.innerHTML = table.rows[1].cells[i].innerHTML;
            }
        } else {
            alert("Max products per document is 30");
        }
    }

    function removeRow(tableID) {
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length - 1;

        for(var i=1; i <= rowCount; i++) {
            var row = table.rows[i];

            var chkbox = row.cells[0].childNodes[1];
            if (null !== chkbox && true === chkbox.checked) {
                if (rowCount <= 2) {
                alert("Can't remove all products");
                break;
                }
                table.deleteRow(i);
                rowCount--;
                i--;
            }
        }
    }

    $(".webiness_numericinput").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
        // Allow: Ctrl+A, Command+A
        (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return;
        }

        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    // submit document_form function
    $("#document_form").submit(function(event) {
        $.ajax({
            type: "POST",
            url: "<?php echo WsUrl::link('document', 'save_document'); ?>",
            data: $("#document_form").serialize(),
            error: function (request, status, error) {
                alert(request.responseText);
            },
        }).done(function(result) {
            window.setTimeout(function(){location.reload();},100);
        }).fail(function() {
            alert("Sorry. Server unavailable.");
        });

        event.preventDefault();
    });
</script>
