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
namespace KiwiSuite\Schema\Elements;

use KiwiSuite\Contract\Type\TypeInterface;

final class SelectElement extends AbstractSingleElement
{
    private $options = [];

    /**
     * @var null
     */
    private $resource = null;

    public function type(): string
    {
        return TypeInterface::TYPE_STRING;
    }

    public function inputType(): string
    {
        return 'select';
    }

    public static function serviceName(): string
    {
        return 'select';
    }

    /**
     * @return array
     */
    public function options(): array
    {
        return $this->options;
    }

    public function withOptions(array $options): SelectElement
    {
        $element = clone $this;
        $element->options = $options;

        return $element;
    }

    public function resource(): ?array
    {
        return $this->resource;
    }

    public function withResource(string $resource, string $value, string $label): SelectElement
    {
        $element = clone $this;
        $element->resource = [
            'resource' => $resource,
            'value' => $value,
            'label' => $label,
        ];

        return $element;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $array = parent::jsonSerialize();
        $array['options'] = $this->options();
        $array['resource'] = $this->resource();

        return $array;
    }
}
