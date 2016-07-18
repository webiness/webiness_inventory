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

        $this->title = WsLocalize::msg(' - dashboard');
        $this->render('index');
    }


    public function company()
    {
        $company_model = new CompanyModel();

        if($company_model->idExists(1)) {
            $company_model->getOne(1);
        }

        $this->title = WsLocalize::msg(' - my company');
        $this->render('company', array(
            'company_model' => $company_model,
        ));
    }
}
