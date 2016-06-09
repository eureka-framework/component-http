<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Http;

require_once __DIR__.'/../Http.php';
require_once __DIR__.'/../Data.php';
require_once __DIR__.'/../Server.php';

/**
 * Class Test for cache
 *
 * @author Romain Cottard
 * @version 2.1.0
 */
class HttpTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Http class
     *
     * @return   void
     * @covers Http::__construct
     * @covers Http::init
     */
    public function testHttp()
    {
        $http  = new Http('http://eureka-framework.com/phpunit/test?query=1&other[]=val1&other[]=val2#frag');

        $query = $http->query(true);

        $this->assertTrue(is_array($query));
        $this->assertEquals($query['query'], '1');
        $this->assertTrue(is_array($query['other']));
        $this->assertEquals($query['other'][0], 'val1');
        $this->assertEquals($query['other'][1], 'val2');

        $http->add('query', 2);
        $http->add(array('newparam' => 'newval', 'other' => array('val2', 'val3', 'val4')));

        $query = $http->query();
        $this->assertEquals($query, 'query=2&other%5B0%5D=val2&other%5B1%5D=val3&other%5B2%5D=val4&newparam=newval');
        $query = $http->query(true);
        $this->assertTrue(is_array($query));
        $this->assertEquals($query['newparam'], 'newval');
        $this->assertEquals($query['query'], '2');
        $this->assertTrue(is_array($query['other']));
        $this->assertEquals($query['other'][0], 'val2');
        $this->assertEquals($query['other'][1], 'val3');
        $this->assertEquals($query['other'][2], 'val4');
    }
}
