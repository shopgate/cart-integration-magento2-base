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

use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Shopgate\Base\Helper\Quote;

class OrderItem extends \ShopgateOrderItem
{
    /** @var ItemInfo */
    protected $decodedInfo;
    /** @var ItemInfoFactory */
    private $itemInfoFactory;
    /** @var int $errorCode */
    private $errorCode = 0;
    /** @var string $errorText */
    private $errorText = '';
    /** @var bool $unhandledError */
    private $unhandledError = false;
    /** @var Quote */
    private $quoteHelper;

    /**
     * @param ItemInfoFactory $itemInfoFactory
     * @param Quote           $quoteHelper
     * @param array           $data
     */
    public function __construct(ItemInfoFactory $itemInfoFactory, Quote $quoteHelper, $data = [])
    {
        $this->itemInfoFactory = $itemInfoFactory;
        $this->quoteHelper     = $quoteHelper;
        parent::__construct($data);
    }

    /**
     * @inheritdoc
     */
    public function getUnitAmount()
    {
        return $this->adjustAmountByStackQty(
            parent::getUnitAmount()
        );
    }

    /**
     * @param float | string $amount
     *
     * @return float
     * @throws \Zend_Json_Exception
     */
    private function adjustAmountByStackQty($amount)
    {
        $qty = $this->getStackQty();

        if ($qty > 1) {
            $amount = $amount / $qty;
        }

        return $amount;
    }

    /**
     * Retrieve stack qty if it exists
     *
     * @return int
     * @throws \Zend_Json_Exception
     */
    public function getStackQty()
    {
        $info = $this->getInternalOrderInfo();
        $qty  = 1;

        if ($info->getStackQuantity() > 1) {
            $qty = $info->getStackQuantity();
        }

        return $qty;
    }

    /**
     * @return ItemInfo
     * @throws \Zend_Json_Exception
     */
    public function getInternalOrderInfo()
    {
        if (!is_null($this->decodedInfo)) {
            return $this->decodedInfo;
        }

        $rawInfo = parent::getInternalOrderInfo();
        $obj     = $this->itemInfoFactory->create();

        return $this->decodedInfo = $obj->loadInfo($rawInfo);
    }

    /**
     * @inheritdoc
     */
    public function getUnitAmountWithTax()
    {
        return $this->adjustAmountByStackQty(
            parent::getUnitAmountWithTax()
        );
    }

    /**
     * Encodes to JSON if it's not empty or already JSON
     *
     * @param mixed $value
     *
     * @return string | null
     */
    public function setInternalOrderInfo($value)
    {
        if ($value instanceof ItemInfo) {
            $value = $value->toJson();
        } elseif (is_array($value)) {
            $value = \Zend_Json_Encoder::encode($value);
        }

        return parent::setInternalOrderInfo($value);
    }

    /**
     * Return parent ID if it exists
     *
     * @return string
     */
    public function getParentId()
    {
        if (!$this->isSimple()) {
            return explode('-', $this->getItemNumber())[0];
        }

        return $this->getItemNumber();
    }

    /**
     * Checks if current product is simple
     *
     * @return bool
     */
    public function isSimple()
    {
        return strpos($this->getItemNumber(), '-') === false;
    }

    /**
     * @return string | null
     */
    public function getChildId()
    {
        if (!$this->isSimple()) {
            return explode('-', $this->getItemNumber())[1];
        }

        return null;
    }

    /**
     * Helps unset the internal parameter
     */
    public function unsetDecodedInfo()
    {
        unset($this->decodedInfo);
    }

    /**
     * Takes a magento quote exception
     * and translated it into Shopgate error
     *
     * @param string $errorText
     *
     * @return OrderItem
     */
    public function setMagentoError($errorText)
    {
        $code    = $this->translateMagentoError($errorText);
        $message = \ShopgateLibraryException::getMessageFor($code);
        $this->setUnhandledError($code, $message);

        return $this;
    }

    /**
     * @param int    $code
     * @param string $message
     *
     * @return OrderItem
     */
    public function setUnhandledError($code, $message = '')
    {
        $this->unhandledError = ($code == 0) ? false : true;
        $this->errorCode      = $code;
        $this->errorText      = $message;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasUnhandledError()
    {
        return $this->unhandledError;
    }

    /**
     * @param string $errorText
     *
     * @return int
     */
    private function translateMagentoError($errorText)
    {
        if (in_array($errorText, $this->quoteHelper->getValidationErrors())) {
            return \ShopgateLibraryException::CART_ITEM_INPUT_VALIDATION_FAILED;
        }

        return \ShopgateLibraryException::CART_ITEM_OUT_OF_STOCK;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getErrorText()
    {
        return $this->errorText;
    }

    /**
     * Uses an export ID based on expectations
     * of our backend API
     */
    public function getExportProductId()
    {
        return $this->getInternalOrderInfo()->getItemType() == Grouped::TYPE_CODE
            ? $this->getChildId()
            : $this->getParentId();
    }
}
