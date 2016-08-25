<?php

class PartnersController extends WsController
{
    public function index()
    {
        $auth = new WsAuth();
        // redirect to login page if no user is loged in
        if (!$auth->checkSession()) {
            $this->redirect('wsauth', 'login');
        }
        unset ($auth);
            
        $this->title = WsLocalize::msg(' - Manage Partners');

        $PartnerModel = new PartnerModel();

        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('partners') => array(
                'partners',
                'index'
            )
        );

        $this->render('index', array(
            'PartnerModel' => $PartnerModel,
        ));
    }
}
