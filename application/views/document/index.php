<?php
$DocumentGrid = new WsModelGridView($document_model, '',
    WsUrl::link('document', 'edit'), WsUrl::link('document', 'delete'));
?>

<br/>
<br/>

<div class="row">
    <div class="column column-10 column-offset-1">
        <?php $DocumentGrid->show(); ?>
    </div>
</div>
