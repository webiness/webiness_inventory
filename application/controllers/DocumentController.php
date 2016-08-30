<?php

class DocumentController extends WsController
{
    public function index()
    {
        $auth = new WsAuth();
        // redirect to login page if no user is loged in
        if (!$auth->checkSession()) {
            $this->redirect('wsauth', 'login');
        }
        unset ($auth);

        $this->title = WsLocalize::msg('Webiness Inventory - Documents');
        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('documents') => array(
                'document',
                'index'
            ),
        );

        $document_model = new DocumentModel();

        $this->render('index', array(
            'document_model' => $document_model
        ));
    }


    public function edit($id = 0)
    {
        $auth = new WsAuth();
        // redirect to login page if no user is loged in
        if (!$auth->checkSession()) {
            $this->redirect('wsauth', 'login');
        }
        unset ($auth);

        $db = new WsDatabase();
        $auth = new WsAuth();

        $document_model = new DocumentModel();

        $lang = substr(filter_input(INPUT_SERVER,
            'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING), 0,2);
        setlocale(LC_ALL, $lang,
            $lang.'_'.strtoupper($lang),
            $lang.'_'.strtoupper($lang).'.utf8');

        // current user
        $d_user = $auth->currentUserID();

        // if form is submited
        if ((isset($_POST['id']) && !empty($_POST['id']))
            && (!isset($_POST['d_date']) && empty($_POST['d_date']))) {
            $id = intval(filter_input(INPUT_POST, 'id',
                FILTER_SANITIZE_NUMBER_INT));
            // hide default layout if form is action is called from
            // WsModelGridView
            $this->layout = 'nonexisting_layout';
        } else if ((isset($_POST['id']) && !empty($_POST))
            && (isset($_POST['d_date']) && !empty($_POST['d_date']))) {
            $id = intval(filter_input(INPUT_POST, 'id',
                FILTER_SANITIZE_NUMBER_INT));
            $d_date = strftime('%x', strtotime(
                filter_input(INPUT_POST, 'd_date', FILTER_SANITIZE_STRING)));
            $d_type = filter_input(INPUT_POST, 'd_type',
                FILTER_SANITIZE_STRING);
            $d_status = filter_input(INPUT_POST, 'd_status',
                FILTER_SANITIZE_STRING);
            $d_partner = filter_input(INPUT_POST, 'd_partner',
                FILTER_SANITIZE_NUMBER_INT);
            $discount = filter_input(INPUT_POST, 'discount',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $DP_product = $_POST['DP_product'];
            $DP_qnty = $_POST['DP_qnty'];

            // save changes to document
            if (count($DP_product) > 0) {
                // remove old items from document
                $db->execute('DELETE FROM document_product WHERE document_id=:id',
                    array('id' => $id));

                // save document
                $document_model->id = $id;
                $document_model->d_date = date('Y-m-d',
                    strtotime($d_date));
                $document_model->d_type = $d_type;
                $document_model->d_status = $d_status;
                $document_model->d_user = $d_user;
                $document_model->d_partner = $d_partner;
                $document_model->discount = $discount;
                $document_model->save();

                // save document items
                for ($x=0; $x < count($DP_product); $x++) {
                    if (floatval($DP_qnty[$x]) > 0) {
                        $db->execute('INSERT INTO document_product'
                            .' (document_id, product_id, quantity) '
                            .' VALUES (:a, :b, :c)',
                            array(
                                'a' => $id,
                                'b' => intval($DP_product[$x]),
                                'c' => floatval($DP_qnty[$x])
                            ));
                    }
                }
            }
        }

        if (isset($id) and $id == 0) {
            $this->layout = 'nonexisting_layout';
        }

        if (isset($id) and $id != 0) {
            if ($document_model->idExists($id)) {
                $document_model->getOne($id);

                $d_date = strftime('%x',
                    strtotime($document_model->d_date));
                $d_type = $document_model->d_type;
                $d_status = $document_model->d_status;
                $d_partner = $document_model->d_partner;
                $discount = $document_model->discount;
                $DP_product = array();
                $DP_qnty = array();

                $products = $db->query(
                    'SELECT product_id, quantity'
                    .' FROM document_product'
                    .' WHERE document_id=:id', array(
                        'id' => $id
                ));
                foreach ($products as $product) {
                    array_push($DP_product, $product['product_id']);
                    array_push($DP_qnty, $product['quantity']);
                }
                unset($products);
            } else {
                // default values for document fields
                $id = $document_model->getNextId();
                $d_date = strftime('%x');
                $d_type = 'entrance';
                $d_status = 'draft';
                $d_user = $auth->currentUserID();
                $d_partner = 0;
                $discount = 0;
                $DP_product = array();
                $DP_qnty = array();
            }
        } else {
            // default values for document fields
            $id = $document_model->getNextId();
            $d_date = strftime('%x');
            $d_type = 'entrance';
            $d_status = 'draft';
            $d_user = $auth->currentUserID();
            $d_partner = 0;
            $discount = 0;
            $DP_product = array();
            $DP_qnty = array();
        }

        // list of all partners
        $all_partners = $db->query('SELECT id, partner_name FROM partner');
        // list of all products
        $all_products = $db->query('SELECT id, product_name FROM product');

        $this->title = WsLocalize::msg('Webiness Inventory - Document '.$id);
        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('documents') => array(
                'document',
                'index'
            ),
            WsLocalize::msg('document_'.$id) => array(
                'document',
                'edit'
            ),
        );

        $this->render('edit', array(
            'id' => $id,
            'd_date' => $d_date,
            'd_type' => $d_type,
            'd_status' => $d_status,
            'd_user' => $d_user,
            'd_partner' => $d_partner,
            'discount' => $discount,
            'DP_product' => $DP_product,
            'DP_qnty' => $DP_qnty,
            'all_partners' => $all_partners,
            'all_products' => $all_products,
        ));
    }


    public function save_document()
    {
        if (!$this->isAjax()) {
            return;
        }

        $db = new WsDatabase();
        $auth = new WsAuth();

        // database connection
        $document_model = new DocumentModel();

        $lang = substr(filter_input(INPUT_SERVER,
            'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_STRING), 0,2);
        setlocale(LC_ALL, $lang,
            $lang.'_'.strtoupper($lang),
            $lang.'_'.strtoupper($lang).'.utf8');

        // current user
        $d_user = $auth->currentUserID();

        if ((isset($_POST['id']) && !empty($_POST))
            && (isset($_POST['d_date']) && !empty($_POST['d_date']))) {
            $id = intval(filter_input(INPUT_POST, 'id',
                FILTER_SANITIZE_NUMBER_INT));
            $d_date = strftime('%x', strtotime(
                filter_input(INPUT_POST, 'd_date', FILTER_SANITIZE_STRING)));
            $d_type = filter_input(INPUT_POST, 'd_type',
                FILTER_SANITIZE_STRING);
            $d_status = filter_input(INPUT_POST, 'd_status',
                FILTER_SANITIZE_STRING);
            $d_partner = filter_input(INPUT_POST, 'd_partner',
                FILTER_SANITIZE_NUMBER_INT);
            $discount = filter_input(INPUT_POST, 'discount',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $DP_product = $_POST['DP_product'];
            $DP_qnty = $_POST['DP_qnty'];

            // save changes to document
            if (count($DP_product) > 0) {
                // remove old items from document
                $db->execute('DELETE FROM document_product WHERE document_id=:id',
                    array('id' => $id));

                // save document
                $document_model->id = $id;
                $document_model->d_date = date('Y-m-d',
                    strtotime($d_date));
                $document_model->d_type = $d_type;
                $document_model->d_status = $d_status;
                $document_model->d_user = $d_user;
                $document_model->d_partner = $d_partner;
                $document_model->discount = $discount;
                $document_model->save();

                // save document items
                for ($x=0; $x < count($DP_product); $x++) {
                    if (floatval($DP_qnty[$x]) > 0) {
                        $db->execute('INSERT INTO document_product'
                            .' (document_id, product_id, quantity) '
                            .' VALUES (:p1, :p2, :p3)',
                            array(
                                'p1' => $id,
                                'p2' => intval($DP_product[$x]),
                                'p3' => floatval($DP_qnty[$x])
                            ));
                    }
                }

                // success
                $this->sendResponse($_POST, 200);
            }
        } else {
            // no data provided
            $this->sendResponse(array(
                'error' => WsLocalize::msg('no document data provided')
            ), 204);
        }
    }


    public function delete($id = 0) {
        $document_model = new DocumentModel();
        $db = new WsDatabase();

        if ((isset($_POST['id']) && !empty($_POST['id']))) {
            $id = filter_input(INPUT_POST, 'id',
                FILTER_SANITIZE_NUMBER_INT);
        } else if (isset($id) and $id != 0) {
            $id = intval($id);
        }

        if ($document_model->idExists($id)) {
            // delete document items
            $sql = 'DELETE FROM document_product WHERE document_id=:id';
            $db->execute($sql, array('id' => $id));
            // delete document
            $sql = 'DELETE FROM document WHERE id=:id';
            $db->execute($sql, array('id' => $id));
            // success
            $this->sendResponse($_POST, 200);
        }  else {
            // no data provided
            $this->sendResponse(array(
                'error' => WsLocalize::msg('no document data provided')
            ), 204);
        }
    }


    public function view($id=0)
    {
        $auth = new WsAuth();
        // redirect to login page if no user is loged in
        if (!$auth->checkSession()) {
            $this->redirect('wsauth', 'login');
        }
        unset ($auth);

        if ((isset($_GET['id']) && !empty($_GET['id']))) {
            $id = filter_input(INPUT_GET, 'id',
                FILTER_SANITIZE_NUMBER_INT);
        } else if ((isset($_POST['id']) && !empty($_POST['id']))) {
            $id = filter_input(INPUT_POST, 'id',
                FILTER_SANITIZE_NUMBER_INT);
        }

        $this->title = WsLocalize::msg('Webiness Inventory - Document ').$id;
        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('documents') => array(
                'document',
                'index'
            ),
            WsLocalize::msg($id) => array(
                'document',
                'view'
            ),
        );

        $error = '';

        $document_model = new DocumentModel();

        if (!$document_model->idExists($id)) {
            $error = WsLocalize::msg('document: ').$id
                .WsLocalize::msg(' does not exist or it is not saved yet!');
        } else {
            $company_model = new CompanyModel();
            $partner_model = new PartnerModel();

            // get document informatiions
            $document_model->foreignKeys['d_partner']['display'] = 'id';
            $document_model->getOne($id);

            // get partner informations
            $partner_model->getOne($document_model->d_partner);

            // get our company informations
            $company_model->getOne(1);
        }

        // get document items
        $sql = '
            SELECT
                p.product_name AS product,
                dp.quantity AS quantity,
                p.uom AS uom,
                p.purchase_price AS price,
                p.trading_margin AS margin,
                pc.vat AS vat,
                pc.consumption_tax AS consumption_tax,
                pc.sales_tax AS sales_tax
            FROM
                document_product dp,
                product p,
                product_category pc
            WHERE dp.document_id = '.$document_model->id.'
                AND dp.product_id = p.id
                AND p.category_id = pc.id
        ';
        $db = new WsDatabase();
        $products = $db->query($sql);
        unset($db);

        $this->render('view', array(
            'error' => $error,
            'id' => $id,
            'document_model' => $document_model,
            'company_model' => $company_model,
            'partner_model' => $partner_model,
            'products' => $products,
        ));
    }
}
