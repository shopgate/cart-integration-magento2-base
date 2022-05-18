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

namespace Shopgate\Base\Tests\Integration\Model\Service\Config;

use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Shopgate\Base\Api\Config\SgCoreInterface;
use Shopgate\Base\Model\Service\Config\SgCore;
use Shopgate\Base\Tests\Integration\Db\ConfigManager;
use Shopgate\Base\Tests\Integration\Db\WebsiteManager;

/**
 * @coversDefaultClass Shopgate\Base\Model\Service\Config\Core
 */
class CoreTest extends \PHPUnit\Framework\TestCase
{
    /** @var WebsiteManager */
    private $siteManager;
    /** @var ConfigManager */
    private $cfgManager;
    /** @var SgCore */
    private $method;

    /**
     * Set up essentials
     */
    public function setUp(): void
    {
        $this->cfgManager  = new ConfigManager;
        $this->siteManager = new WebsiteManager;
        $this->method      = Bootstrap::getObjectManager()->create('Shopgate\Base\Model\Service\Config\Core');
    }

    /**
     * @uses \Shopgate\Base\Model\Service\Config\Core::getConfigByPath
     * @covers ::getCollectionByPath
     * @covers ::findConfigByStoreId
     */
    public function testFindConfigByStoreId()
    {
        $this->cfgManager->setConfigValue(SgCoreInterface::PATH_SHOP_NUMBER, '1235');
        $this->cfgManager->setConfigValue(SgCoreInterface::PATH_SHOP_NUMBER, '1236', ScopeInterface::SCOPE_STORES, 1);

        $collection = $this->method->getCollectionByPath(SgCoreInterface::PATH_SHOP_NUMBER);
        $this->assertCount(2, $collection);

        /**
         * Checks store specific config retrieval
         */
        $config = $this->method->findConfigByStoreId($collection, 1);
        $this->assertEquals('1236', $config->getData('value'));

        /**
         * Checks default path config retrieval
         */
        $defaultConfig = $this->method->findConfigByStoreId($collection, null);
        $this->assertEquals('1235', $defaultConfig->getData('value'));

        /**
         * Checks store config, then loads website config
         */
        $site = $this->siteManager->createSite();
        $this->cfgManager->setConfigValue(
            SgCoreInterface::PATH_SHOP_NUMBER,
            '1237',
            ScopeInterface::SCOPE_WEBSITES,
            $site->getWebsite()->getId()
        );
        $collection2 = $this->method->getCollectionByPath(SgCoreInterface::PATH_SHOP_NUMBER);
        $websiteCfg  = $this->method->findConfigByStoreId($collection2, $site->getStore()->getId());
        $this->assertEquals('1237', $websiteCfg->getData('value'));

        /**
         * Checks empty object on empty collection
         */
        $emptyConfig = $this->method->getConfigByPath('invalid_path', 1);
        $this->assertEmpty($emptyConfig->getData('value'));
    }

    /**
     * @after
     */
    public function cleanup()
    {
        $this->cfgManager->removeConfigs();
        $this->siteManager->removeSites();
    }
}
