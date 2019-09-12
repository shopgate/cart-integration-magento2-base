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

use Magento\Bundle\Api\Data\OptionInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Model\Product as MageProduct;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Spi\StockStateProviderInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Shopgate\Base\Helper\Product\Utility;
use Shopgate\Base\Model\Shopgate\Extended\OrderItem;

class Generic
{

    /** @var StockRegistryInterface */
    protected $stockRegistry;
    /** @var StockStateProviderInterface */
    protected $stateProvider;
    /** @var Option\Repository */
    private $productOptionRepo;
    /** @var ProductFactory */
    protected $productFactory;
    /** @var OrderItem | MageProduct | QuoteItem $item */
    protected $item;
    /** @var Utility */
    private $productUtility;

    /**
     * @param Option\Repository           $productOptionRepo
     * @param StockRegistryInterface      $stockRegistry
     * @param StockStateProviderInterface $stateProvider
     * @param ProductFactory              $productFactory
     * @param Utility                     $productUtility
     */
    public function __construct(
        Option\Repository $productOptionRepo,
        StockRegistryInterface $stockRegistry,
        StockStateProviderInterface $stateProvider,
        ProductFactory $productFactory,
        Utility $productUtility
    ) {
        $this->productOptionRepo = $productOptionRepo;
        $this->stockRegistry     = $stockRegistry;
        $this->stateProvider     = $stateProvider;
        $this->productFactory    = $productFactory;
        $this->productUtility    = $productUtility;
    }

    /**
     * todo-sg: move options into their own class
     *
     * @param MageProduct | null $product
     *
     * @return array
     * @throws \Exception
     * @throws NoSuchEntityException
     */
    public function parseOptions($product)
    {
        if (!$this->getItem() instanceof OrderItem) {
            throw new \Exception('The item provided is supposed to be of type OrderItem in class: ' . get_class($this));
        }

        $parentSku = $this->getItem()->getInternalOrderInfo()->getParentSku();
        $sku       = empty($parentSku) ? $product->getSku() : $parentSku;

        $data = [];
        foreach ($this->getItem()->getOptions() as $orderOption) {
            /* @var $orderOption \ShopgateOrderItemOption */
            $optionId = $orderOption->getOptionNumber();
            $value    = $orderOption->getValueNumber();

            $productOption = $this->getProductOptionById($sku, $optionId);
            if ($this->isHierarchyType($productOption)) {
                if ($value == 0) {
                    continue;
                }
                $value = [$value];
            }
            $data['options'][$optionId] = $value;
        }

        return $data;
    }

    /**
     * @param string       $sku
     * @param string | int $optionId
     *
     * @return OptionInterface
     * @throws NoSuchEntityException
     */
    public function getProductOptionById($sku, $optionId)
    {
        return $this->productOptionRepo->get($sku, $optionId);
    }

    /**
     * @param OptionInterface|ProductCustomOptionInterface $option
     *
     * @return bool
     */
    protected function isHierarchyType($option)
    {
        $type = $option->getType();

        return $type === Option::OPTION_TYPE_CHECKBOX || $type === Option::OPTION_TYPE_MULTIPLE;
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

        $errors          = [];
        $isBuyable       = true;
        $product         = $this->getItem()->getProduct();
        $stockItem       = $this->stockRegistry->getStockItem($product->getId(), $product->getStoreId());
        $checkIncrements = $this->stateProvider->checkQtyIncrements($stockItem, $this->getItem()->getQty());

        if ($stockItem->getManageStock() && !$product->isSaleable() && !$stockItem->getBackorders()) {
            $isBuyable        = false;
            $error['type']    = \ShopgateLibraryException::CART_ITEM_OUT_OF_STOCK;
            $error['message'] = \ShopgateLibraryException::getMessageFor(
                \ShopgateLibraryException::CART_ITEM_OUT_OF_STOCK
            );
            $errors[]         = $error;
        } elseif ($stockItem->getManageStock()
            && !$this->stateProvider->checkQty($stockItem, $this->getItem()->getQty())
            && !$stockItem->getBackorders()
        ) {
            $isBuyable        = false;
            $error['type']    = \ShopgateLibraryException::CART_ITEM_REQUESTED_QUANTITY_NOT_AVAILABLE;
            $error['message'] = \ShopgateLibraryException::getMessageFor(
                \ShopgateLibraryException::CART_ITEM_REQUESTED_QUANTITY_NOT_AVAILABLE
            );
            $errors[]         = $error;
        } elseif ($stockItem->getManageStock() && $checkIncrements->getHasError()) {
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
        $qtyBuyable = $isBuyable ? (int) $this->getItem()->getQty() : (int) $stockItem->getQty();
        $error      = array_pop($errors);

        return [
            'is_buyable'     => (int) $isBuyable,
            'qty_buyable'    => $qtyBuyable,
            'stock_quantity' => (int) $stockItem->getQty(),
            'error'          => isset($error['type']) ? $error['type'] : null,
            'error_text'     => isset($error['message']) ? $error['message'] : null,
        ];
    }

    /**
     * @param OrderItem | MageProduct | QuoteItem $item $item
     *
     * @return $this
     */
    public function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return MageProduct | QuoteItem | OrderItem
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return \Magento\Catalog\Model\Product[]
     * @throws NoSuchEntityException
     */
    public function getChildren()
    {
        return [];
    }

    /**
     * @param MageProduct $product - passed by reference as in configurable case we return parent instead of child
     *
     * @return DataObject
     * @throws \Exception
     */
    public function getBuyInfo(MageProduct &$product)
    {
        if (!$this->getItem() instanceof OrderItem) {
            throw new \Exception('The item provided is supposed to be of type QuoteItem in class: ' . get_class($this));
        }

        $orderInfo = $this->getItem()->getInternalOrderInfo();
        $buyInfo   = new DataObject(
            [
                'qty'     => $this->getItem()->getQuantity() * $this->getItem()->getStackQty(),
                'product' => $product->getId(),
            ]
        );

        if ($orderInfo->getOptions()) {
            $buyInfo->setData('super_attribute', $orderInfo->getOptions());
        }

        $options = $this->parseOptions($product);
        foreach ($this->getItem()->getInputs() as $orderInput) {
            $options['options'][$orderInput->getInputNumber()] = $orderInput->getUserInput();
        }

        return $buyInfo->addData($options);
    }

    /**
     * @param string | int $productId
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface | MageProduct
     * @throws NoSuchEntityException
     */
    public function loadProductById($productId)
    {
        return $this->productUtility->loadById($productId);
    }

    /**
     * Retrieve current item's product ID
     *
     * @return string
     */
    public function getItemId()
    {
        return $this->getItem()->getData('product_id');
    }
}
