<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Package\Schema;

interface SchemaProviderInterface
{
    public function provideSchema($name, BuilderInterface $builder, $options = []): SchemaInterface;
}
