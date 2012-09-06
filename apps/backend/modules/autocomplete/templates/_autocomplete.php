<?php use_helper('mdAsset') ?>
<?php

//required for TextboxList`
use_javascript('../mdGeneratorPlugin/js/TextboxList/GrowingInput.js');
use_javascript('../mdGeneratorPlugin/js/TextboxList/SuggestInput.js');
use_javascript('../mdGeneratorPlugin/js/TextboxList/TextboxList.js');
use_javascript('../mdGeneratorPlugin/js/TextboxList/TextboxList.Autocomplete.js');
//required for TextboxList.Autocomplete if method set to 'binary'
use_javascript('../mdGeneratorPlugin/js/TextboxList/TextboxList.Autocomplete.Binary.js');


use_stylesheet('../mdGeneratorPlugin/css/TextboxList/TextboxList.css', 'last');
use_stylesheet('../mdGeneratorPlugin/css/TextboxList/TextboxList.Autocomplete.css', 'last');
?>

<div class="##MODEL_CLASS##">
  <h3><?php echo __('Productos_##MODEL_CLASS##', array(), 'messages') ?></h3>

  <p style="margin-bottom: 1em;">
    <?php echo __('Productos_##MODEL_CLASS##, Escriba items que desea agregar o borre los ya existentes. Los cambios se ejecutan instantáneamente.'); ?>
  </p>

  <div class="autosuggest">  
    <input id="##MODEL_CLASS##">
  </div>
</div>
<script type="text/javascript">
  $(function(){
				
    //manejo js de la lista y el autocomplete
				
    var _EC_OBJECT_ID = '<?php echo $ec_object->getId(); ?>';
    // Autocomplete initialization
    var t4 = new $.TextboxList('###MODEL_CLASS##', {unique: true, 
      plugins: {autocomplete: {onlyFromValues: true,placeholder: '<?php echo __('Productos_Escriba para recibir sugerencias'); ?>'}}});

    //cargo por ajax las opciones
    t4.getContainer().addClass('textboxlist-loading');				
    $.ajax({
      url:   __MD_CONTROLLER_BACKEND_SYMFONY + "/##MODULE_NAME##/getItems",
      type: 'post',
      data: 'ec_object_id=' + _EC_OBJECT_ID,
      dataType: 'json',
      success: function(r){
        t4.plugins['autocomplete'].setValues(r);
        t4.getContainer().removeClass('textboxlist-loading');
      }});
				
    //agrego los que ya existen
    <?php foreach ($items as $item): ?>
      t4.add('<?php echo $item->getNombre(); ?>',<?php echo $item->getId() ?>);
    <?php endforeach; ?>
				
    //cuando se agrega uno envio el ajax para salvarlo
    t4.addEvent('bitBoxAdd', function(box){
      t4.getContainer().addClass('textboxlist-loading');
      $.ajax({
        url:   __MD_CONTROLLER_BACKEND_SYMFONY + "/##MODULE_NAME##/addItem",
        type: 'post',
        dataType: 'json',
        data: [{name: "ec_object_id", value: _EC_OBJECT_ID}, {name: "##ROUTE_NAME##_id", value: box.value[0]}],
        success: function(r){
          t4.getContainer().removeClass('textboxlist-loading');
        }
      });
    });
    t4.addEvent('bitBoxRemove', function(box){
      t4.getContainer().addClass('textboxlist-loading');
      $.ajax({
        url:   __MD_CONTROLLER_BACKEND_SYMFONY + "/##MODULE_NAME##/removeItem",
        type: 'post',
        dataType: 'json',
        data: [{name: "ec_object_id", value: _EC_OBJECT_ID}, {name: "##ROUTE_NAME##_id", value: box.value[0]}],
        success: function(r){
          t4.getContainer().removeClass('textboxlist-loading');
        }
      });
    });
  });
</script>