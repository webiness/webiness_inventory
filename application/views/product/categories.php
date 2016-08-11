<?php
$CategoriesGrid = new WsModelGridView($PCModel);
$CategoriesGrid->itemsPerPage = 5;
?>

<br/>
<br/>

<div class="row">
    <div class="column column-10 column-offset-1">
        <?php $CategoriesGrid->show(); ?>
    </div>
</div>
