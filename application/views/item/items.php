<?php
$ItemsGrid = new WsModelGridView($ItemModel, '',
    WsUrl::link('item', 'edit'));
?>

<br/>
<br/>

<div class="row">
    <div class="column column-10 column-offset-1">
        <?php $ItemsGrid->show(); ?>
    </div>
</div>
