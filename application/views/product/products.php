<?php
$ProductsGrid = new WsModelGridView($ProductModel, '',
    WsUrl::link('product', 'edit'));
?>

<div class="row">
    <div class="col-sm-12 col-md-10 col-md-offset-1">
        <?php $ProductsGrid->show(); ?>
    </div>
</div>
