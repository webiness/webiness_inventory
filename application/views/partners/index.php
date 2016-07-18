<?php
$PartnerGrid = new WsModelGridView($PartnerModel);
?>

<br/>
<br/>

<div class="row">
    <div class="column column-10 column-offset-1">
        <?php $PartnerGrid->show(); ?>
    </div>
</div>
