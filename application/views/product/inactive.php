<?php
$InactiveGrid = new WsModelGridView($model);
?>

<div class="uk-grid">
    <div class="uk-width-small-1-1 uk-width-medium-3-4 uk-container-center">
        <div class="uk-alert uk-alert-info">
<?php echo WsLocalize::msg('In most cases you will not be able to remove products directly because thei refernces to the documents.'); ?>
<?php echo WsLocalize::msg(' Add products that are not longer available in stock to this list,'); ?>
<?php echo WsLocalize::msg(' so they will be no longer offered in new documents,') ?>
<?php echo WsLocalize::msg(' nor they will be shown in inventory list, for that matter.'); ?>
        </div>
    </div>
</div>

<div class="uk-grid">
    <div class="uk-width-1-1">
        <?php $InactiveGrid->show(); ?>
    </div>
</div>
