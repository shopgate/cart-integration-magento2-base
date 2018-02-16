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

use Shopgate\Base\Model\Shopgate\Extended\Base;

/**
 * Main export interface functions
 */
interface ExportInterface
{
    const SECTION_EXPORT             = 'shopgate_export';
    const PATH_CATEGORIES            = self::SECTION_EXPORT . '/categories';
    const PATH_PRODUCTS              = self::SECTION_EXPORT . '/products';
    const PATH_PROD_EAN_CODE         = self::PATH_PRODUCTS . '/ean_code';
    const PATH_PROD_FORCE_ATTRIBUTES = self::PATH_PRODUCTS . '/force_property_export';

    /**
     * Returns JSON to comply with Mage 2 WebApi
     *
     * @link http://developer.shopgate.com/plugin_api/export/get_categories
     *
     * @param string $action     - Requested method, e.g. get_categories
     * @param string $shopNumber - Your shop number as configured in the merchant API
     * @param string $traceId    - Unique request trace ID, handshake related
     * @param string $limit      - limit number of categories to pull
     * @param string $offset     - the offset number to pull from, e.g. 10 means next $limit from 10th item
     * @param int[]  $uids       - array of category ID's to pull
     *
     * @return string
     */
    public function getCategories($action, $shopNumber, $traceId, $limit = null, $offset = null, array $uids = []);

    /**
     * Raw data retriever for regular Merchant API requests
     *
     * @link http://developer.shopgate.com/plugin_api/export/get_categories
     *
     * @param null  $limit  - limit number of categories to pull
     * @param null  $offset - the offset number to pull from, e.g. 10 means next $limit from 10th item
     * @param int[] $uids   - array of category ID's to pull
     *
     * @return \Shopgate\Export\Model\Export\Category[]
     */
    public function getCategoriesRaw($limit = null, $offset = null, array $uids = []);

    /**
     * Returns JSON to comply with Mage 2 WebApi
     *
     * @link http://developer.shopgate.com/plugin_api/export/get_items
     *
     * @param string $action     - Requested method, e.g. get_items
     * @param string $shopNumber - Your shop number as configured in the merchant API
     * @param string $traceId    - Unique request trace ID, handshake related
     * @param string $limit      - limit number of categories to pull
     * @param string $offset     - the offset number to pull from, e.g. 10 means next $limit from 10th item
     * @param int[]  $uids       - array of item ID's to pull
     *
     * @return string
     */
    public function getItems($action, $shopNumber, $traceId, $limit = null, $offset = null, array $uids = []);

    /**
     * Raw data retriever for regular Merchant API requests
     *
     * @link http://developer.shopgate.com/plugin_api/export/get_items
     *
     * @param null  $limit  - limit number of categories to pull
     * @param null  $offset - the offset number to pull from, e.g. 10 means next $limit from 10th item
     * @param int[] $uids   - array of item ID's to pull
     *
     * @return \Shopgate\Export\Model\Export\Product[]
     */
    public function getItemsRaw($limit = null, $offset = null, array $uids = []);

    /**
     * Raw data retriever for regular Merchant API requests
     *
     * @link http://developer.shopgate.com/plugin_api/export/get_reviews
     *
     * @param null  $limit  - limit number of reviews to pull
     * @param null  $offset - the offset number to pull from, e.g. 10 means next $limit from 10th item
     * @param int[] $uids   - array of review ID's to pull
     *
     * @return \Shopgate\Export\Model\Export\Review[]
     */
    public function getReviewsRaw($limit = null, $offset = null, array $uids = []);

    /**
     * Returns JSON to comply with Mage 2 WebApi
     *
     * @link http://developer.shopgate.com/plugin_api/export/get_reviews
     *
     * @param string $action     - Requested method, e.g. get_items
     * @param string $shopNumber - Your shop number as configured in the merchant API
     * @param string $traceId    - Unique request trace ID, handshake related
     * @param string $limit      - limit number of reviews to pull
     * @param string $offset     - the offset number to pull from, e.g. 10 means next $limit from 10th item
     * @param int[]  $uids       - array of review ID's to pull
     *
     * @return string
     */
    public function getReviews($action, $shopNumber, $traceId, $limit = null, $offset = null, array $uids = []);

    /**
     * Returns the customer data, if the credentials are valid
     *
     * @link http://developer.shopgate.com/plugin_api/customers/get_customer
     *
     * @param string $user - The user name the customer entered at Shopgate Connect.
     * @param string $pass - The password the customer entered at Shopgate Connect.
     *
     * @return string
     * @throws \ShopgateLibraryException
     */
    public function getCustomer($user, $pass);

    /**
     * Returns the customer data for regular Merchant API requests
     *
     * @link http://developer.shopgate.com/plugin_api/customers/get_customer
     *
     * @param string $user - The user name the customer entered at Shopgate Connect.
     * @param string $pass - The password the customer entered at Shopgate Connect.
     *
     * @return \ShopgateCustomer
     * @throws \ShopgateLibraryException
     */
    public function getCustomerRaw($user, $pass);

    /**
     * Returns the check_cart data for magento API service.
     * Only allowed methods inside the DI.xml will be returned.
     *
     * @link http://developer.shopgate.com/plugin_api/cart
     *
     * @param array $cart
     *
     * @return string
     */
    public function checkCart(array $cart);

    /**
     * Returns the check_cart data for regular Merchant API requests.
     * Only allowed methods inside the DI.xml will be returned.
     *
     * @link http://developer.shopgate.com/plugin_api/cart
     *
     * @param Base | \ShopgateCart $cart
     *
     * @return array
     */
    public function checkCartRaw($cart);

    /**
     * Returns the check_stock data for regular Merchant API requests.
     *
     * @link http://developer.shopgate.com/plugin_api/stock
     *
     * @param Base | \ShopgateCart $cart
     *
     * @return array
     */
    public function checkStockRaw($cart);
}
