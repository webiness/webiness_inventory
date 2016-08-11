<?php
$ProductsGrid = new WsModelGridView($ProductModel, '',
    WsUrl::link('product', 'edit'));
?>

<br/>
<br/>

<div class="row">
    <div class="column column-10 column-offset-1">
        <?php $ProductsGrid->show(); ?>
    </div>
</div>
