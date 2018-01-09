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

namespace Shopgate\Base\Tests\Unit\Model\Utility;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Shopgate\Base\Model\Utility\Registry;

class RegistryTest extends \PHPUnit\Framework\TestCase
{
    /** @var Registry */
    private $registry;

    /**
     * Load object manager for initialization
     */
    public function setUp()
    {
        $this->registry = (new ObjectManager($this))->getObject(Registry::class);
    }

    /**
     * Tests the getter / setter of API initialization.
     */
    public function testApiFag()
    {
        //flags twice to test that it does not error
        $this->registry->flagApi();
        $this->registry->flagApi();
        $this->assertTrue($this->registry->isApi());
        $this->registry->unregister(Registry::API);
        $this->assertFalse($this->registry->isApi());
    }

    /**
     * Tests the getter / setter of frontend initialization
     */
    public function testRegistryFlag()
    {
        $this->registry->flagRedirect();
        $this->registry->flagRedirect();
        $this->assertTrue($this->registry->isRedirect());
        $this->registry->unregister(Registry::REDIRECT);
        $this->assertFalse($this->registry->isRedirect());
    }

    /**
     * @param bool   $expected
     * @param array $list   - actions in default list
     * @param string $action - action to check
     *
     * @dataProvider actionListProvider
     */
    public function testIsActionInList($expected, array $list, $action)
    {
        $this->registry->setAction($action);
        $this->assertEquals($expected, $this->registry->isActionInList($list));
    }

    /**
     * @return array
     */
    public function actionListProvider()
    {
        return [
            'add_order is in list'      => [
                'expected' => true,
                'list'     => ['check_cart', 'add_order'],
                'action'   => 'add_order'
            ],
            'add_order is not in list'     => [
                'expected' => false,
                'list'     => ['check_cart', 'ping'],
                'action'   => 'add_order'
            ]
        ];
    }
    /**
     * @param bool   $expected
     * @param string $call   - what action is actually set
     * @param string $action - check against action
     *
     * @dataProvider actionProvider
     */
    public function testAction($expected, $call, $action)
    {
        $this->registry->setAction($call);
        $this->assertEquals($expected, $this->registry->isAction($action));
    }

    /**
     * @return array
     */
    public function actionProvider()
    {
        return [
            'action is add_order'  => [
                'expected' => true,
                'call'     => 'add_order',
                'action'   => 'add_order'
            ],
            'action not add_order' => [
                'expected' => false,
                'call'     => 'check_cart',
                'action'   => 'add_order'
            ],
            'weird entries via call' => [
                'expected' => false,
                'call'     => false,
                'action'   => 'add_order'
            ],
            'weird entries via call 2' => [
                'expected' => false,
                'call'     => null,
                'action'   => 'add_order'
            ],
        ];
    }
}
