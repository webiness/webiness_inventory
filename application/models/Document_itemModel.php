<?php

class Document_itemModel extends WsModel
{
    public function __construct()
    {
        parent::__construct();

        $this->metaName = WsLocalize::msg('Document items');

        $this->columnHeaders = array(
            'id' => 'ID',
            'document_id' => WsLocalize::msg('document'),
            'item_id' => WsLocalize::msg('item'),
            'quantity' => WsLocalize::msg('quantity')
        );

        $this->columnType['quantity'] = 'numeric_type';

        $this->foreignKeys['item_id']['display'] = 'item_name';
    }
}
