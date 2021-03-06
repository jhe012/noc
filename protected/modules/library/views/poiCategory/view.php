<?php
$this->breadcrumbs = array(
    Poi::POI_LABEL . ' Categories' => array('admin'),
    $model->category_name,
);
?>

<div class="row">
    <?php
    $this->widget('booster.widgets.TbDetailView', array(
        'data' => $model,
        'type' => 'bordered condensed',
        'attributes' => array(
            'poi_category_id',
            'company.name',
            'category_name',
            'created_date',
            'created_by',
            'updated_date',
            'updated_by',
        ),
    ));
    ?>
</div>
