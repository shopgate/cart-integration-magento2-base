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

namespace Shopgate\Base\Model\Shopgate;

use Shopgate\Base\Api\OrderRepositoryInterface;
use Shopgate\Base\Model\Config;
use Shopgate\Base\Model\Shopgate\Extended\Base;

class OrderRepository implements OrderRepositoryInterface
{
    /** @var OrderFactory */
    private $orderFactory;
    /** @var Base */
    private $sgOrder;
    /** @var Config */
    private $config;

    /**
     * @param OrderFactory $orderFactory
     * @param Base         $sgOrder
     * @param Config       $config
     */
    public function __construct(OrderFactory $orderFactory, Base $sgOrder, Config $config)
    {
        $this->orderFactory = $orderFactory;
        $this->sgOrder      = $sgOrder;
        $this->config       = $config;
    }

    /**
     * @inheritdoc
     */
    public function getByMageOrder($id)
    {
        return $this->orderFactory->create()->loadByMageOrderId($id);
    }

    /**
     * @inheritdoc
     */
    public function createAndSave($mageOrderId)
    {
        $order = $this->orderFactory->create()
                                    ->setOrderId($mageOrderId)
                                    ->setStoreId($this->config->getStoreViewId())
                                    ->setShopgateOrderNumber($this->sgOrder->getOrderNumber())
                                    ->setIsShippingBlocked($this->sgOrder->getIsShippingBlocked())
                                    ->setIsPaid($this->sgOrder->getIsPaid())
                                    ->setIsTest($this->sgOrder->getIsTest())
                                    ->setIsCustomerInvoiceBlocked($this->sgOrder->getIsCustomerInvoiceBlocked());

        $order->setReceivedData(\Zend_Serializer::serialize($this->sgOrder->toArray()));
        $order->save();
    }

    /**
     * @inheritdoc
     */
    public function update(Order $order)
    {
        if ($this->sgOrder->getUpdatePayment()) {
            $order->setIsPaid($this->sgOrder->getIsPaid());
        }

        if ($this->sgOrder->getUpdateShipping()) {
            $order->setIsShippingBlocked($this->sgOrder->getIsShippingBlocked());
        }
    }

    /**
     * @inheritdoc
     */
    public function checkOrderExists($orderNumber, $throwExceptionOnDuplicate = false)
    {
        $sgOrder = $this->get($orderNumber);
        if ($throwExceptionOnDuplicate && $sgOrder->getId() !== null) {
            throw new \ShopgateLibraryException(
                \ShopgateLibraryException::PLUGIN_DUPLICATE_ORDER,
                $orderNumber,
                true
            );
        }

        return $sgOrder;
    }

    /**
     * @inheritdoc
     */
    public function get($number)
    {
        return $this->orderFactory->create()->loadByShopgateOrderNumber($number);
    }
}
