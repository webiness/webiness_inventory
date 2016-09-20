<?php

class SiteController extends WsController {

    /**
     * index
     *
    */
    public function index()
    {
        $auth = new WsAuth();
        // redirect to login page if no user is loged in
        if (!$auth->checkSession()) {
            $this->redirect('wsauth', 'login');
        }
        unset ($auth);
        
        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
        );
        // title
        $this->title = WsConfig::get('app_name')
            .WsLocalize::msg(' - Dashboard');
            
        $this->render('index');
    }


    public function company()
    {
        $auth = new WsAuth();
        // redirect to login page if no user is loged in
        if (!$auth->checkSession()) {
            $this->redirect('wsauth', 'login');
        }
        unset ($auth);
        
        $company_model = new CompanyModel();

        if($company_model->idExists(1)) {
            $company_model->getOne(1);
        }
        
        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('company settings') => array(
                'site',
                'company'
            ),
        );
        // title
        $this->title = WsConfig::get('app_name')
            .WsLocalize::msg(' - Company Settings');
            
        $this->render('company', array(
            'company_model' => $company_model,
        ));
    }
}
