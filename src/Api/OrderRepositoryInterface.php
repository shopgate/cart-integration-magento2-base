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

use Shopgate\Base\Model\Shopgate\Order;

interface OrderRepositoryInterface
{
    /**
     * @param string $orderNumber - shopgate order number
     *
     * @return Order
     */
    public function get($orderNumber);

    /**
     * @param string $id - magento order increment id
     *
     * @return Order
     */
    public function getByMageOrder($id);

    /**
     * Requires magento object & Base order to be loaded globally
     *
     * @param string $mageOrderId
     *
     * @throws \Exception
     * @throws \Zend_Serializer_Exception
     */
    public function createAndSave($mageOrderId);

    /**
     * @param string $orderNumber
     * @param bool   $throwExceptionOnDuplicate
     *
     * @return Order
     * @throws \ShopgateLibraryException
     */
    public function checkOrderExists($orderNumber, $throwExceptionOnDuplicate = false);

    /**
     * Updates isPaid and isShippingBlocked settings
     * using the loaded SG Base class
     *
     * @param Order $order
     */
    public function update(Order $order);
}
