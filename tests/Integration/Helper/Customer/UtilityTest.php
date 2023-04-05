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

namespace Shopgate\Base\Integration\Unit\Helper\Customer;

use Magento\Framework\App\ObjectManager;
use Shopgate\Base\Tests\Bootstrap;

/**
 * @coversDefaultClass \Shopgate\Base\Helper\Customer\Utility
 */
class UtilityTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Load object manager for initialization
     */
    public function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * @param $expected - expected array to come out
     * @param $params   - params to pass to the tested method
     *
     * @covers ::getMagentoGender
     *
     * @dataProvider magentoGenderProvider
     */
    public function testGetMagentoGender($expected, $params)
    {
        $customerModel = $this->objectManager->get('Shopgate\Base\Helper\Customer\Utility');
        $reflection    = new \ReflectionClass($customerModel);
        $method        = $reflection->getMethod('getMagentoGender');
        $method->setAccessible(true);

        $actual = $method->invoke($customerModel, $params);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function magentoGenderProvider()
    {
        return
            [
                [
                    'expected' => '3',
                    'param'    => '0'
                ],
                [
                    'expected' => '1',
                    'param'    => 'm'
                ],
                [
                    'expected' => '2',
                    'param'    => 'f'
                ],
                [
                    'expected' => '3',
                    'param'    => 'xyz'
                ],
                [
                    'expected' => '3',
                    'param'    => 'd'
                ],
            ];
    }

    /**
     * @param $expected - expected array to come out
     * @param $params   - params to pass to the tested method
     *
     * @covers ::getShopgateGender
     *
     * @dataProvider shopgateGenderProvider
     */
    public function testGetShopgateGender($expected, $params)
    {
        $customerModel = $this->objectManager->get(
            'Shopgate\Base\Helper\Customer\Utility'
        );

        $reflection = new \ReflectionClass($customerModel);
        $method     = $reflection->getMethod('getShopgateGender');
        $method->setAccessible(true);

        $actual = $method->invoke($customerModel, $params);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function shopgateGenderProvider()
    {
        return
            [
                [
                    'expected' => '',
                    'param'    => '0'
                ],
                [
                    'expected' => 'm',
                    'param'    => '1'
                ],
                [
                    'expected' => 'f',
                    'param'    => '2'
                ],
                [
                    'expected' => 'd',
                    'param'    => '3'
                ],
                [
                    'expected' => '',
                    'param'    => '3'
                ]
            ];
    }
}
