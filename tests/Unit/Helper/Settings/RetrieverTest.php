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

namespace Shopgate\Base\Tests\Unit\Helper\Settings;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * @coversDefaultClass \Shopgate\Base\Helper\Settings\Retriever
 */
class RetrieverTest extends \PHPUnit\Framework\TestCase
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
        $this->objectManager = new ObjectManager($this);
    }

    /**
     * @param $expected - expected array to come out
     * @param $params   - params to pass into the retriever's constructor, impersonating DI
     *
     * @covers ::getSettings
     * @covers ::methodLoader
     *
     * @dataProvider settingsProvider
     */
    public function testGetSettings($expected, $params)
    {
        /** @var \Shopgate\Base\Helper\Settings\Retriever $retrieverModel */
        $retrieverModel = $this->objectManager->getObject(
            '\Shopgate\Base\Helper\Settings\Retriever',
            [
                'exportParams' => $params
            ]
        );

        $actual = $retrieverModel->getSettings();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers ::getExportParams
     */
    public function testGetExportParams()
    {
        $params = $this->settingsProvider();
        /** @var \Shopgate\Base\Helper\Settings\Retriever $retrieverModel */
        $retrieverModel = $this->objectManager->getObject(
            '\Shopgate\Base\Helper\Settings\Retriever',
            [
                'exportParams' => $params[0]['params']
            ]
        );

        $reflection = new \ReflectionClass($retrieverModel);
        $method     = $reflection->getMethod('getExportParams');
        $method->setAccessible(true);

        $result = $method->invoke($retrieverModel);
        $this->assertCount(3, $result);

        $result2 = $method->invoke($retrieverModel, 'tax');
        $this->assertCount(2, $result2);
    }

    /**
     * @return array
     */
    public function settingsProvider()
    {
        return
            [
                [
                    'expected' => [
                        'tax'             => [
                            'tax_rates' => [],
                            'tax_rules' => []
                        ],
                        'customer_groups' => null,
                        'payment_methods' => null,
                    ],
                    'params'   => [
                        'customer_groups',
                        'payment_methods',
                        'tax' => [
                            'tax_rates',
                            'tax_rules'
                        ]
                    ]
                ]
            ];
    }
}
