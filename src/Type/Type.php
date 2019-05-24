<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Schema\Type;

use Ixocreate\Schema\Type\Exception\InvalidTypeException;
use Ixocreate\Schema\Type\Exception\TypeNotCreatedException;
use Ixocreate\Schema\Type\Exception\TypeNotFoundException;
use Ixocreate\ServiceManager\SubManager\SubManagerInterface;

final class Type
{
    /**
     * @var Type
     */
    private static $type;

    /**
     * @var SubManagerInterface
     */
    private $subManager;

    /**
     * @param SubManagerInterface $subManager
     */
    private function __construct(SubManagerInterface $subManager = null)
    {
        $this->subManager = $subManager;
    }

    /**
     * @param SubManagerInterface|null $subManager
     */
    public static function initialize(SubManagerInterface $subManager = null)
    {
        self::$type = new Type($subManager);
    }

    /**
     * @return Type
     */
    private static function getInstance(): Type
    {
        if (!(self::$type instanceof Type)) {
            self::initialize();
        }

        return self::$type;
    }

    /**
     * @param $value
     * @param string $type
     * @param array $options
     * @return mixed
     */
    public static function create($value, string $type, array $options = [])
    {
        return self::getInstance()->doCreate($value, $type, $options);
    }

    /**
     * @param string $type
     * @return TypeInterface
     */
    public static function get(string $type): TypeInterface
    {
        return self::getInstance()->doGet($type);
    }

    /**
     * @param $value
     * @param string $type
     * @param array $options
     * @return mixed
     */
    private function doCreate($value, string $type, array $options = [])
    {
        $value = $this->convertValue($value, $type);

        if ($this->isPhpType($type)) {
            $functionName = "\is_" . $type;
            if (!$functionName($value)) {
                throw new InvalidTypeException(\sprintf("'%s' is not a '%s'", \gettype($value), $type));
            }

            return $value;
        }

        /** @var TypeInterface $typeObject */
        $typeObject = $this->doGet($type);

        if ($value instanceof $typeObject) {
            return $value;
        }

        return $typeObject->create($value, $options);
    }

    /**
     * @param string $type
     * @return TypeInterface
     */
    private function doGet(string $type): TypeInterface
    {
        if (!($this->subManager instanceof SubManagerInterface)) {
            throw new TypeNotCreatedException(\sprintf("'%s' was not initialized with a SubManager", Type::class));
        }

        if (!$this->subManager->has($type)) {
            throw new TypeNotFoundException(\sprintf("Can't find type '%s'", $type));
        }

        /** @var TypeInterface $typeObject */
        return $this->subManager->get($type);
    }

    /**
     * @param $type
     * @return bool
     */
    private function isPhpType($type): bool
    {
        return \in_array(
            $type,
            [
                TypeInterface::TYPE_STRING,
                TypeInterface::TYPE_ARRAY,
                TypeInterface::TYPE_BOOL,
                TypeInterface::TYPE_CALLABLE,
                TypeInterface::TYPE_FLOAT,
                TypeInterface::TYPE_INT,
            ]
        );
    }

    /**
     * @param $value
     * @param string $type
     * @return mixed
     */
    private function convertValue($value, string $type)
    {
        if ($value instanceof $type) {
            return $value;
        }

        if (!$this->isPhpType($type) && \class_exists($type)) {
            return $value;
        }

        switch ($type) {
            case TypeInterface::TYPE_STRING:
            case TypeInterface::TYPE_BOOL:
            case TypeInterface::TYPE_FLOAT:
            case TypeInterface::TYPE_INT:
                $value = \call_user_func(Convert::class . "::convert" . \ucfirst($type), $value);
                break;
            case TypeInterface::TYPE_ARRAY:
            case TypeInterface::TYPE_CALLABLE:
            default:
                break;
        }

        return $value;
    }
}
