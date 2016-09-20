<div class="row">
    <div class="col-sm-12 col-md-10 col-md-offset-1">
        <h3 class="text-center">
            <?php
            echo Wslocalize::msg('Item no: ').$id;
            ?>
        </h3>

        <div class="row">
            <div class="col-sm-12 col-md-2 col-md-offset-10">
                <a class="btn btn-info" target="_blank"
                    href="<?php echo WsUrl::link('product', 'card', array(
                        'id' => $id)); ?>">
                    <?php echo WsLocalize::msg('Product Card'); ?>
                </a>
            </div>
        </div>
        
        <br/>
        
        <form id="item_form"
            class="form-horizontal"
            action="<?php echo WsUrl::link('product', 'edit'); ?>"
            enctype="multipart/form-data"
            method="post">

            <div class="form-group">
                <label for="item_name" class="col-sm-2 control-label">
                    <?php echo WsLocalize::msg('product name'); ?>
                </label>
                <input type="hidden" name="csrf"
                    value="<? echo $_SESSION['ws_auth_token']; ?>"/>
                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                <div class="col-sm-4">
                    <input type="text" name="product_name" class="form-control"
                        value="<?php echo $product_name; ?>"/>
                </div>

                <label for="barcode" class="col-sm-2 control-label">
                    <?php echo WsLocalize::msg('barcode'); ?>
                </label>
                <div class="col-sm-4">
                    <input type="text" name="barcode" class="form-control"
                        value="<?php echo $barcode; ?>"/>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description" class="col-sm-2 control-label">
                    <?php echo WsLocalize::msg('description'); ?>
                </label>
                <input type="hidden" name="csrf"
                    value="<? echo $_SESSION['ws_auth_token']; ?>"/>
                <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                <div class="col-sm-4">
                    <textarea class="form-control" name="description"><?php echo $description; ?></textarea>
                </div>

                <label for="declaration" class="col-sm-2 control-label">
                    <?php echo WsLocalize::msg('declaration'); ?>
                </label>
                <div class="col-sm-4">
                    <textarea class="form-control" name="declaration"><?php echo $declaration; ?></textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="picture" class="col-sm-2 control-label">
                    <?php echo WsLocalize::msg('product image'); ?>
                </label>
                <div class="col-sm-4">
                    <input type="file" id="picture" name="picture"/>
                </div>
                <div class="col-sm-4">
                    <?php
                        $file = 'runtime/ProductModel/'.$picture;
                        $file_url = WsSERVER_ROOT.'/runtime/ProductModel/'.$picture;
                        if (file_exists(WsROOT.'/'.$file) && is_file(WsROOT.'/'.$file)) {
                            // if file is image then show it
                            $img = new WsImage();
                            if ($img->read($file)) {
                                echo '<img width=100 height=100 src="'
                                    .$file_url.'" />';
                            } else {
                                echo '<a href="';
                                echo WsUrl::link(WsSERVER_ROOT.'/'.$file_url);
                                echo '">';
                                echo $value;
                                echo '</a>';
                            }
                            unset ($img, $file, $file_url);
                        }
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="position" class="col-sm-2 control-label">
                    <?php echo WsLocalize::msg('position'); ?>
                </label>
                <div class="col-sm-4">
                    <input type="text" name="pos" class="form-control"
                        value="<?php echo $pos; ?>"/>
                </div>
                
                <label for="category_id" class="col-sm-2 control-label" >
                    <?php echo WsLocalize::msg('item category'); ?>
                </label>
                <div class="col-sm-4">
                    <select name="category_id" class="webiness_select"
                        id="category_id"
                        style="width: 100%">
                        <?php
                        foreach ($all_product_categories as $category) {
                            if ($category['category_name'] == $category_id) {
                                echo '<option value="'.$category['id']
                                        .'" selected>';
                            } else {
                                echo '<option value="'.$category['id'].'">';
                            }
                            echo $category['category_name'];
                            echo '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="quantitymin" class="col-sm-2 control-label">
                    <?php echo WsLocalize::msg('minimum quantity'); ?>
                </label>
                <div class="col-sm-4">
                    <input type="text" name="quantitymin"
                        class="webiness_numericinput form-control"
                        value="<?php echo $quantitymin; ?>"/>
                </div>

                <label for="uom" class="col-sm-2 control-label">
                    <?php echo WsLocalize::msg('unit of measurement'); ?>
                </label>
                <div class="col-sm-4">
                    <input type="text" name="uom" class="form-control"
                        value="<?php echo $uom; ?>"/>
                </div>
            </div>

            <div class="form-group">
                <label for="purchase_price" class="col-sm-2 control-label">
                    <?php echo WsLocalize::msg('purchase price'); ?>
                </label>
                <div class="col-sm-4">
                    <input type="text" name="purchase_price" id="purchase_price"
                        class="webiness_numericinput form-control"
                        value="<?php echo $purchase_price; ?>"/>
                </div>

                <label for="trading_margin" class="col-sm-2 control-label">
                    <?php echo WsLocalize::msg('trading margin (%)'); ?>
                </label>
                <div class="col-sm-4">
                    <input type="text" name="trading_margin" id="trading_margin"
                        class="webiness_numericinput form-control"
                        value="<?php echo $trading_margin; ?>"/>
                </div>
            </div>

            <div class="form-group">
                <label class="text-primary col-sm-2 control-label"
                    for="tax_base">
                    <?php echo WsLocalize::msg('tax base'); ?>
                </label>
                <div class="col-sm-4">
                    <input type="text" name="tax_base" id="tax_base"
                        class="webiness_numericinput form-control" readonly />
                </div>
            </div>

            <div class="form-group">
                <label for="vat" class="col-sm-3 control-label">
                    <?php echo WsLocalize::msg('vat rate (%)'); ?>
                </label>
                <div class="col-sm-1">
                    <input type="text" name="vat" id="vat"
                        class="webiness_numericinput form-control"
                        readonly />
                </div>

                <label for="consumption_tax" class="col-sm-3 control-label">
                    <?php
                        echo WsLocalize::msg('consumption tax rate (%)');
                    ?>
                </label>
                <div class="col-sm-1">
                    <input type="text" name="consumption_tax"
                        id="consumption_tax"
                        class="webiness_numericinput form-control"
                        readonly />
                </div>

                <label for="sales_tax" class="col-sm-3 control-label">
                    <?php echo WsLocalize::msg('sales tax rate (%)'); ?>
                </label>
                <div class="col-sm-1">
                    <input type="text" name="sales_tax" id="sales_tax"
                        class="webiness_numericinput form-control"
                        readonly />
                </div>
            </div>
            
            <div class="form-group">
                <label class="text-primary col-sm-2 control-label" for="price">
                    <?php echo WsLocalize::msg('sell price'); ?>
                </label>
                <div class="col-sm-4">    
                    <input type="text" name="price" id="price" readonly
                        class="webiness_numericinput form-control"/>
                </div>
            </div>

            <button type="submit" class="btn btn-success">
                <?php echo WsLocalize::msg('Save'); ?>
            </button>
        </form>

    </div>
</div>

<script type="text/javascript">
    function get_tax_rates() {
        var result = "";
        $.get("<?php echo WsUrl::link('product', 'tax_rates'); ?>", {
            category_id: $("#category_id").val()
        }, function (data) {
            var result = JSON.parse(JSON.stringify(data));
            $("#vat").val(result.vat);
            $("#consumption_tax").val(result.consumption_tax);
            $("#sales_tax").val(result.sales_tax);
            calculate_price();
        }, "json")
    }

    function calculate_price() {
        var purchase_price = parseFloat($("#purchase_price").val());
        var trading_margin = parseFloat($("#trading_margin").val());
        var vat = parseFloat($("#vat").val());
        var consumption_tax = parseFloat($("#consumption_tax").val());
        var sales_tax = parseFloat($("#sales_tax").val());

        if (isNaN(purchase_price))
            purchase_price = 1;
        if (isNaN(trading_margin))
            trading_margin = 0;
        if (isNaN(vat))
            vat = 0;
        if (isNaN(consumption_tax))
            consumption_tax = 0;
        if (isNaN(sales_tax))
            sales_tax = 0;

        var tm_value = purchase_price + (purchase_price * trading_margin) / 100;
        $("#tax_base").val(tm_value.toFixed(2));

        var vat_value = (tm_value * vat) / 100;
        var ct_value = (tm_value * consumption_tax) / 100;
        var st_value = (tm_value * sales_tax) / 100;

        var price = tm_value + vat_value + ct_value + st_value;

        $("#price").val(price.toFixed(2));
    }

    $("#category_id").on('change', function() {
        get_tax_rates();
    });
    $("#purchase_price").on('change keydown paste input', function(){
        get_tax_rates();
    });
    $("#trading_margin").on('change keydown paste input', function(){
        get_tax_rates();
    });

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
    $("#item_form").submit(function(event) {
        var fd = new FormData();

        var $inputs = $( "#item_form :input");
        $inputs.each(function() {
            if (this.type == "file") {
                var file = $(this).prop('files')[0];
                fd.append(this.name, file);
            } else {
                fd.append(this.name, $(this).val());
            }
        });

        $.ajax({
            type: "POST",
            url: "<?php echo WsUrl::link('product', 'edit'); ?>",
            contentType: false,
            processData: false,
            data: fd,
            error: function (request, status, error) {
                alert(request.responseText);
            },
        }).done(function(result) {
            window.setTimeout(function(){location.reload();},100);
        }).fail(function() {
            alert("Sorry. Server unavailable.");
        });

        event.preventDefault();

        return false;
    });

    (function() {
        $(".webiness_select").select2();
        get_tax_rates();
    })();
</script>
