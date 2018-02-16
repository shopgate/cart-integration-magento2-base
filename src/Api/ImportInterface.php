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
use ShopgateCustomer;

interface ImportInterface
{
    /**
     * Registers a customer using magento API
     *
     * @link http://developer.shopgate.com/plugin_api/customers/register_customer
     *
     * @param string  $action     - requested method, "register_customer" in this case
     * @param string  $shopNumber - your shop number as configured in the merchant API
     * @param string  $user       - email address a customer used to register a new account
     * @param string  $pass       - the password a customer used to register a new account
     * @param string  $traceId    - unique request trace ID, handshake related
     * @param mixed[] $userData   - user specific data, like address, etc
     *
     * @return void
     */
    public function registerCustomer($action, $shopNumber, $user, $pass, $traceId, $userData);

    /**
     * Registers a customer using the Shopgate Merchant API call
     *
     * @link http://developer.shopgate.com/plugin_api/customers/register_customer
     *
     * @param string           $user     - email address a customer used to register a new account
     * @param string           $pass     - the password a customer used to register a new account
     * @param ShopgateCustomer $customer - customer object
     */
    public function registerCustomerRaw($user, $pass, ShopgateCustomer $customer);

    /**
     * Performs the necessary queries to add an order to the shop system's database.
     *
     * @see http://developer.shopgate.com/merchant_api/orders/get_orders
     * @see http://developer.shopgate.com/plugin_api/orders/add_order
     *
     * @param Base | \ShopgateOrder $order The ShopgateOrder object to be added to the shop system's database.
     *
     * @return array(
     *          <ul>
     *            <li>'external_order_id' => <i>string</i>, # the actual order ID as in database</li>
     *              <li>'external_order_number' => <i>string</i> # the increment ID of the order</li>
     *          </ul>)
     * @throws \ShopgateLibraryException if an error occurs.
     */
    public function addOrder($order);

    /**
     * Performs the necessary queries to update an order in the shop system's database.
     *
     * @see http://developer.shopgate.com/merchant_api/orders/get_orders
     * @see http://developer.shopgate.com/plugin_api/orders/update_order
     *
     * @param \ShopgateOrder $order The ShopgateOrder object to be updated in the shop system's database.
     *
     * @return array(
     *          <ul>
     *              <li>'external_order_id' => <i>string</i>, # the ID of the order in your shop system's database</li>
     *              <li>'external_order_number' => <i>string</i> # the number of the order in your shop system</li>
     *          </ul>)
     * @throws \ShopgateLibraryException if an error occurs.
     */
    public function updateOrder($order);
}
