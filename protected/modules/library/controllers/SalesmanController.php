<?php

class SalesmanController extends Controller {
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
                'actions' => array('create', 'update', 'data', 'getZoneBySalesoffice'),
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

        Salesman::model()->search_string = $_GET['search']['value'] != "" ? $_GET['search']['value'] : null;

        $dataProvider = Salesman::model()->data($_GET['order'][0]['column'], $_GET['order'][0]['dir'], $_GET['length'], $_GET['start'], $_GET['columns']);

        $count = Salesman::model()->countByAttributes(array('company_id' => Yii::app()->user->company_id));

        $output = array(
            "draw" => intval($_GET['draw']),
            "recordsTotal" => $count,
            "recordsFiltered" => $dataProvider->totalItemCount,
            "data" => array()
        );



        foreach ($dataProvider->getData() as $key => $value) {
            $row = array();
            $row['salesman_id'] = $value->salesman_id;
            $row['team_leader_id'] = $value->team_leader_id;
            $row['salesman_name'] = $value->salesman_name;
            $row['salesman_code'] = $value->salesman_code;
            $row['sales_office_name'] = isset($value->salesOffice->sales_office_name) ? $value->salesOffice->sales_office_name : null;
            $row['zone_name'] = isset($value->zone->zone_name) ? $value->zone->zone_name : null;
            $row['mobile_number'] = $value->mobile_number;
            $row['device_no'] = $value->device_no;
            $row['other_fields_1'] = $value->other_fields_1;
            $row['other_fields_2'] = $value->other_fields_2;
            $row['other_fields_3'] = $value->other_fields_3;
            $row['created_date'] = $value->created_date;
            $row['created_by'] = $value->created_by;
            $row['updated_date'] = $value->updated_date;
            $row['updated_by'] = $value->updated_by;
            $row['is_team_leader'] = $value->is_team_leader;


            $row['links'] = '<a class="view" title="View" data-toggle="tooltip" href="' . $this->createUrl('/library/salesman/view', array('id' => $value->salesman_id)) . '" data-original-title="View"><i class="fa fa-eye"></i></a>'
                    . '&nbsp;<a class="update" title="Update" data-toggle="tooltip" href="' . $this->createUrl('/library/salesman/update', array('id' => $value->salesman_id)) . '" data-original-title="View"><i class="fa fa-pencil"></i></a>'
                    . '&nbsp;<a class="delete" title="Delete" data-toggle="tooltip" href="' . $this->createUrl('/library/salesman/delete', array('id' => $value->salesman_id)) . '" data-original-title="Delete"><i class="fa fa-trash-o"></i></a>';

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

        $this->pageTitle = 'View Salesman ' . $model->salesman_name;

        $this->menu = array(
            array('label' => 'Create Salesman', 'url' => array('create')),
            array('label' => 'Update Salesman', 'url' => array('update', 'id' => $model->salesman_id)),
            array('label' => 'Delete Salesman', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->salesman_id), 'confirm' => 'Are you sure you want to delete this item?')),
            array('label' => 'Manage Salesman', 'url' => array('admin')),
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

        $this->pageTitle = 'Create Salesman';

        $this->menu = array(
            array('label' => 'Manage Salesman', 'url' => array('admin')),
            '',
            array('label' => 'Help', 'url' => '#'),
        );

        $model = new Salesman('create');

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Salesman'])) {
            $model->attributes = $_POST['Salesman'];
            $model->company_id = Yii::app()->user->company_id;
            $model->created_by = Yii::app()->user->name;
            unset($model->created_date);
            $model->salesman_id = Globals::generateV4UUID();

            if ($model->save()) {
                Yii::app()->user->setFlash('success', "Successfully created");
                $this->redirect(array('view', 'id' => $model->salesman_id));
            }
        }

        $sales_office = CHtml::listData(SalesOffice::model()->findAll(array('condition' => 'company_id = "' . Yii::app()->user->company_id . '"', 'order' => 'sales_office_name ASC')), 'sales_office_id', 'sales_office_name');
        $so_zones = array();

        $this->render('create', array(
            'model' => $model,
            'sales_office' => $sales_office,
            'so_zones' => $so_zones,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);

        $this->menu = array(
            array('label' => 'Create Salesman', 'url' => array('create')),
            array('label' => 'View Salesman', 'url' => array('view', 'id' => $model->salesman_id)),
            array('label' => 'Manage Salesman', 'url' => array('admin')),
            '',
            array('label' => 'Help', 'url' => '#'),
        );

        $this->pageTitle = 'Update Salesman ' . $model->salesman_name;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Salesman'])) {
            $model->attributes = $_POST['Salesman'];
            $model->updated_by = Yii::app()->user->name;
            $model->updated_date = date('Y-m-d H:i:s');

            if ($model->save()) {
                Yii::app()->user->setFlash('success', "Successfully updated");
                $this->redirect(array('view', 'id' => $model->salesman_id));
            }
        }

        $sales_office = CHtml::listData(SalesOffice::model()->findAll(array('condition' => 'company_id = "' . Yii::app()->user->company_id . '"', 'order' => 'sales_office_name ASC')), 'sales_office_id', 'sales_office_name');
        $so_zones = CHtml::listData(Zone::model()->findAll(array('condition' => 'company_id = "' . Yii::app()->user->company_id . '" AND sales_office_id = "' . $model->sales_office_id . '"', 'order' => 'zone_name ASC')), 'zone_id', 'zone_name');

        $this->render('update', array(
            'model' => $model,
            'sales_office' => $sales_office,
            'so_zones' => $so_zones,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        if (Yii::app()->request->isPostRequest) {
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
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $dataProvider = new CActiveDataProvider('Salesman');

        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $this->layout = '//layouts/column1';
        $this->pageTitle = 'Manage Salesman';

        $model = new Salesman('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Salesman']))
            $model->attributes = $_GET['Salesman'];

        $sales_office = CHtml::listData(SalesOffice::model()->findAll(array('condition' => 'company_id = "' . Yii::app()->user->company_id . '"', 'order' => 'sales_office_name ASC')), 'sales_office_name', 'sales_office_name');

        $this->render('admin', array(
            'model' => $model,
            'sales_office' => $sales_office,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id) {
        $model = Salesman::model()->findByAttributes(array('salesman_id' => $id, 'company_id' => Yii::app()->user->company_id));
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');

        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'salesman-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionGetZoneBySalesoffice() {

        $sales_office_id = Yii::app()->request->getParam('sales_office_id');

        echo "<option value=''>Select Zone</option>";
        $data = Zone::model()->findAll(array('condition' => 'company_id = "' . Yii::app()->user->company_id . '" AND sales_office_id = "' . $sales_office_id . '"', 'order' => 'zone_name ASC'));

        foreach ($data as $key => $val) {
            echo CHtml::tag('option', array('value' => $val['zone_id']), CHtml::encode($val['zone_name']), true);
        }
    }

}
