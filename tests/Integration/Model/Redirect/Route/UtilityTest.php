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

namespace Shopgate\Base\Tests\Integration\Model\Redirect\Route;

use Magento\Framework\App\Request\Http;
use Shopgate\Base\Model\Redirect\Route\Type;
use Shopgate\Base\Tests\Bootstrap;

/**
 * @coversDefaultClass \Shopgate\Base\Model\Redirect\Route\Utility
 * @group Shopgate_Base_Redirect
 */
class UtilityTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Shopgate\Base\Model\Redirect\Route\Utility */
    private $class;
    /** @var \Magento\Backend\App\Action\Context */
    private $context;

    public function setUp()
    {
        $manager       = Bootstrap::getObjectManager();
        $this->class   = $manager->create('Shopgate\Base\Model\Redirect\Route\Utility');
        $this->context = $manager->create('Magento\Backend\App\Action\Context');
    }

    /**
     * @param string $controller
     * @param string $expectedType
     *
     * @covers ::getRoute
     * @dataProvider routeProvider
     */
    public function testGetRouteDefault($controller, $expectedType)
    {
        /** @var Http $req */
        $req = $this->context->getRequest();
        $req->setControllerName($controller);
        $route = $this->class->getRoute();

        $this->assertInstanceOf('Shopgate\Base\Model\Redirect\Route\Type\\' . $expectedType, $route);
    }

    /**
     * Provides all the routes
     *
     * @return array
     */
    public function routeProvider()
    {
        return [
            'Undefined type'     => ['la-la-land', 'Generic'],
            'Category page type' => [Type\Category::CONTROLLER_KEY, 'Category'],
            'Home page type'     => [Type\Home::CONTROLLER_KEY, 'Home'],
            'CMS page type'      => [Type\Page::CONTROLLER_KEY, 'Page'],
            'Product page type'  => [Type\Product::CONTROLLER_KEY, 'Product'],
            'Search page type'   => [Type\Search::CONTROLLER_KEY, 'Search']
        ];
    }
}
