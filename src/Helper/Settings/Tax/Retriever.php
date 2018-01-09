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

namespace Shopgate\Base\Helper\Settings\Tax;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Directory\Model\ResourceModel\Region;
use Magento\Framework\DataObject;
use Magento\Tax\Api\Data\TaxClassInterface;
use Magento\Tax\Model\ClassModel;
use Magento\Tax\Model\ResourceModel\Calculation\Rate;
use Magento\Tax\Model\ResourceModel\Calculation\Rule;
use Shopgate\Base\Helper\Regions;

class Retriever
{

    /** Type used by non specific zip codes */
    const ZIP_CODE_TYPE_ALL = 'all';
    /** Type used by range based zip codes */
    const ZIP_CODE_TYPE_RANGE = 'range';
    /** Type used by pattern based zip codes */
    const ZIP_CODE_TYPE_PATTERN = 'pattern';

    /**
     * @var ClassModel - main interface for tax collection retrieval
     */
    private $taxClass;
    /**
     * @var GroupManagementInterface
     */
    private $customerGroup;
    /**
     * @var Rate\Collection
     */
    private $taxRates;
    /**
     * @var Region\Collection
     */
    private $regionCollection;
    /**
     * @var Regions
     */
    private $regionHelper;
    /**
     * @var Rule\Collection
     */
    private $taxRules;

    /**
     * @param TaxClassInterface        $taxClass - tax collection
     * @param GroupManagementInterface $customerGroup
     * @param Rate\Collection          $taxRates
     * @param Region\Collection        $regionCollection
     * @param Regions                  $regionHelper
     * @param Rule\Collection          $taxRules
     */
    public function __construct(
        TaxClassInterface $taxClass,
        GroupManagementInterface $customerGroup,
        Rate\Collection $taxRates,
        Region\Collection $regionCollection,
        Regions $regionHelper,
        Rule\Collection $taxRules
    ) {
        $this->taxClass         = $taxClass;
        $this->customerGroup    = $customerGroup;
        $this->taxRates         = $taxRates;
        $this->regionCollection = $regionCollection;
        $this->regionHelper     = $regionHelper;
        $this->taxRules         = $taxRules;
    }

    /**
     * Retrieves all product related tax classes
     * for Merchant API to pick up
     *
     * @return array
     */
    public function getProductTaxClasses()
    {
        /** @var \Magento\Tax\Model\ResourceModel\TaxClass\Collection $taxCollection */
        $classes       = [];
        $taxCollection = $this->taxClass->getCollection();
        $taxCollection->setClassTypeFilter(ClassModel::TAX_CLASS_TYPE_PRODUCT);

        /** @var ClassModel $tax */
        foreach ($taxCollection as $tax) {
            $classes[] = [
                'id'  => $tax->getId(),
                'key' => $tax->getClassName()
            ];
        }

        return $classes;
    }

    /**
     * Retrieves all customer related tax classes
     * for Merchant API to pick up
     *
     * @return array
     */
    public function getCustomerTaxClasses()
    {
        /** @var \Magento\Tax\Model\ResourceModel\TaxClass\Collection $taxCollection */
        $classes       = [];
        $defaultTaxId  = $this->customerGroup->getNotLoggedInGroup()->getTaxClassId();
        $taxCollection = $this->taxClass->getCollection();
        $taxCollection->setClassTypeFilter(ClassModel::TAX_CLASS_TYPE_CUSTOMER);

        /** @var ClassModel $tax */
        foreach ($taxCollection as $tax) {
            $classes[] = [
                'id'         => $tax->getId(),
                'key'        => $tax->getClassName(),
                'is_default' => intval($defaultTaxId == $tax->getId()),
            ];
        }

        return $classes;
    }

    /**
     * Retrieve Magento tax rates for
     * Merchant API to pick up
     *
     * @return array
     */
    public function getTaxRates()
    {
        $rates = [];
        /** @var \Magento\Tax\Model\Calculation\Rate $rate */
        foreach ($this->taxRates as $rate) {
            $zipCodeType      = self::ZIP_CODE_TYPE_ALL;
            $zipCodePattern   = '';
            $zipCodeRangeFrom = '';
            $zipCodeRangeTo   = '';

            if ($rate->getZipIsRange()) {
                $zipCodeType      = self::ZIP_CODE_TYPE_RANGE;
                $zipCodeRangeFrom = $rate->getZipFrom();
                $zipCodeRangeTo   = $rate->getZipTo();
            } elseif ($rate->getTaxPostcode() && $rate->getTaxPostcode() != '*') {
                $zipCodeType    = self::ZIP_CODE_TYPE_PATTERN;
                $zipCodePattern = $rate->getTaxPostcode();
            }

            $state    = '';
            $regionId = $rate->getTaxRegionId();

            if ($regionId) {
                /** @var \Magento\Directory\Model\Region $region */
                $region = $this->regionCollection->getItemById($regionId);
                $state  = $this->regionHelper->getIsoStateByMagentoRegion(
                    new DataObject(
                        [
                            'region_code' => $region->getCode(),
                            'country_id'  => $rate->getTaxCountryId()
                        ]
                    )
                );
            }

            $rates[] = [
                'id'                 => $rate->getId(),
                'key'                => $rate->getId(),
                'display_name'       => $rate->getCode(),
                'tax_percent'        => round($rate->getRate(), 4),
                'country'            => $rate->getTaxCountryId(),
                'state'              => $state,
                'zipcode_type'       => $zipCodeType,
                'zipcode_pattern'    => $zipCodePattern,
                'zipcode_range_from' => $zipCodeRangeFrom,
                'zipcode_range_to'   => $zipCodeRangeTo
            ];
        }

        return $rates;
    }

    /**
     * Retrieve Magento tax rules for
     * Merchant API to pick up
     *
     * @return array
     */
    public function getTaxRules()
    {
        $rules = [];
        /* @var \Magento\Tax\Model\Calculation\Rule $rule */
        foreach ($this->taxRules as $rule) {
            $_rule = [
                'id'                   => $rule->getId(),
                'name'                 => $rule->getCode(),
                'priority'             => $rule->getPriority(),
                'product_tax_classes'  => [],
                'customer_tax_classes' => [],
                'tax_rates'            => [],
            ];

            foreach (array_unique($rule->getProductTaxClasses()) as $taxClass) {
                $_rule['product_tax_classes'][] = [
                    'id'  => $taxClass,
                    'key' => $taxClass
                ];
            }

            foreach (array_unique($rule->getCustomerTaxClasses()) as $taxClass) {
                $_rule['customer_tax_classes'][] = [
                    'id'  => $taxClass,
                    'key' => $taxClass
                ];
            }

            foreach (array_unique($rule->getRates()) as $taxRates) {
                $_rule['tax_rates'][] = [
                    'id'  => $taxRates,
                    'key' => $taxRates
                ];
            }

            $rules[] = $_rule;
        }

        return $rules;
    }
}
