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

namespace Shopgate\Base\Helper\Shipping;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrierInterface;
use Magento\Shipping\Model\Config;

class Main
{
    /**
     * @var Config
     */
    private $carrierConfig;
    /**
     * @var CollectionFactory
     */
    private $countryCollectionFactory;

    public function __construct(
        CollectionFactory $countryCollectionFactory,
        Config $carrierConfig
    ) {
        $this->carrierConfig = $carrierConfig;
        $this->countryCollectionFactory = $countryCollectionFactory;
    }

    /**
     * Retrieves Magento allowed shipping countries
     *
     * @param null|string|int $storeId - store ID of which to get the ship countries
     *
     * @return array
     */
    public function getMageShippingCountries($storeId = null)
    {
        $countryCollection = $this->countryCollectionFactory->create();
        return $countryCollection
            ->loadByStore($storeId)
            ->getColumnValues('country_id');
    }

    /**
     * Retrieves all carriers that are currently
     * set to active in magento
     *
     * @param null|string|int $storeId - store ID of which to get the carriers
     *
     * @return AbstractCarrierInterface[]
     */
    public function getActiveCarriers($storeId = null)
    {
        return $this->carrierConfig->getActiveCarriers($storeId);
    }
}
