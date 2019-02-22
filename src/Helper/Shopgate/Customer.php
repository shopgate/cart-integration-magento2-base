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

namespace Shopgate\Base\Helper\Shopgate;

use Shopgate\Base\Helper\Address;
use Shopgate\Base\Helper\Regions;
use Shopgate\Base\Model\Shopgate\Extended\Base;

/**
 * Helps with data translation from shopgate customer data to mage data
 */
class Customer
{
    /** @var Regions */
    private $regionsHelper;
    /** @var Address */
    private $addressHelper;

    /**
     * @param Regions $regionsHelper
     * @param Address $addressHelper
     */
    public function __construct(Regions $regionsHelper, Address $addressHelper)
    {
        $this->regionsHelper = $regionsHelper;
        $this->addressHelper = $addressHelper;
    }

    /**
     * @param \ShopgateCartBase | \ShopgateCustomer $order
     * @param \ShopgateAddress                      $address
     * @param int                                   $customerId
     *
     * @return array
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createAddressData($order, \ShopgateAddress $address, $customerId)
    {
        $addressData                         = $this->buildMagentoAddressArray($order, $address);
        $addressData['save_in_address_book'] =
            (int)($customerId && !$this->addressHelper->exists($customerId, $addressData));

        $addressData = array_merge($addressData, $this->getCustomFields($address));

        return $addressData;
    }

    /**
     * @param \ShopgateCartBase | \ShopgateCustomer $order
     * @param \ShopgateAddress                      $address
     *
     * @return array
     */
    private function buildMagentoAddressArray($order, $address)
    {
        $phoneNumber = $order->getMobile() ? : $order->getPhone();
        $region      = $this->regionsHelper->getMageRegionByAddress($address);
        $street2     = $address->getStreet2() ? "\n" . $address->getStreet2() : '';

        return [
            'prefix'     => ' ',
            'company'    => $address->getCompany(),
            'firstname'  => $address->getFirstName(),
            'lastname'   => $address->getLastName(),
            'street'     => $address->getStreet1() . $street2,
            'city'       => $address->getCity(),
            'postcode'   => $address->getZipcode(),
            'telephone'  => $phoneNumber ? : 'n.a.',
            'email'      => $order->getMail(),
            'country_id' => $address->getCountry(),
            'region_id'  => $region->getId() ? : ''
        ];
    }

    /**
     * Creates a custom field array
     *
     * @param \ShopgateAddress | \ShopgateCustomer $object
     * todo-sg: refactor this method into Shopgate\Extended\Customer own class
     *
     * @return array
     */
    public function getCustomFields($object)
    {
        $customFields = [];
        foreach ($object->getCustomFields() as $field) {
            $customFields[$field->getInternalFieldName()] = $field->getValue();
        }

        return $customFields;
    }
}
