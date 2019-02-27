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

namespace Shopgate\Base\Helper\Settings\Country;

use Magento\OfflineShipping\Model\Carrier\Tablerate;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Shopgate\Base\Helper\Shipping;

class Retriever
{
    /**
     * @var Shipping\Carrier\TableRate
     */
    private $tableRate;
    /**
     * @var Shipping\Main
     */
    private $mainShipHelper;

    /**
     * @param Shipping\Main              $mainShipHelper
     * @param Shipping\Carrier\TableRate $tableRate
     */
    public function __construct(
        Shipping\Main $mainShipHelper,
        Shipping\Carrier\TableRate $tableRate
    ) {
        $this->mainShipHelper = $mainShipHelper;
        $this->tableRate      = $tableRate;
    }

    /**
     * Returns Merchant API ready array of Country, State pairs of
     * allowed addresses by Magento
     *
     * @return array() - array(['country'=>'US', 'state'=> 'All'], [...])
     */
    public function getAllowedAddressCountries()
    {
        $allowedShippingCountries    = $this->getAllowedShippingCountries();
        $allowedShippingCountriesMap = array_map(
            function ($country) {
                return $country['country'];
            },
            $allowedShippingCountries
        );

        $allowedAddressCountries = [];
        foreach ($this->mainShipHelper->getMageShippingCountries() as $addressCountry) {
            $state  = array_search($addressCountry, $allowedShippingCountriesMap, true);
            $states = $state !== false ? $allowedShippingCountries[$state]['state'] : ['All'];

            $allowedAddressCountries[] =
                [
                    'country' => $addressCountry,
                    'state'   => $states
                ];
        }

        return $allowedAddressCountries;
    }

    /**
     * Returns Merchant API ready array of Country, State pairs of
     * allowed shipping addresses by Magento
     *
     * @return array() - array(['country'=>'US', 'state'=> 'All'], ...)
     */
    public function getAllowedShippingCountries()
    {
        $allowedShippingCountriesRaw = $this->getAllowedShippingCountriesRaw();
        $allowedShippingCountries    = [];

        foreach ($allowedShippingCountriesRaw as $countryCode => $states) {
            $states = empty($states) ? ['All' => true] : $states;
            $states = array_filter(
                array_keys($states),
                function ($st) {
                    return is_string($st) ? $st : 'All';
                }
            );

            $states = in_array('All', $states, true) ? ['All'] : $states;

            array_walk(
                $states,
                function (&$state, $key, $country) {
                    $state = $state === 'All' ? $state : $country . '-' . $state;
                },
                $countryCode
            );

            $allowedShippingCountries[] =
                [
                    'country' => $countryCode,
                    'state'   => $states
                ];
        }

        return $allowedShippingCountries;
    }

    /**
     * Collects the raw allowed countries from Magento
     */
    private function getAllowedShippingCountriesRaw()
    {
        $allowedCountries = array_fill_keys($this->mainShipHelper->getMageShippingCountries(), []);
        $carriers         = $this->mainShipHelper->getActiveCarriers();
        $countries        = [];

        /**
         * @var AbstractCarrier $carrier
         */
        foreach ($carriers as $carrier) {
            /* skip shopgate cause its a container carrier */
            if ($carrier->getCarrierCode() === Shipping\Carrier\Shopgate::CODE) {
                continue;
            }

            /* if any carrier is using the allowed_countries collection, merge this into the result */
            if ($carrier->getConfigData('sallowspecific') === '0') {
                $countries = array_merge_recursive($countries, $allowedCountries);
                continue;
            }

            /* fetching active shipping targets from rates direct from the database */
            if ($carrier->getCarrierCode() === Shipping\Carrier\TableRate::CODE) {
                $collection    = $this->tableRate->getTableRateCollection();
                $countryHolder = [];

                /** @var Tablerate $rate */
                foreach ($collection as $rate) {
                    $countryHolder[$rate->getData('dest_country_id')][$rate->getData('dest_region') ? : 'All'] = true;
                }
                $countries = array_merge_recursive($countryHolder, $countries);
                continue;
            }

            $specificCountries = $carrier->getConfigData('specificcountry');
            $countries         = array_merge_recursive(
                $countries,
                array_fill_keys(explode(",", $specificCountries), [])
            );
        }

        foreach ($countries as $countryCode => $item) {
            if (!isset($allowedCountries[$countryCode])) {
                unset($countries[$countryCode]);
            }
        }

        return $countries;
    }
}
