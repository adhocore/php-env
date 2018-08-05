<?php

/*
 * This file is part of the PHP-ENV package.
 *
 * (c) Jitendra Adhikari <jiten.adhikary@gmail.com>
 *     <https://github.com/adhocore>
 *
 * Licensed under MIT license.
 */

namespace Ahc\Env\Test;

use Ahc\Env\Loader;

class LoaderTest extends \PHPUnit\Framework\TestCase
{
    public function testLoadPutenv()
    {
        $loader = new Loader;

        $_SERVER['x'] = $_ENV['x'] = 'X';

        $loader->load(__DIR__ . '/stubs/test.env', false);

        $this->assertEquals('1', getenv('a'), 'Unquoted number');
        $this->assertEquals('2', getenv('b'), 'Quoted number');
        $this->assertEquals('$3#', getenv('c'), 'Unquoted string');
        $this->assertEquals('lol', getenv('d'), 'Quoted string');
        $this->assertEquals('', getenv('e'), 'Empty string');
        $this->assertEquals('"6"', getenv('f'), 'Escaped numeric string');
        $this->assertEquals('one_two', getenv('1_2'), 'Underscored string');
        $this->assertEquals('Apple Ball', getenv('A_B'), 'Multi word string');

        $this->assertFalse(getenv('MuL'), 'Key should be case sensitive');

        $this->assertArrayNotHasKey('a', $_SERVER, 'By default should not set to $_SERVER');
        $this->assertArrayNotHasKey('b', $_ENV, 'By default should not set to $_ENV');
    }

    public function testLoadGlobals()
    {
        $loader = new Loader;

        $loader->load(__DIR__ . '/stubs/test.env', true, Loader::ENV | Loader::SERVER);

        foreach (['SERVER', 'ENV'] as $name) {
            $source = $name === 'ENV' ? $_ENV : $_SERVER;

            $this->assertEquals('1', $source['a'], 'Unquoted number');
            $this->assertEquals('2', $source['b'], 'Quoted number');
            $this->assertEquals('$3#', $source['c'], 'Unquoted string');
            $this->assertEquals('lol', $source['d'], 'Quoted string');
            $this->assertEquals('', $source['e'], 'Empty string');
            $this->assertEquals('"6"', $source['f'], 'Escaped numeric string');
            $this->assertEquals('one_two', $source['1_2'], 'Underscored string');
            $this->assertEquals('Apple Ball', $source['A_B'], 'Multi word string');

            $this->assertArrayNotHasKey('mUl', $source, 'Key should be case sensitive');
        }
    }

    public function testLoadOverrideAll()
    {
        $loader = new Loader;

        $loader->load(__DIR__ . '/stubs/override.env', true, Loader::ALL);

        $this->assertNotEquals('1', getenv('a'), 'Unquoted number old');
        $this->assertNotEquals('2', getenv('b'), 'Quoted number old');
        $this->assertNotEquals('$3#', getenv('c'), 'Unquoted string old');
        $this->assertNotEquals('lol', getenv('d'), 'Quoted string old');
        $this->assertNotEquals('"6"', getenv('f'), 'Escaped numeric string old');
        $this->assertNotEquals("line 1\nline 2", getenv('MUL'), 'Multi line string old');

        $this->assertEquals('o1', getenv('a'), 'Unquoted number new');
        $this->assertEquals('o2', getenv('b'), 'Quoted number new');
        $this->assertEquals('o$3#', getenv('c'), 'Unquoted string new');
        $this->assertEquals('olol', getenv('d'), 'Quoted string new');
        $this->assertEquals('"o6"', getenv('f'), 'Escaped numeric string new');

        foreach (['SERVER', 'ENV'] as $name) {
            $source = $name === 'ENV' ? $_ENV : $_SERVER;

            $this->assertNotEquals('1', $source['a'], 'Unquoted number old');
            $this->assertNotEquals('2', $source['b'], 'Quoted number old');
            $this->assertNotEquals('$3#', $source['c'], 'Unquoted string old');
            $this->assertNotEquals('lol', $source['d'], 'Quoted string old');
            $this->assertNotEquals('"6"', $source['f'], 'Escaped numeric string old');

            $this->assertEquals('o1', $source['a'], 'Unquoted number new');
            $this->assertEquals('o2', $source['b'], 'Quoted number new');
            $this->assertEquals('o$3#', $source['c'], 'Unquoted string new');
            $this->assertEquals('olol', $source['d'], 'Quoted string new');
            $this->assertEquals('"o6"', $source['f'], 'Escaped numeric string new');
        }
    }

    public function testRef()
    {
        (new Loader)->load(__DIR__ . '/stubs/ref.env');

        $this->assertSame('1/2', getenv('REF'));
        $this->assertSame('1/2/3', getenv('REF2'));
        $this->assertSame('${INV}', getenv('REF3'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The .env file does not exist or is not readable
     */
    public function testLoadInvalidPath()
    {
        $loader = new Loader;

        $loader->load(__DIR__ . '/' . rand(1, 1000));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage The .env file cannot be parsed due to malformed values
     */
    public function testLoadInvalidData()
    {
        $loader = new Loader;

        @$loader->load(__DIR__ . '/stubs/invalid.env');
    }
}
