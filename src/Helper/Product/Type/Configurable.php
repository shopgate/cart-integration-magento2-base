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

use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote\Item;

class Configurable extends Generic
{

    /**
     * @return array
     * @throws \Exception
     */
    public function getStockData()
    {
        if (!$this->getItem() instanceof Item) {
            throw new \Exception('The item provided is supposed to be of type QuoteItem in class: ' . get_class($this));
        }

        $option = $this->getProductCustomOption();
        if (!$option) {
            return [
                'is_buyable'     => 1,
                'stock_quantity' => 0,
            ];
        }

        $product     = $option->getProduct();
        $currentItem = $this->getItem();

        $tempItem = clone $currentItem;
        $tempItem->setProduct($product);
        $this->setItem($tempItem);

        $data = parent::getStockData();
        $this->setItem($currentItem);

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getChildren()
    {
        $children        = [];
        $childProductIds = $this->getItem()->getTypeInstance()->getChildrenIds($this->getItem()->getId());
        $childProductIds = current($childProductIds);

        foreach ($childProductIds as $childProductId) {
            $children[] = $this->productRepository->getById($childProductId);
        }

        return $children;
    }

    /**
     * @inheritdoc
     */
    public function getBuyInfo(Product &$product)
    {
        $parent    = $this->loadProductById($this->getItem()->getParentId());
        $buyObject = parent::getBuyInfo($parent);

        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $type */
        $type            = $parent->getTypeInstance();
        $superAttributes = $type->getConfigurableAttributesAsArray($parent);
        $superAttConfig  = [];

        foreach ($superAttributes as $productAttribute) {
            $superAttConfig[$productAttribute['attribute_id']] = $product->getData(
                $productAttribute['attribute_code']
            );
        }
        $buyObject->setData('super_attribute', $superAttConfig);
        $product = $parent; //use parent instead of child, cascade the chain of reference

        return $buyObject;
    }

    /**
     * Retrieve parent-child pairing
     *
     * @inheritdoc
     */
    public function getItemId()
    {
        if ($this->getProductCustomOption()) {
            return $this->getItem()->getData('product_id')
                . '-' . $this->getProductCustomOption()->getProduct()->getId();
        }

        return parent::getItemId();
    }

    /**
     * @return \Magento\Quote\Model\Quote\Item\Option | null
     */
    private function getProductCustomOption()
    {
        return $this->getItem()->getOptionByCode('simple_product');
    }
}
