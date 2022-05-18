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

namespace Shopgate\Base\Test\Unit\Helper\Quote;

use Magento\Customer\Model\Data\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Model\Quote as MageQuote;
use Shopgate\Base\Helper\Customer as CustomerHelper;
use Shopgate\Base\Model\Shopgate\Extended\Base;

/**
 * @coversDefaultClass \Shopgate\Base\Helper\Quote\Customer
 */
class CustomerTest extends \PHPUnit\Framework\TestCase
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
     * @param string    $expectedIp
     * @param bool      $isGuest
     *
     * @covers \Shopgate\Base\Helper\Quote\Customer::setEntity
     *
     * @dataProvider ipProvider
     */
    public function testIpSetOnQuote($expectedIp, $isGuest)
    {
        $customerMock = $this->getMockBuilder(Customer::class)
            ->setMethods(['getId', 'getGroupId'])
            ->disableOriginalConstructor()
            ->getMock();

        $customerMock
            ->method('getId')
            ->will($this->returnValue(1));

        $customerSessionMock = $this->getMockBuilder(Session::class)
            ->setMethods(['setCustomerId', 'setCustomerGroupId'])
            ->disableOriginalConstructor()
            ->getMock();

        $customerSessionMock
            ->method('setCustomerId')
            ->will($this->returnValue($customerSessionMock));

        $customerHelperMock = $this->getMockBuilder(CustomerHelper::class)
            ->setMethods(['getById', 'getByEmail'])
            ->disableOriginalConstructor()
            ->getMock();

        $customerHelperMock
            ->method('getById')
            ->will($this->returnValue($customerMock));

        $customerHelperMock
            ->method('getByEmail')
            ->will($this->returnValue($customerMock));

        $sgBaseMock = $this->getMockBuilder(Base::class)
            ->setMethods(['getCustomerIp', 'isGuest', 'getExternalCustomerId'])
            ->disableOriginalConstructor()
            ->getMock();

        $sgBaseMock->expects($this->once())
            ->method('getCustomerIp')
            ->will($this->returnValue($expectedIp));

        $sgBaseMock
            ->method('getExternalCustomerId')
            ->will($this->returnValue(1));

        $sgBaseMock->expects($this->atLeastOnce())
            ->method('isGuest')
            ->will($this->returnValue($isGuest));

        $quoteMock = $this->getMockBuilder(MageQuote::class)
            ->setMethods(['setRemoteIp', 'setCustomer'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteMock->expects($this->once())
            ->method('setRemoteIp')
            ->with($expectedIp);

        $quoteMock->expects($this->any())
            ->method('setCustomer')
            ->will($this->returnValue($quoteMock));

        /** @var \Shopgate\Base\Helper\Quote\Customer $exportModel */
        $customerHelper = $this->objectManager->getObject(\Shopgate\Base\Helper\Quote\Customer::class,
            [
                'sgBase' => $sgBaseMock,
                'customerHelper' => $customerHelperMock,
                'session' => $customerSessionMock
            ]
        );

        $customerHelper->setEntity($quoteMock);
    }

    /**
     * @return array
     */
    public function ipProvider()
    {
        return [
            'Check Ip for guests' => ['1.1.1.1', true],
            'Check Ip for logged in users' => ['1.1.1.1', false]
        ];
    }
}
