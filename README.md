mdGeneratorPlugin
=================

Generate Automatic Autcomplete Relations

=================

Generador Automatico del Autocompleter:

php symfony mdGenerate:generate $modelName $modelRelation

ejemplo:

Para el siguiente schema.yml de prueba:

ecColor:
  actAs:
    I18n:
      fields: [nombre]
  columns:
    id:
      type: integer(4)
      primary: true
      autoincrement: true
    nombre:
      type: string(255)
      notnull: true

ecProduct:
  columns:
    codigo:
      type: string(255)
      unique: true
  relations:
    ecColors: #### Primer Observacion: el nombre de la relacion debe llamarse $modelName . 's' ####
      class: ecColor
      refClass: ecProductToecColor
      local: ec_product_id
      foreign: ec_color_id

ecProductToecColor: #### Segunda Observacion: la tabla de relacion debe llamarse: $modelRelation . 'To' . $modelName ####
  columns:
    id:
      type: integer(4)
      primary: true
      autoincrement: true
    ec_product_id:
      type: integer(4)
      primary: true
    ec_color_id:
      type: integer(4)
      primary: true
  relations:
    ecProduct:
      local: ec_product_id
      onDelete: CASCADE
    ecColor:
      local: ec_color_id
      onDelete: CASCADE

el comando generador seria: php symfony mdGenerate:generate ecColor ecProduct

Ahora ya puedes incluir el componente:

<?php include_component($modelName . 'Backend', 'autocomplete', array('ec_object' => $ec_object)); ?>
