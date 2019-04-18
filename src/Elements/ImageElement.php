<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Schema\Package\Elements;

use Ixocreate\Media\Package\Type\ImageType;

final class ImageElement extends AbstractSingleElement
{
    public function type(): string
    {
        return ImageType::class;
    }

    public function inputType(): string
    {
        return 'image';
    }

    public static function serviceName(): string
    {
        return 'image';
    }
}
