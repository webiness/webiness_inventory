<?php
$ProductsGrid = new WsModelGridView($ProductModel, '',
    WsUrl::link('product', 'edit'));
?>

<div class="uk-grid">
    <div class="uk-width-1-1">
        <?php $ProductsGrid->show(); ?>
    </div>
</div>
