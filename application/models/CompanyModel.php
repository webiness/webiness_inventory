<?php

class CompanyModel extends WsModel
{
    public function __construct()
    {
        parent::__construct();

        $this->metaName = WsLocalize::msg('Company Information');

        $this->columnHeaders = array(
            'id' => 'ID',
            'company_name' => WsLocalize::msg('company name'),
            'logo' => WsLocalize::msg('company logo'),
            'id_number' => WsLocalize::msg('identification number'),
            'tax_number' => WsLocalize::msg('tax number'),
            'iban' => WsLocalize::msg('international bank account number'),
            'address1' => WsLocalize::msg('address'),
            'address2' => WsLocalize::msg('address (line 2)'),
            'zip' => WsLocalize::msg('postal code'),
            'city' => WsLocalize::msg('city'),
            'country' => WsLocalize::msg('country'),
            'email' => WsLocalize::msg('contact email address'),
            'web' => WsLocalize::msg('corporate web address')
        );

        $this->columnType['logo'] = 'file_type';
        $this->columnType['email'] = 'email_type';
        $this->columnType['web'] = 'url_type';

    }
}
