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

namespace Shopgate\Base\Helper;

use Magento\Customer\Model\AddressFactory;

/**
 * Helps primarily comparing address data
 */
class Address
{
    /** @var AddressFactory */
    protected $addressFactory;

    /**
     * @param AddressFactory $addressFactory
     */
    public function __construct(AddressFactory $addressFactory)
    {
        $this->addressFactory = $addressFactory;
    }

    /**
     * Checks if provided address is also saved as an address for the given customer
     * by filtering its customer address
     *
     * @param int   $customerId
     * @param array $addressToCheck
     *
     * @return bool
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function exists($customerId, array $addressToCheck)
    {
        unset($addressToCheck['email']);
        if (empty($addressToCheck['company'])) {
            // company would be cast to an empty string, which does not match NULL in the database
            unset($addressToCheck['company']);
        }
        $addressCollection = $this->addressFactory->create()
            ->getCollection()
            ->addFieldToFilter('parent_id', $customerId);


        foreach ($addressToCheck as $addressField => $fieldValue) {
            $addressCollection->addFieldToFilter(
                $addressField,
                $fieldValue
            );
        }

        return $addressCollection->count() > 0;
    }
}
