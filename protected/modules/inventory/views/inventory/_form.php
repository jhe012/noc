<style>
    .typeahead {
        background-color: #fff;
    }
    .tt-dropdown-menu {
        width: 422px;
        margin-top: 12px;
        padding: 8px 0;
        background-color: #fff;
        border: 1px solid #ccc;
        border: 1px solid rgba(0, 0, 0, 0.2);
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
        -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
        box-shadow: 0 5px 10px rgba(0,0,0,.2);
    }
    .tt-suggestion {
        padding: 8px 20px;
        font-size: 14px;
        line-height: 18px;
    }

    .tt-suggestion + .tt-suggestion {
        font-size: 14px;
        border-top: 1px solid #ccc;
    }

    .tt-suggestions .repo-language {
        float: right;
        font-style: italic;
    }

    .tt-suggestions .repo-name {
        font-size: 20px;
        font-weight: bold;
    }

    .tt-suggestions .repo-description {
        margin: 0;
    }

    .twitter-typeahead .tt-suggestion.tt-cursor {
        color: #03739c;
    }

    #scrollable-dropdown-menu .tt-dropdown-menu {
        max-height: 150px;
        overflow-y: auto;
    }


</style>
<?php
$baseUrl = Yii::app()->theme->baseUrl;
$cs = Yii::app()->getClientScript();
$cs->registerScriptFile($baseUrl . '/js/plugins/input-mask/jquery.inputmask.js', CClientScript::POS_END);
$cs->registerScriptFile($baseUrl . '/js/plugins/input-mask/jquery.inputmask.date.extensions.js', CClientScript::POS_END);
$cs->registerScriptFile($baseUrl . '/js/plugins/input-mask/jquery.inputmask.extensions.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/handlebars-v1.3.0.js', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/typeahead.bundle.js', CClientScript::POS_END);
?>

<div class="row">

    <!-- left column -->
    <div class="col-md-5">
        <!-- general form elements -->
        <div class="box box-solid box-primary">
            <div class="box-header">
                <h3 class="box-title">Create a New Inventory Record</h3>
            </div><!-- /.box-header -->

            <?php
            $form = $this->beginWidget('booster.widgets.TbActiveForm', array(
                'id' => 'inventory-form',
//                'enableClientValidation'=>true,
//                'clientOptions'=>array(
//                        'validateOnSubmit'=>true,
//                ),
            ));
            ?>

            <div class="box-body">

                <?php /* if ($form->errorSummary($model)) { ?>
                  <div class="alert alert-danger alert-dismissable">
                  <i class="fa fa-ban"></i>
                  <?php echo $form->errorSummary($model); ?></div>
                  <?php } */ ?>        


                <div class="form-group">
                    <?php echo $form->textFieldGroup($model, 'sku_code', array('widgetOptions' => array('htmlOptions' => array('class' => 'span5 typeahead', 'maxlength' => 50)))); ?>

                    <?php
                    /*
                      echo $form->dropDownListGroup(
                      $model, 'sku_id', array(

                      'widgetOptions' => array(
                      'data' => $sku,
                      'htmlOptions' => array('multiple' => false, 'prompt' => 'Select Sku'),
                      )
                      )
                      );
                     */
                    ?>
                    <b>Sku Name:</b> <span id="sku_name"><?php echo $selectedSkuname; ?></span><br/>
                    <b>Brand Name:</b> <span id="brand_name"><?php echo $selectedSkuBrand; ?></span>
                </div>

                <?php echo $form->textFieldGroup($model, 'qty', array('widgetOptions' => array('htmlOptions' => array('class' => 'span5', 'value' => 1)))); ?>

                <?php
                echo $form->dropDownListGroup(
                        $model, 'default_uom_id', array(
                    'wrapperHtmlOptions' =>
                    array(
                        'class' => 'col-sm-5',
                    ),
                    'widgetOptions' => array(
                        'data' => $uom,
                        'htmlOptions' => array('multiple' => false, 'prompt' => 'Select UOM'),
                    )
                        )
                );
                ?>

                <?php
                echo $form->dropDownListGroup(
                        $model, 'default_zone_id', array(
                    'wrapperHtmlOptions' => array(
                        'class' => 'col-sm-5',
                    ),
                    'widgetOptions' => array(
                        'data' => $zone,
                        'htmlOptions' => array('multiple' => false, 'prompt' => 'Select Zone'),
                    )
                        )
                );
                ?>

                <?php
                echo $form->textFieldGroup($model, 'cost_per_unit', array('widgetOptions' => array('htmlOptions' => array())
                    , 'prepend' => 'P'
                    , 'append' => '.00'
                ));
                ?>

                <?php // echo $form->textFieldGroup($model, 'sku_status_id', array('widgetOptions' => array('htmlOptions' => array('class' => 'span5', 'maxlength' => 50)))); ?>
                <?php
                echo $form->dropDownListGroup(
                        $model, 'sku_status_id', array(
                    'wrapperHtmlOptions' => array(
                        'class' => 'col-sm-5',
                    ),
                    'widgetOptions' => array(
                        'data' => $sku_status,
                        'htmlOptions' => array('multiple' => false, 'prompt' => 'Select ' . Sku::SKU_LABEL . ' Status'),
                    )
                        )
                );
                ?>

                <?php
                echo $form->textFieldGroup($model, 'transaction_date', array('widgetOptions' => array('htmlOptions' => array('value' => date('Y-m-d'), 'data-inputmask' => "'alias': 'yyyy-mm-dd'", 'data-mask' => 'data-mask'))
                    , 'prepend' => '<i class="glyphicon glyphicon-calendar"></i>'
                ));
                ?>

                <?php
                echo $form->textFieldGroup($model, 'unique_tag', array('widgetOptions' => array('htmlOptions' => array('maxlength' => 250))
                    , 'prepend' => '<i class="fa fa-tag"></i>'
                ));
                ?>

                <?php
                echo $form->textFieldGroup($model, 'unique_date', array('widgetOptions' => array('htmlOptions' => array('data-inputmask' => "'alias': 'yyyy-mm-dd'", 'data-mask' => 'data-mask'))
                    , 'prepend' => '<i class="glyphicon glyphicon-calendar"></i>'
                ));
                ?>

                <div class="form-group">
                    <?php echo CHtml::submitButton('Create & New', array('name' => 'create', 'class' => 'btn btn-primary btn-flat')); ?>
                    <?php echo CHtml::submitButton('Save', array('name' => 'save', 'class' => 'btn btn-primary btn-flat')); ?>
                    <?php echo CHtml::resetButton('Reset', array('class' => 'btn btn-primary btn-flat')); ?>
                </div>

            </div>

            <?php $this->endWidget(); ?>

        </div>
    </div>

    <!-- right column -->
    <div class="col-md-7">
        <?php
        echo $this->renderPartial('_inventory_history', array('recentlyCreatedItems' => $recentlyCreatedItems));
        ?>
    </div>

</div>

<script type="text/javascript">
    $(function() {
        //Datemask dd/mm/yyyy
        $("[data-mask]").inputmask();

        var sku = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('sku_name'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
//            prefetch: 'http://localhost/noc/index.php?r=library/sku/search&value=',
//            remote: 'http://localhost/noc/index.php?r=library/sku/search&value=%QUERY'
            prefetch: '<?php echo Yii::app()->createUrl("library/sku/search", array('value' => '')) ?>',
            remote: '<?php echo Yii::app()->createUrl("library/sku/search") ?>&value=%QUERY'
        });

        sku.initialize();

        $('#CreateInventoryForm_sku_code').typeahead(null, {
            name: 'skus',
            displayKey: 'sku_code',
            source: sku.ttAdapter(),
            templates: {
                suggestion: Handlebars.compile([
                    '<p class="repo-language">{{brand}}</p>',
                    '<p class="repo-name">{{sku_code}}</p>',
                    '<p class="repo-description">{{value}}</p>'
                ].join(''))
            }

        }).on('typeahead:selected', function(obj, datum) {
        
            $('#sku_name').html(datum.value);
            $('#brand_name').html(datum.brand);
            $('#CreateInventoryForm_default_uom_id').val(datum.uom_id);
            $('#CreateInventoryForm_default_zone_id').val(datum.zone_id);
            $('#CreateInventoryForm_cost_per_unit').val(datum.cost_per_unit);

        });

    });
</script>