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
use Magento\Catalog\Model\Product as MageProduct;
use Magento\GroupedProduct\Model\Product\Type\Grouped as MageGrouped;

class Grouped extends Generic
{
    /**
     * @param MageProduct[] $associatedProducts
     *
     * @return array
     */
    public function getSuperGroup($associatedProducts)
    {
        $superGroup = [];
        foreach ($associatedProducts as $associatedProduct) {
            $superGroup[$associatedProduct->getId()] = 1;
        }

        return $superGroup;
    }

    /**
     * @inheritdoc
     */
    public function getChildren()
    {
        $associatedProductIds = $this->getItem()->getTypeInstance()->getAssociatedProductIds($this->getItem());
        $productCollection    = $this->productFactory->create()->getCollection();
        $productCollection->addAttributeToFilter('entity_id', ['in' => $associatedProductIds]);
        $productCollection->addStoreFilter();

        $children = [];
        foreach ($productCollection as $product) {
            $children[] = $this->productFactory->create()->load($product->getId());
        }

        return $children;
    }

    /**
     * @inheritdoc
     */
    public function getBuyInfo(Product &$product)
    {
        $child     = $this->loadProductById($this->getItem()->getChildId());
        $parent    = $this->loadProductById($this->getItem()->getParentId());
        $buyObject = parent::getBuyInfo($child);

        /** @var MageGrouped $type */
        $type = $parent->getTypeInstance();

        $assocProducts = $type->getAssociatedProducts($parent);
        $buyObject->setData('super_group', $this->getSuperGroup($assocProducts));
        $buyObject->setData(
            'super_product_config',
            [
                'product_type' => MageGrouped::TYPE_CODE,
                'product_id'   => $parent->getId()
            ]
        );

        return $buyObject;
    }
}
