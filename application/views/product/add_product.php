<div class="uk-modal-dialog">
    <a class="uk-modal-close uk-close"></a>
    <div class="uk-modal-header">
        <div class="uk-grid">
            <div class="uk-width-small-1-1 uk-width-medium-1-2">
                <h3 class="uk-text-left">
                    <?php
                    echo Wslocalize::msg('Document no: ').$doc_id;
                    ?>
                </h3>
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
            <input type="hidden" name="id" value="<?php echo $doc_id; ?>"/>

            <div class="uk-form-row">
                <label for="d_date" class="uk-form-label">
                    <?php echo WsLocalize::msg('document date'); ?>
                </label>
                <div class="uk-form-controls">
                    <input type="date" name="d_date" maxlength=11
                        class="webiness_datepicker uk-width-1-1"
                        value="<?php echo date('Y-m-d'); ?>" />
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
                                echo '<option value="'.$partner['id'].'">';
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
                        <option value="purchase" selected>
<?php echo WsLocalize::msg('purchase'); ?></option>
                    </select>
                </div>
                <div class="uk-width-1-2">
                    <label for="d_status" class="uk-form-label">
                        <?php echo WsLocalize::msg('document status'); ?>
                    </label>
                    <select name="d_status" class="uk-width-1-1">
                        <option value="draft">
<?php echo WsLocalize::msg('draft'); ?></option>
                        <option value="approved">
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
                        value="0"/>
                </div>
            </div>

            <hr/>

            <div class="uk-grid">
                <div class="uk-width-1-1">
                    <div class="uk-overflow-container">
                    <table id="dpTable"
                        class="uk-table uk-table-hover uk-table-condensed">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo WsLocalize::msg('product'); ?>
                                </th>
                                <th>
                                    <?php echo WsLocalize::msg('quantity'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="uk-width-6-10">
                                    <input type="text" class="uk-width-1-1"
                                        name="it_s_not_important" disabled
                                        value="<?php echo $product; ?>"/>
                                    <input type="hidden" name="DP_product[]"
                                        value="<?php echo $id; ?>"/>
                                </td>
                                <td class="uk-width-3-10">
                                    <input type="number" autofocus
                                        class="uk-width-1-1 webiness_numericinput"
                                        name="DP_qnty[]"/>
                                </td>
                            </tr>

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
