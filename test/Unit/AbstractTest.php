<?php

declare(strict_types=1);

use CITool\Registry\Store;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;

abstract class AbstractTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $builder;

    protected function setUp(): void
    {
        $this->builder = new DI\ContainerBuilder();
        $this->builder->addDefinitions([
            Store::class => Di\autowire()->constructorParameter("storageFile", "/tmp/test.json")
        ]);
    }
}