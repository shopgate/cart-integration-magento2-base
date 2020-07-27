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

namespace Shopgate\Base\Api\Config;

interface SgCoreInterface
{
    const SECTION_BASE                = 'shopgate_base';
    const SECTION_ADVANCED            = 'shopgate_advanced';
    const PATH_GENERAL                = self::SECTION_BASE . '/general';
    const PATH_REDIRECT               = self::SECTION_BASE . '/redirect';
    const PATH_CUSTOMER_CONFIGURATION = self::SECTION_BASE . '/customer_configuration';
    const PATH_UNDEFINED              = self::SECTION_BASE . '/undefined/';
    const PATH_ACTIVE                 = self::PATH_GENERAL . '/active';
    const PATH_CUSTOMER_NUMBER        = self::PATH_GENERAL . '/customer_number';
    const PATH_SHOP_NUMBER            = self::PATH_GENERAL . '/shop_number';
    const PATH_API_KEY                = self::PATH_GENERAL . '/api_key';
    const PATH_ALIAS                  = self::PATH_REDIRECT . '/alias';
    const PATH_CNAME                  = self::PATH_REDIRECT . '/cname';
    const PATH_REDIRECT_TYPE          = self::PATH_REDIRECT . '/type';
    const PATH_CMS_MAP                = self::PATH_REDIRECT . '/cms_map';
    const PATH_PREFIX_MAP             = self::PATH_CUSTOMER_CONFIGURATION . '/prefix_map';
    const PATH_HTUSER                 = self::SECTION_ADVANCED . '/staging/htuser';
    const PATH_HTPASS                 = self::SECTION_ADVANCED . '/staging/htpass';
    const PATH_SERVER_TYPE            = self::SECTION_ADVANCED . '/staging/server';
    const PATH_API_URL                = self::SECTION_ADVANCED . '/staging/api_url';
    const PATH_USE_SHOPGATE_PRICES    = self::SECTION_ADVANCED . '/order/use_shopgate_prices';

    const VALUE_REDIRECT_HTTP = 'http';
    const VALUE_REDIRECT_JS   = 'js';

    const VALUE_SERVER_LIVE   = 'live';
    const VALUE_SERVER_PG     = 'pg';
    const VALUE_SERVER_CUSTOM = 'custom';

    /**
     * Retrieves the store ID based on Shopgate
     * shop number provided by the Merchant API
     *
     * @param string $shopNumber
     *
     * @return int
     */
    public function getStoreId($shopNumber);

    /**
     * Retrieves the Shopnumber collection
     *
     * @param string $shopNumber
     *
     * @return \Magento\Config\Model\ResourceModel\Config\Data\Collection
     */
    public function getShopNumberCollection($shopNumber);

    /**
     * Sets values onto stores scope if a websites scope value is threatening to overwrite them
     *
     * @param string $path
     * @param string $shopNumber
     *
     * @return \Magento\Framework\App\Config\Value
     */
    public function getSaveScope($path, $shopNumber);

    /**
     * Checks if the current store's vital
     * configurations are set:
     * 1) Enabled
     * 2) API Key
     * 3) Shop Number
     * 4) Customer Number
     *
     * @return bool
     */
    public function isValid();
}
