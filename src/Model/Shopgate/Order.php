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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Shopgate\Base\Helper\Encoder;
use Shopgate\Base\Model\ResourceModel\Shopgate\Order as OrderResource;

/**
 * @method int getShopgateOrderId()
 * @method Order setShopgateOrderId(\int $id)
 * @method int getOrderId()
 * @method Order setOrderId(\int $id)
 * @method int getStoreId()
 * @method Order setStoreId(\int $id)
 * @method string getShopgateOrderNumber()
 * @method Order setShopgateOrderNumber(\string $orderNumber)
 * @method int getIsShippingBlocked()
 * @method Order setIsShippingBlocked(\int $flag)
 * @method int getIsPaid()
 * @method Order setIsPaid(\int $flag)
 * @method int getIsSentToShopgate()
 * @method Order setIsSentToShopgate(\int $flag)
 * @method int getIsCancellationSentToShopgate()
 * @method Order setIsCancellationSentToShopgate(\int $flag)
 * @method int getIsTest()
 * @method Order setIsTest(int $flag)
 * @method int getIsCustomerInvoiceBlocked()
 * @method Order setIsCustomerInvoiceBlocked(\int $flag)
 * @method string getReceivedData() - a serialized or json encoded string
 * @method Order setReceivedData(\string $serializedData)
 * @method OrderResource _getResource()
 */
class Order extends AbstractModel
{
    /** @var Encoder */
    private $encoder;

    /**
     * Class constructor
     *
     * @param Encoder $encoder
     */
    public function __construct(
        Encoder $encoder
    ) {
        $this->encoder = $encoder;

        parent::__construct();
        $this->_construct();
    }

    /**
     * Define resource model
     *
     * @codingStandardsIgnoreStart
     */
    protected function _construct()
    {
        $this->_init('Shopgate\Base\Model\ResourceModel\Shopgate\Order');
    }

    /**
     * @codingStandardsIgnoreEnd
     *
     * @param string $number
     *
     * @return Order
     * @throws LocalizedException
     */
    public function loadByShopgateOrderNumber($number)
    {
        $order = $this->_getResource()->getByOrderNumber($number);
        if (!empty($order)) {
            $this->setData($order);
        }

        return $this;
    }

    /**
     * @param string $id
     *
     * @return Order
     * @throws LocalizedException
     */
    public function loadByMageOrderId($id)
    {
        $order = $this->_getResource()->getByOrderId($id);
        if (!empty($order)) {
            $this->setData($order);
        }

        return $this;
    }

    /**
     * Rewriting default to avoid deprecation
     * and have the ability to modify the saving process later
     *
     * @throws \Exception
     */
    public function save()
    {
        $this->_getResource()->save($this);
    }

    /**
     * Get all shipments for the order
     *
     * @return string[]
     */
    public function getReportedShippingCollections()
    {
        $data = $this->getData('reported_shipping_collections');
        $data = $this->encoder->decode($data);
        if (!$data) {
            $data = [];
        }

        return $data;
    }

    /**
     * @param int[] $collectionIds
     *
     * @return Order
     */
    public function setReportedShippingCollections(array $collectionIds)
    {
        $collectionIds = $this->encoder->encode($collectionIds);
        $this->setData('reported_shipping_collections', $collectionIds);

        return $this;
    }
}
