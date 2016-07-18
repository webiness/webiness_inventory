<?php

class DocumentController extends WsController
{
    public function index()
    {
        $this->title = WsLocalize::msg(' - Documents');
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
                FILTER_SANITIZE_NUMBER_FLOAT);
            $DI_item = $_POST['DI_item'];
            $DI_qnty = $_POST['DI_qnty'];

            // save changes to document
            if (count($DI_item) > 0) {
                // remove old items from document
                $db->execute('DELETE FROM document_item WHERE document_id=:id',
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
                for ($x=0; $x < count($DI_item); $x++) {
                    if (floatval($DI_qnty[$x]) > 0) {
                        $db->execute('INSERT INTO document_item'
                            .' (document_id, item_id, quantity) '
                            .' VALUES (:a, :b, :c)',
                            array(
                                'a' => $id,
                                'b' => intval($DI_item[$x]),
                                'c' => floatval($DI_qnty[$x])
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
                $DI_item = array();
                $DI_qnty = array();

                $items = $db->query(
                    'SELECT item_id, quantity'
                    .' FROM document_item'
                    .' WHERE document_id=:id', array(
                        'id' => $id
                ));
                foreach ($items as $item) {
                    array_push($DI_item, $item['item_id']);
                    array_push($DI_qnty, $item['quantity']);
                }
                unset($items);
            } else {
                // default values for document fields
                $id = $document_model->getNextId();
                $d_date = strftime('%x');
                $d_type = 'entrance';
                $d_status = 'draft';
                $d_user = $auth->currentUserID();
                $d_partner = 0;
                $discount = 0;
                $DI_item = array();
                $DI_qnty = array();
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
            $DI_item = array();
            $DI_qnty = array();
        }

        // list of all partners
        $all_partners = $db->query('SELECT id, partner_name FROM partner');
        // list of all items
        $all_items = $db->query('SELECT id, item_name FROM item');

        $this->title = WsLocalize::msg(' - Document '.$id);
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
            'DI_item' => $DI_item,
            'DI_qnty' => $DI_qnty,
            'all_partners' => $all_partners,
            'all_items' => $all_items,
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
                FILTER_SANITIZE_NUMBER_FLOAT);
            $DI_item = $_POST['DI_item'];
            $DI_qnty = $_POST['DI_qnty'];

            // save changes to document
            if (count($DI_item) > 0) {
                // remove old items from document
                $db->execute('DELETE FROM document_item WHERE document_id=:id',
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
                for ($x=0; $x < count($DI_item); $x++) {
                    if (floatval($DI_qnty[$x]) > 0) {
                        $db->execute('INSERT INTO document_item'
                            .' (document_id, item_id, quantity) '
                            .' VALUES (:p1, :p2, :p3)',
                            array(
                                'p1' => $id,
                                'p2' => intval($DI_item[$x]),
                                'p3' => floatval($DI_qnty[$x])
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
            $sql = 'DELETE FROM document_item WHERE document_id=:id';
            $db->execute($sql, array('id' => $id));
            // delete document
            $sql = 'DELETE FROM document WHERE id=:id';
            $db->execute($sql, array('id' => $id));
        }  else {
            // no data provided
            $this->sendResponse(array(
                'error' => WsLocalize::msg('no document data provided')
            ), 204);
        }
    }
}
