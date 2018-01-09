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
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Shopgate\Base\Api\Config\SgCoreInterface;
use Shopgate\Base\Model\Service\Config\SgCore;
use Shopgate\Base\Tests\Integration\Db\ConfigManager;
use Shopgate\Base\Tests\Integration\Db\WebsiteManager;

/**
 * @coversDefaultClass Shopgate\Base\Model\Service\Config\SgCore
 */
class SgCoreTest extends \PHPUnit\Framework\TestCase
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
     * @var SgCore
     */
    private $method;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Set up essentials
     */
    public function setUp()
    {
        $this->config        = new ConfigManager;
        $this->websiteConfig = new WebsiteManager;
        $this->method        = Bootstrap::getObjectManager()->create('Shopgate\Base\Model\Service\Config\SgCore');
        $this->storeManager  = Bootstrap::getObjectManager()->create('Magento\Store\Model\StoreManagerInterface');
    }

    /**
     * Test if the collection is cleared between calls
     *
     * @covers ::getShopNumberCollection
     */
    public function testShopCollectionClearing()
    {
        $this->config->setConfigValue(SgCoreInterface::PATH_SHOP_NUMBER, '1235');
        $this->config->setConfigValue(SgCoreInterface::PATH_SHOP_NUMBER, '1236', ScopeInterface::SCOPE_STORES, 1);

        $this->assertCount(1, $this->method->getShopNumberCollection('1235'));
        $this->assertNotCount(0, $this->method->getShopNumberCollection('1236'));
    }

    /**
     * @covers ::getStoreId
     */
    public function testGetStoreId()
    {
        $this->config->setConfigValue(SgCoreInterface::PATH_SHOP_NUMBER, '1235');
        $this->config->setConfigValue(SgCoreInterface::PATH_SHOP_NUMBER, '1236', ScopeInterface::SCOPE_STORES, 1);

        $this->assertEquals(1, $this->method->getStoreId(1235));
        $this->assertEquals(1, $this->method->getStoreId(1236));

        /**
         * Testing Website -> Site relationship
         */
        $site = $this->websiteConfig->createSite();
        $this->config->setConfigValue(
            SgCoreInterface::PATH_SHOP_NUMBER,
            '1237',
            ScopeInterface::SCOPE_WEBSITES,
            $site->getWebsite()->getId()
        );

        $this->assertEquals($site->getStore()->getId(), $this->method->getStoreId('1237'));
    }

    /**
     * Tests the store level scope config
     *
     * @covers ::getSaveScope
     */
    public function testGetSaveScopeStoreLevel()
    {
        /**
         * Setup, scenario 1
         */
        $site = $this->websiteConfig->createSite();
        $this->config->setConfigValue(
            SgCoreInterface::PATH_SHOP_NUMBER,
            '1235',
            ScopeInterface::SCOPE_STORES,
            $site->getStore()->getId()
        );
        $this->config->setConfigValue(
            SgCoreInterface::PATH_ALIAS,
            'test2',
            ScopeInterface::SCOPE_STORES,
            $site->getStore()->getId()
        );

        /**
         * Shop # & alias in store scope
         * stores  | 3 | shopgate/general/shop_number | 1235 <- should pull this scope
         * stores  | 3 | shopgate/mobile/alias        | test2
         */
        $config = $this->method->getSaveScope(SgCoreInterface::PATH_ALIAS, '1235');
        $this->assertEquals('test2', $config->getData('value'));

        /**
         * Setup, scenario 2
         */
        $this->config->setConfigValue(SgCoreInterface::PATH_SHOP_NUMBER, '1235');
        $this->config->setConfigValue(SgCoreInterface::PATH_ALIAS, 'test1');
        $this->storeManager->setCurrentStore($site->getStore()->getId());

        /**
         * Same shop # on store & default scopes, different alias on both scopes
         * default | 0 | shopgate/general/shop_number | 1235
         * default | 0 | shopgate/mobile/alias        | test1
         * stores  | 3 | shopgate/general/shop_number | 1235 <- should still pull this scope
         * stores  | 3 | shopgate/mobile/alias        | test2
         */
        $config2 = $this->method->getSaveScope(SgCoreInterface::PATH_ALIAS, '1235');
        $this->assertEquals($site->getStore()->getId(), $config2->getScopeId());

        /**
         * Setup, scenario 3
         */
        $this->config->setConfigValue(
            SgCoreInterface::PATH_SHOP_NUMBER,
            '1235',
            ScopeInterface::SCOPE_WEBSITES,
            $site->getWebsite()->getId()
        );
        $this->config->setConfigValue(
            SgCoreInterface::PATH_ALIAS,
            'test3',
            ScopeInterface::SCOPE_WEBSITES,
            $site->getWebsite()->getId()
        );

        /**
         * Same shop# on default, website & store scopes. Different aliases on all scopes.
         * default  | 0 | shopgate/general/shop_number | 1235
         * default  | 0 | shopgate/mobile/alias        | test1
         * websites | 2 | shopgate/general/shop_number | 1235
         * websites | 2 | shopgate/mobile/alias        | test3
         * stores   | 3 | shopgate/general/shop_number | 1235 <- should still pull this scope
         * stores   | 3 | shopgate/mobile/alias        | test2
         */
        $config2 = $this->method->getSaveScope(SgCoreInterface::PATH_ALIAS, '1235');
        $this->assertEquals($site->getStore()->getId(), $config2->getScopeId());
    }

    /**
     * Tests the store level scope config
     *
     * @covers ::getSaveScope
     */
    public function testGetSaveScopeWebsiteLevel()
    {
        /**
         * Setup, scenario 1
         */
        $site = $this->websiteConfig->createSite();
        $this->config->setConfigValue(
            SgCoreInterface::PATH_SHOP_NUMBER,
            '1235',
            ScopeInterface::SCOPE_WEBSITES,
            $site->getWebsite()->getId()
        );
        $this->config->setConfigValue(
            SgCoreInterface::PATH_ALIAS,
            'test1',
            ScopeInterface::SCOPE_WEBSITES,
            $site->getWebsite()->getId()
        );

        /**
         * Shop # & alias in website scope
         * websites  | 3 | shopgate/general/shop_number | 1235 <- should pull this scope
         * websites  | 3 | shopgate/mobile/alias        | test1
         */
        $config = $this->method->getSaveScope(SgCoreInterface::PATH_ALIAS, '1235');
        $this->assertEquals($site->getWebsite()->getId(), $config->getScopeId());

        /**
         * Setup, scenario 2
         */
        $this->config->setConfigValue(SgCoreInterface::PATH_SHOP_NUMBER, '1235');
        $this->config->setConfigValue(SgCoreInterface::PATH_ALIAS, 'test2');

        /**
         * Same shop # in website & default scopes, different alias in both scopes
         * default  | 0 | shopgate/general/shop_number | 1235
         * default  | 0 | shopgate/mobile/alias        | test2
         * websites | 3 | shopgate/general/shop_number | 1235 <- should still pull this scope
         * websites | 4 | shopgate/mobile/alias        | test1
         */
        $this->storeManager->setCurrentStore($site->getStore()->getId());
        $config2 = $this->method->getSaveScope(SgCoreInterface::PATH_ALIAS, '1235');
        $this->assertEquals($site->getWebsite()->getId(), $config2->getScopeId());

        /**
         * Setup, scenario 3
         */
        $this->config->setConfigValue(SgCoreInterface::PATH_ALIAS, 'test3', ScopeInterface::SCOPE_STORES, 1);

        /**
         * Same shop # in website & default scopes, different alias in both scopes /w extra store alias
         * default  | 0 | shopgate/general/shop_number | 1235
         * default  | 0 | shopgate/mobile/alias        | test2
         * stores   | 1 | shopgate/mobile/alias        | test3
         * websites | 3 | shopgate/general/shop_number | 1235 <- should still pull this scope
         * websites | 3 | shopgate/mobile/alias        | test1
         */
        $config3 = $this->method->getSaveScope(SgCoreInterface::PATH_ALIAS, '1235');
        $this->assertEquals('test1', $config3->getData('value'));

        /**
         * Setup, scenario 4
         */
        $this->config->setConfigValue(SgCoreInterface::PATH_ALIAS, 'test4', ScopeInterface::SCOPE_WEBSITES, 1);

        /**
         * Same shop # in website & default scopes, different alias in both scopes /w extra store & website alias
         * default  | 0 | shopgate/general/shop_number | 1235
         * default  | 0 | shopgate/mobile/alias        | test2
         * stores   | 1 | shopgate/mobile/alias        | test3
         * websites | 1 | shopgate/mobile/alias        | test4
         * websites | 3 | shopgate/general/shop_number | 1235 <- should still pull this scope
         * websites | 3 | shopgate/mobile/alias        | test1
         */
        $config4 = $this->method->getSaveScope(SgCoreInterface::PATH_ALIAS, '1235');
        $this->assertEquals('test1', $config4->getData('value'));
    }

    /**
     * Tests the default scope config
     *
     * @covers ::getSaveScope
     */
    public function testGetSaveScopeDefault()
    {
        $this->config->setConfigValue(SgCoreInterface::PATH_SHOP_NUMBER, '1235');

        /**
         * Testing that other store values do not affect default setting
         */
        $site = $this->websiteConfig->createSite();
        $this->config->setConfigValue(
            SgCoreInterface::PATH_ALIAS,
            'test2',
            ScopeInterface::SCOPE_STORES,
            $site->getStore()->getId()
        );

        /**
         * shop# on default level & no entry for alias path
         * default | 0 | shopgate/general/shop_number | 1235 <- should pull this scope
         * store   | 3 | shopgate/mobile/alias        | test2
         */
        $config = $this->method->getSaveScope(SgCoreInterface::PATH_ALIAS, '1235');
        $this->assertEquals(0, $config->getScopeId());

        /**
         * shop# on default level & default's website entry exists for alias path
         * default  | 0 | shopgate/general/shop_number | 1235
         * websites | 1 | shopgate/mobile/alias        | test1 <- should pull this scope
         * stores   | 3 | shopgate/mobile/alias        | test2
         */
        $this->config->setConfigValue(SgCoreInterface::PATH_ALIAS, 'test1', ScopeInterface::SCOPE_WEBSITES, 1);
        $config = $this->method->getSaveScope(SgCoreInterface::PATH_ALIAS, '1235');
        $this->assertEquals(ScopeInterface::SCOPE_WEBSITES, $config->getScope());
        $this->assertEquals(1, $config->getScopeId());

        /**
         * shop# on default level & default's store entry exists for alias
         * default  | 0 | shopgate/general/shop_number | 1235
         * websites | 1 | shopgate/mobile/alias        | test1
         * stores   | 1 | shopgate/mobile/alias        | test3 <- should pull this scope
         * stores   | 3 | shopgate/mobile/alias        | test2
         */
        $this->config->setConfigValue(SgCoreInterface::PATH_ALIAS, 'test3', ScopeInterface::SCOPE_STORES, 1);
        $config = $this->method->getSaveScope(SgCoreInterface::PATH_ALIAS, '1235');
        $this->assertEquals(ScopeInterface::SCOPE_STORES, $config->getScope());
        $this->assertEquals(1, $config->getScopeId());
    }

    /**
     * Edge case testing where multiple stores have same shop #
     */
    public function testGetSaveScopeEdgeCasesForStores()
    {
        /**
         * Setup, scenario 1
         */
        $site  = $this->websiteConfig->createSite();
        $site2 = $this->websiteConfig->createStore($site);

        $this->config->setConfigValue(
            SgCoreInterface::PATH_SHOP_NUMBER,
            '1234',
            ScopeInterface::SCOPE_STORES,
            $site->getStore()->getId()
        );
        $this->config->setConfigValue(
            SgCoreInterface::PATH_SHOP_NUMBER,
            '1234',
            ScopeInterface::SCOPE_STORES,
            $site2->getStore()->getId()
        );

        /**
         * Get scope of first highest scope ID, so second created item
         *
         * stores | 4 | shopgate/general/shop_number | 1234 <- should pull this scope
         * stores | 3 | shopgate/general/shop_number | 1234
         */
        $config = $this->method->getSaveScope(SgCoreInterface::PATH_ALIAS, '1234');
        $this->assertEquals($config->getScopeId(), $site2->getStore()->getId());

        /**
         * Setup, scenario 2
         */
        $this->config->setConfigValue(
            SgCoreInterface::PATH_ALIAS,
            'test3',
            ScopeInterface::SCOPE_STORES,
            $site->getStore()->getId()
        );

        /**
         * Having an alias exist should affect which one it pulls
         *
         * stores | 4 | shopgate/general/shop_number | 1234
         * stores | 3 | shopgate/general/shop_number | 1234 <- should pull this scope as an alias is set
         * stores | 3 | shopgate/mobile/alias        | test3
         */
        $config = $this->method->getSaveScope(SgCoreInterface::PATH_ALIAS, '1234');
        $this->assertEquals($config->getScopeId(), $site->getStore()->getId());
    }

    /**
     * Edge case testing where multiple websites have same shop #
     */
    public function testGetSaveScopeEdgeCasesForWebsites()
    {
        /**
         * Setup, scenario 1
         */
        $site  = $this->websiteConfig->createSite();
        $site2 = $this->websiteConfig->createSite();

        $this->config->setConfigValue(
            SgCoreInterface::PATH_SHOP_NUMBER,
            '1234',
            ScopeInterface::SCOPE_WEBSITES,
            $site->getWebsite()->getId()
        );
        $this->config->setConfigValue(
            SgCoreInterface::PATH_SHOP_NUMBER,
            '1234',
            ScopeInterface::SCOPE_WEBSITES,
            $site2->getWebsite()->getId()
        );

        /**
         * Get scope of first highest scope ID, so second created item
         *
         * websites | 4 | shopgate/general/shop_number | 1234 <- should pull this scope
         * websites | 3 | shopgate/general/shop_number | 1234
         */
        $config = $this->method->getSaveScope(SgCoreInterface::PATH_ALIAS, '1234');
        $this->assertEquals($config->getScopeId(), $site2->getWebsite()->getId());

        /**
         * Setup, scenario 2
         */
        $this->config->setConfigValue(
            SgCoreInterface::PATH_ALIAS,
            'test3',
            ScopeInterface::SCOPE_WEBSITES,
            $site->getWebsite()->getId()
        );

        /**
         * Having an alias exist should affect which one it pulls
         *
         * websites | 4 | shopgate/general/shop_number | 1234
         * websites | 3 | shopgate/general/shop_number | 1234 <- should pull this scope as an alias is set
         * websites | 3 | shopgate/mobile/alias        | test3
         */
        $config = $this->method->getSaveScope(SgCoreInterface::PATH_ALIAS, '1234');
        $this->assertEquals($config->getScopeId(), $site->getWebsite()->getId());
    }

    /**
     * @after
     */
    public function cleanup()
    {
        $this->config->removeConfigs();
        $this->websiteConfig->removeSites();
        $this->storeManager->setCurrentStore(1);
    }
}
