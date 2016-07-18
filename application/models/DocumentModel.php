<?php

class DocumentModel extends WsModel
{
    public function __construct()
    {
        parent::__construct();

        $this->metaName = 'Documents';

        $this->columnHeaders = array(
            'id' => 'ID',
            'd_date' => WsLocalize::msg('document date'),
            'd_type' => WsLocalize::msg('document type'),
            'd_status' => WsLocalize::msg('status'),
            'd_user' => WsLocalize::msg('creator of the document'),
            'd_partner' => WsLocalize::msg('partner'),
            'discount' => WsLocalize::msg('discount')
        );

        $this->foreignKeys['d_user']['display'] = 'email';
        $this->foreignKeys['d_partner']['display'] = 'partner_name';

        $this->columnType['discount'] = 'numeric_type';
    }
}
