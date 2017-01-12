<div class="uk-grid">
    <div class="uk-width-small-1-1 uk-width-medium-1-4">
        <?php
            $top_purchases_chart = new WsChart(100, 100);
            $top_purchases_chart->setType('bar');

            $dataset = array();

            foreach ($top_purchases as $purchases) {
                $top_purchases_chart->addLabels($purchases['product_name']);
                array_push($dataset, $purchases['purchased']);
            }

            $top_purchases_chart->addDataset($dataset,
                WsLocalize::msg('quantity'));
            $top_purchases_chart->show();
        ?>
    </div>
</div>
