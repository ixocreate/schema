<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Schema\Type;

use Ixocreate\Application\Bootstrap\BootstrapItemInterface;
use Ixocreate\Application\Configurator\ConfiguratorInterface;

class TypeBootstrapItem implements BootstrapItemInterface
{
    /**
     * @return ConfiguratorInterface
     */
    public function getConfigurator(): ConfiguratorInterface
    {
        return new TypeConfigurator();
    }

    /**
     * @return string
     */
    public function getVariableName(): string
    {
        return 'type';
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return 'type.php';
    }
}
