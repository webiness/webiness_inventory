<?php
$PartnerGrid = new WsModelGridView($PartnerModel);
?>

<div class="uk-grid">
    <div class="uk-width-small-1-1">
        <?php $PartnerGrid->show(); ?>
    </div>
</div>
