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

namespace Shopgate\Base\Model\Shopgate\Extended;

/**
 * Class that combines both cart and order objects into one
 */
class Base extends \ShopgateOrder
{
    /** @var string */
    protected $internalCartInfo;
    /** @var OrderItemFactory */
    private $itemFactory;
    /** @var ExternalCouponFactory */
    private $externalCouponFactory;
    /** @var AddressFactory */
    private $addressFactory;

    /**
     * @inheritdoc
     */
    public function __construct(
        OrderItemFactory $itemFactory,
        ExternalCouponFactory $externalCouponFactory,
        AddressFactory $addressFactory,
        $data = []
    ) {
        $this->itemFactory           = $itemFactory;
        $this->externalCouponFactory = $externalCouponFactory;
        $this->addressFactory        = $addressFactory;

        return parent::__construct($data);
    }

    /**
     * Returns cart items with errors
     *
     * @return OrderItem[]
     */
    public function getItemsWithUnhandledErrors()
    {
        $list = [];
        foreach ($this->getItems() as $item) {
            if ($item->hasUnhandledError()) {
                $list[] = $item;
            }
        }

        return $list;
    }

    /**
     * @return OrderItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Rewrite of the original just to
     * swap out the objects into our
     * custom item classes
     *
     * @param OrderItem[] | null $list
     */
    public function setItems($list)
    {
        if (!is_array($list)) {
            $this->items = null;

            return;
        }

        $items = [];
        foreach ($list as $index => $element) {
            if ((!is_object($element) || !($element instanceof \ShopgateOrderItem)) && !is_array($element)) {
                unset($list[$index]);
                continue;
            }

            if ($element instanceof \ShopgateOrderItem) {
                $element = $element->toArray();
            }

            $item = $this->itemFactory->create();
            $item->loadArray($element);
            $itemIds = explode('-', $item->getItemNumber());
            $items[array_pop($itemIds)] = $item;
        }

        $this->items = $items;
    }

    /**
     * @param ExternalCoupon[] $list
     *
     * @throws \ShopgateLibraryException
     */
    public function setExternalCoupons($list)
    {
        if (!is_array($list)) {
            $this->external_coupons = null;

            return;
        }

        foreach ($list as $index => &$element) {
            if ((!is_object($element) || !($element instanceof \ShopgateExternalCoupon)) && !is_array($element)) {
                unset($list[$index]);
                continue;
            }

            if ($element instanceof \ShopgateExternalCoupon) {
                $coupon = $this->externalCouponFactory->create();
                $coupon->loadArray($element->toArray());
                $list[$index] = $coupon;
            } elseif (is_array($element)) {
                $coupon = $this->externalCouponFactory->create();
                $coupon->loadArray($element);
                $list[$index] = $coupon;
            }
        }

        $this->external_coupons = $list;
    }

    /**
     * @param int | string $id
     *
     * @return null|OrderItem
     */
    public function getItemById($id)
    {
        $items = $this->getItems();
        if (isset($items[$id])) {
            return $items[$id];
        }

        return null;
    }

    /**
     * @return ExternalCoupon[]
     */
    public function getExternalCoupons()
    {
        return $this->external_coupons;
    }

    /**
     * Checks if the customer in Cart is a guest
     * based on external id
     *
     * @return bool
     */
    public function isGuest()
    {
        return empty($this->getExternalCustomerId());
    }

    /**
     * @return string
     */
    public function getInternalCartInfo()
    {
        return $this->internalCartInfo;
    }

    /**
     * @param string $value
     */
    public function setInternalCartInfo($value)
    {
        $this->internalCartInfo = $value;
    }

    /**
     * @param array | \ShopgateAddress $value
     */
    public function setInvoiceAddress($value)
    {
        if (!is_object($value) && !($value instanceof \ShopgateAddress) && !is_array($value)) {
            $this->invoice_address = null;

            return;
        }

        if ($value instanceof \ShopgateAddress) {
            $newAddress = $this->addressFactory->create();
            $newAddress->loadArray($value->toArray());
            $value = $newAddress;
        } elseif (is_array($value)) {
            $newAddress = $this->addressFactory->create();
            $newAddress->loadArray($value);
            $newAddress->setIsDeliveryAddress(false);
            $newAddress->setIsInvoiceAddress(true);
            $value = $newAddress;
        }

        $this->invoice_address = $value;
    }

    /**
     * @param array | \ShopgateAddress $value
     */
    public function setDeliveryAddress($value)
    {
        if (!is_object($value) && !($value instanceof \ShopgateAddress) && !is_array($value)) {
            $this->delivery_address = null;

            return;
        }

        if ($value instanceof \ShopgateAddress) {
            $newAddress = $this->addressFactory->create();
            $newAddress->loadArray($value->toArray());
            $value = $newAddress;
        } elseif (is_array($value)) {
            $newAddress = $this->addressFactory->create();
            $newAddress->loadArray($value);
            $newAddress->setIsDeliveryAddress(true);
            $newAddress->setIsInvoiceAddress(false);
            $value = $newAddress;
        }

        $this->delivery_address = $value;
    }

    /**
     * @return Address
     */
    public function getInvoiceAddress()
    {
        return $this->invoice_address;
    }

    /**
     * @return Address
     */
    public function getDeliveryAddress()
    {
        return $this->delivery_address;
    }

    /**
     * Returns fields in array format ready
     * for magento import
     *
     * @return array
     */
    public function customFieldsToArray()
    {
        $result = [];
        foreach ($this->getCustomFields() as $field) {
            $result[$field->getInternalFieldName()] = $field->getValue();
        }

        return $result;
    }
}
