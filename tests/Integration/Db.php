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

namespace Shopgate\Base\Tests\Integration;

use Magento\Store\Model\ScopeInterface;
use Shopgate\Base\Api\Config\SgCoreInterface;
use Shopgate\Base\Tests\Integration\Db\ConfigManager;
use Shopgate\Base\Tests\Integration\Db\WebsiteManager;

class Db extends \PHPUnit\Framework\TestCase
{

    /**
     * @var WebsiteManager
     */
    private $websiteConfig;

    /**
     * @var ConfigManager
     */
    private $config;

    /**
     * Init website manager
     */
    public function setUp(): void
    {
        $this->config        = new ConfigManager;
        $this->websiteConfig = new WebsiteManager;
    }

    /**
     * Test config manager and website manager
     *
     * @uses   \Shopgate\Base\Tests\Integration\Db\ConfigManager::setConfigValue
     * @uses   \Shopgate\Base\Tests\Integration\Db\ConfigManager::getConfigValue
     *
     * @covers \Shopgate\Base\Tests\Integration\Db\WebsiteManager::createSite
     */
    public function testConfigUtility()
    {
        $site = $this->websiteConfig->createSite();
        $this->config->setConfigValue(
            sgCoreInterface::PATH_SHOP_NUMBER, '1234', ScopeInterface::SCOPE_WEBSITES, $site->getWebsite()->getId()
        );
        $val = $this->config->getConfigValue(
            sgCoreInterface::PATH_SHOP_NUMBER, ScopeInterface::SCOPE_WEBSITES, $site->getWebsite()->getId()
        );
        $this->assertEquals('1234', $val);
    }

    /**
     * Tests creation of a store using a created website as base
     *
     * @uses   \Shopgate\Base\Tests\Integration\Db\WebsiteManager::createSite
     *
     * @covers \Shopgate\Base\Tests\Integration\Db\WebsiteManager::createStore
     */
    public function testCreateAnotherStoreInWebsite()
    {
        $site  = $this->websiteConfig->createSite();
        $site2 = $this->websiteConfig->createStore($site);

        $this->assertEquals($site->getWebsite()->getId(), $site2->getWebsite()->getId());
        $this->assertNotEquals($site->getStore()->getId(), $site2->getStore()->getId());
    }

    /**
     * Tests config setting & getting on store level
     *
     * @covers   \Shopgate\Base\Tests\Integration\Db\ConfigManager::setConfigValue
     * @covers   \Shopgate\Base\Tests\Integration\Db\ConfigManager::getConfigValue
     */
    public function testStoreConfigSetting()
    {
        $site = $this->websiteConfig->createSite();

        $this->config->setConfigValue(
            sgCoreInterface::PATH_SHOP_NUMBER, '1234', ScopeInterface::SCOPE_STORES, $site->getStore()->getId()
        );
        $val = $this->config->getConfigValue(
            sgCoreInterface::PATH_SHOP_NUMBER, ScopeInterface::SCOPE_STORES, $site->getStore()->getId()
        );
        $this->assertEquals('1234', $val);
    }

    /**
     * Remove all created websites after each test
     *
     * @after
     */
    public function cleanup()
    {
        $this->websiteConfig->removeSites();
        $this->config->removeConfigs();
    }
}
