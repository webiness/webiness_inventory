<?php
$PartnerGrid = new WsModelGridView($PartnerModel);
?>

<div class="row">
    <div class="col-sm-12 col-md-10 col-md-offset-1">
        <?php $PartnerGrid->show(); ?>
    </div>
</div>
