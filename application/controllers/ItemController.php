<?php

class ItemController extends WsController
{
    public function categories()
    {
        $this->title = WsLocalize::msg(' - Categories of items');
        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('categories of items') => array(
                'item',
                'categories'
            ),
        );

        $ICModel = new Item_categoryModel();

        $this->render('categories', array(
            'ICModel' => $ICModel,
        ));
    }


    public function items()
    {
        $this->title = WsLocalize::msg(' - Items');
        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('stock items') => array(
                'item',
                'items'
            ),
        );

        $ItemModel = new ItemModel();

        $this->render('items', array(
            'ItemModel' => $ItemModel,
        ));
    }


    public function edit($id = 0)
    {
        $db = new WsDatabase();
        $item_model = new ItemModel();

        if ((isset($_POST['id']) && !empty($_POST['id']))
            && (!isset($_POST['item_name']) && empty($_POST['item_name']))) {

            $id = intval(filter_input(INPUT_POST, 'id',
                FILTER_SANITIZE_NUMBER_INT));
            // hide default layout if form is action is called from
            // WsModelGridView
            $this->layout = 'nonexisting_layout';
        } else if ((isset($_POST['id']) && !empty($_POST['id']))
            && (isset($_POST['item_name']) && !empty($_POST['item_name']))) {

            // save item image
            foreach ($_FILES as $file) {
                $fileName = $file['name'];
                $fileTmp = $file['tmp_name'];
                $destDir = WsROOT.'/runtime/'.$item_model->className;

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
                $item_model->$field= $fileName;
            }

            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
            $barcode = filter_input(INPUT_POST, 'barcode',
                FILTER_SANITIZE_STRING);
            $item_name = filter_input(INPUT_POST, 'item_name',
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
                FILTER_SANITIZE_NUMBER_FLOAT);
            $trading_margin = filter_input(INPUT_POST, 'trading_margin',
                FILTER_SANITIZE_NUMBER_FLOAT);

            // save item
            $item_model->id = $id;
            $item_model->barcode = $barcode;
            $item_model->item_name = $item_name;
            $item_model->description = $description;
            $item_model->declaration = $declaration;
            $item_model->pos = $pos;
            $item_model->category_id = $category_id;
            $item_model->quantitymin = $quantitymin;
            $item_model->uom = $uom;
            $item_model->purchase_price = $purchase_price;
            $item_model->trading_margin = $trading_margin;
            $item_model->save();
        }

        if (isset($id) and $id == 0) {
            $this->layout = 'nonexisting_layout';
        }

        if (isset($id) and $id != 0) {
            if ($item_model->idExists($id)) {
                $item_model->getOne($id);

                // load existing item from model
                $id = $item_model->id;
                $barcode = $item_model->barcode;
                $item_name = $item_model->item_name;
                $description = $item_model->description;
                $declaration = $item_model->declaration;
                $picture = $item_model->picture;
                $pos = $item_model->pos;
                $category_id = $item_model->category_id;
                $quantitymin = $item_model->quantitymin;
                $uom = $item_model->uom;
                $purchase_price = $item_model->purchase_price;
                $trading_margin = $item_model->trading_margin;
            } else {
                // set default values
                $id = $item_model->getNextId();
                $barcode = '';
                $item_name = '';
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
            $id = $item_model->getNextId();
            $barcode = '';
            $item_name = '';
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

        $all_item_categories =
            $db->query('SELECT id, category_name FROM item_category');

        $this->title = WsLocalize::msg(' - Item '.$id);
        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('stock items') => array(
                'item',
                'items'
            ),
            WsLocalize::msg('item_'.$id) => array(
                'item',
                'edit'
            ),
        );

        $this->render('edit', array(
            'id' => $id,
            'barcode' => $barcode,
            'item_name' => $item_name,
            'description' => $description,
            'declaration' => $declaration,
            'picture' => $picture,
            'pos' => $pos,
            'category_id' => $category_id,
            'quantitymin' => $quantitymin,
            'uom' => $uom,
            'purchase_price' => $purchase_price,
            'trading_margin' => $trading_margin,
            'all_item_categories' => $all_item_categories
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
                FROM item_category
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
        $db = new WsDatabase();

        $sql = '
            SELECT
                item.barcode AS barcode,
                item.item_name AS name,
                item.pos AS pos,
                item.quantitymin AS min_qnty,
                item.uom AS uom,
                SUM(document_item.quantity) AS entrance,
                CASE
                    WHEN sale.sale IS NULL THEN 0
                    ELSE sale.sale
                END AS sale,
                CASE
                    WHEN issue.issue IS NULL THEN 0
                    ELSE issue.issue
                END AS issue
            FROM
                item
            JOIN document_item ON document_item.item_id = item.id
            JOIN document ON document.id = document_item.document_id
                AND document.d_type = \'entrance\'
                AND document.d_status = \'approved\'
            LEFT JOIN
            (
                SELECT
                    di.item_id AS id,
                    SUM(di.quantity) AS sale
                FROM
                    document_item di,
                    document d
                WHERE di.document_id = d.id
                    AND d.d_type = \'sale\'
                    AND d.d_status = \'approved\'
                GROUP BY di.item_id
            ) sale ON sale.id = item.id
            LEFT JOIN
            (
                SELECT
                    di.item_id AS id,
                    SUM(di.quantity) AS issue
                FROM
                    document_item di,
                    document d
                WHERE di.document_id = d.id
                    AND d.d_type = \'issue\'
                    AND d.d_status = \'approved\'
                GROUP BY di.item_id
            ) issue ON issue.id = item.id
            GROUP BY barcode, name, pos, min_qnty, uom, sale.sale, issue.issue
            ORDER BY pos, name, min_qnty, uom
        ';

        $items = $db->query($sql);

        $this->title = WsLocalize::msg(' - inventory list');
        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('stock items') => array(
                'item',
                'items'
            ),
            WsLocalize::msg('inventory list') => array(
                'item',
                'inventory_list'
            ),
        );
        $this->render('inventory_list', array(
            'items' => $items
        ));
    }


    public function card($id=0)
    {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = filter_input(INPUT_POST, 'id',
                FILTER_SANITIZE_NUMBER_INT);
        }

        $db = new WsDatabase();
        $item_model = new ItemModel();

        $item_model->getOne($id);

        $sql = '
            SELECT
                item.id AS id,
                SUM(document_item.quantity) AS entrance,
                CASE
                    WHEN sale.sale IS NULL THEN 0
                    ELSE sale.sale
                END AS sale,
                CASE
                    WHEN issue.issue IS NULL THEN 0
                    ELSE issue.issue
                END AS issue
            FROM
                item
            JOIN document_item ON document_item.item_id = item.id
            JOIN document ON document.id = document_item.document_id
                AND document.d_type = \'entrance\'
                AND document.d_status = \'approved\'
            LEFT JOIN
            (
                SELECT
                    di.item_id AS id,
                    SUM(di.quantity) AS sale
                FROM
                    document_item di,
                    document d
                WHERE di.document_id = d.id
                    AND d.d_type = \'sale\'
                    AND d.d_status = \'approved\'
                GROUP BY di.item_id
            ) sale ON sale.id = item.id
            LEFT JOIN
            (
                SELECT
                    di.item_id AS id,
                    SUM(di.quantity) AS issue
                FROM
                    document_item di,
                    document d
                WHERE di.document_id = d.id
                    AND d.d_type = \'issue\'
                    AND d.d_status = \'approved\'
                GROUP BY di.item_id
            ) issue ON issue.id = item.id
            WHERE item.id = :id
            GROUP BY item.id, sale.sale, issue.issue
        ';
        $item_qnty = $db->query($sql, array('id' => intval($id)));

        $sql = '
            SELECT
            	d.id AS document_id,
                d.d_date AS document_date,
                p.partner_name AS partner_name,
                d.discount AS discount,
                SUM(di.quantity) AS quantity
            FROM
            	document d,
                partner p,
                document_item di
            WHERE di.item_id = :id
            	AND di.document_id = d.id
                AND d.d_partner = p.id
                AND d.d_status = \'approved\'
                AND d.d_type = \'entrance\'
            GROUP BY d.id, d.d_date, p.partner_name, d.discount
            ORDER BY d.d_date
        ';
        $item_enter = $db->query($sql, array('id' => intval($id)));

        $sql = '
            SELECT
            	d.id AS document_id,
                d.d_date AS document_date,
                p.partner_name AS partner_name,
                d.discount AS discount,
                SUM(di.quantity) AS quantity
            FROM
            	document d,
                partner p,
                document_item di
            WHERE di.item_id = :id
            	AND di.document_id = d.id
                AND d.d_partner = p.id
                AND d.d_status = \'approved\'
                AND d.d_type = \'issue\'
            GROUP BY d.id, d.d_date, p.partner_name, d.discount
            ORDER BY d.d_date
        ';
        $item_issue = $db->query($sql, array('id' => intval($id)));

        $sql = '
            SELECT
            	d.id AS document_id,
                d.d_date AS document_date,
                p.partner_name AS partner_name,
                d.discount AS discount,
                SUM(di.quantity) AS quantity
            FROM
            	document d,
                partner p,
                document_item di
            WHERE di.item_id = :id
            	AND di.document_id = d.id
                AND d.d_partner = p.id
                AND d.d_status = \'approved\'
                AND d.d_type = \'sale\'
            GROUP BY d.id, d.d_date, p.partner_name, d.discount
            ORDER BY d.d_date
        ';
        $item_sale = $db->query($sql, array('id' => intval($id)));

        $this->title = WsLocalize::msg(' - '.$item_model->item_name);
        // breadcrumbs
        $this->breadcrumbs = array(
            WsLocalize::msg('home') => array(
                'site',
                'index'
            ),
            WsLocalize::msg('stock items') => array(
                'item',
                'items'
            ),
            $item_model->item_name => array(
                'item',
                'card'
            ),
        );
        $this->render('card', array(
            'item_model' => $item_model,
            'item_qnty' => $item_qnty[0],
            'item_enter' => $item_enter,
            'item_issue' => $item_issue,
            'item_sale' => $item_sale,
        ));
    }

}
