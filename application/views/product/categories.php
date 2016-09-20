<?php
$CategoriesGrid = new WsModelGridView($PCModel);
$CategoriesGrid->itemsPerPage = 5;
?>

<div class="row">
    <div class="col-sm-12 col-md-10 col-md-offset-1">
        <?php $CategoriesGrid->show(); ?>
    </div>
</div>
