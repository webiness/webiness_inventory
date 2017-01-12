<?php

class Inactive_productModel extends WsModel
{
    public function __construct()
    {
        parent::__construct();

        $this->metaName = WsLocalize::msg('Inactive Products');

        $this->columnHeaders = array(
            'id' => 'ID',
            'product_id' => WsLocalize::msg('product'),
        );

        $this->foreignKeys['product_id']['display'] = 'product_name';
    }
}

