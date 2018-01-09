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

namespace Shopgate\Base\Api;

interface SettingsInterface
{
    const CUSTOMER_GROUPS            = 'customer_groups';
    const ALLOWED_ADDRESS_COUNTRIES  = 'allowed_address_countries';
    const ALLOWED_SHIPPING_COUNTRIES = 'allowed_shipping_countries';
    const PAYMENT_METHODS            = 'payment_methods';
    const TAX                        = 'tax';
    const TAX_RATES                  = 'tax_rates';
    const TAX_RULES                  = 'tax_rules';
    const PRODUCT_TAX_CLASSES        = 'product_tax_classes';
    const CUSTOMER_TAX_CLASSES       = 'customer_tax_classes';

    /**
     * Returns the JSON for Shopgate Backend
     *
     * @link http://developer.shopgate.com/plugin_api/system_information/get_settings
     *
     * @param string $action     - Requested method, e.g. get_settings
     * @param string $shopNumber - Your shop number as configured in the merchant API
     * @param string $traceId    - Unique request trace ID, handshake related
     *
     * @return string
     */
    public function getSettings($action, $shopNumber, $traceId);
}
