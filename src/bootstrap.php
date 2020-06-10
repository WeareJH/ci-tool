<?php

use CITool\Registry\Store;

$builder = new DI\ContainerBuilder();
$builder->addDefinitions([
    Store::class => Di\autowire()->constructorParameter("storageFile", "/var/registry.json")
]);
$container = $builder->build();
  