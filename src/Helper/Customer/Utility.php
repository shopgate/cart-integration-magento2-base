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

namespace Shopgate\Base\Helper\Customer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection as GroupCollection;
use Magento\Directory\Model\CountryFactory;
use Magento\Tax\Model\ResourceModel\TaxClass\Collection as TaxClassCollection;

class Utility
{
    const MAGENTO_GENDER_MALE         = '1';
    const MAGENTO_GENDER_FEMALE       = '2';
    const MAGENTO_GENDER_NO_SPECIFIED = '3';

    /** @var GroupCollection */
    private $customerGroupCollection;
    /** @var TaxClassCollection */
    private $taxCollection;
    /** @var  CountryFactory */
    protected $countryFactory;

    /**
     * @param GroupCollection    $customerGroupCollection
     * @param TaxClassCollection $taxCollection
     * @param CountryFactory     $countryFactory
     */
    public function __construct(
        GroupCollection $customerGroupCollection,
        TaxClassCollection $taxCollection,
        CountryFactory $countryFactory
    ) {
        $this->customerGroupCollection = $customerGroupCollection;
        $this->taxCollection           = $taxCollection;
        $this->countryFactory          = $countryFactory;
    }

    /**
     * @param CustomerInterface $magentoCustomer
     *
     * @return \ShopgateCustomer
     */
    public function loadByMagentoCustomer($magentoCustomer)
    {
        $shopgateCustomer = new \ShopgateCustomer();
        $this->setBaseData($shopgateCustomer, $magentoCustomer);
        $this->setGroupData($shopgateCustomer, $magentoCustomer);
        $this->setAddresses($shopgateCustomer, $magentoCustomer);

        return $shopgateCustomer;
    }

    /**
     * @param \ShopgateCustomer $shopgateCustomer
     * @param CustomerInterface $magentoCustomer
     */
    protected function setBaseData($shopgateCustomer, $magentoCustomer)
    {
        $shopgateCustomer->setCustomerId($magentoCustomer->getId());
        $shopgateCustomer->setFirstName($magentoCustomer->getFirstname());
        $shopgateCustomer->setLastName($magentoCustomer->getLastname());
        $shopgateCustomer->setMail($magentoCustomer->getEmail());
        $shopgateCustomer->setBirthday($magentoCustomer->getDob());
        $shopgateCustomer->setGender($this->getShopgateGender($magentoCustomer->getGender()));
        $shopgateCustomer->setRegistrationDate($magentoCustomer->getCreatedAt());
    }

    /**
     * @param string $magentoGender
     *
     * @return string
     */
    protected function getShopgateGender($magentoGender)
    {
        $gender = '';
        if ($magentoGender === self::MAGENTO_GENDER_MALE) {
            $gender = \ShopgateCustomer::MALE;
        } elseif ($magentoGender === self::MAGENTO_GENDER_FEMALE) {
            $gender = \ShopgateCustomer::FEMALE;
        }

        return $gender;
    }

    /**
     * @param string $shopgateGender
     *
     * @return string
     */
    public function getMagentoGender($shopgateGender)
    {
        switch ($shopgateGender) {
            case \ShopgateCustomer::MALE:
                return self::MAGENTO_GENDER_MALE;
                break;
            case \ShopgateCustomer::FEMALE:
                return self::MAGENTO_GENDER_FEMALE;
                break;
            default:
                return self::MAGENTO_GENDER_NO_SPECIFIED;
        }
    }

    /**
     * @param \ShopgateCustomer $shopgateCustomer
     * @param CustomerInterface $magentoCustomer
     */
    public function setGroupData($shopgateCustomer, $magentoCustomer)
    {
        /** @var \Magento\Customer\Model\Group $magentoCustomerGroup */
        $magentoCustomerGroup =
            $this->customerGroupCollection->getItemByColumnValue('customer_group_id', $magentoCustomer->getGroupId());
        if ($magentoCustomerGroup) {
            $shopgateCustomerGroup = new \ShopgateCustomerGroup;
            $shopgateCustomerGroup->setId($magentoCustomerGroup->getId());
            $shopgateCustomerGroup->setName($magentoCustomerGroup->getCode());
            $shopgateCustomer->setCustomerGroups([($shopgateCustomerGroup)]);
        }

        /** @var \Magento\Tax\Model\ClassModel $taxClass */
        $taxClass = $this->taxCollection->getItemByColumnValue('class_id', $magentoCustomerGroup->getTaxClassId());
        if ($taxClass) {
            $shopgateCustomer->setTaxClassId($taxClass->getClassId());
            $shopgateCustomer->setTaxClassKey($taxClass->getClassName());
        }
    }

    /**
     * @param \ShopgateCustomer $shopgateCustomer
     * @param CustomerInterface $magentoCustomer
     */
    protected function setAddresses($shopgateCustomer, $magentoCustomer)
    {
        $addresses = [];
        foreach ($magentoCustomer->getAddresses() as $magentoCustomerAddress) {
            /** @var  \Magento\Customer\Model\Data\Address $magentoCustomerAddress */
            $shopgateAddress = new \ShopgateAddress();
            $shopgateAddress->setId($magentoCustomerAddress->getId());
            $shopgateAddress->setIsDeliveryAddress(1);
            $shopgateAddress->setIsInvoiceAddress(1);
            $shopgateAddress->setFirstName($magentoCustomerAddress->getFirstname());
            $shopgateAddress->setLastName($magentoCustomerAddress->getLastname());
            $shopgateAddress->setCompany($magentoCustomerAddress->getCompany());
            $shopgateAddress->setPhone($magentoCustomerAddress->getTelephone());

            $streetArray = $magentoCustomerAddress->getStreet();
            if (isset($streetArray[0])) {
                $shopgateAddress->setStreet1($streetArray[0]);
            }
            if (isset($streetArray[1])) {
                $shopgateAddress->setStreet2($streetArray[1]);
            }
            $shopgateAddress->setCity($magentoCustomerAddress->getCity());
            $shopgateAddress->setZipcode($magentoCustomerAddress->getPostcode());
            $shopgateAddress->setCountry($magentoCustomerAddress->getCountryId());
            $shopgateAddress->setState($magentoCustomerAddress->getRegion()->getRegionCode());

            $addresses[] = $shopgateAddress;
        }
        $shopgateCustomer->setAddresses($addresses);
    }
}
