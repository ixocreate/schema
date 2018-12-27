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
namespace Ixocreate\Schema\Elements;

use Ixocreate\Media\Type\MediaType;

final class MediaElement extends AbstractSingleElement
{
    public function type(): string
    {
        return MediaType::class;
    }

    public function inputType(): string
    {
        return 'media';
    }

    public static function serviceName(): string
    {
        return 'media';
    }
}