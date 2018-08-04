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

use KiwiSuite\CommonTypes\Entity\DateTimeType;

final class DateTimeElement extends AbstractSingleElement
{
    public function inputType(): string
    {
        return 'datetime';
    }

    public function type(): string
    {
        return DateTimeType::class;
    }

    public static function serviceName(): string
    {
        return 'datetime';
    }
}
