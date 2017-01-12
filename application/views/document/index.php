<?php
$DocumentGrid = new WsModelGridView($document_model, '',
    WsUrl::link('document', 'edit'), WsUrl::link('document', 'delete'));
?>

<div class="uk-grid">
    <div class="uk-width-small-1-1 uk-width-medium-1-1">
        <?php $DocumentGrid->show(); ?>
    </div>
</div>
