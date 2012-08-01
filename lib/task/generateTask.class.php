<?php

class generateTask extends sfBaseTask
{
  protected function configure()
  {
    // add your own arguments here
		$this->addArguments(array(
       new sfCommandArgument('model', sfCommandArgument::REQUIRED, ''),
       new sfCommandArgument('modelRelation', sfCommandArgument::REQUIRED, '')
     ));

    $this->addOptions(array(
      //new sfCommandOption('chugas', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'backend'),
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'backend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'mdGenerate';
    $this->name             = 'generate';
    $this->briefDescription = 'Generador automatico';
    $this->detailedDescription = <<<EOF
The [mdCheckNewNewsletters|INFO] task does things.
Call it with:

  [php symfony mdCheckNewNewsletters|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $model = $arguments['model'];
    $relationModelName = $arguments['modelRelation'];
    $name = strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), '\\1_\\2', $model));
    $relationName = strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), '\\1_\\2', $relationModelName));
    $module = $model . 'Backend';
    
    $this->runTask('doctrine:generate-admin', array('application' => 'backend', 'route_or_model' => $model), array('module' => $module));
    
    $moduleDir = sfConfig::get('sf_app_module_dir') .'/' . $module;
    $pluginDir = sfConfig::get('sf_plugins_dir') . '/mdGeneratorPlugin/apps/backend/modules/autocomplete';    
        
    // create basic application structure
    $finder = sfFinder::type('any')->discard('.sf');
    
    $this->getFilesystem()->remove($moduleDir . '/actions/actions.class.php');
    $this->getFilesystem()->remove($moduleDir . '/templates/_autocomplete.php');
    
    $this->getFilesystem()->mirror($pluginDir, $moduleDir, $finder);
    
    // customize php and yml files
    $finder = sfFinder::type('file')->name('*.php', '*.yml');
    
    $properties = parse_ini_file(sfConfig::get('sf_config_dir').'/properties.ini', true);

    $constants = array(
      'PROJECT_NAME'          => isset($properties['symfony']['name']) ? $properties['symfony']['name'] : 'symfony',
      'MODULE_NAME'           => $module,
      'UC_MODULE_NAME'        => ucfirst($module),
      'MODEL_CLASS'           => $model,
      'UC_MODEL_CLASS'           => ucfirst($model),      
      'AUTHOR_NAME'           => isset($properties['symfony']['author']) ? $properties['symfony']['author'] : 'Your name here',
      'MODEL_RELATION'        => $relationModelName,
      'ROUTE_NAME'            => $name,
      'ROUTE_RELATION_NAME'   => $relationName
    );
    
    $this->getFilesystem()->replaceTokens($finder->in($moduleDir), '##', '##', $constants);

    exit(0);
  }
}
