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

namespace Shopgate\Base\Model\ResourceModel\Shopgate\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Shopgate\Base\Model\ResourceModel\Shopgate\Order;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            \Shopgate\Base\Model\Shopgate\Order::class,
            Order::class
        );
    }

    /**
     * Filters for all orders that are not already synchronized to Shopgate
     *
     * @return Collection
     */
    public function filterByUnsynchronizedOrders(): Collection
    {
        $this->getSelect()->where('is_sent_to_shopgate=?', '0');

        return $this;
    }

    /**
     * Filters for all orders that are already cancelled
     *
     * @return Collection
     */
    public function filterByCancelledOrders(): Collection
    {
        $this->getSelect()->where('is_cancellation_sent_to_shopgate=?', '0');

        return $this;
    }

    /**
     * @return array
     */
    public function getMageOrderIds(): array
    {
        return array_map(
            static function ($item) {
                /** @var \Shopgate\Base\Model\Shopgate\Order $item */
                return (int) $item->getOrderId();
            },
            $this->getItems()
        );
    }
}
