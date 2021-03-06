<div class="row">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-md-12">

                <?php
                $form = $this->beginWidget('booster.widgets.TbActiveForm', array(
                    'id' => 'sku-form', 'enableAjaxValidation' => false,
                ));
                ?>

                <div class="row">
                    <div class="col-md-6">

                        <?php /* if($form->errorSummary($model)){?><div class="alert alert-danger alert-dismissable">
                          <i class="fa fa-ban"></i>
                          <?php echo $form->errorSummary($model); ?></div>
                         */ ?>   

                        <div class="form-group">
                            <?php echo $form->textFieldGroup($model, 'sku_code', array('widgetOptions' => array('htmlOptions' => array('class' => 'span5', 'maxlength' => 50)))); ?>
                        </div>

                        <div class="form-group">
                            <?php // echo $form->textFieldGroup($model, 'brand_id', array('widgetOptions' => array('htmlOptions' => array('class' => 'span5', 'maxlength' => 50))));  ?>
                            <?php
                            echo $form->dropDownListGroup(
                                    $model, 'brand_id', array('wrapperHtmlOptions' => array(
                                    'class' => 'col-sm-5',
                                ),
                                'widgetOptions' => array(
                                    'data' => $brand,
                                    'htmlOptions' => array('multiple' => false, 'prompt' => 'Select Brand'
                                    ),)));
                            ?>
                        </div>

                        <div class="form-group">
                            <?php echo $form->textFieldGroup($model, 'sku_name', array('widgetOptions' => array('htmlOptions' => array('class' => 'span5', 'maxlength' => 150)))); ?>
                        </div>

                        <div class="form-group">
                            <?php echo $form->textFieldGroup($model, 'description', array('widgetOptions' => array('htmlOptions' => array('class' => 'span5', 'maxlength' => 150)))); ?>
                        </div>

                        <div id="sku_custom_datas">

                            <?php
                            echo $this->renderPartial('_customItems', array('model' => $model, 'custom_datas' => $custom_datas, 'sku_custom_data' => $sku_custom_data, 'form' => $form,));
                            ?>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <h4 class="control-label text-primary"><b>Item Settings</b></h4>

                        <div class="form-group">
                            <?php // echo $form->textFieldGroup($model, 'default_uom_id', array('widgetOptions' => array('htmlOptions' => array('class' => 'span5', 'maxlength' => 50))));  ?>
                            <?php
                            echo $form->dropDownListGroup(
                                    $model, 'default_uom_id', array(
                                'wrapperHtmlOptions' => array(
                                    'class' => 'col-sm-5',
                                ),
                                'widgetOptions' => array(
                                    'data' => $uom,
                                    'htmlOptions' => array('multiple' => false, 'prompt' => 'Select UOM'
                                    ),)));
                            ?>
                        </div>

                        <div class="form-group">
                            <?php echo $form->textFieldGroup($model, 'default_unit_price', array('widgetOptions' => array('htmlOptions' => array('class' => 'span5', 'maxlength' => 18)))); ?>
                        </div>

                        <div class="form-group">
                            <?php // echo $form->textFieldGroup($model, 'type', array('widgetOptions' => array('htmlOptions' => array('class' => 'span5', 'maxlength' => 50)))); ?>
                            <?php
                            echo $form->dropDownListGroup(
                                    $model, 'type', array(
                                'wrapperHtmlOptions' => array(
                                    'class' => 'col-sm-5',
                                ),
                                'widgetOptions' => array(
                                    'data' => $sku_category,
                                    'htmlOptions' => array('multiple' => false, 'prompt' => 'Select ' . Sku::SKU_LABEL . ' Category', 'id' => 'sku_category',
                                    ),)));
                            ?>
                        </div>

                        <div id="sku_sub_category" class="form-group" style="<?php echo isset($_POST['Sku']['type']) && $_POST['Sku']['type'] == Sku::INFRA ? "display: block;" : "display: none;"; ?>">
                            <?php
                            echo $form->dropDownListGroup(
                                    $model, 'sub_type', array(
                                'wrapperHtmlOptions' => array(
                                    'class' => 'col-sm-5',
                                ),
                                'widgetOptions' => array(
                                    'data' => $infra_sub_category,
                                    'htmlOptions' => array('multiple' => false, 'prompt' => 'Select ' . Sku::SKU_LABEL . ' Sub Category', 'id' => 'sku_sub_type',
                                    ),)));
                            ?>
                        </div>

                        <div class="form-group">
                            <?php // echo $form->textFieldGroup($model, 'default_zone_id', array('widgetOptions' => array('htmlOptions' => array('class' => 'span5', 'maxlength' => 50))));  ?>
                            <?php
                            echo $form->dropDownListGroup(
                                    $model, 'default_zone_id', array(
                                'wrapperHtmlOptions' => array(
                                    'class' => 'col-sm-5',
                                ),
                                'widgetOptions' => array(
                                    'data' => $zone,
                                    'htmlOptions' => array('multiple' => false, 'prompt' => 'Select Zone'
                                    ),)));
                            ?>
                        </div>

                        <div class="form-group">
                            <?php echo $form->textFieldGroup($model, 'supplier', array('widgetOptions' => array('htmlOptions' => array('class' => 'span5', 'maxlength' => 250)))); ?>
                        </div>

                        <h4 class="control-label text-primary"><b>Restock Levels</b></h4>

                        <div class="form-group">
                            <?php echo $form->textFieldGroup($model, 'low_qty_threshold', array('widgetOptions' => array('htmlOptions' => array('class' => 'span5')))); ?>
                        </div>

                        <div class="form-group">
                            <?php echo $form->textFieldGroup($model, 'high_qty_threshold', array('widgetOptions' => array('htmlOptions' => array('class' => 'span5')))); ?>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn btn-primary btn-flat')); ?>
                            <?php echo CHtml::resetButton('Reset', array('class' => 'btn btn-primary btn-flat')); ?>
                        </div>
                    </div>
                </div>

                <?php $this->endWidget(); ?>                       

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function onlyDotsAndNumbers(txt, event, dots) {

        var charCode = (event.which) ? event.which : event.keyCode;

        if (charCode == 46) {
            if (dots == 0) {
                return false;
            }
            if (txt.value.indexOf(".") < 0) {
                return true;
            } else {
                return false;
            }
        }

        if (txt.value.indexOf(".") > 0) {
            var txtlen = txt.value.length;
            var dotpos = txt.value.indexOf(".");

            if ((txtlen - dotpos) > dots) {
                return false;
            }
        }

        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

        return true;
    }

    $('#sku_category').change(function() {
        var sku_category = $("#sku_category").val();

        if (sku_category == "INFRA") {
            $("#sku_sub_category").show();
        } else {
            $("#sku_sub_category").hide();
            $("#sku_sub_type").val("");
        }
    });

</script>

