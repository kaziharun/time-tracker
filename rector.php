<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/public',
        __DIR__.'/src',
    ])
    // uncomment to reach your current PHP version
   // ->withPhpSets(php83: true)
    ->withSets([
        SetList::PHP_83,
        SetList::TYPE_DECLARATION,
    ])
    ->withRules([
        AddVoidReturnTypeWhereNoReturnRector::class,
    ]);
