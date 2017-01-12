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

        $db = new WsDatabase();

        $top_purchases_sql = '
            SELECT
	            p.product_name AS product_name,
                SUM(dp.quantity) AS purchased
            FROM
	            product p,
	            document_product dp,
	            document d
            WHERE dp.product_id = p.id
	            AND dp.document_id = d.id
	            AND d.d_type = \'purchase\'
	            AND d.d_status = \'approved\'
            GROUP BY product_name
            ORDER BY purchased
            LIMIT 10;
        ';
        $top_purchases = $db->query($top_purchases_sql);


        $this->render('index', array(
            'top_purchases' => $top_purchases,
        ));
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
