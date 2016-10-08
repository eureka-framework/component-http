<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

require_once __DIR__ . '/../src/Http/Data.php';
require_once __DIR__ . '/../src/Http/Env.php';
require_once __DIR__ . '/../src/Http/Server.php';

/**
 * Class Test for cache
 *
 * @author Romain Cottard
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Server / Data class
     *
     * @return   void
     * @covers Data::getInstance
     * @covers Server::__construct
     * @covers Server::getBaseUri
     * @covers Server::getCurrentUri
     * @covers Server::init
     * @covers Server::isPost
     * @covers Server::isGet
     * @covers Server::isAjax
     * @covers Data::get
     * @covers Data::set
     * @covers Data::has
     */
    public function testServer()
    {
        $server = Server::getInstance();

        $this->assertTrue($server->has('SCRIPT_NAME'));
        $this->assertTrue($server->has('name'));

        $this->assertEquals($server->get('USER'), getenv('USER'));

        $this->assertTrue(!$server->isPost());
        $this->assertTrue(!$server->isGet());
        $this->assertTrue(!$server->isAjax());

        $this->assertTrue(!$server->has('my_key'));
        $server->set('my_key', 'my_value');
        $this->assertTrue($server->has('my_key'));
        $this->assertEquals($server->get('my_key'), 'my_value');
    }

    /**
     * Test Http class
     *
     * @return   void
     * @covers Data::getInstance
     * @covers Server::__construct
     */
    public function testEnv()
    {
        $env = Env::getInstance();

        $this->assertTrue(!$env->has('name'));
    }
}
