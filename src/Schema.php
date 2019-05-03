<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Schema;

use Ixocreate\Schema\Element\ElementInterface;
use Ixocreate\Schema\Type\SchemaType;
use Ixocreate\Schema\Type\TransformableInterface;
use Ixocreate\Schema\Type\Type;
use Ixocreate\Schema\Type\TypeInterface;

final class Schema implements SchemaInterface, TransformableInterface
{
    /**
     * @var ElementInterface[]
     */
    private $elements = [];

    /**
     * @var SchemaReceiverInterface|null
     */
    private $schemaReceiver;

    /**
     * Schema constructor.
     *
     * @param SchemaReceiverInterface|null $schemaReceiver
     */
    public function __construct(SchemaReceiverInterface $schemaReceiver = null)
    {
        $this->schemaReceiver = $schemaReceiver;
    }

    /**
     * @param SchemaReceiverInterface $schemaReceiver
     * @return Schema
     */
    public function withSchemaReceiver(SchemaReceiverInterface $schemaReceiver): SchemaInterface
    {
        $schema = clone $this;
        $schema->schemaReceiver = $schemaReceiver;

        return $schema;
    }

    /**
     * @param ElementInterface ...$elements
     * @return SchemaInterface
     */
    public function withElements(ElementInterface ...$elements): SchemaInterface
    {
        $schema = $this;
        foreach ($elements as $element) {
            $schema = $schema->withElement($element);
        }

        return $schema;
    }

    /**
     * @param ElementInterface $element
     * @return SchemaInterface
     * @deprecated use SchemaInterface::withElements(ElementInterface ...$elements)
     */
    public function withAddedElement(ElementInterface $element): SchemaInterface
    {
        return $this->withElement($element);
    }

    /**
     * @param ElementInterface $element
     * @return SchemaInterface
     */
    private function withElement(ElementInterface $element): SchemaInterface
    {
        $schema = clone $this;
        $schema->elements[$element->name()] = $element;

        return $schema;
    }

    /**
     * @param string $name
     * @return SchemaInterface
     */
    public function remove(string $name): SchemaInterface
    {
        $schema = clone $this;

        if (!\array_key_exists($name, $schema->elements)) {
            return $schema;
        }

        unset($schema->elements[$name]);

        return $schema;
    }

    /**
     * @return ElementInterface[]
     */
    public function elements(): array
    {
        return $this->elements;
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
     * @return bool
     */
    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->elements);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return \array_values($this->elements);
    }

    public function transform($data): TypeInterface
    {
        return Type::create($data, SchemaType::class, ['schema' => $this]);
    }

    public function schemaReceiver(): ?SchemaReceiverInterface
    {
        return $this->schemaReceiver;
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
}
