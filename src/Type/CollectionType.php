<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Schema\Type;

use Doctrine\DBAL\Types\JsonType;
use Ixocreate\Schema\Builder\BuilderInterface;
use Ixocreate\Schema\SchemaInterface;
use Ixocreate\Schema\SubSchemaReceiverInterface;
use Ixocreate\ServiceManager\ServiceManager;
use Ixocreate\ServiceManager\ServiceManagerInterface;

final class CollectionType extends AbstractType implements DatabaseTypeInterface, \Iterator
{
    /**
     * @var SchemaInterface
     */
    private $schema;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var BuilderInterface
     */
    private $builder;

    /**
     * CollectionType constructor.
     *
     * @param ServiceManagerInterface $serviceManager
     * @param BuilderInterface $builder
     */
    public function __construct(ServiceManagerInterface $serviceManager, BuilderInterface $builder)
    {
        $this->serviceManager = $serviceManager;
        $this->builder = $builder;
    }

    /**
     * Doesn't work when the object is unserialized!
     *
     * @throws \Exception
     * @return SchemaInterface|null
     */
    private function getSchema(): ?SchemaInterface
    {
        if (!empty($this->schema)) {
            return $this->schema;
        }

        if (!empty($this->options['schema'])) {
            //TODO check instance of
            $this->schema = $this->options['schema'];
            return $this->schema;
        }

        if (!empty($this->options['subSchema'])) {

            //TODO this is a dirty way of receiving the service
            $receiver = null;
            if ($this->serviceManager->has($this->options['subSchema'])) {
                $receiver = $this->serviceManager->get($this->options['subSchema']);
            }

            if (empty($receiver)) {
                foreach (\array_keys($this->serviceManager->getServiceManagerConfig()->getSubManagers()) as $subManager) {
                    if ($this->serviceManager->get($subManager)->has($this->options['subSchema'])) {
                        $receiver = $this->serviceManager->get($subManager)->get($this->options['subSchema']);
                        break;
                    }
                }
            }

            if (!($receiver instanceof SubSchemaReceiverInterface)) {
                throw new \Exception($this->options['subSchema'] . " must implement " . SubSchemaReceiverInterface::class);
            }

            $this->schema = $receiver->receiveSchema($this->options['subSchemaName'], $this->builder);
            return $this->schema;
        }

        throw new \Exception('Cant initialize without schema');
    }

    /**
     * @param $value
     * @throws \Exception
     * @return mixed
     */
    protected function transform($value)
    {
        $result = [];
        if (!\is_array($value) || empty($value)) {
            return $result;
        }

        if (\array_key_exists('__values__', $value) && \array_key_exists('__options__', $value)) {
            if (\array_key_exists('subSchema', $value['__options__'])) {
                $this->options['subSchema'] = $value['__options__']['subSchema'];
            }
            if (\array_key_exists('subSchemaName', $value['__options__'])) {
                $this->options['subSchemaName'] = $value['__options__']['subSchemaName'];
            }

            $value = $value['__values__'];
        }

        foreach ($value as $item) {
            if (empty($item['_type'])) {
                continue;
            }

            if (!$this->getSchema()->has($item['_type'])) {
                continue;
            }

            $type = $item['_type'];

            // HACK
            if (\array_key_exists('_type', $item) && \array_key_exists('value', $item) && \count($item) == 2) {
                $item = $item['value'];
            }

            unset($item['_type']);
            $result[] = [
                '_type' => $type,
                'value' => Type::create($item, SchemaType::class, ['schema' => $this->getSchema()->get($type)]),
            ];
        }

        return $result;
    }

    public function __toString()
    {
        return '';
    }

    public function jsonSerialize()
    {
        $return = [];
        foreach ($this->value() as $name => $value) {
            $return[$name] = \array_merge(
                ['_type' => $value['_type']],
                $value['value']->value()
            );
        }
        return $return;
    }

    public function convertToDatabaseValue()
    {
        $options = [];
        $values = [];

        foreach ($this->value() as $name => $val) {
            if ($val['value'] instanceof DatabaseTypeInterface) {
                $values[$name] = [
                    '_type' => $val['_type'],
                    'value' => $val['value']->convertToDatabaseValue(),
                ];
                continue;
            }

            $values[$name] = $val;
        }

        if (!empty($this->options['subSchema']) && !empty($this->options['subSchemaName'])) {
            $options['subSchema'] = $this->options['subSchema'];
            $options['subSchemaName'] = $this->options['subSchemaName'];
        }

        return [
            '__options__' => $options,
            '__values__' => $values,
        ];
    }

    public function __debugInfo()
    {
        return [
            'value' => $this->value(),
        ];
    }

    /**
     * @return mixed
     */
    public function current()
    {
        $value = \current($this->value);

        return $value['value'];
    }

    /**
     *
     */
    public function next()
    {
        \next($this->value);
    }

    /**
     * Return the key of the current element
     *
     * @see http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure
     * @since 5.0.0
     */
    public function key()
    {
        return \key($this->value);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        $key = \key($this->value);
        return $key !== null && $key !== false;
    }

    /**
     *
     */
    public function rewind()
    {
        \reset($this->value);
    }

    /**
     * @return string
     */
    public static function baseDatabaseType(): string
    {
        return JsonType::class;
    }

    /**
     * @return string
     */
    public static function serviceName(): string
    {
        return 'collection';
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        /** @var CollectionType $type */
        $type = Type::get(CollectionType::serviceName());
        $this->serviceManager = $type->serviceManager;
        $this->builder = $type->builder;

        parent::unserialize($serialized);
    }
}
