<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Schema\Element;

use Ixocreate\Schema\Type\TypeInterface;

final class MultiSelectElement extends AbstractSingleElement
{
    /**
     * @var array
     */
    private $options = [];

    /**
     * @var null
     */
    private $resource = null;

    /**
     * @var bool
     */
    private $createNew = false;

    /**
     * @var bool
     */
    private $createNewDeferred = false;

    /**
     * @var bool
     */
    private $extendedSelect = false;

    /**
     * @return string
     */
    public function type(): string
    {
        return TypeInterface::TYPE_ARRAY;
    }

    /**
     * @return string
     */
    public function inputType(): string
    {
        return 'multiselect';
    }

    /**
     * @return string
     */
    public static function serviceName(): string
    {
        return 'multiselect';
    }

    /**
     * @return array
     */
    public function options(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return MultiSelectElement
     */
    public function withOptions(array $options): MultiSelectElement
    {
        $element = clone $this;
        $element->options = $options;

        return $element;
    }

    /**
     * @return array|null
     */
    public function resource(): ?array
    {
        return $this->resource;
    }

    /**
     * @param string $resource
     * @param string $value
     * @param string $label
     * @return MultiSelectElement
     */
    public function withResource(string $resource, string $value, string $label): MultiSelectElement
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
     * @return bool
     */
    public function createNew(): bool
    {
        return $this->createNew;
    }

    /**
     * @return bool
     */
    public function createNewDeferred(): bool
    {
        return $this->createNewDeferred;
    }

    /**
     * @return MultiSelectElement
     */
    public function withCreateNew(): MultiSelectElement
    {
        $element = clone $this;
        $element->createNew = true;
        return $element;
    }

    /**
     * @return MultiSelectElement
     */
    public function withCreateNewDeferred(): MultiSelectElement
    {
        $element = clone $this;
        $element->createNew = true;
        $element->createNewDeferred = true;
        return $element;
    }

    /**
     * @return bool
     */
    public function extendedSelect(): bool
    {
        return $this->extendedSelect;
    }

    /**
     * @param bool $extendedSelect
     * @return MultiSelectElement
     */
    public function withExtendedSelect(bool $extendedSelect): MultiSelectElement
    {
        $element = clone $this;
        $element->extendedSelect = $extendedSelect;

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
        $array['createNew'] = $this->createNew();
        $array['createNewDeferred'] = $this->createNewDeferred();
        $array['extendedSelect'] = $this->extendedSelect();

        return $array;
    }
}
