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

namespace Shopgate\Base\Tests\Integration\Model\Redirect;

use Shopgate\Base\Api\Config\SgCoreInterface;
use Shopgate\Base\Tests\Bootstrap;
use Shopgate\Base\Tests\Integration\Db\ConfigManager;

/**
 * @coversDefaultClass \Shopgate\Base\Model\Redirect\Builder
 * @group Shopgate_Base_Redirect
 */
class BuilderTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Shopgate\Base\Model\Redirect\Builder */
    private $class;
    /** @var ConfigManager */
    private $cfg;

    public function setUp()
    {
        $this->cfg   = new ConfigManager();
        $manager     = Bootstrap::getObjectManager();
        $this->class = $manager->create('Shopgate\Base\Model\Redirect\Builder');
    }

    /**
     * @covers ::init
     * @covers ::initConfig
     */
    public function testInit()
    {
        $this->setUpConfig();
        $forwarder = $this->class->buildJsRedirect();

        $this->assertNotNull($forwarder);
    }

    /**
     * Helps set up the main config to redirect shop
     */
    private function setUpConfig()
    {
        $this->cfg->setConfigValue(SgCoreInterface::PATH_ACTIVE, 1);
        $this->cfg->setConfigValue(SgCoreInterface::PATH_SHOP_NUMBER, 1234);
        $this->cfg->setConfigValue(SgCoreInterface::PATH_CUSTOMER_NUMBER, 2345);
        $this->cfg->setConfigValue(SgCoreInterface::PATH_API_KEY, 1111111111);
        $this->cfg->setConfigValue(SgCoreInterface::PATH_CNAME, 'lao-cha.com');
    }

    /**
     * @after
     */
    public function tearDown()
    {
        $this->cfg->removeConfigs();
    }
}
