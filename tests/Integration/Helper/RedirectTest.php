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

namespace Shoggate\Base\Test\Integration\Helper;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\Exception\LocalizedException;
use Shopgate\Base\Api\Config\SgCoreInterface;
use Shopgate\Base\Tests\Bootstrap;
use Shopgate\Base\Tests\Integration\Db\ConfigManager;

/**
 * @coversDefaultClass \Shopgate\Base\Helper\Redirect
 * @group Shopgate_Base_Redirect
 */
class RedirectTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Shopgate\Base\Helper\Redirect */
    private $class;
    /** @var \Magento\Framework\Model\Context */
    private $context;
    /** @var \Magento\Framework\App\Request\Http */
    private $http;
    /** @var ConfigManager */
    private $cfgManager;

    public function setUp(): void
    {
        $manager          = Bootstrap::getObjectManager();
        $this->cfgManager = new ConfigManager;
        $this->context    = $manager->get('Magento\Framework\Model\Context');
        $this->http       = $manager->get('Magento\Framework\App\Request\Http');
        $this->class      = $manager->create('Shopgate\Base\Helper\Redirect');
    }

    /**
     * Sets up all methods so that isAllowed is true
     *
     * @covers ::isAllowed
     */
    public function testIsAllowedAllTrue()
    {
        $this->setAllowedTrue();

        $this->assertTrue($this->class->isAllowed());
    }

    /**
     * Helps set all the configs to get isAllowed to be true
     *
     * @throws LocalizedException
     */
    private function setAllowedTrue()
    {
        $this->context->getAppState()->setAreaCode('default');

        $this->http->setParam('ajax', false);
        $this->http->setParam('isAjax', false);
        $this->http->getHeaders()->addHeaderLine('USER_AGENT', 'flash');

        $this->cfgManager->setConfigValue(SgCoreInterface::PATH_ACTIVE, 1);
        $this->cfgManager->setConfigValue(SgCoreInterface::PATH_SHOP_NUMBER, 12345);
        $this->cfgManager->setConfigValue(SgCoreInterface::PATH_API_KEY, 11111);
        $this->cfgManager->setConfigValue(SgCoreInterface::PATH_CUSTOMER_NUMBER, 1234);
    }

    /**
     * Fails if in admin area
     *
     * @covers ::isAllowed
     * @throws LocalizedException
     */
    public function testIsAllowedAdminArea()
    {
        $this->setAllowedTrue();
        $this->context->getAppState()->setAreaCode(FrontNameResolver::AREA_CODE);

        $this->assertFalse($this->class->isAllowed());
    }

    /**
     * Fails if it's an XML request
     *
     * @covers ::isAllowed
     * @throws LocalizedException
     */
    public function testIsAllowedNotXmlRequest()
    {
        $this->setAllowedTrue();
        $this->http->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');

        $this->assertFalse($this->class->isAllowed());
    }

    /**
     * Fails if it's an ajax request
     *
     * @covers ::isAllowed
     * @throws LocalizedException
     */
    public function testIsAllowedAjaxRequest()
    {
        $this->setAllowedTrue();
        $this->http->setParam('ajax', true);

        $this->assertFalse($this->class->isAllowed());
    }

    /**
     * Fails if the module is deactivated
     *
     * @covers ::isAllowed
     * @throws LocalizedException
     */
    public function testIsAllowedNotActive()
    {
        $this->setAllowedTrue();
        $this->cfgManager->setConfigValue(SgCoreInterface::PATH_ACTIVE, 0);

        $this->assertFalse($this->class->isAllowed());
    }

    /**
     * Fails if there is no shop number set in config
     *
     * @covers ::isAllowed
     * @throws LocalizedException
     */
    public function testIsAllowedNoShopNumber()
    {
        $this->setAllowedTrue();
        $this->cfgManager->setConfigValue(SgCoreInterface::PATH_SHOP_NUMBER, 0);

        $this->assertFalse($this->class->isAllowed());
    }

    /**
     * Fails if there is no API key is set in config
     *
     * @covers ::isAllowed
     * @throws LocalizedException
     */
    public function testIsAllowedNoApiKey()
    {
        $this->setAllowedTrue();
        $this->cfgManager->setConfigValue(SgCoreInterface::PATH_API_KEY, null);

        $this->assertFalse($this->class->isAllowed());
    }

    /**
     * Fails if there is no customer number set in config
     *
     * @covers ::isAllowed
     * @throws LocalizedException
     */
    public function testIsAllowedNoCustomerNumber()
    {
        $this->setAllowedTrue();
        $this->cfgManager->setConfigValue(SgCoreInterface::PATH_CUSTOMER_NUMBER, null);

        $this->assertFalse($this->class->isAllowed());
    }

    /**
     * Asserts that the config is true if JS redirect is set
     * in the system > config
     *
     * @covers ::isTypeJavaScript
     */
    public function testIsTypeJavaScriptTrue()
    {
        $this->cfgManager->setConfigValue(SgCoreInterface::PATH_REDIRECT_TYPE, SgCoreInterface::VALUE_REDIRECT_JS);

        $this->assertTrue($this->class->isTypeJavaScript());
    }

    /**
     * Asserts that the config is false if HTTP redirect is set
     * in the system > config
     *
     * @covers ::isTypeJavaScript
     */
    public function testIsTypeJavaScriptFalse()
    {
        $this->cfgManager->setConfigValue(SgCoreInterface::PATH_REDIRECT_TYPE, SgCoreInterface::VALUE_REDIRECT_HTTP);

        $this->assertFalse($this->class->isTypeJavaScript());
    }

    /**
     * @after
     */
    public function tearDown(): void
    {
        $this->cfgManager->removeConfigs();
    }
}
