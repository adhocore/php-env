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

use Ahc\Env\Retriever;

class RetrieverTest extends \PHPUnit\Framework\TestCase
{
    public function testLoad()
    {
        putenv('TEST=true');
        putenv('REST=false');
        putenv('NULL=null');
        putenv('ADMIN_EMAIL=admin@example.com');
        putenv('MAX_LIMIT=1000');

        $this->assertTrue(Retriever::getEnv('TEST'));
        $this->assertFalse(Retriever::getEnv('REST'));
        $this->assertNull(Retriever::getEnv('NULL'));
        $this->assertEquals(
            'admin@example.com',
            Retriever::getEnv('ADMIN_EMAIL', null, FILTER_VALIDATE_EMAIL)
        );
        $this->assertSame(1000, Retriever::getEnv('MAX_LIMIT', null, FILTER_VALIDATE_INT));

        $_SERVER += [
            'USE_CYPHER' => 'true',
        ];

        $this->assertTrue(Retriever::getEnv('USE_CYPHER'));

        $_ENV += [
            'DEFAULT_PAYMENT' => 'paypal',
        ];

        $this->assertSame('paypal', Retriever::getEnv('DEFAULT_PAYMENT'));
    }
}
