<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\Schema\Type\Convert;

use Ixocreate\Schema\Type\Convert;
use PHPUnit\Framework\TestCase;

class ConvertTest extends TestCase
{
    public function testConvertString()
    {
        $this->assertSame("1", Convert::convertString((int)1));
        $this->assertSame("1.1", Convert::convertString((float)1.1));
        $this->assertSame("1", Convert::convertString(true));
        $this->assertSame("string", Convert::convertString("string"));
        $this->assertSame([], Convert::convertString([]));
    }

    public function testConvertBool()
    {
        $this->assertTrue(Convert::convertBool(1));
        $this->assertTrue(Convert::convertBool("1"));
        $this->assertFalse(Convert::convertBool(0));
        $this->assertFalse(Convert::convertBool("0"));
        $this->assertSame([], Convert::convertBool([]));
        $this->assertTrue(Convert::convertBool(true));
        $this->assertFalse(Convert::convertBool(false));
    }

    public function testConvertFloat()
    {
        $this->assertSame((float)1, Convert::convertFloat((int)1));
        $this->assertSame((float)1, Convert::convertFloat((float)1));
        $this->assertSame([], Convert::convertFloat([]));
    }

    public function testConvertInt()
    {
        $this->assertSame((int)1, Convert::convertInt((int)1));
        $this->assertSame((int)1, Convert::convertInt((float)1));
        $this->assertSame([], Convert::convertInt([]));
    }
}
