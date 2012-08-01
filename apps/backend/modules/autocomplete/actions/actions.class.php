<?php

require_once dirname(__FILE__).'/../lib/##MODULE_NAME##GeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/##MODULE_NAME##GeneratorHelper.class.php';

/**
 * ##MODULE_NAME## actions.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage ##MODULE_NAME##
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ##MODULE_NAME##Actions extends auto##UC_MODULE_NAME##Actions
{

  public function executeGetItems(sfWebRequest $request) {
    $result = array();
    $items = array();
    $result['ec_items'] = array();
    $excepts = array();

    if ($request->getParameter('ec_object_id')) {
      $ec_object_id = $request->getParameter('ec_object_id');
      $ecObject = Doctrine::getTable('##MODEL_RELATION##')->find($ec_object_id);
    }

    $datas = Doctrine::getTable('##MODEL_CLASS##')->findAll();

    // the format is: 
    // id, searchable plain text, html (for the textboxlist item, if empty the plain is used), html (for the autocomplete dropdown)

    foreach ($datas as $data) {
      if (!array_key_exists($data->getId(), $excepts))
        $items[] = array(intval($data->getId()), $data->getNombre());
    }

    $result['ec_items'] = $items;

    return $this->renderText(json_encode($items));
  }
  
  public function executeAddItem($request) {
    $ec_object_id = $request->getParameter('ec_object_id');
    $ec_item_id = $request->getParameter('##ROUTE_NAME##_id');

    $p_to_s = new ##MODEL_RELATION##To##MODEL_CLASS##();
    $p_to_s->set##MODEL_RELATION##Id($ec_object_id);
    $p_to_s->set##MODEL_CLASS##Id($ec_item_id);
    $response = ($p_to_s->save() !== false);

    return $this->renderText(mdBasicFunction::basic_json_response($response, array()));
  }

  public function executeRemoveItem(sfWebRequest $request) {
    $ec_object_id = $request->getParameter('ec_object_id');
    $ec_item_id = $request->getParameter('##ROUTE_NAME##_id');

    $p_to_s = Doctrine::getTable('##MODEL_RELATION##To##UC_MODEL_CLASS##')->findByec_product_idAndec_color_id($ec_object_id, $ec_item_id);
    foreach ($p_to_s as $del)
      $del->delete();

    return $this->renderText(mdBasicFunction::basic_json_response(true, array()));
  }

}
