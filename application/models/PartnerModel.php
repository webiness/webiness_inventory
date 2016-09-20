<?php

class PartnerModel extends WsModel
{
    public function __construct()
    {
        parent::__construct();

        $this->metaName = WsLocalize::msg('Bussiness Partners (supplier and custumers)');

        $this->columnHeaders = array(
            'id' => 'ID',
            'partner_name' => WsLocalize::msg('name of partner'),
            'logo' => WsLocalize::msg('partner logo'),
            'id_number' => WsLocalize::msg('identification number'),
            'tax_number' => WsLocalize::msg('tax number'),
            'iban' => WsLocalize::msg('international bank account number'),
            'address1' => WsLocalize::msg('address'),
            'address2' => WsLocalize::msg('address (line 2)'),
            'region_state' => WsLocalize::msg('region/state'),
            'zip' => WsLocalize::msg('postal code'),
            'city' => WsLocalize::msg('city'),
            'country' => WsLocalize::msg('country'),
            'email' => WsLocalize::msg('contact email address'),
            'web' => WsLocalize::msg('partners web address'),
            'phone_number' => WsLocalize::msg('phone number')
        );

        $this->columnType['logo'] = 'file_type';
        $this->columnType['email'] = 'mail_type';
        $this->columnType['web'] = 'url_type';
    }
}

