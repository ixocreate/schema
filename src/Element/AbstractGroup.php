<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Schema\Element;

use Ixocreate\Schema\SchemaInterface;
use Ixocreate\Schema\SchemaReceiverInterface;
use Ixocreate\Schema\Type\SchemaType;
use Ixocreate\Schema\Type\TransformableInterface;
use Ixocreate\Schema\Type\Type;
use Ixocreate\Schema\Type\TypeInterface;

abstract class AbstractGroup extends AbstractElement implements GroupInterface, TransformableInterface
{
    /**
     * @var ElementInterface[]
     */
    protected $elements = [];

    /**
     * @var SchemaReceiverInterface|null
     */
    protected $schemaReceiver;

    /**
     * @return ElementInterface[]
     */
    public function elements(): array
    {
        return $this->elements;
    }

    /**
     * @return ElementInterface[]
     */
    public function all(): array
    {
        $elements = [];

        foreach ($this->elements() as $name => $element) {
            if (!($element instanceof StructuralGroupingInterface)) {
                $elements[$name] = $element;
                continue;
            }

            foreach ($element->all() as $innerName => $innerElement) {
                $elements[$innerName] = $innerElement;
            }
        }
        return $elements;
    }

    /**
     * @param string $name
     * @return ElementInterface
     */
    public function get(string $name): ElementInterface
    {
        return $this->elements[$name];
    }

    /**
     * @param string $name
     * @return SchemaInterface
     */
    public function remove(string $name): SchemaInterface
    {
        $group = clone $this;

        if (!\array_key_exists($name, $group->elements)) {
            return $group;
        }

        unset($group->elements[$name]);

        return $group;
    }

    /**
     * @param array $elements
     * @return SchemaInterface
     */
    public function withElements(array $elements): SchemaInterface
    {
        $group = $this;
        foreach ($elements as $element) {
            $group = $group->withAddedElement($element);
        }

        return $group;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->elements);
    }

    /**
     * @param ElementInterface $element
     * @return SchemaInterface
     */
    public function withAddedElement(ElementInterface $element): SchemaInterface
    {
        $schema = clone $this;
        $schema->elements[$element->name()] = $element;

        return $schema;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $array = parent::jsonSerialize();
        $array['elements'] = \array_values($this->elements());

        return $array;
    }

    public function transform($data): TypeInterface
    {
        return Type::create($data, SchemaType::class, ['schema' => $this]);
    }

    public function withSchemaReceiver(SchemaReceiverInterface $schemaReceiver): SchemaInterface
    {
        $group = clone $this;
        $group->schemaReceiver = $schemaReceiver;

        return $group;
    }

    public function schemaReceiver(): ?SchemaReceiverInterface
    {
        return $this->schemaReceiver;
    }
}
