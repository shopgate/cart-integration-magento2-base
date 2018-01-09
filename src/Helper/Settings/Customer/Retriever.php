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

namespace Shopgate\Base\Helper\Settings\Customer;

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Model\ResourceModel\Group;
use Magento\Tax\Model\ResourceModel\TaxClass;

class Retriever
{
    /** @var TaxClass\Collection */
    private $taxCollection;
    /** @var Group\Collection */
    private $customerGroupCollection;

    /**
     * @param TaxClass\Collection $taxCollection
     * @param Group\Collection    $customerGroupCollection
     */
    public function __construct(
        TaxClass\Collection $taxCollection,
        Group\Collection $customerGroupCollection
    ) {
        $this->taxCollection           = $taxCollection;
        $this->customerGroupCollection = $customerGroupCollection;
    }

    /**
     * Retrieves Customer Group data
     *
     * @return array
     */
    public function getCustomerGroups()
    {
        $groups = [];

        /** @var GroupInterface $customerGroup */
        foreach ($this->customerGroupCollection->getItems() as $customerGroup) {
            $group               = [];
            $group['id']         = $customerGroup->getId();
            $group['name']       = $customerGroup->getCode();
            $group['is_default'] = intval($customerGroup->getId() == GroupInterface::NOT_LOGGED_IN_ID);

            $matchingTaxClasses =
                $this->taxCollection->getItemsByColumnValue('class_id', $customerGroup->getTaxClassId());

            if (count($matchingTaxClasses)) {
                $group['customer_tax_class_key'] = array_pop($matchingTaxClasses)->getClassName();
            }

            $groups[] = $group;
        }

        return $groups;
    }
}
