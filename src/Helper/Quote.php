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

namespace Shopgate\Base\Helper;

use Magento\Catalog\Model\Product as MageProduct;
use Magento\Catalog\Model\Product\Attribute\Source\Status as MageStatus;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\Api\SimpleDataObjectConverter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Webapi\Exception;
use Magento\Quote\Model\Quote as MageQuote;
use Magento\Quote\Model\QuoteRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Helper\Data as Tax;
use Shopgate\Base\Helper\Product\Type;
use Shopgate\Base\Helper\Product\Utility;
use Shopgate\Base\Helper\Quote\Coupon;
use Shopgate\Base\Model\Shopgate\Extended;
use Shopgate\Base\Model\Utility\Registry;
use Shopgate\Base\Model\Utility\SgLoggerInterface;
use ShopgateLibraryException;

/**
 * This class must not return anything except itself as it only
 * handles quote loaders
 */
class Quote
{

    /** @var MageQuote */
    protected $quote;
    /** @var Extended\Base */
    protected $sgBase;
    /** @var SgLoggerInterface */
    protected $log;
    /** @var Utility */
    protected $productHelper;
    /** @var Tax */
    protected $taxData;
    /** @var Quote\Customer */
    protected $quoteCustomer;
    /** @var Registry */
    protected $coreRegistry;
    /** @var StoreManagerInterface */
    protected $storeManager;
    /** @var Coupon */
    protected $quoteCouponHelper;
    /** @var QuoteRepository */
    protected $quoteRepository;
    /** @var Type */
    protected $typeHelper;

    /**
     * @param MageQuote             $quote
     * @param Extended\Base         $cart
     * @param SgLoggerInterface     $logger
     * @param Utility               $productHelper
     * @param Tax                   $taxData
     * @param Quote\Customer        $quoteCustomer
     * @param Registry              $coreRegistry
     * @param StoreManagerInterface $storeManager
     * @param Coupon                $quoteCoupon
     * @param QuoteRepository       $quoteRepository
     * @param Type                  $typeHelper
     */
    public function __construct(
        MageQuote $quote,
        Extended\Base $cart,
        SgLoggerInterface $logger,
        Utility $productHelper,
        Tax $taxData,
        Quote\Customer $quoteCustomer,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        Coupon $quoteCoupon,
        QuoteRepository $quoteRepository,
        Type $typeHelper
    ) {
        $this->quote             = $quote;
        $this->sgBase            = $cart;
        $this->log               = $logger;
        $this->productHelper     = $productHelper;
        $this->taxData           = $taxData;
        $this->quoteCustomer     = $quoteCustomer;
        $this->coreRegistry      = $coreRegistry;
        $this->storeManager      = $storeManager;
        $this->quoteCouponHelper = $quoteCoupon;
        $this->quoteRepository   = $quoteRepository;
        $this->typeHelper        = $typeHelper;
    }

    /**
     * @param array $fields - quote methods to load
     *
     * @return MageQuote
     */
    public function load(array $fields)
    {
        $this->quote->setStoreId($this->storeManager->getStore()->getId());

        foreach ($fields as $rawFields) {
            $method = 'set' . SimpleDataObjectConverter::snakeCaseToUpperCamelCase($rawFields);
            $this->log->debug('Starting method ' . $method);
            $this->{$method}();
            $this->log->debug('Finished method ' . $method);
        }

        return $this->quote;
    }

    /**
     * Clean up quote table
     */
    public function cleanup()
    {
        $this->quoteRepository->delete($this->quote);
    }

    /**
     * Assign coupons to the quote
     */
    protected function setExternalCoupons()
    {
        $quote = $this->quoteCouponHelper->setCoupon();
        $this->quote->loadActive($quote->getEntityId());
    }

    /**
     * Assigns Shopgate cart items to quote
     */
    protected function setItems()
    {
        foreach ($this->sgBase->getItems() as $item) {
            if ($item->isSgCoupon()) {
                continue;
            }

            $info        = $item->getInternalOrderInfo();
            $amountNet   = $item->getUnitAmount();
            $amountGross = $item->getUnitAmountWithTax();

            try {
                $product = $this->productHelper->loadById($info->getProductId());
            } catch (\Exception $e) {
                $product = null;
            }

            if (!is_object($product) || !$product->getId() || $product->getStatus() != MageStatus::STATUS_ENABLED) {
                $this->log->error('Product with ID ' . $info->getProductId() . ' could not be loaded.');
                $this->log->error('SG item number: ' . $item->getItemNumber());
                $item->setUnhandledError(\ShopgateLibraryException::CART_ITEM_PRODUCT_NOT_FOUND, 'product not found');
                continue;
            }

            try {
                $quoteItem = $this->getQuoteItem($item, $product);
                if ($this->useShopgatePrices()) {
                    if ($this->taxData->priceIncludesTax($this->storeManager->getStore())) {
                        $quoteItem->setCustomPrice($amountGross);
                        $quoteItem->setOriginalCustomPrice($amountGross);
                    } else {
                        $quoteItem->setCustomPrice($amountNet);
                        $quoteItem->setOriginalCustomPrice($amountNet);
                    }
                }
                $quoteItem->setTaxPercent($item->getTaxPercent());

                if (!$item->isSimple()) {
                    $productWeight = $product->getTypeInstance()->getWeight($product);
                    $quoteItem->setWeight($productWeight);
                }

                $quoteItem->setRowWeight($quoteItem->getWeight() * $quoteItem->getQty());
                $this->quote->setItemsCount($this->quote->getItemsCount() + 1);

                /**
                 * Magento's flow is to save Quote on addItem, then on saveOrder load quote again. We mimic this here.
                 */
                $this->quoteRepository->save($this->quote);
                $this->quote = $this->quoteRepository->get($this->quote->getId());
            } catch (\Exception $e) {
                $this->log->error(
                    "Error importing product to quote by id: {$product->getId()}, error: {$e->getMessage()}"
                );
                $this->log->error('SG item number: ' . $item->getItemNumber());
                $item->setMagentoError($e->getMessage());
            }
        }
    }

    /**
     * @param Extended\OrderItem $item
     * @param MageProduct        $product - is "child" product only in Configurable/Grouped case
     *
     * @return bool | MageQuote\Item
     * @throws Exception
     * @throws ShopgateLibraryException
     * @throws \Exception
     * @throws LocalizedException
     */
    private function getQuoteItem(Extended\OrderItem $item, MageProduct &$product)
    {
        $buyObject   = $this->typeHelper->getType($item)->getBuyInfo($product);
        $processMode = $this->coreRegistry->isAction('check_stock') ? AbstractType::PROCESS_MODE_LITE
            : AbstractType::PROCESS_MODE_FULL;
        $quoteItem   = $this->quote->addProduct($product, $buyObject, $processMode);

        if (is_string($quoteItem)) {
            if (in_array($quoteItem, $this->getValidationErrors())) {
                throw new Exception(new Phrase($quoteItem));
            } else {
                throw new ShopgateLibraryException(
                    ShopgateLibraryException::UNKNOWN_ERROR_CODE,
                    "Error on adding product to quote! Details: " . var_export($quoteItem, true),
                    true
                );
            }
        }

        return $this->quote->getItemByProduct($product);
    }

    /**
     * Magento option/input validation
     * error list
     *
     * @return array
     */
    public function getValidationErrors()
    {
        return [
            /** Bundle option related errors */
            __('The options you selected are not available.')->render(),
            __('The required options you selected are not available.')->render(),
            __('Please specify product option(s).')->render(),
            __('Please select all required options.')->render(),
            __('You need to choose options for your item.')->render(),

            /** Input field related errors */
            __('The text is too long.')->render(),
            __('Please specify product\'s required option(s).')
        ];
    }

    /**
     * @todo-sg: create config for this at some point
     *
     * @return bool
     */
    private function useShopgatePrices()
    {
        return false;
    }

    /**
     * Assigns Shopgate cart customer to quote
     */
    protected function setCustomer()
    {
        $this->quoteCustomer->setEntity($this->quote);
        $this->quoteCustomer->setAddress($this->quote);
        $this->quoteCustomer->resetGuest($this->quote);
    }
}
