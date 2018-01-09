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

namespace Shopgate\Base\Tests\Integration\Model\Redirect\Route\Type;

use Magento\Framework\App\Request\Http;
use Shopgate\Base\Api\Config\SgCoreInterface;
use Shopgate\Base\Model\Redirect\Route\Type;
use Shopgate\Base\Tests\Bootstrap;
use Shopgate\Base\Tests\Integration\Db\ConfigManager;

/**
 * @coversDefaultClass \Shopgate\Base\Model\Redirect\Type\Js
 * @group Shopgate_Base_Redirect
 */
class JsTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Shopgate\Base\Model\Redirect\Type\Js */
    private $class;
    /** @var ConfigManager */
    private $cfg;
    /** @var \Magento\Backend\App\Action\Context */
    private $context;

    public function setUp()
    {
        $manager       = Bootstrap::getObjectManager();
        $this->cfg     = new ConfigManager();
        $this->class   = $manager->create('Shopgate\Base\Model\Redirect\Type\Js');
        $this->context = $manager->create('Magento\Backend\App\Action\Context');
    }

    /**
     * @covers ::run
     */
    public function testInit()
    {
        $this->setUpConfig();

        /** @var Http $req */
        $req = $this->context->getRequest();
        $req->setControllerName(Type\Page::CONTROLLER_KEY)
            ->setParam('page_id', '5');

        $this->class->run();

        /** @var \Shopgate\Base\Model\Storage\Session $session */
        $session = Bootstrap::getObjectManager()->get('Shopgate\Base\Model\Storage\Session');
        $script  = $session->getScript();
        $this->assertNotEmpty($script);
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

        /** @var \Shopgate\Base\Model\Storage\Session $session */
        $session = Bootstrap::getObjectManager()->get('Shopgate\Base\Model\Storage\Session');
        $session->unsetScript();
    }
}
