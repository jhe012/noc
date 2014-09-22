<?php
$this->breadcrumbs = array(
    'Outgoing Inventories' => array('admin'),
    'Manage',
);


Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
$('.search-form').toggle();
return false;
});
$('.search-form form').submit(function(){
$.fn.yiiGridView.update('outgoing-inventory-grid', {
data: $(this).serialize()
});
return false;
});
");
?>

<style type="text/css">

    #outgoing-inventory_table tbody tr { cursor: pointer }

    .hide_row { display: none; }

</style>  

<?php // echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn btn-primary btn-flat')); ?>
<?php echo CHtml::link('Create', array('OutgoingInventory/create'), array('class' => 'btn btn-primary btn-flat')); ?>

<div class="btn-group">
    <button type="button" class="btn btn-info btn-flat">More Options</button>
    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li><a href="#">Download All Records</a></li>
        <li><a href="#">Download All Filtered Records</a></li>
        <!--<li><a href="#">Upload</a></li>-->
    </ul>
</div>
<br/>
<br/>

<?php $fields = OutgoingInventory::model()->attributeLabels(); ?>
<div class="box-body table-responsive">
    <table id="outgoing-inventory_table" class="table table-bordered">
        <thead>
            <tr>
                <th><?php echo $fields['rra_no']; ?></th>
                <th><?php echo $fields['rra_name']; ?></th>
                <th><?php echo $fields['dr_no']; ?></th>
                <th><?php echo $fields['destination_zone_id']; ?></th>
                <th><?php echo $fields['campaign_no']; ?></th>
                <th><?php echo $fields['pr_no']; ?></th>
                <th><?php echo $fields['status']; ?></th>
                <th><?php echo $fields['contact_person']; ?></th>
                <th><?php echo $fields['total_amount']; ?></th>
                <th><?php echo $fields['created_date']; ?></th>
                <!--<th>Actions</th>-->
            </tr>
        </thead>
        <thead>
            <tr id="filter_row">
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
            </tr>
        </thead>
    </table>
</div><br/><br/><br/>

<h4 class="control-label text-primary"><b>Item Details Table</b></h4>
<?php $skuFields = Sku::model()->attributeLabels(); ?>
<?php $outgoingInvFields = OutgoingInventoryDetail::model()->attributeLabels(); ?>
<div class="box-body table-responsive">
    <table id="outgoing-inventory-details_table" class="table table-bordered">
        <thead>
            <tr>
                <th><?php echo $outgoingInvFields['batch_no']; ?></th>
                <th><?php echo $skuFields['sku_code']; ?></th>
                <th><?php echo $skuFields['sku_name']; ?></th>
                <th><?php echo $skuFields['brand_id']; ?></th>
                <th><?php echo $outgoingInvFields['source_zone_id']; ?></th>
                <th><?php echo $outgoingInvFields['unit_price']; ?></th>
                <th><?php echo $outgoingInvFields['planned_quantity']; ?></th>
                <th><?php echo $outgoingInvFields['quantity_issued']; ?></th>
                <th><?php echo $outgoingInvFields['amount']; ?></th>
                <th><?php echo $outgoingInvFields['return_date']; ?></th>
                <th><?php echo $outgoingInvFields['remarks']; ?></th>
            </tr>
        </thead>
        <thead>
            <tr id="filter_row">
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
                <td class="filter"></td>
            </tr>
        </thead>
    </table>
</div>

<script type="text/javascript">

    var outgoing_inventory_table;
    var outgoing_inventory_table_detail;
    $(function() {
        outgoing_inventory_table = $('#outgoing-inventory_table').dataTable({
            "filter": true,
            "dom": 'l<"text-center"r>t<"pull-left"i><"pull-right"p>',
            "processing": true,
            "serverSide": true,
            "bAutoWidth": false,
            "order": [[9, "desc"]],
            "ajax": "<?php echo Yii::app()->createUrl($this->module->id . '/OutgoingInventory/data'); ?>",
            "columns": [
                {"name": "rra_no", "data": "rra_no"},
                {"name": "rra_name", "data": "rra_name"},
                {"name": "dr_no", "data": "dr_no"},
                {"name": "destination_zone_name", "data": "destination_zone_name"},
                {"name": "campaign_no", "data": "campaign_no"},
                {"name": "pr_no", "data": "pr_no"},
                {"name": "status", "data": "status"},
                {"name": "contact_person", "data": "contact_person"},
                {"name": "total_amount", "data": "total_amount"},
                {"name": "created_date", "data": "created_date"},
//                {"name": "links", "data": "links", 'sortable': false}
            ],
            "columnDefs": [{
                    "targets": [9],
                    "visible": false
                }]
        });

        $('#outgoing-inventory_table tbody').on('click', 'tr', function() {
            if ($(this).hasClass('success')) {
                $(this).removeClass('success');
                loadOutgoingInvDetails(null);
            }
            else {
                outgoing_inventory_table.$('tr.success').removeClass('success');
                $(this).addClass('success');
                var row_data = outgoing_inventory_table.fnGetData(this);
                loadOutgoingInvDetails(row_data.outgoing_inventory_id);
            }
        });

        var i = 0;
        $('#outgoing-inventory_table thead tr#filter_row td.filter').each(function() {
            $(this).html('<input type="text" class="form-control input-sm" placeholder="" colPos="' + i + '" />');
            i++;
        });

        $("#outgoing-inventory_table thead input").keyup(function() {
            outgoing_inventory_table.fnFilter(this.value, $(this).attr("colPos"));
        });

        outgoing_inventory_table_detail = $('#outgoing-inventory-details_table').dataTable({
            "filter": true,
            "dom": '<"text-center"r>t',
            "bSort": false,
            "processing": false,
            "serverSide": false,
            "bAutoWidth": false
        });

        var i = 0;
        $('#outgoing-inventory-details_table thead tr#filter_row td.filter').each(function() {
            $(this).html('<input type="text" class="form-control input-sm" placeholder="" colPos="' + i + '" />');
            i++;
        });

        $("#outgoing-inventory-details_table thead input").keyup(function() {
            outgoing_inventory_table_detail.fnFilter(this.value, $(this).attr("colPos"));
        });

        $('#btnSearch').click(function() {
            table.fnMultiFilter({
                "outgoing_inventory_id": $("#OutgoingInventory_outgoing_inventory_id").val(), "rra_no": $("#OutgoingInventory_rra_no").val(), "rra_name": $("#OutgoingInventory_rra_name").val(), "destination_zone_id": $("#OutgoingInventory_destination_zone_id").val(), "contact_person": $("#OutgoingInventory_contact_person").val(), "contact_no": $("#OutgoingInventory_contact_no").val(), "address": $("#OutgoingInventory_address").val(), });
        });

        jQuery(document).on('click', '#outgoing-inventory_table a.delete', function() {
            if (!confirm('Are you sure you want to delete this item?'))
                return false;
            $.ajax({
                'url': jQuery(this).attr('href') + '&ajax=1',
                'type': 'POST',
                'dataType': 'text',
                'success': function(data) {
                    $.growl(data, {
                        icon: 'glyphicon glyphicon-info-sign',
                        type: 'success'
                    });

                    table.fnMultiFilter();
                },
                error: function(jqXHR, exception) {
                    alert('An error occured: ' + exception);
                }
            });
            return false;
        });
    });

    function loadOutgoingInvDetails(outgoing_inv_id) {

        $.ajax({
            type: 'POST',
            url: '<?php echo Yii::app()->createUrl('/inventory/OutgoingInventory/outgoingInvDetailData'); ?>' + '&outgoing_inv_id=' + outgoing_inv_id,
            dataType: "json",
            success: function(data) {

                var oSettings = outgoing_inventory_table_detail.fnSettings();
                var iTotalRecords = oSettings.fnRecordsTotal();
                for (var i = 0; i <= iTotalRecords; i++) {
                    outgoing_inventory_table_detail.fnDeleteRow(0, null, true);
                }

                $.each(data.data, function(i, v) {
                    outgoing_inventory_table_detail.fnAddData([
                        v.batch_no,
                        v.sku_code,
                        v.sku_name,
                        v.brand_name,
                        v.source_zone_name,
                        v.unit_price,
                        v.planned_quantity,
                        v.quantity_issued,
                        v.amount,
                        v.return_date,
                        v.remarks
                    ]);
                });
            },
            error: function(data) {
                alert("Error occured: Please try again.");
            }
        });
    }

</script>