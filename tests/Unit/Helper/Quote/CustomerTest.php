<?php
/**
 * Shopgate GmbH
 *
 * URHEBERRECHTSHINWEIS
 *
 * Dieses Plugin ist urheberrechtlich geschützt. Es darf ausschließlich von Kunden der Shopgate GmbH
 * zum Zwecke der eigenen Kommunikation zwischen dem IT-System des Kunden mit dem IT-System der
 * Shopgate GmbH über www.shopgate.com verwendet werden. Eine darüber hinausgehende Vervielfältigung, Verbreitung,
 * öffentliche Zugänglichmachung, Bearbeitung oder Weitergabe an Dritte ist nur mit unserer vorherigen
 * schriftlichen Zustimmung zulässig. Die Regelungen der §§ 69 d Abs. 2, 3 und 69 e UrhG bleiben hiervon unberührt.
 *
 * COPYRIGHT NOTICE
 *
 * This plugin is the subject of copyright protection. It is only for the use of Shopgate GmbH customers,
 * for the purpose of facilitating communication between the IT system of the customer and the IT system
 * of Shopgate GmbH via www.shopgate.com. Any reproduction, dissemination, public propagation, processing or
 * transfer to third parties is only permitted where we previously consented thereto in writing. The provisions
 * of paragraph 69 d, sub-paragraphs 2, 3 and paragraph 69, sub-paragraph e of the German Copyright Act shall remain
 * unaffected.
 *
 * @author Shopgate GmbH <interfaces@shopgate.com>
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
    public function setUp()
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
