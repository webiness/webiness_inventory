<?php

class ProductController extends WsController
{
    public function categories()
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
            WsLocalize::msg('product categories') => array(
                'product',
                'categories'
            ),
        );
        // title
        $this->title = WsConfig::get('app_name')
            .WsLocalize::msg(' - Product Categories');
            
        $PCModel = new Product_categoryModel();

        $this->render('categories', array(
            'PCModel' => $PCModel,
        ));
    }


    public function products()
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
            WsLocalize::msg('products') => array(
                'product',
                'products'
            ),
        );
        // title
        $this->title = WsConfig::get('app_name')
            .WsLocalize::msg(' - Manage Products');
            
        $ProductModel = new ProductModel();

        $this->render('products', array(
            'ProductModel' => $ProductModel,
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
        $product_model = new ProductModel();

        if ((isset($_POST['id']) && !empty($_POST['id']))
            && (!isset($_POST['product_name']) && empty($_POST['product_name']))) {

            $id = intval(filter_input(INPUT_POST, 'id',
                FILTER_SANITIZE_NUMBER_INT));
            // hide default layout if form is action is called from
            // WsModelGridView
            $this->layout = 'nonexisting_layout';
        } else if ((isset($_POST['id']) && !empty($_POST['id']))
            && (isset($_POST['product_name']) && !empty($_POST['product_name']))) {

            // save item image
            foreach ($_FILES as $file) {
                $fileName = $file['name'];
                $fileTmp = $file['tmp_name'];
                $destDir = WsROOT.'/runtime/'.$product_model->className;

                $field = key($_FILES);

                // files are upload to "runtime" directory create destination directory
                // if not exist
                if (!file_exists($destDir)) {
                    mkdir($destDir, 0777, true);
                }

                // allowed file size is 3MB
                if ($file['size'] > 3145728) {
                    continue;
                }

                // remove old file with same name
                if (file_exists($destDir.'/'.$fileName)) {
                    unlink($destDir.'/'.$fileName);
                }

                // upload file
                move_uploaded_file($fileTmp, $destDir.'/'.$fileName);
                $product_model->$field = $fileName;
            }

            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $barcode = filter_input(INPUT_POST, 'barcode',
                FILTER_SANITIZE_STRING);
            $product_name = filter_input(INPUT_POST, 'product_name',
                FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description',
                FILTER_SANITIZE_STRING);
            $declaration = filter_input(INPUT_POST, 'declaration',
                FILTER_SANITIZE_STRING);
            $pos = filter_input(INPUT_POST, 'pos',
                FILTER_SANITIZE_STRING);
            $category_id = filter_input(INPUT_POST, 'category_id',
                FILTER_SANITIZE_NUMBER_INT);
            $quantitymin = filter_input(INPUT_POST, 'quantitymin',
                FILTER_SANITIZE_NUMBER_FLOAT);
            $uom = filter_input(INPUT_POST, 'uom', FILTER_SANITIZE_STRING);
            $purchase_price = filter_input(INPUT_POST, 'purchase_price',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $trading_margin = filter_input(INPUT_POST, 'trading_margin',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

            // save item
            $product_model->id = $id;
            $product_model->barcode = $barcode;
            $product_model->product_name = $product_name;
            $product_model->description = $description;
            $product_model->declaration = $declaration;
            $product_model->pos = $pos;
            $product_model->category_id = $category_id;
            $product_model->quantitymin = $quantitymin;
            $product_model->uom = $uom;
            $product_model->purchase_price = $purchase_price;
            $product_model->trading_margin = $trading_margin;
            $product_model->save();
        }

        if (isset($id) and $id == 0) {
            $this->layout = 'nonexisting_layout';
        }

        if (isset($id) and $id != 0) {
            if ($product_model->idExists($id)) {
                $product_model->getOne($id);

                // load existing item from model
                $id = $product_model->id;
                $barcode = $product_model->barcode;
                $product_name = $product_model->product_name;
                $description = $product_model->description;
                $declaration = $product_model->declaration;
                $picture = $product_model->picture;
                $pos = $product_model->pos;
                $category_id = $product_model->category_id;
                $quantitymin = $product_model->quantitymin;
                $uom = $product_model->uom;
                $purchase_price = $product_model->purchase_price;
                $trading_margin = $product_model->trading_margin;
            } else {
                // set default values
                $id = $product_model->getNextId();
                $barcode = '';
                $product_name = '';
                $description = '';
                $declaration = '';
                $picture = '';
                $pos = '';
                $category_id = '';
                $quantitymin = 0;
                $uom = '';
                $purchase_price = 0;
                $trading_margin = 0;
            }
        } else {
            // set default values
            $id = $product_model->getNextId();
            $barcode = '';
            $product_name = '';
            $description = '';
            $declaration = '';
            $picture = '';
            $pos = '';
            $category_id = '';
            $quantitymin = 0;
            $uom = '';
            $purchase_price = 0;
            $trading_margin = 0;
        }

        $all_product_categories =
            $db->query('SELECT id, category_name FROM product_category');

        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('inventory') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('products') => array(
                'product',
                'products'
            ),
            WsLocalize::msg('product_'.$id) => array(
                'product',
                'edit'
            ),
        );
        // title
        $this->title = WsConfig::get('app_name')
            .WsLocalize::msg(' - Item ').$id;
        
        $this->render('edit', array(
            'id' => $id,
            'barcode' => $barcode,
            'product_name' => $product_name,
            'description' => $description,
            'declaration' => $declaration,
            'picture' => $picture,
            'pos' => $pos,
            'category_id' => $category_id,
            'quantitymin' => $quantitymin,
            'uom' => $uom,
            'purchase_price' => $purchase_price,
            'trading_margin' => $trading_margin,
            'all_product_categories' => $all_product_categories
        ));
    }


    public function tax_rates() {
        if (!$this->isAjax()) {
            return;
        }


        if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
            $category_id = filter_input(INPUT_GET, 'category_id',
                FILTER_SANITIZE_NUMBER_INT);

            $db = new WsDatabase();

            $sql = 'SELECT
                vat, consumption_tax, sales_tax
                FROM product_category
                WHERE id='.$category_id;

            $res = $db->query($sql);
            echo json_encode($res[0]);
        } else {
            $res = array(
                'vat' => 0,
                'consumption_tax' => 0,
                'sales_tax' => 0
            );
            echo json_encode($res);
        }
    }


    public function inventory_list()
    {
        $auth = new WsAuth();
        // redirect to login page if no user is loged in
        if (!$auth->checkSession()) {
            $this->redirect('wsauth', 'login');
        }
        unset ($auth);

        $db = new WsDatabase();

        $sql = '
            SELECT
                product.barcode AS barcode,
                product.product_name AS name,
                product.pos AS pos,
                product.quantitymin AS min_qnty,
                product.uom AS uom,
                SUM(document_product.quantity) AS purchase,
                CASE
                    WHEN sale.sale IS NULL THEN 0
                    ELSE sale.sale
                END AS sale,
                CASE
                    WHEN dismission.dismission IS NULL THEN 0
                    ELSE dismission.dismission
                END AS dismission
            FROM
                product
            JOIN document_product ON document_product.product_id = product.id
            JOIN document ON document.id = document_product.document_id
                AND document.d_type = \'purchase\'
                AND document.d_status = \'approved\'
            LEFT JOIN
            (
                SELECT
                    dp.product_id AS id,
                    SUM(dp.quantity) AS sale
                FROM
                    document_product dp,
                    document d
                WHERE dp.document_id = d.id
                    AND d.d_type = \'sale\'
                    AND d.d_status = \'approved\'
                GROUP BY dp.product_id
            ) sale ON sale.id = product.id
            LEFT JOIN
            (
                SELECT
                    dp.product_id AS id,
                    SUM(dp.quantity) AS dismission
                FROM
                    document_product dp,
                    document d
                WHERE dp.document_id = d.id
                    AND d.d_type = \'dismission\'
                    AND d.d_status = \'approved\'
                GROUP BY dp.product_id
            ) dismission ON dismission.id = product.id
            GROUP BY barcode, name,
                pos, min_qnty, uom, sale.sale, dismission.dismission
            ORDER BY pos, name, min_qnty, uom
        ';

        $products = $db->query($sql);

        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('products') => array(
                'product',
                'products'
            ),
            WsLocalize::msg('inventory summary') => array(
                'product',
                'inventory_list'
            ),
        );
        // title
        $this->title = WsConfig::get('app_name')
            .WsLocalize::msg(' - Inventory Summary');
            
        $this->render('inventory_list', array(
            'products' => $products
        ));
    }


    public function card($id=0)
    {
        $auth = new WsAuth();
        // redirect to login page if no user is loged in
        if (!$auth->checkSession()) {
            $this->redirect('wsauth', 'login');
        }
        unset ($auth);

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = filter_input(INPUT_POST, 'id',
                FILTER_SANITIZE_NUMBER_INT);
        } else if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = filter_input(INPUT_GET, 'id',
                FILTER_SANITIZE_NUMBER_INT);
         }

        $db = new WsDatabase();
        $product_model = new ProductModel();

        $product_model->getOne($id);

        $sql = '
            SELECT
                product.id AS id,
                SUM(document_product.quantity) AS purchase,
                CASE
                    WHEN purchase_draft.purchase_draft IS NULL THEN 0
                    ELSE purchase_draft.purchase_draft
                END AS purchase_draft,
                CASE
                    WHEN sale.sale IS NULL THEN 0
                    ELSE sale.sale
                END AS sale,
                CASE
                    WHEN sale_draft.sale_draft IS NULL THEN 0
                    ELSE sale_draft.sale_draft
                END AS sale_draft,
                CASE
                    WHEN dismission.dismission IS NULL THEN 0
                    ELSE dismission.dismission
                END AS dismission,
                CASE
                    WHEN dismission_draft.dismission_draft IS NULL THEN 0
                    ELSE dismission_draft.dismission_draft
                END AS dismission_draft
            FROM
                product
            JOIN document_product ON document_product.product_id = product.id
            JOIN document ON document.id = document_product.document_id
                AND document.d_type = \'purchase\'
                AND document.d_status = \'approved\'
            LEFT JOIN
            (
                SELECT
                    dp.product_id AS id,
                    SUM(dp.quantity) AS purchase_draft
                FROM
                    document_product dp,
                    document d
                WHERE dp.document_id = d.id
                    AND d.d_type = \'purchase\'
                    AND d.d_status = \'draft\'
                GROUP BY dp.product_id
            ) purchase_draft ON purchase_draft.id = product.id
            LEFT JOIN
            (
                SELECT
                    dp.product_id AS id,
                    SUM(dp.quantity) AS sale
                FROM
                    document_product dp,
                    document d
                WHERE dp.document_id = d.id
                    AND d.d_type = \'sale\'
                    AND d.d_status = \'approved\'
                GROUP BY dp.product_id
            ) sale ON sale.id = product.id
            LEFT JOIN
            (
                SELECT
                    dp.product_id AS id,
                    SUM(dp.quantity) AS sale_draft
                FROM
                    document_product dp,
                    document d
                WHERE dp.document_id = d.id
                    AND d.d_type = \'sale\'
                    AND d.d_status = \'draft\'
                GROUP BY dp.product_id
            ) sale_draft ON sale_draft.id = product.id
            LEFT JOIN
            (
                SELECT
                    dp.product_id AS id,
                    SUM(dp.quantity) AS dismission
                FROM
                    document_product dp,
                    document d
                WHERE dp.document_id = d.id
                    AND d.d_type = \'dismission\'
                    AND d.d_status = \'approved\'
                GROUP BY dp.product_id
            ) dismission ON dismission.id = product.id
            LEFT JOIN
            (
                SELECT
                    dp.product_id AS id,
                    SUM(dp.quantity) AS dismission_draft
                FROM
                    document_product dp,
                    document d
                WHERE dp.document_id = d.id
                    AND d.d_type = \'dismission\'
                    AND d.d_status = \'draft\'
                GROUP BY dp.product_id
            ) dismission_draft ON dismission_draft.id = product.id
            WHERE product.id = :id
            GROUP BY product.id,
                purchase_draft.purchase_draft,
                sale.sale, sale_draft,
                dismission.dismission, dismission_draft.dismission_draft
        ';
        $res = $db->query($sql, array('id' => intval($id)));
        if ($db->nRows == 0) {
            $totals = array(
                'purchase' => 0,
                'sale' => 0,
                'dismission' => 0,
                'purchase_draft' => 0,
                'sale_draft' => 0,
                'dismission_draft' => 0,
            );
        } else {
            $totals = array(
                'purchase' => floatval($res[0]['purchase']),
                'sale' => floatval($res[0]['sale']),
                'dismission' => floatval($res[0]['dismission']),
                'purchase_draft' => floatval($res[0]['purchase_draft']),
                'sale_draft' => floatval($res[0]['sale_draft']),
                'dismission_draft' => floatval($res[0]['dismission_draft']),
            );
        }
        unset($res);

        $sql = '
            SELECT
            	d.id AS document_id,
                d.d_date AS document_date,
                p.partner_name AS partner_name,
                d.discount AS discount,
                SUM(dp.quantity) AS quantity
            FROM
            	document d,
                partner p,
                document_product dp
            WHERE dp.product_id = :id
            	AND dp.document_id = d.id
                AND d.d_partner = p.id
                AND d.d_type = \'purchase\'
            GROUP BY d.id, d.d_date, p.partner_name, d.discount
            ORDER BY d.d_date
        ';
        $product_purchase = $db->query($sql, array('id' => intval($id)));

        $sql = '
            SELECT
            	d.id AS document_id,
                d.d_date AS document_date,
                p.partner_name AS partner_name,
                d.discount AS discount,
                SUM(dp.quantity) AS quantity
            FROM
            	document d,
                partner p,
                document_product dp
            WHERE dp.product_id = :id
            	AND dp.document_id = d.id
                AND d.d_partner = p.id
                AND d.d_status = \'approved\'
                AND d.d_type = \'sale\'
            GROUP BY d.id, d.d_date, p.partner_name, d.discount
            ORDER BY d.d_date
        ';
        $product_sale = $db->query($sql, array('id' => intval($id)));
        
        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('products') => array(
                'product',
                'products'
            ),
            $product_model->product_name => array(
                'product',
                'card'
            ),
        );
        // title
        $this->title = WsConfig::get('app_name')
            .' -  '.$product_model->product_name;
            
        $this->render('card', array(
            'product_model' => $product_model,
            'totals' => $totals,
            'product_purchase' => $product_purchase,
            'product_sale' => $product_sale,
        ));
    }

}
