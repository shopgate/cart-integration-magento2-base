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

use Magento\Directory\Model\ResourceModel\Country;
use Magento\Shipping\Model\Config;
use Magento\Store\Model\StoreManagerInterface;

class Main
{
    /**
     * @param StoreManagerInterface $storeManager
     * @param Country\Collection    $countryCollection
     * @param Config                $carrierConfig
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Country\Collection $countryCollection,
        Config $carrierConfig
    ) {
        $this->storeManager      = $storeManager;
        $this->countryCollection = $countryCollection;
        $this->carrierConfig     = $carrierConfig;
    }

    /**
     * Retrieves magento's allowed shipping countries
     *
     * @param null|string|int $storeId - store ID of which to get the ship countries
     *
     * @return mixed
     */
    public function getMageShippingCountries($storeId = null)
    {
        return $this
            ->countryCollection
            ->loadByStore($this->storeManager->getStore($storeId))
            ->getColumnValues('country_id');
    }

    /**
     * Retrieves all carriers that are currently
     * set to active in magento
     *
     * @param null|string|int $storeId - store ID of which to get the carriers
     *
     * @return mixed
     */
    public function getActiveCarriers($storeId = null)
    {
        return $this->carrierConfig->getActiveCarriers($this->storeManager->getStore($storeId));
    }
}
