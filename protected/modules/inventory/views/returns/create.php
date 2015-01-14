<?php
$this->breadcrumbs = array(
    'Returns' => array('admin'),
    'Create',
);
?>

<?php
$baseUrl = Yii::app()->theme->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/plugins/input-mask/jquery.inputmask.js', CClientScript::POS_END);
$cs->registerScriptFile($baseUrl . '/js/plugins/input-mask/jquery.inputmask.date.extensions.js', CClientScript::POS_END);
$cs->registerScriptFile($baseUrl . '/js/plugins/input-mask/jquery.inputmask.extensions.js', CClientScript::POS_END);
?>

<script src="<?php echo Yii::app()->baseUrl; ?>/js/jquery.validate.js" type="text/javascript"></script>

<style type="text/css">
    #input_label label { margin-bottom: 20px; padding: 5px; }

    .span5  { width: 200px; }

    .hide_col { display: none; }

    .x-scroll { overflow-x: scroll; } 

    #sku_table tbody tr { cursor: pointer }

    .hide_row { display: none; }

    #transaction_table2 td { text-align:center; }
    #transaction_table2 td + td { text-align: left; }
</style>

<?php 
$not_set = "'<center>--</center>'"; 
$hide_notReturnable = $isReturnable === true ? "display: none;" : "";
$hide_Returnable = $isReturnable === false ? "display: none;" : "";
?>

<div class="nav-tabs-custom" id ="custTabs">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab_1" data-toggle="tab" class="returns_tab_cls"  style=''><?php echo Returnable::RETURNABLE; ?></a></li>
        <li><a href="#tab_2" data-toggle="tab" class="returns_tab_cls" style='<?php echo $hide_notReturnable; ?>'><?php echo Returnable::RETURN_RECEIPT; ?></a></li>
        <li><a href="#tab_3" data-toggle="tab" class="returns_tab_cls" style='<?php echo $hide_notReturnable; ?>'><?php echo Returnable::RETURN_MDSE; ?></a></li>
    </ul>
    <div class="tab-content" id ="info">
        <div class="tab-pane active" id="tab_1">
            <?php
            $this->renderPartial("_returnable", array(
                'returnable' => $returnable,
                'return_from_list' => $return_from_list,
                'zone_list' => $zone_list,
                'poi_list' => $poi_list,
                'salesoffice_list' => $salesoffice_list,
                'employee' => $employee,
                'not_set' => $not_set,
                'isReturnable' => $isReturnable,
                'sku_id' => $sku_id
            ));
            ?>
        </div>

        <div class="tab-pane" id="tab_2">
            <?php
            $this->renderPartial("_return_receipt", array(
                'return_receipt' => $return_receipt,
                'return_from_list' => $return_from_list,
                'zone_list' => $zone_list,
                'poi_list' => $poi_list,
                'salesoffice_list' => $salesoffice_list,
                'employee' => $employee,
                'not_set' => $not_set,
                'return_receipt_detail' => $return_receipt_detail,
                'uom' => $uom,
                'sku_status' => $sku_status,
                'isReturnable' => $isReturnable
            ));
            ?>
        </div>

        <div class="tab-pane" id="tab_3">
            <?php $this->renderPartial("_return_mdse", array()); ?>
        </div>
    </div>
</div>

<script type="text/javascript">

    function onlyNumbers(txt, event, point) {

        var charCode = (event.which) ? event.which : event.keyCode;

        if ((charCode >= 48 && charCode <= 57) || (point === true && charCode == 46)) {
            return true;
        }

        return false;
    }

    function loadSODetailByID(sales_office_id, return_type_label) {
        $("." + return_type_label + "autofill_text").html(<?php echo $not_set; ?>);
        $("#" + return_type_label + "selected_salesoffice").select2("val", "");

        $.ajax({
            type: 'POST',
            url: '<?php echo Yii::app()->createUrl('/library/salesoffice/getSODetailsByID'); ?>' + '&sales_office_id=' + sales_office_id,
            dataType: "json",
            success: function(data) {
                $("#" + return_type_label + "selected_salesoffice").select2("val", data.so_detail.sales_office_id);
                $("#" + return_type_label + "salesoffice_code").html(data.so_detail.sales_office_code);
                $("#" + return_type_label + "salesoffice_address1").html(data.so_detail.sales_office_address1);
            },
            error: function(data) {
                alert("Error occured: Please try again.");
            }
        });
    }

    function loadSalesmanDetailByID(employee_id, return_type_label) {
        $("." + return_type_label + "autofill_text").html(<?php echo $not_set; ?>);
        $("#" + return_type_label + "selected_salesman").select2("val", "");

        $.ajax({
            type: 'POST',
            url: '<?php echo Yii::app()->createUrl('/library/employee/loadEmployeeDetailsByID'); ?>' + '&employee_id=' + employee_id,
            dataType: "json",
            success: function(data) {
                $("#" + return_type_label + "selected_salesman").select2("val", data.employee_id);
                $("#" + return_type_label + "employee_code").html(data.employee_code);
                $("#" + return_type_label + "employee_address1").html(data.address1);
                $("#" + return_type_label + "employee_default_zone").html(data.default_zone_name);
            },
            error: function(data) {
                alert("Error occured: Please try again.");
            }
        });

    }

    function loadPOIDetailsByID(poi_id, return_type_label) {
        $("." + return_type_label + "autofill_text").html(<?php echo $not_set; ?>);
        $("#" + return_type_label + "selected_outlet").select2("val", "");

        $.ajax({
            type: 'POST',
            url: '<?php echo Yii::app()->createUrl('/library/poi/getPOIDetails'); ?>' + '&poi_id=' + poi_id,
            dataType: "json",
            success: function(data) {
                $("#" + return_type_label + "selected_outlet").select2("val", data.poi_id);
                $("#" + return_type_label + "poi_primary_code").html(data.primary_code);
                $("#" + return_type_label + "poi_address1").html(data.address1);
            },
            error: function(data) {
                alert("Error occured: Please try again.");
            }
        });

    }

    function loadSelect2POIDetailsByID(poi_id, return_type_label) {
        $("." + return_type_label + "autofill_text").html(<?php echo $not_set; ?>);
//        $("#" + return_type_label + "selected_outlet").select2("val", "");
        $("#" + return_type_label + "selected_outlet").select2('data', {poi_id: "", short_name: ""});

        $.ajax({
            type: 'POST',
            url: '<?php echo Yii::app()->createUrl('/library/poi/getPOIDetails'); ?>' + '&poi_id=' + poi_id,
            dataType: "json",
            success: function(data) {
//                $("#" + return_type_label + "selected_outlet").select2("val", data.poi_id);
                $("#" + return_type_label + "selected_outlet").select2('data', {poi_id: data.poi_id, short_name: data.short_name});
                $("#" + return_type_label + "poi_primary_code").html(data.primary_code);
                $("#" + return_type_label + "poi_address1").html(data.address1);
            },
            error: function(data) {
                alert("Error occured: Please try again.");
            }
        });

    }

    function deleteTransactionRow(delete_row_butt, selected_transaction_table, total_amount_var, total_amount_field) {
        if (!confirm('Are you sure you want to delete selected item?'))
            return false;

        var aTrs = selected_transaction_table.fnGetNodes();

        for (var i = 0; i < aTrs.length; i++) {
            $(aTrs[i]).find('input:checkbox:checked').each(function() {
                var row_data = selected_transaction_table.fnGetData(aTrs[i]);
                total_amount_var = (parseFloat(total_amount_var) - parseFloat(row_data[14]));
                total_amount_field.val(parseFloat(total_amount_var).toFixed(2));

                selected_transaction_table.fnDeleteRow(aTrs[i]);
            });
        }

        delete_row_butt.hide();
    }

</script>