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

use KiwiSuite\CommonTypes\Entity\YouTubeType;

final class YouTubeElement extends AbstractSingleElement
{
    public function type(): string
    {
        return YouTubeType::class;
    }

    public function inputType(): string
    {
        return 'youtube';
    }

    public static function serviceName(): string
    {
        return 'youtube';
    }
}
