<?php
$CategoriesGrid = new WsModelGridView($PCModel);
$CategoriesGrid->itemsPerPage = 5;
?>

<div class="uk-grid">
    <div class="uk-width-1-1">
        <?php $CategoriesGrid->show(); ?>
    </div>
</div>
