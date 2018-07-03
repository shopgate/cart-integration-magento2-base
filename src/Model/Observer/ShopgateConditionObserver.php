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

namespace Shopgate\Base\Model\Observer;

use Magento\Framework\Event\ObserverInterface;
use Shopgate\Base\Model\Rule\Condition\ShopgateOrder;

class AddAppOnlySalesRuleCondition implements ObserverInterface
{
    /**
     * Execute observer.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $additional = $observer->getAdditional();
        $conditions = (array) $additional->getConditions();

        $conditions = array_merge_recursive($conditions, [$this->getShopgateCondition()]);

        $additional->setConditions($conditions);

        return $this;
    }

    /**
     * Get condition for Shopgate carts.
     *
     * @return array
     */
    private function getShopgateCondition()
    {
        return [
            'label' => __('Shopgate Mobile App'),
            'value' => ShopgateOrder::class,
        ];
    }
}
