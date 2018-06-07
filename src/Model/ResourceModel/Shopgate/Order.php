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

namespace Shopgate\Base\Model\ResourceModel\Shopgate;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Order extends AbstractDb
{
    /**
     * Define main table
     *
     * @codingStandardsIgnoreStart
     */
    protected function _construct()
    {
        $this->_init('shopgate_order', 'shopgate_order_id');
    }
    /** @codingStandardsIgnoreEnd */

    /**
     * Gets order data using shopgate order number
     *
     * @param string $number
     *
     * @return array
     * @throws LocalizedException
     */
    public function getByOrderNumber($number)
    {
        $connection = $this->getConnection();
        $select     = $connection->select()->from($this->getMainTable())->where('shopgate_order_number = :number');
        $bind       = [':number' => (string) $number];

        return $connection->fetchRow($select, $bind);
    }

    /**
     * Gets order data using magento increment id
     *
     * @param string $id
     *
     * @return array
     * @throws LocalizedException
     */
    public function getByOrderId($id)
    {
        $connection = $this->getConnection();
        $select     = $connection->select()->from($this->getMainTable())->where('order_id = :internal_order_id');
        $bind       = [':internal_order_id' => (string) $id];

        return $connection->fetchRow($select, $bind);
    }

    /**
     * Filters for all orders that are not already synchronized to Shopgate
     *
     * @return Order
     */
    public function getUnsynchronizedOrders()
    {
        $this->getSelect()->where('is_sent_to_shopgate=?', '0');

        return $this;
    }

    /**
     * Filters for all orders that are already cancelled
     *
     * @return Order
     */
    public function getCancelledOrders()
    {
        $this->getSelect()->where('is_cancellation_sent_to_shopgate=?', '0');

        return $this;
    }
}
