<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Schema\Element;

use Ixocreate\Schema\Type\HtmlType;

final class HtmlElement extends AbstractSingleElement
{
    public function type(): string
    {
        return HtmlType::class;
    }

    public function inputType(): string
    {
        return 'html';
    }

    public static function serviceName(): string
    {
        return 'html';
    }
}
