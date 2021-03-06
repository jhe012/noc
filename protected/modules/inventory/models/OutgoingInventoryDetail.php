<?php

/**
 * This is the model class for table "outgoing_inventory_detail".
 *
 * The followings are the available columns in table 'outgoing_inventory_detail':
 * @property integer $outgoing_inventory_detail_id
 * @property integer $outgoing_inventory_id
 * @property string $company_id
 * @property string $batch_no
 * @property string $sku_id
 * @property string $source_zone_id
 * @property string $unit_price
 * @property string $expiration_date
 * @property integer $quantity_issued
 * @property string $amount
 * @property integer $inventory_on_hand
 * @property string $return_date
 * @property string $remarks
 * @property string $created_date
 * @property string $created_by
 * @property string $updated_date
 * @property string $updated_by
 *
 * The followings are the available model relations:
 * @property OutgoingInventory $outgoingInventory
 */
class OutgoingInventoryDetail extends CActiveRecord {

   /**
     * @var string outgoing_inventory_detail_id
     * @soap
     */
    public $outgoing_inventory_detail_id;
   
    /**
     * @var string batch_no
     * @soap
     */
    public $batch_no;

    /**
     * @var string source_zone_id
     * @soap
     */
    public $source_zone_id;

    /**
     * @var string unit_price
     * @soap
     */
    public $unit_price;

    /**
     * @var string expiration_date
     * @soap
     */
    public $expiration_date;

    /**
     * @var string quantity_issued
     * @soap
     */
    public $quantity_issued;

    /**
     * @var string amount
     * @soap
     */
    public $amount;
    public $inventory_on_hand;

    /**
     * @var string return_date
     * @soap
     */
    public $return_date;

    /**
     * @var string remarks
     * @soap
     */
    public $remarks;

    /**
     * @var string po_no
     * @soap
     */
    public $po_no;
    
    /**
     * @var string uom_id
     * @soap
     */
    public $uom_id;
    
    /**
     * @var string pr_no
     * @soap
     */
    public $pr_no;
    
    /**
     * @var string pr_date
     * @soap
     */
    public $pr_date;
    
    /**
     * @var string plan_arrival_date
     * @soap
     */
    public $plan_arrival_date;

    /**
     * @var Sku[] sku_obj
     * @soap
     */
    public $sku_obj;
    public $search_string;
    public $inventory_id;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'outgoing_inventory_detail';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('company_id, sku_id, uom_id, quantity_issued, amount', 'required'),
            array('outgoing_inventory_id, inventory_id, planned_quantity, quantity_issued', 'numerical', 'integerOnly' => true),
            array('company_id, batch_no, sku_id, uom_id, sku_status_id, source_zone_id, status, created_by, updated_by, po_no', 'length', 'max' => 50),
            array('unit_price, amount', 'length', 'max' => 18),
            array('remarks', 'length', 'max' => 150),
            array('source_zone_id', 'isValidZone'),
            array('unit_price, amount', 'match', 'pattern' => '/^[0-9]{1,9}(\.[0-9]{0,2})?$/'),
            array('expiration_date, return_date', 'type', 'type' => 'date', 'message' => '{attribute} is not a date!', 'dateFormat' => 'yyyy-MM-dd'),
            array('expiration_date, return_date, created_date, updated_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('outgoing_inventory_detail_id, outgoing_inventory_id, company_id, batch_no, sku_id, uom_id, sku_status_id, source_zone_id, unit_price, expiration_date, quantity_issued, amount, return_date, status, remarks, created_date, created_by, updated_date, updated_by, po_no', 'safe', 'on' => 'search'),
        );
    }

    public function isValidZone($attribute) {
        $model = Zone::model()->findByPk($this->$attribute);

        if (!Validator::isResultSetWithRows($model)) {
            $this->addError($attribute, 'Zone is invalid.');
        }

        return;
    }

    public function beforeValidate() {
        return parent::beforeValidate();
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'outgoingInventory' => array(self::BELONGS_TO, 'OutgoingInventory', 'outgoing_inventory_id'),
            'zone' => array(self::BELONGS_TO, 'Zone', 'source_zone_id'),
            'sku' => array(self::BELONGS_TO, 'Sku', 'sku_id'),
            'uom' => array(self::BELONGS_TO, 'Uom', 'uom_id'),
            'skuStatus' => array(self::BELONGS_TO, 'SkuStatus', 'sku_status_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'outgoing_inventory_detail_id' => 'Outgoing Inventory Detail',
            'outgoing_inventory_id' => 'Outgoing Inventory',
            'company_id' => 'Company',
            'inventory_id' => 'Inventory',
            'batch_no' => 'Batch No',
            'sku_id' => 'Sku',
            'uom_id' => 'UOM',
            'sku_status_id' => Sku::SKU_LABEL . ' Status',
            'source_zone_id' => 'Source Zone',
            'unit_price' => 'Unit Price',
            'expiration_date' => 'Expiration Date',
            'planned_quantity' => 'Planned Quantity',
            'quantity_issued' => 'Actual Quantity',
            'amount' => 'Amount',
//            'inventory_on_hand' => 'Inventory On Hand',
            'return_date' => 'Return Date',
            'status' => 'Status',
            'remarks' => 'Remarks',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'updated_date' => 'Updated Date',
            'updated_by' => 'Updated By',
            'campaign_no' => 'Campaign No',
            'pr_no' => 'PR No',
            'pr_date' => 'PR Date',
            'plan_arrival_date' => 'Plan Arrival Date',
            'revised_delivery_date' => 'Revised Delivery Date',
            'po_no' => 'PO No',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('outgoing_inventory_detail_id', $this->outgoing_inventory_detail_id);
        $criteria->compare('outgoing_inventory_id', $this->outgoing_inventory_id);
        $criteria->compare('company_id', Yii::app()->user->company_id);
        $criteria->compare('inventory_id', $this->inventory_id);
        $criteria->compare('batch_no', $this->batch_no, true);
        $criteria->compare('sku_id', $this->sku_id, true);
        $criteria->compare('uom_id', $this->uom_id, true);
        $criteria->compare('sku_status_id', $this->sku_status_id, true);
        $criteria->compare('source_zone_id', $this->source_zone_id, true);
        $criteria->compare('unit_price', $this->unit_price, true);
        $criteria->compare('expiration_date', $this->expiration_date, true);
        $criteria->compare('planned_quantity', $this->planned_quantity);
        $criteria->compare('quantity_issued', $this->quantity_issued);
        $criteria->compare('amount', $this->amount, true);
//        $criteria->compare('inventory_on_hand', $this->inventory_on_hand);
        $criteria->compare('return_date', $this->return_date, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('remarks', $this->remarks, true);
        $criteria->compare('created_date', $this->created_date, true);
        $criteria->compare('created_by', $this->created_by, true);
        $criteria->compare('updated_date', $this->updated_date, true);
        $criteria->compare('updated_by', $this->updated_by, true);
        $criteria->compare('po_no', $this->po_no, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function data($col, $order_dir, $limit, $offset, $columns) {
        switch ($col) {

            case 0:
                $sort_column = 'outgoing_inventory_detail_id';
                break;

            case 1:
                $sort_column = 'outgoing_inventory_id';
                break;

            case 2:
                $sort_column = 'batch_no';
                break;

            case 3:
                $sort_column = 'sku_id';
                break;

            case 4:
                $sort_column = 'source_zone_id';
                break;

            case 5:
                $sort_column = 'unit_price';
                break;

            case 6:
                $sort_column = 'expiration_date';
                break;
        }


        $criteria = new CDbCriteria;
        $criteria->compare('company_id', Yii::app()->user->company_id);
        $criteria->compare('outgoing_inventory_detail_id', $columns[0]['search']['value']);
        $criteria->compare('outgoing_inventory_id', $columns[1]['search']['value']);
        $criteria->compare('batch_no', $columns[2]['search']['value'], true);
        $criteria->compare('sku_id', $columns[3]['search']['value'], true);
        $criteria->compare('source_zone_id', $columns[4]['search']['value'], true);
        $criteria->compare('unit_price', $columns[5]['search']['value'], true);
        $criteria->compare('expiration_date', $columns[6]['search']['value'], true);
        $criteria->order = "$sort_column $order_dir";
        $criteria->limit = $limit;
        $criteria->offset = $offset;

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => false,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OutgoingInventoryDetail the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function createOutgoingTransactionDetails($outgoing_inventory_id, $company_id, $inventory_id, $batch_no, $sku_id, $source_zone_id, $unit_price, $expiration_date, $planned_quantity, $quantity_issued, $amount, $return_date, $remarks, $created_by = null, $uom_id, $sku_status_id, $transaction_date) {

        $inventory = Inventory::model()->findByAttributes(array("inventory_id" => $inventory_id, "company_id" => $company_id));

        $ret_date = ($return_date != "" ? $return_date : null);
        $exp_date = ($expiration_date != "" ? $expiration_date : null);
        $cost_per_unit = (isset($unit_price) ? $unit_price : 0);

        $outgoing_transaction_detail = new OutgoingInventoryDetail;
        $outgoing_transaction_detail->outgoing_inventory_id = $outgoing_inventory_id;
        $outgoing_transaction_detail->company_id = $company_id;
        $outgoing_transaction_detail->inventory_id = $inventory_id;
        $outgoing_transaction_detail->batch_no = $batch_no;
        $outgoing_transaction_detail->sku_id = $sku_id;
        $outgoing_transaction_detail->uom_id = $uom_id;
        $outgoing_transaction_detail->sku_status_id = $sku_status_id;
        $outgoing_transaction_detail->source_zone_id = $source_zone_id;
        $outgoing_transaction_detail->unit_price = $cost_per_unit;
        $outgoing_transaction_detail->expiration_date = $exp_date;
        $outgoing_transaction_detail->planned_quantity = $planned_quantity;
        $outgoing_transaction_detail->quantity_issued = $quantity_issued;
        $outgoing_transaction_detail->amount = $amount;
//        $outgoing_transaction_detail->inventory_on_hand = $inventory_on_hand;
        $outgoing_transaction_detail->return_date = $ret_date;
        $outgoing_transaction_detail->status = OutgoingInventory::OUTGOING_PENDING_STATUS;
        $outgoing_transaction_detail->remarks = $remarks;
        $outgoing_transaction_detail->created_by = $created_by;
        $outgoing_transaction_detail->po_no = $inventory->po_no;
        $outgoing_transaction_detail->pr_no = $inventory->pr_no;
        $outgoing_transaction_detail->pr_date = $inventory->pr_date;
        $outgoing_transaction_detail->plan_arrival_date = $inventory->plan_arrival_date;
        $outgoing_transaction_detail->revised_delivery_date = $inventory->revised_delivery_date;

        if ($outgoing_transaction_detail->save(false)) {
            $this->decreaseInventory($inventory_id, $outgoing_transaction_detail->quantity_issued, $transaction_date, $outgoing_transaction_detail->unit_price, $outgoing_transaction_detail->created_by, $outgoing_transaction_detail->remarks);
            
            return $outgoing_transaction_detail;
        } else {
            return $outgoing_transaction_detail->getErrors();
        }
    }

    public function decreaseInventory($inventory_id, $quantity_issued, $transaction_date, $cost_per_unit, $created_by, $remarks) {

        $inventory = Inventory::model()->findByPk($inventory_id);

        $decrease_inventory = new DecreaseInventoryForm();
        $decrease_inventory->qty = $quantity_issued;
        $decrease_inventory->transaction_date = $transaction_date;
        $decrease_inventory->cost_per_unit = $cost_per_unit;
        $decrease_inventory->created_by = $created_by;
        $decrease_inventory->remarks = $remarks;
        $decrease_inventory->inventoryObj = $inventory;

        if ($decrease_inventory->decrease(false)) {
            return true;
        } else {
            return $decrease_inventory->getErrors();
        }
    }

    public function getByOutgoingInventoryID($outgoing_inventory_id) {
        $outgoing_inventory_details = OutgoingInventoryDetail::model()->findallByAttributes(array('outgoing_inventory_id' => $outgoing_inventory_id));
        return $outgoing_inventory_details;
    }

    public function updateOutgoingTransactionDetails($outgoing_inventory_id, $outgoing_inventory_detail_id, $company_id, $qty_for_new_inventory, $quantity_issued, $source_zone_id, $amount, $updated_by, $updated_date) {

        $outgoing_inv_detail = OutgoingInventoryDetail::model()->findByAttributes(array("company_id" => $company_id, "outgoing_inventory_id" => $outgoing_inventory_id, "outgoing_inventory_detail_id" => $outgoing_inventory_detail_id));

        $status_id = ($outgoing_inv_detail->sku_status_id != "" ? $outgoing_inv_detail->sku_status_id : null);

        $inventory = Inventory::model()->findByAttributes(
                array(
                    'company_id' => $outgoing_inv_detail->company_id,
                    'sku_id' => $outgoing_inv_detail->sku_id,
                    'uom_id' => $outgoing_inv_detail->uom_id,
                    'zone_id' => $outgoing_inv_detail->source_zone_id,
                    'sku_status_id' => $status_id,
                    'expiration_date' => $outgoing_inv_detail->expiration_date,
                    'po_no' => $outgoing_inv_detail->po_no,
                    'pr_no' => $outgoing_inv_detail->pr_no,
                    'pr_date' => $outgoing_inv_detail->pr_date,
                    'plan_arrival_date' => $outgoing_inv_detail->plan_arrival_date,
                )
        );

        $new_qty_value = trim($qty_for_new_inventory);
        $qty_issued = $outgoing_inv_detail->quantity_issued;

        if ($inventory) {
            if ($quantity_issued == $qty_issued) {
                
            } else if ($quantity_issued > $qty_issued) {

                $new_qty = $quantity_issued - $qty_issued;

                if ($inventory->qty > $new_qty || $inventory->qty == $new_qty) {

                    $decrease_inv = new DecreaseInventoryForm();
                    $decrease_inv->inventoryObj = $inventory;
                    $decrease_inv->qty = $new_qty;
                    $decrease_inv->transaction_date = date("Y-m-d", strtotime($updated_date));
                    $decrease_inv->created_by = $updated_by;

                    $decrease_inv->decrease(false);
                }
            } else {

                $new_inv_qty = $qty_issued - $quantity_issued;

                $increase_inv = new IncreaseInventoryForm();
                $increase_inv->inventoryObj = $inventory;
                $increase_inv->qty = $new_inv_qty;
                $increase_inv->transaction_date = date("Y-m-d", strtotime($updated_date));
                $increase_inv->created_by = $updated_by;

                $increase_inv->increase(false);
            }
        } else {

            if ($new_qty_value != "") {

                $saved_inv = ReceivingInventoryDetail::model()->createInventory($company_id, $outgoing_inv_detail->sku_id, $outgoing_inv_detail->uom_id, $outgoing_inv_detail->unit_price, $new_qty_value, $source_zone_id, date("Y-m-d", strtotime($updated_date)), $updated_by, $outgoing_inv_detail->expiration_date, $outgoing_inv_detail->batch_no, $status_id, $outgoing_inv_detail->pr_no, $outgoing_inv_detail->pr_date, $outgoing_inv_detail->plan_arrival_date, $outgoing_inv_detail->po_no, $outgoing_inv_detail->remarks);

                if ($saved_inv) {

                    $inv = Inventory::model()->findByAttributes(array(
                        'sku_id' => $outgoing_inv_detail->sku_id,
                        'company_id' => $outgoing_inv_detail->company_id,
                        'uom_id' => $outgoing_inv_detail->uom_id,
                        'zone_id' => $source_zone_id,
                        'sku_status_id' => $status_id,
                        'expiration_date' => $outgoing_inv_detail->expiration_date,
                        'reference_no' => $outgoing_inv_detail->batch_no,
                        'po_no' => $outgoing_inv_detail->po_no,
                        'pr_no' => $outgoing_inv_detail->pr_no,
                        'pr_date' => $outgoing_inv_detail->pr_date,
                        'plan_arrival_date' => $outgoing_inv_detail->plan_arrival_date,
                    ));

                    $outgoing_inv_detail->inventory_id = $inv->inventory_id;
                }
            }
        }

        $outgoing_inv_detail->amount = $amount;
        $outgoing_inv_detail->quantity_issued = $quantity_issued;
        $outgoing_inv_detail->updated_date = $updated_date;
        $outgoing_inv_detail->updated_by = $updated_by;

        if ($outgoing_inv_detail->save(false)) {
            
            return $outgoing_inv_detail;
        } else {
            
            return $outgoing_inv_detail->getErrors();
        }
    }
    
    public function returnInvIfOutgoingInvDetailDeleted($company_id, $outgoing_inv_detail_id, $created_date, $created_by) {
        
        $c = new CDbCriteria;
        $c->condition = "outgoingInventory.company_id = '" . $company_id . "' AND t.outgoing_inventory_detail_id = '" . $outgoing_inv_detail_id . "'";
        $c->with = array('outgoingInventory');
        $outgoing_inv_detail = OutgoingInventoryDetail::model()->findAll($c);
        
        $data = array();
        $data['success'] = false;
        
        if (count($outgoing_inv_detail) > 0) {
            if (trim($outgoing_inv_detail[0]->outgoingInventory->status) != OutgoingInventory::OUTGOING_COMPLETE_STATUS) {
               for ($x = 0; $x < count($outgoing_inv_detail); $x++) {
                
                    if ($outgoing_inv_detail[$x]->status == OutgoingInventory::OUTGOING_PENDING_STATUS) {
                        
                        ReceivingInventoryDetail::model()->createInventory($outgoing_inv_detail[$x]->company_id, $outgoing_inv_detail[$x]->sku_id, $outgoing_inv_detail[$x]->uom_id, $outgoing_inv_detail[$x]->unit_price, $outgoing_inv_detail[$x]->quantity_issued, $outgoing_inv_detail[$x]->source_zone_id, $created_date, $created_by, $outgoing_inv_detail[$x]->expiration_date, $outgoing_inv_detail[$x]->batch_no, $outgoing_inv_detail[$x]->sku_status_id, $outgoing_inv_detail[$x]->pr_no, $outgoing_inv_detail[$x]->pr_date, $outgoing_inv_detail[$x]->plan_arrival_date, $outgoing_inv_detail[$x]->po_no, $outgoing_inv_detail[$x]->remarks);  
                    } else if ($outgoing_inv_detail[$x]->status == OutgoingInventory::OUTGOING_INCOMPLETE_STATUS) {
                       
                        $data = OutgoingInventory::model()->getRemainingQtyByOutgoingInvDetailIDAndDRNo($outgoing_inv_detail[$x]->outgoing_inventory_detail_id, $outgoing_inv_detail[$x]->outgoingInventory->dr_no);
                       
                        ReceivingInventoryDetail::model()->createInventory($outgoing_inv_detail[$x]->company_id, $outgoing_inv_detail[$x]->sku_id, $outgoing_inv_detail[$x]->uom_id, $outgoing_inv_detail[$x]->unit_price, $data[0]['remaining_qty'], $outgoing_inv_detail[$x]->source_zone_id, $created_date, $created_by, $outgoing_inv_detail[$x]->expiration_date, $outgoing_inv_detail[$x]->batch_no, $outgoing_inv_detail[$x]->sku_status_id, $outgoing_inv_detail[$x]->pr_no, $outgoing_inv_detail[$x]->pr_date, $outgoing_inv_detail[$x]->plan_arrival_date, $outgoing_inv_detail[$x]->po_no, $outgoing_inv_detail[$x]->remarks);  
                    }
                }
                
                $data['success'] = true;
            }          
        }
        
        return $data;
    }
}
