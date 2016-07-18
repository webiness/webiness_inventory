<?php

class Item_categoryModel extends WsModel
{
    public function __construct()
    {
        parent::__construct();

        $this->metaName = WsLocalize::msg('Categories of items');

        $this->columnHeaders = array(
            'id' => 'ID',
            'category_name' => WsLocalize::msg('category name'),
            'description' => WsLocalize::msg('category description'),
            'vat' => WsLocalize::msg('VAT rate (%)'),
            'consumption_tax' => WsLocalize::msg('consumption tax rate (%)'),
            'sales_tax' => WsLocalize::msg('sales tax rate (%)')
        );

        $this->columnType['vat'] = 'numeric_type';
        $this->columnType['consumption_tax'] = 'numeric_type';
        $this->columnType['sales_tax'] = 'numeric_type';
    }
}
