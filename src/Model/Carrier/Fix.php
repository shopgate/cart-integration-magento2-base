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

namespace Shopgate\Base\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Tax\Model\Config as TaxConfig;
use Psr\Log\LoggerInterface;
use Shopgate\Base\Model\Config as MainConfig;
use Shopgate\Base\Model\Shopgate\Extended\Base;

class Fix extends AbstractCarrier implements CarrierInterface
{
    /** @codingStandardsIgnoreStart */
    /** @var string */
    protected $_code = 'shopgate';
    /** @var bool */
    protected $_isFixed = true;
    /** /** @codingStandardsIgnoreEnd */
    /** @var string */
    protected $method = 'fix';
    /** @var ResultFactory */
    private $rateResultFactory;
    /** @var MethodFactory */
    private $rateMethodFactory;
    /** @var TaxConfig */
    private $taxConfig;
    /** @var MainConfig */
    private $mainConfig;
    /** @var Base */
    private $sgOrder;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory         $rateErrorFactory
     * @param LoggerInterface      $logger
     * @param ResultFactory        $rateResultFactory
     * @param MethodFactory        $rateMethodFactory
     * @param TaxConfig            $taxConfig
     * @param MainConfig           $mainConfig
     * @param Base                 $sgOrder
     * @param array                $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        TaxConfig $taxConfig,
        MainConfig $mainConfig,
        Base $sgOrder,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->taxConfig         = $taxConfig;
        $this->mainConfig        = $mainConfig;
        $this->sgOrder           = $sgOrder;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->getCarrierCode() => __($this->getConfigData('name'))->render()];
    }

    /**
     * @param RateRequest $request
     *
     * @return bool | \Magento\Shipping\Model\Rate\Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->sgOrder->getOrderNumber() || !$this->sgOrder->getShippingInfos()
            || $this->sgOrder->getShippingType() === 'PLUGINAPI') {
            return false;
        }

        $shippingIncTax = $this->taxConfig->shippingPriceIncludesTax($this->mainConfig->getStoreViewId());
        $shippingInfo   = $this->sgOrder->getShippingInfos();
        $shippingAmount = $shippingIncTax ? $shippingInfo->getAmountGross() : $shippingInfo->getAmountNet();

        $method = $this->getDefaultMethod(round($shippingAmount, 2));
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();
        $result->append($method);

        return $result;
    }

    /**
     * Return the default shopgate payment method
     *
     * @param float        $price
     * @param float | null $cost
     *
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    private function getDefaultMethod($price, $cost = null)
    {
        $method = $this->rateMethodFactory->create();
        $method->setData('carrier', $this->getCarrierCode());
        $method->setData('carrier_title', 'Shopgate');
        $method->setData('method', $this->method);
        $method->setData('method_title', $this->getConfigData('name'));
        $method->setData('cost', $cost ? : $price);
        $method->setPrice($price);

        return $method;
    }
}
