
<?php

$this->breadcrumbs = array(
    Sku::SKU_LABEL . ' Custom Datas' => array('admin'),
    'Create',
);
?>

<?php

echo $this->renderPartial('_form', array(
    'model' => $model,
    'unserialize_attribute' => $unserialize_attribute,
    'sku_custom_data' => $sku_custom_data,
));
?>