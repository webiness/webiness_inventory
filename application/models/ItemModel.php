<?php

Class ItemModel extends WsModel
{
    public function __construct()
    {
        parent::__construct();

        $this->metaName = WsLocalize::msg('Stock Items');

        $this->columnHeaders = array(
            'id' => 'ID',
            'barcode' => WsLocalize::msg('item barcode'),
            'item_name' => WsLocalize::msg('name of item'),
            'description' => WsLocalize::msg('description of item'),
            'declaration' => WsLocalize::msg('item declaration'),
            'picture' => WsLocalize::msg('item image'),
            'pos' => WsLocalize::msg('position'),
            'category_id' => WsLocalize::msg('item category'),
            'quantitymin' => WsLocalize::msg('minimum quantity'),
            'uom' => WsLocalize::msg('measuring unit'),
            'purchase_price' => WsLocalize::msg('purchase price'),
            'trading_margin' => WsLocalize::msg('trading margin (%)')
        );

        $this->foreignKeys['category_id']['display'] = 'category_name';

        $this->columnType['picture'] = 'file_type';
        $this->columnType['quantitymin'] = 'numeric_type';
        $this->columnType['purchase_price'] = 'numeric_type';
        $this->columnType['trading_margin'] = 'numeric_type';

        $this->hiddenColumns = array(
            'id',
            'description',
            'declaration',
            'purchase_price',
            'trading_margin'
        );
    }
}
