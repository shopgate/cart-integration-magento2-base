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

namespace Shopgate\Base\Helper\Product\Type;

use Magento\Bundle\Model\OptionRepository;
use Magento\Bundle\Model\ResourceModel\Selection;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Spi\StockStateProviderInterface;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Store\Model\StoreManagerInterface;
use Shopgate\Base\Helper\Product\Utility;
use Shopgate\Base\Model\Shopgate\Extended\OrderItem;

class Bundle extends Generic
{
    /** @var OptionRepository */
    private $bundleOptions;
    /** @var Selection\CollectionFactory */
    private $selectionCollection;

    /**
     * @param Selection\CollectionFactory $selectCollection
     * @param Option\Repository           $productOptionRepo
     * @param OptionRepository            $bundleOptions
     * @param StockRegistryInterface      $stockRegistry
     * @param StockStateProviderInterface $stateProvider
     * @param ProductFactory              $productFactory
     * @param StoreManagerInterface       $storeManager
     * @param Utility                     $productUtility
     */
    public function __construct(
        Selection\CollectionFactory $selectCollection,
        Option\Repository $productOptionRepo,
        OptionRepository $bundleOptions,
        StockRegistryInterface $stockRegistry,
        StockStateProviderInterface $stateProvider,
        ProductFactory $productFactory,
        StoreManagerInterface $storeManager,
        Utility $productUtility
    ) {
        $this->bundleOptions       = $bundleOptions;
        $this->selectionCollection = $selectCollection;
        parent::__construct($productOptionRepo, $stockRegistry, $stateProvider, $productFactory, $productUtility);
    }

    /**
     * @inheritdoc
     */
    public function parseOptions($product)
    {
        if (!$this->getItem() instanceof OrderItem) {
            throw new \Exception('The item provided is supposed to be of type OrderItem in class: ' . get_class($this));
        }

        $data = [];
        foreach ($this->getItem()->getOptions() as $orderOption) {
            /* @var $orderOption \ShopgateOrderItemOption */
            $optionId = $orderOption->getOptionNumber();
            $value    = $orderOption->getValueNumber();

            $productOption = $this->getOptionById($product->getSku(), $optionId);
            //todo-sg: need to replicate @see https://shopgate.atlassian.net/browse/MAGENTO-820
            if (!$productOption->getOptionId()
                /*|| ($productOption->getOptionId() && $productOption->getParentId() != $product->getId())*/
            ) {
                $productOption = $this->getProductOptionById($product->getSku(), $optionId);

                if ($this->isHierarchyType($productOption)) {
                    if ($value == 0) {
                        continue;
                    }
                    $value = [$value];
                }
                $data['options'][$optionId] = $value;
            } else {
                if ($this->isHierarchyType($productOption)) {
                    if (!$value) {
                        continue;
                    }
                    $value = [$value];
                }

                $bundleSelection = $this->getSelectionById($value);

                $data['bundle_option_qty'][$optionId] = max(1, (int) $bundleSelection->getSelectionQty());
                $data['bundle_option'][$optionId]     = $value;
            }
        }

        return $data;
    }

    /**
     * @param string       $sku
     * @param string | int $optionId
     *
     * @return \Magento\Bundle\Api\Data\OptionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOptionById($sku, $optionId)
    {
        return $this->bundleOptions->get($sku, $optionId);
    }

    /**
     * @param string | int $selectionId
     *
     * @return Product | \Magento\Bundle\Model\Selection | DataObject - joined return of both
     */
    public function getSelectionById($selectionId)
    {
        $collection = $this->selectionCollection->create()->setSelectionIdsFilter([$selectionId]);

        return $collection->getFirstItem();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getStockData()
    {
        if (!$this->getItem() instanceof QuoteItem) {
            throw new \Exception('The item provided is supposed to be of type QuoteItem in class: ' . get_class($this));
        }

        $product    = $this->getItem()->getProduct();
        $stockItem  = $this->stockRegistry->getStockItem($product->getId(), $product->getStoreId());
        $errors     = [];
        $isBuyable  = true;
        $qtyBuyable = null;

        foreach ($this->getItem()->getChildren() as $childItem) {
            $childProduct    = $childItem->getProduct();
            $childStock      = $this->stockRegistry->getStockItem($childProduct->getId(), $childProduct->getStoreId());
            $checkIncrements = $this->stateProvider->checkQtyIncrements($childStock, $childItem->getQty());

            if ($childStock->getManageStock()
                && !$childProduct->isSaleable()
                && !$childStock->getBackorders()
            ) {
                $isBuyable        = false;
                $error['type']    = \ShopgateLibraryException::CART_ITEM_OUT_OF_STOCK;
                $error['message'] = \ShopgateLibraryException::getMessageFor(
                    \ShopgateLibraryException::CART_ITEM_OUT_OF_STOCK
                );
                $errors[]         = $error;
            } elseif ($childStock->getManageStock()
                && !$this->stateProvider->checkQty($childStock, $childItem->getQty())
                && !$childStock->getBackorders()
            ) {
                $isBuyable        = false;
                $error['type']    = \ShopgateLibraryException::CART_ITEM_REQUESTED_QUANTITY_NOT_AVAILABLE;
                $error['message'] = \ShopgateLibraryException::getMessageFor(
                    \ShopgateLibraryException::CART_ITEM_REQUESTED_QUANTITY_NOT_AVAILABLE
                );
                $errors[]         = $error;
                if ($qtyBuyable == null || $qtyBuyable > $childStock->getQty()) {
                    $qtyBuyable = $childStock->getQty();
                }
            } elseif ($childStock->getManageStock() && $checkIncrements->getData('has_error')) {
                $isBuyable        = false;
                $error['type']    = \ShopgateLibraryException::CART_ITEM_REQUESTED_QUANTITY_NOT_AVAILABLE;
                $error['message'] = \ShopgateLibraryException::getMessageFor(
                    \ShopgateLibraryException::CART_ITEM_REQUESTED_QUANTITY_NOT_AVAILABLE
                );
                $errors[]         = $error;
                $stockItem->setQty(
                    (int) ($this->getItem()->getQtyToAdd() / $stockItem->getQtyIncrements())
                    * $stockItem->getQtyIncrements()
                );
            }
        }

        $qtyBuyable = (int) ($qtyBuyable !== null) ? $qtyBuyable : $this->getItem()->getQty();
        $error      = array_pop($errors);

        return [
            'is_buyable'     => (int) $isBuyable,
            'qty_buyable'    => $qtyBuyable,
            'stock_quantity' => (int) $stockItem->getQty(),
            'error'          => isset($error['type']) ? $error['type'] : null,
            'error_text'     => isset($error['message']) ? $error['message'] : null,
        ];
    }
}
