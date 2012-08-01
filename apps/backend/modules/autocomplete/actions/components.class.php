<?php

class ##MODULE_NAME##Components extends sfComponents
{
  public function executeAutocomplete($request)
  {
    $this->items = $this->ec_object->get##UC_MODEL_CLASS##s();
  }
}
