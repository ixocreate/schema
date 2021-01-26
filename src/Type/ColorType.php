<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Schema\Type;

use Doctrine\DBAL\Types\StringType;
use Ixocreate\Schema\Builder\BuilderInterface;
use Ixocreate\Schema\Element\ColorElement;
use Ixocreate\Schema\Element\ElementInterface;
use Ixocreate\Schema\Element\ElementProviderInterface;

final class ColorType extends AbstractType implements DatabaseTypeInterface, ElementProviderInterface
{
    /**
     * @param $value
     * @throws \Exception
     * @return string
     */
    protected function transform($value)
    {
        if (!\is_string($value)) {
            throw new \Exception('invalid hex color');
        }

        if (\mb_substr($value, 0, 1) !== '#') {
            //TODO Exception
            throw new \Exception('invalid hex color');
        }
        $value = \mb_substr($value, 1);

        if (\mb_strlen($value) === 3) {
            $value = $value . $value;
        }

        if (\mb_strlen($value) !== 6) {
            //TODO Exception
            throw new \Exception('invalid hex color');
        }

        if (!\preg_match('/^[a-f0-9]{6}$/i', $value)) {
            //TODO Exception
            throw new \Exception('invalid hex color');
        }

        return '#' . $value;
    }

    /**
     * @return array
     */
    public function toRgb()
    {
        return [
            'r' => \hexdec($this->value[1] . $this->value[2]),
            'g' => \hexdec($this->value[3] . $this->value[4]),
            'b' => \hexdec($this->value[5] . $this->value[6]),
        ];
    }

    /**
     * @return string
     */
    public function convertToDatabaseValue()
    {
        return (string)$this;
    }

    /**
     * @return string
     */
    public static function baseDatabaseType(): string
    {
        return StringType::class;
    }

    public static function serviceName(): string
    {
        return 'color';
    }

    public function provideElement(BuilderInterface $builder): ElementInterface
    {
        return $builder->get(ColorElement::class);
    }
}
