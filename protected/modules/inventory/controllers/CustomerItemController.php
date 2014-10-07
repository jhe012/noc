<?php

class CustomerItemController extends Controller {
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    //public $layout='//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('index', 'view'),
                'users' => array('@'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'update', 'data', 'loadTransactionByDRNo', 'loadInventoryDetails', 'customerItemDetailData', 'deleteCustomerItemDetail'),
                'users' => array('@'),
            ),
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionData() {

        CustomerItem::model()->search_string = $_GET['search']['value'] != "" ? $_GET['search']['value'] : null;

        $dataProvider = CustomerItem::model()->data($_GET['order'][0]['column'], $_GET['order'][0]['dir'], $_GET['length'], $_GET['start'], $_GET['columns']);

        $count = CustomerItem::model()->countByAttributes(array('company_id' => Yii::app()->user->company_id));

        $output = array(
            "draw" => intval($_GET['draw']),
            "recordsTotal" => $count,
            "recordsFiltered" => $dataProvider->totalItemCount,
            "data" => array()
        );

        foreach ($dataProvider->getData() as $key => $value) {
            $row = array();
            $row['customer_item_id'] = $value->customer_item_id;
            $row['rra_no'] = $value->rra_no;
            $row['campaign_no'] = $value->campaign_no;
            $row['pr_no'] = $value->pr_no;
            $row['pr_date'] = $value->pr_date;
            $row['dr_no'] = $value->dr_no;
            $row['source_zone_id'] = $value->source_zone_id;
            $row['source_zone_name'] = isset($value->zone->zone_name) ? $value->zone->zone_name : null;
            $row['poi_id'] = $value->poi_id;
            $row['poi_name'] = isset($value->poi->short_name) ? $value->poi->short_name : null;
            $row['transaction_date'] = $value->transaction_date;
            $row['total_amount'] = $value->total_amount;
            $row['created_date'] = $value->created_date;
            $row['created_by'] = $value->created_by;
            $row['updated_date'] = $value->updated_date;
            $row['updated_by'] = $value->updated_by;


            $row['links'] = '<a class="btn btn-sm btn-default delete" title="Delete" href="' . $this->createUrl('/inventory/customerItem/delete', array('id' => $value->customer_item_id)) . '">
                                <i class="glyphicon glyphicon-trash"></i>
                            </a>';

            $output['data'][] = $row;
        }

        echo json_encode($output);
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $model = $this->loadModel($id);

        $this->pageTitle = 'View CustomerItem ' . $model->customer_item_id;

        $this->menu = array(
            array('label' => 'Create CustomerItem', 'url' => array('create')),
            array('label' => 'Update CustomerItem', 'url' => array('update', 'id' => $model->customer_item_id)),
            array('label' => 'Delete CustomerItem', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->customer_item_id), 'confirm' => 'Are you sure you want to delete this item?')),
            array('label' => 'Manage CustomerItem', 'url' => array('admin')),
            '',
            array('label' => 'Help', 'url' => '#'),
        );

        $this->render('view', array(
            'model' => $model,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {

        $this->pageTitle = CustomerItem::CUSTOMER_ITEM_LABEL . ' Inventory';
        $this->layout = '//layouts/column1';

        $customer_item = new CustomerItem;
        $transaction_detail = new CustomerItemDetail;
        $sku = new Sku;
        $reference_dr_nos = CHtml::listData(IncomingInventory::model()->findAllByAttributes(array("company_id" => Yii::app()->user->company_id)), "dr_no", "dr_no");
        $uom = CHtml::listData(UOM::model()->findAll(array('condition' => 'company_id = "' . Yii::app()->user->company_id . '"', 'order' => 'uom_name ASC')), 'uom_id', 'uom_name');
        $sku_status = CHtml::listData(SkuStatus::model()->findAll(array('condition' => 'company_id = "' . Yii::app()->user->company_id . '"', 'order' => 'status_name ASC')), 'sku_status_id', 'status_name');

        if (Yii::app()->request->isPostRequest && Yii::app()->request->isAjaxRequest) {

            $data = array();
            $data['success'] = false;
            $data["type"] = "success";
            $data['form'] = $_POST['form'];

            if ($_POST['form'] == "transaction") {

                if (isset($_POST['CustomerItem'])) {
                    $customer_item->attributes = $_POST['CustomerItem'];
                    $customer_item->company_id = Yii::app()->user->company_id;
                    $customer_item->created_by = Yii::app()->user->name;
                    unset($customer_item->created_date);

                    $validatedCustomerItem = CActiveForm::validate($customer_item);

                    if ($validatedCustomerItem != '[]') {

                        $data['error'] = $validatedCustomerItem;
                        $data['message'] = 'Unable to process';
                        $data['success'] = false;
                        $data["type"] = "danger";
                    } else {

                        $transaction_details = isset($_POST['transaction_details']) ? $_POST['transaction_details'] : array();

                        if ($customer_item->create($transaction_details)) {
                            $data['message'] = 'Successfully created';
                            $data['success'] = true;
                        } else {
                            $data['message'] = 'Unable to process';
                            $data['success'] = false;
                            $data["type"] = "danger";
                        }
                    }
                }
            } else if ($_POST['form'] == "details") {

                if (isset($_POST['CustomerItemDetail'])) {
                    $transaction_detail->attributes = $_POST['CustomerItemDetail'];
                    $transaction_detail->company_id = Yii::app()->user->company_id;
                    $transaction_detail->created_by = Yii::app()->user->name;
                    unset($transaction_detail->created_date);

                    $validatedCustomerItemDetail = CActiveForm::validate($transaction_detail);

                    if ($validatedCustomerItemDetail != '[]') {

                        $data['error'] = $validatedCustomerItemDetail;
                        $data['message'] = 'Unable to process';
                        $data['success'] = false;
                        $data["type"] = "danger";
                    } else {

                        $c = new CDbCriteria;
                        $c->compare('t.company_id', Yii::app()->user->company_id);
                        $c->compare('t.inventory_id', $transaction_detail->inventory_id);
                        $c->with = array("sku");
                        $inventory = Inventory::model()->find($c);

                        if ($transaction_detail->quantity_issued <= $inventory->qty) {
                            $data['success'] = true;
                            $data['message'] = 'Successfully Added Item';

                            $data['details'] = array(
                                "inventory_id" => isset($inventory->inventory_id) ? $inventory->inventory_id : null,
                                "sku_id" => isset($inventory->sku->sku_id) ? $inventory->sku->sku_id : null,
                                "sku_code" => isset($inventory->sku->sku_code) ? $inventory->sku->sku_code : null,
                                "sku_description" => isset($inventory->sku->description) ? $inventory->sku->description : null,
                                'brand_name' => isset($inventory->sku->brand->brand_name) ? $inventory->sku->brand->brand_name : null,
                                'unit_price' => isset($transaction_detail->unit_price) ? $transaction_detail->unit_price : 0,
                                'batch_no' => isset($transaction_detail->batch_no) ? $transaction_detail->batch_no : null,
                                'expiration_date' => isset($transaction_detail->expiration_date) ? $transaction_detail->expiration_date : null,
                                'planned_quantity' => $transaction_detail->planned_quantity != "" ? $transaction_detail->planned_quantity : 0,
                                'quantity_issued' => $transaction_detail->quantity_issued != "" ? $transaction_detail->quantity_issued : 0,
                                'amount' => $transaction_detail->amount != "" ? $transaction_detail->amount : 0,
                                'inventory_on_hand' => $transaction_detail->inventory_on_hand != "" ? $transaction_detail->inventory_on_hand : 0,
                                'return_date' => isset($transaction_detail->return_date) ? $transaction_detail->return_date : null,
                                'remarks' => isset($transaction_detail->remarks) ? $transaction_detail->remarks : null,
                                'uom_id' => isset($transaction_detail->uom_id) ? $transaction_detail->uom_id : null,
                                'sku_status_id' => isset($transaction_detail->sku_status_id) ? $transaction_detail->sku_status_id : null,
                            );
                        } else {

                            $data['message'] = 'Quantity Received greater than inventory on hand';
                            $data['success'] = false;
                            $data["type"] = "danger";
                        }
                    }
                }
            }

            echo json_encode($data);
            Yii::app()->end();
        }

        $this->render('customerItemForm', array(
            'customer_item' => $customer_item,
            'transaction_detail' => $transaction_detail,
            'reference_dr_nos' => $reference_dr_nos,
            'sku' => $sku,
            'uom' => $uom,
            'sku_status' => $sku_status,
        ));
    }

    public function actionLoadTransactionByDRNo($dr_no) {

        $c = new CDbCriteria;
        $c->condition = "incomingInventory.company_id = '" . Yii::app()->user->company_id . "' AND incomingInventory.dr_no = '" . $dr_no . "'";
        $c->with = array("incomingInventory");
        $incoming_inv_details = IncomingInventoryDetail::model()->findAll($c);

        $inv_ids = "";
        foreach ($incoming_inv_details as $key => $val) {

            $inventoryObj = Inventory::model()->findByAttributes(
                    array(
                        'company_id' => $val->incomingInventory->company_id,
                        'sku_id' => $val->sku_id,
                        'uom_id' => $val->uom_id,
                        'zone_id' => $val->incomingInventory->zone_id,
                        'sku_status_id' => $val->sku_status_id != "" ? $val->sku_status_id : null,
                        'expiration_date' => isset($val->expiration_date) ? $val->expiration_date : null,
                        'reference_no' => $val->batch_no,
                    )
            );

            if ($inventoryObj) {
                $inv_ids .= $inventoryObj->inventory_id . ",";
            }
        }

        $c2 = new CDbCriteria;
        $c2->select = new CDbExpression('t.*, CONCAT(t.first_name, " ",t.last_name) AS fullname');
        $c2->compare('t.company_id', Yii::app()->user->company_id);
        $c2->compare('t.default_zone_id', isset($val->incomingInventory->zone_id) ? $val->incomingInventory->zone_id : 0);
        $employee = Employee::model()->find($c2);

        $header = array(
            "rra_no" => isset($val->incomingInventory->rra_no) ? $val->incomingInventory->rra_no : null,
            "campaign_no" => isset($val->incomingInventory->campaign_no) ? $val->incomingInventory->campaign_no : null,
            "pr_no" => isset($val->incomingInventory->pr_no) ? $val->incomingInventory->pr_no : null,
            "pr_date" => isset($val->incomingInventory->pr_date) ? $val->incomingInventory->pr_date : null,
            "source_zone_id" => isset($val->incomingInventory->zone_id) ? $val->incomingInventory->zone_id : null,
            "source_zone_name" => isset($val->incomingInventory->zone->zone_name) ? $val->incomingInventory->zone->zone_name : null,
        );

        $inventory = array();
        if ($inv_ids != "") {
            $c1 = new CDbCriteria;
            $c1->compare("company_id", Yii::app()->user->company_id);
            $c1->condition = "inventory_id IN (" . substr($inv_ids, 0, -1) . ")";
            $inventory = Inventory::model()->findAll($c1);
        }

        $output = array();
        foreach ($inventory as $key => $value) {
            $row = array();

            $row['inventory_id'] = $value->inventory_id;
            $row['company_id'] = $value->company_id;
            $row['sku_id'] = $value->sku_id;
            $row['sku_code'] = isset($value->sku->sku_code) ? $value->sku->sku_code : null;
            $row['sku_description'] = isset($value->sku->description) ? $value->sku->description : null;
            $row['brand_name'] = isset($value->sku->brand->brand_name) ? $value->sku->brand->brand_name : null;
            $row['cost_per_unit'] = isset($value->cost_per_unit) ? $value->cost_per_unit : null;
            $row['inventory_on_hand'] = isset($value->qty) ? $value->qty : null;
            $row['uom_name'] = isset($value->uom->uom_name) ? $value->uom->uom_name : null;
            $row['sku_status_name'] = isset($value->skuStatus->status_name) ? $value->skuStatus->status_name : null;
            $row['expiration_date'] = isset($value->expiration_date) ? $value->expiration_date : null;
            $row['reference_no'] = isset($value->reference_no) ? $value->reference_no : null;

            $output['data'][] = $row;
        }

        $output['headers'] = $header;

        echo json_encode($output);
    }

    public function actionLoadInventoryDetails($inventory_id) {

        if ($inventory_id != "") {
            $c = new CDbCriteria;
            $c->select = 't.*, sum(t.qty) AS inventory_on_hand';
            $c->compare('t.company_id', Yii::app()->user->company_id);
            $c->compare('t.inventory_id', $inventory_id);
            $c->group = "t.sku_id";
            $inventory = Inventory::model()->find($c);

            $sku = Sku::model()->findByAttributes(array("company_id" => Yii::app()->user->company_id, "sku_id" => $inventory->sku_id));
        }

        $data = array(
            "sku_id" => isset($sku->sku_id) ? $sku->sku_id : null,
            "sku_category" => isset($sku->type) ? $sku->type : null,
            "sku_sub_category" => isset($sku->sub_type) ? $sku->sub_type : null,
            'brand_name' => isset($sku->brand->brand_name) ? $sku->brand->brand_name : null,
            'sku_code' => isset($sku->sku_code) ? $sku->sku_code : null,
            'sku_description' => isset($sku->description) ? $sku->description : null,
            'inventory_uom_selected' => isset($inventory->uom->uom_name) ? $inventory->uom->uom_name : null,
            'source_zone_id' => isset($inventory->zone_id) ? $inventory->zone_id : null,
            'source_zone_name' => isset($inventory->zone->zone_name) ? $inventory->zone->zone_name : null,
            'unit_price' => isset($inventory->cost_per_unit) ? $inventory->cost_per_unit : 0,
            'reference_no' => isset($inventory->reference_no) ? $inventory->reference_no : null,
            'expiration_date' => isset($inventory->expiration_date) ? $inventory->expiration_date : null,
            'inventory_on_hand' => isset($inventory->inventory_on_hand) ? $inventory->inventory_on_hand : 0,
            'uom_id' => isset($inventory->uom_id) ? $inventory->uom_id : null,
            'sku_status_id' => isset($inventory->sku_status_id) ? $inventory->sku_status_id : null,
        );

        echo json_encode($data);
    }
    
    public function actionCustomerItemDetailData($customer_item_id) {
        
        $c = new CDbCriteria;
        $c->compare("company_id", Yii::app()->user->company_id);
        $c->compare("customer_item_id", $customer_item_id);
        $customer_item_details = CustomerItemDetail::model()->findAll($c);

        $output = array();
        foreach ($customer_item_details as $key => $value) {
            $row = array();

            $row['customer_item_detail_id'] = $value->customer_item_detail_id;
            $row['customer_item_id'] = $value->customer_item_id;
            $row['batch_no'] = $value->batch_no;
            $row['sku_code'] = isset($value->sku->sku_code) ? $value->sku->sku_code : null;
            $row['sku_name'] = isset($value->sku->sku_name) ? $value->sku->sku_name : null;
            $row['sku_description'] = isset($value->sku->description) ? $value->sku->description : null;
            $row['brand_name'] = isset($value->sku->brand->brand_name) ? $value->sku->brand->brand_name : null;
            $row['unit_price'] = $value->unit_price;
            $row['expiration_date'] = $value->expiration_date;
            $row['planned_quantity'] = $value->planned_quantity;
            $row['quantity_issued'] = $value->quantity_issued;
            $row['amount'] = $value->amount;
            $row['inventory_on_hand'] = $value->inventory_on_hand;
            $row['return_date'] = $value->return_date;
            $row['remarks'] = $value->remarks;

            $row['links'] = '<a class="btn btn-sm btn-default delete" title="Delete" href="' . $this->createUrl('/inventory/customerItem/deleteCustomerItemDetail', array('customer_item_detail_id' => $value->customer_item_detail_id)) . '">
                                <i class="glyphicon glyphicon-trash"></i>
                            </a>';

            $output['data'][] = $row;
        }

        echo json_encode($output);
        
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);

        $this->menu = array(
            array('label' => 'Create CustomerItem', 'url' => array('create')),
            array('label' => 'View CustomerItem', 'url' => array('view', 'id' => $model->customer_item_id)),
            array('label' => 'Manage CustomerItem', 'url' => array('admin')),
            '',
            array('label' => 'Help', 'url' => '#'),
        );

        $this->pageTitle = 'Update CustomerItem ' . $model->customer_item_id;

// Uncomment the following line if AJAX validation is needed
// $this->performAjaxValidation($model);

        if (isset($_POST['CustomerItem'])) {
            $model->attributes = $_POST['CustomerItem'];
            $model->updated_by = Yii::app()->user->name;
            $model->updated_date = date('Y-m-d H:i:s');

            if ($model->save()) {
                Yii::app()->user->setFlash('success', "Successfully updated");
                $this->redirect(array('view', 'id' => $model->customer_item_id));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        if (Yii::app()->request->isPostRequest) {
            try {

                // delete customer item details by customer_item_id
                CustomerItemDetail::model()->deleteAll("company_id = '" . Yii::app()->user->company_id . "' AND customer_item_id = " . $id);
                // delete attachment by customer_item_id as transaction_id
//                $this->deleteAttachmenntByReceivingInvID($id);
                // we only allow deletion via POST request
                $this->loadModel($id)->delete();

                // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                if (!isset($_GET['ajax'])) {
                    Yii::app()->user->setFlash('success', "Successfully deleted");
                    $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
                } else {

                    echo "Successfully deleted";
                    exit;
                }
            } catch (CDbException $e) {
                if ($e->errorInfo[1] == 1451) {
                    if (!isset($_GET['ajax'])) {
                        Yii::app()->user->setFlash('danger', "Unable to delete");
                        $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('view', 'id' => $id));
                    } else {
                        echo "1451";
                        exit;
                    }
                }
            }
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }
    
    public function actionDeleteCustomerItemDetail($customer_item_detail_id) {
        if (Yii::app()->request->isPostRequest) {
            try {

                CustomerItemDetail::model()->deleteAll("company_id = '" . Yii::app()->user->company_id . "' AND customer_item_detail_id = " . $customer_item_detail_id);

                // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
                if (!isset($_GET['ajax'])) {
                    Yii::app()->user->setFlash('success', "Successfully deleted");
                    $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
                } else {

                    echo "Successfully deleted";
                    exit;
                }
            } catch (CDbException $e) {
                if ($e->errorInfo[1] == 1451) {
                    echo "1451";
                    exit;
                }
            }
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $dataProvider = new CActiveDataProvider('CustomerItem');

        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $this->layout = '//layouts/column1';
        $this->pageTitle = 'Manage ' . CustomerItem::CUSTOMER_ITEM_LABEL . ' Inventory';

        $model = new CustomerItem('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['CustomerItem']))
            $model->attributes = $_GET['CustomerItem'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = CustomerItem::model()->findByAttributes(array('customer_item_id' => $id, 'company_id' => Yii::app()->user->company_id));
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');

        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'customer-item-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
