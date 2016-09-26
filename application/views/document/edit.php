<div class="row">
    <div class="col-sm-12 col-md-8 col-md-offset-1">
        <h3 class="text-left">
            <?php
            echo Wslocalize::msg('Document no: ').$id;
            ?>
        </h3>
    </div>
    <div class="col-sm-2 col-md-2 text-right">
        <a class="btn btn-info" target="_blank"
            href="<?php echo WsUrl::link('document', 'view', array(
                'id' => $id)); ?>">
            <?php echo WsLocalize::msg('View Document'); ?>
        </a>
    </div>
</div>

<br/>
<br/>
<div class="row">
    <div class="col-sm-12 col-md-10 col-md-offset-1">
    
        <form id="document_form"
            action="<?php echo WsUrl::link('document', 'edit'); ?>"
            method="post">
        
            <label for="d_date">
                <?php echo WsLocalize::msg('document date'); ?>
            </label>
            <input type="hidden" name="csrf"
                value="<? echo $_SESSION['ws_auth_token']; ?>"/>
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
            <input type="text" name="d_date"
                class="form-control webiness_datepicker" ro
                value="<?php echo $d_date; ?>"/>

            <label for="d_partner">
                <?php echo WsLocalize::msg('partner'); ?>
            </label>
            <select name="d_partner" class="form-control"
                style="width: 100%">
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
            
            <div class="col-sm-12 col-md-6">
                <label for="d_type">
                    <?php echo WsLocalize::msg('document type'); ?>
                </label>
                <select name="d_type" class="form-control"
                    style="width: 100%">
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

            <div class="col-sm-12 col-md-6">
                <label for="d_status">
                    <?php echo WsLocalize::msg('document status'); ?>
                </label>
                <select name="d_status" class="form-control"
                    style="width: 100%">
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

            <label for="d_discount">
                <?php echo WsLocalize::msg('discount'); ?>
            </label>
            <input type="number" name="discount"
                class="form-control webiness_numericinput"
                value="<?php echo $discount; ?>"/>

            <hr/>

            <div class="row">
                <div class="column column-12">
                    <p>
                        <input type="button" onClick="addRow('dpTable')"
                            class="btn btn-success" value="<?php
                            echo WsLocalize::msg('add product'); ?>"/>
                        <input type="button" onClick="removeRow('dpTable')"
                            class="btn btn-danger" value="<?php
                            echo WsLocalize::msg('remove product'); ?>"/>
                    </p>
                    <p class="text-primary">
                        <?php
                        echo WsLocalize::msg('(All acions apply only to entries
                                with check marked check boxes only.)'); ?>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                    <table id="dpTable"
                        class="table table-bordered table-hover table-condensed">
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
                                <td>
                                    <input class="form-control" type="checkbox"
                                        name="chk[]"/>
                                </td>
                                <td>
                                    <select class="form-control"
                                        name="DP_product[]"
                                        style="width: 100%;">
                                        <?php
                                        foreach ($all_products as $product) {
                                            echo '<option value="'
                                                    .$product['id'].'">';
                                            echo $product['product_name'];
                                            echo '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number"
                                        class="form-control webiness_numericinput"
                                        name="DP_qnty[]"/>
                                </td>
                            </tr>
                            <?php
                            for ($x=0; $x < count($DP_product); $x++) {
                            ?>
                            <tr>
                                <td>
                                    <input class="form-control" type="checkbox"
                                        name="chk[]"/>
                                </td>
                                <td>
                                    <select name="DP_product[]"
                                        class="form-control"
                                        style="width: 100%;">
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
                                <td>
                                    <input type="number"
                                        class="form-control webiness_numericinput"
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
                    
            <br/><br/>

                    <button type="submit" class="btn btn-success">
                        <?php echo WsLocalize::msg('Save'); ?>
                    </button>
        </form>
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
