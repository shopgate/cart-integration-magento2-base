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

namespace Shopgate\Base\Tests\Integration\Model\ResourceModel\Shopgate;

use Shopgate\Base\Tests\Bootstrap;

/**
 * Testing database integration
 *
 * @package Shopgate\Base\Model\ResourceModel\Shopgate
 */
class CustomerTest extends \PHPUnit\Framework\TestCase
{
    /** @var int */
    protected $id;

    /**
     * Init DB entry, save ID for retrieval
     */
    protected function setUp(): void
    {
        /** @var \Shopgate\Base\Model\Shopgate\Customer $model */
        $model = Bootstrap::getObjectManager()->create('Shopgate\Base\Model\Shopgate\Customer');
        $model->setToken('token1')->save();
        $this->id = $model->getId();
    }

    /**
     * Test if we have 3 columns available and
     * data is saved correctly
     */
    public function testRetrieveShopgateCustomer()
    {
        /** @var \Shopgate\Base\Model\Shopgate\Customer $model */
        $model = Bootstrap::getObjectManager()->create('Shopgate\Base\Model\Shopgate\Customer');
        $model->load($this->id);

        $this->assertCount(3, $model->getData());
        $this->assertEquals('token1', $model->getToken());
        $model->delete();
    }
}
