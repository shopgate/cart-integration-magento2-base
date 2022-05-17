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

namespace Shopgate\Base\Tests\Unit\Helper\Settings\Tax;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Tax\Model\Calculation\Rate;
use Magento\Tax\Model\ResourceModel\Calculation\Rate\Collection;
use Shopgate\Base\Helper\Settings\Tax\Retriever;

/**
 * @coversDefaultClass \Shopgate\Base\Helper\Settings\Tax\Retriever
 */
class RetrieverTest extends \PHPUnit\Framework\TestCase
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
     * @param array $rates
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getTaxCollectionStubWithRates($rates)
    {
        $taxRates = $this->getMockBuilder(Collection::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $taxRates->method('getIterator')
                 ->will($this->returnValue(new \ArrayIterator($rates)));

        return $taxRates;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getBlankTaxRateDouble()
    {
        $taxRate = $this->getMockBuilder(Rate::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        return $taxRate;
    }

    /**
     * @param string $expectedCodeType
     * @param string $expectedZipCodePattern
     * @param string $zipCodeTo
     * @param string $zipCodeFrom
     * @param string $zipIsRange
     * @param string $postCode
     *
     * @covers ::getTaxRates
     * @dataProvider taxRateProvider
     */
    public function testGetTaxRatesReturns(
        $expectedCodeType,
        $expectedZipCodePattern,
        $zipCodeTo,
        $zipCodeFrom,
        $zipIsRange,
        $postCode
    ) {
        $taxRate = $this->getBlankTaxRateDouble();

        $taxRate->method('getZipIsRange')
                ->will($this->returnValue($zipIsRange));

        $taxRate->method('getZipFrom')
                ->will($this->returnValue($zipCodeFrom));

        $taxRate->method('getZipTo')
                ->will($this->returnValue($zipCodeTo));

        $taxRate->method('getTaxPostcode')
                ->will($this->returnValue($postCode));

        $taxRates = $this->getTaxCollectionStubWithRates([$taxRate]);

        /** @var \Shopgate\Base\Helper\Settings\Tax\Retriever $retrieverModel */
        $retrieverModel = $this->objectManager->getObject(
            '\Shopgate\Base\Helper\Settings\Tax\Retriever',
            [
                'taxRates' => $taxRates
            ]
        );

        $actual = $retrieverModel->getTaxRates();

        $this->assertEquals($actual[0]['zipcode_type'], $expectedCodeType);
        $this->assertEquals($actual[0]['zipcode_pattern'], $expectedZipCodePattern);
        $this->assertEquals($actual[0]['zipcode_range_from'], $zipCodeFrom);
        $this->assertEquals($actual[0]['zipcode_range_to'], $zipCodeTo);
    }

    /**
     * @return array
     */
    public function taxRateProvider()
    {
        return [
            'taxRate with range'    => [Retriever::ZIP_CODE_TYPE_RANGE, '', '1', '2', '3', ''],
            'taxRate without range' => [Retriever::ZIP_CODE_TYPE_ALL, '', '', '', '', ''],
            'taxRate with code'     => [Retriever::ZIP_CODE_TYPE_PATTERN, '23', '', '', '', '23']
        ];
    }
}
