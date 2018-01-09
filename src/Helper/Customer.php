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

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Helps primarily loading Magento data
 * For Shopgate object translation @see \Shopgate\Base\Helper\Shopgate\Customer
 * For Quote customer insertion @see \Shopgate\Base\Helper\Quote\Customer
 */
class Customer
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /**
     * @param CustomerRepositoryInterface $customerRepo
     */
    public function __construct(CustomerRepositoryInterface $customerRepo)
    {
        $this->customerRepository = $customerRepo;
    }

    /**
     * @param string | int $customerId
     *
     * @return CustomerInterface | ExtensibleDataInterface | DataObject
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getById($customerId)
    {
        if (!$customerId) {
            return new DataObject();
        }

        return $this->customerRepository->getById($customerId);
    }

    /**
     * @param string $customerEmail
     *
     * @return CustomerInterface | ExtensibleDataInterface | DataObject
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getByEmail($customerEmail)
    {
        if (!$customerEmail) {
            return new DataObject();
        }

        return $this->customerRepository->get($customerEmail);
    }
}
