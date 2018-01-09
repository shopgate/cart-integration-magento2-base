<?php
/**
 * Copyright Shopgate Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    Shopgate Inc, 804 Congress Ave, Austin, Texas 78701 <interfaces@shopgate.com>
 * @copyright Shopgate Inc
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace Shopgate\Base\Tests\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * @coversDefaultClass \Shopgate\Base\Model\Config
 */
class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /** @var ObjectManager */
    private $objectManager;
    /** @var \Shopgate\Base\Model\Config */
    private $configModel;

    /**
     * Load object manager for initialization
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->configModel   = $this->getMock('Shopgate\Base\Model\Config', [], [], '', false);
    }

    /**
     * Test property retriever from doc block
     *
     * @param $expected - expected from provider
     * @param $property - property name of ShopgateConfig class
     *
     * @covers       ::getPropertyType()
     * @dataProvider propertyProvider
     */
    public function testGetProperty($expected, $property)
    {
        $reflection = new \ReflectionClass($this->configModel);
        $method     = $reflection->getMethod('getPropertyType');
        $method->setAccessible(true);
        $result = $method->invoke($this->configModel, $property);

        $this->assertEquals($expected, $result);
    }

    /**
     * Test casting of value based on property
     *
     * @param $expected
     * @param $value
     * @param $property
     *
     * @covers       ::castToType()
     * @dataProvider castTypeProvider
     */
    public function testCastToType($expected, $value, $property)
    {
        $reflection = new \ReflectionClass($this->configModel);
        $method     = $reflection->getMethod('castToType');
        $method->setAccessible(true);
        $result = $method->invoke($this->configModel, $value, $property);

        $this->assertEquals($expected, $result);
    }

    /**
     * Test type conversion to save to core_config table
     *
     * @param $expected
     * @param $property
     * @param $value
     *
     * @covers ::prepareForDatabase()
     * @dataProvider prepareForDatabaseProvider
     */
    public function testPrepareForDatabase($expected, $value, $property)
    {
        $reflection = new \ReflectionClass($this->configModel);
        $method     = $reflection->getMethod('prepareForDatabase');
        $method->setAccessible(true);
        $result = $method->invoke($this->configModel, $property, $value);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function propertyProvider()
    {
        return [
            ['string', 'apikey'],
            ['int', 'shop_number'],
            ['bool', 'shop_is_active'],
            ['array', 'api_urls'],
            ['string', 'random_var'],
        ];
    }

    /**
     * @return array
     */
    public function castTypeProvider()
    {
        return [
            'int converts to str'    => ['1', 1, 'apikey'],
            'str converts to str'    => ['test', 'test', 'apikey'],
            'str converts to int'    => [345, '345', 'shop_number'],
            'int converts to int'    => [345, 345, 'shop_number'],
            'int converts to bool'   => [true, 1, 'shop_is_active'],
            'bool converts to bool'  => [true, true, 'shop_is_active'],
            'str converts to array'  => [[1, 2, 3], '1,2,3', 'api_urls'],
            'str converts to array2' => [['test1', 'test2'], 'test1,test2', 'api_urls'],
        ];
    }

    /**
     * @return array
     */
    public function prepareForDatabaseProvider()
    {
        return [
            'array converts to str'  => ['test1,test2', ['test1', 'test2'], 'api_urls'],
            'array converts to str2' => ['1,2,3', [1, 2, 3], 'api_urls'],
            'bool converts to int'   => [1, true, 'shop_is_active'],
            'str converts to int'    => ['345', '345', 'shop_number'],
        ];
    }
}
