<?php
$DocumentGrid = new WsModelGridView($document_model, '',
    WsUrl::link('document', 'edit'), WsUrl::link('document', 'delete'));
?>

<div class="row">
    <div class="col-sm-12 col-md-10 col-md-offset-1">
        <?php $DocumentGrid->show(); ?>
    </div>
</div>
