<?php
/**
 * kiwi-suite/schema (https://github.com/kiwi-suite/schema)
 *
 * @package kiwi-suite/schema
 * @link https://github.com/kiwi-suite/schema
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace KiwiSuite\Schema\Entity;

use KiwiSuite\Cms\Block\BlockInterface;
use KiwiSuite\Entity\Entity\DefinitionCollection;
use KiwiSuite\Entity\Entity\EntityInterface;
use KiwiSuite\Entity\Entity\EntityTrait;
use KiwiSuite\Entity\Exception\InvalidPropertyException;
use KiwiSuite\Entity\Type\Type;

final class Block implements EntityInterface
{
    use EntityTrait;

    private $schemaProperties = [];
    /**
     * @var BlockInterface
     */
    private $block;

    /**
     * Block constructor.
     * @param array $data
     * @param DefinitionCollection $definitionCollection
     * @param BlockInterface $block
     */
    public function __construct(array $data, DefinitionCollection $definitionCollection, BlockInterface $block)
    {
        self::$definitionCollection = $definitionCollection;
        $this->applyData($data);
        $this->block = $block;
    }

    /**
     * @return DefinitionCollection
     */
    protected static function createDefinitions() : DefinitionCollection
    {
        return self::$definitionCollection;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    private function setValue(string $name, $value) : void
    {
        if ($value === null && self::getDefinitions()->get($name)->isNullAble()) {
            $this->schemaProperties[$name] = null;
            return;
        }
        $this->schemaProperties[$name] = Type::create(
            $value,
            self::getDefinitions()->get($name)->getType(),
            self::getDefinitions()->get($name)->getOptions()
        );
    }

    /**
     * @return BlockInterface
     */
    public function block(): BlockInterface
    {
        return $this->block;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name) : bool
    {
        return isset($this->schemaProperties[$name]);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (!self::getDefinitions()->has($name)) {
            throw new InvalidPropertyException(
                \sprintf("Invalid property '%s' in '%s'", $name, \get_class($this))
            );
        }

        return $this->schemaProperties[$name];
    }
}
