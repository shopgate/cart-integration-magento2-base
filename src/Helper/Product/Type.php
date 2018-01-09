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

namespace Shopgate\Base\Helper\Product;

use Magento\Bundle\Model\Product\Type as Bundle;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Shopgate\Base\Model\Shopgate\Extended\OrderItem;

class Type
{
    /** @var Type\Bundle */
    private $bundle;
    /** @var Type\Generic */
    private $generic;
    /** @var Type\Grouped */
    private $grouped;
    /** @var Type\Configurable */
    private $configurable;

    /**
     * @param Type\BundleFactory       $bundle
     * @param Type\GenericFactory      $generic
     * @param Type\GroupedFactory      $grouped
     * @param Type\ConfigurableFactory $configurable
     */
    public function __construct(
        Type\BundleFactory $bundle,
        Type\GenericFactory $generic,
        Type\GroupedFactory $grouped,
        Type\ConfigurableFactory $configurable
    ) {
        $this->bundle       = $bundle;
        $this->grouped      = $grouped;
        $this->generic      = $generic;
        $this->configurable = $configurable;
    }

    /**
     * Retrieves the correct type of product helper
     * based on passed item object
     *
     * @param OrderItem | Product | QuoteItem $item
     *
     * @return Type\Bundle | Type\Generic | Type\Grouped | Type\Configurable
     * @throws \Exception
     */
    public function getType($item)
    {
        switch ($this->getProductType($item)) {
            case Bundle::TYPE_CODE:
                return $this->bundle->create()->setItem($item);
            case Grouped::TYPE_CODE:
                return $this->grouped->create()->setItem($item);
            case Configurable::TYPE_CODE:
                return $this->configurable->create()->setItem($item);
            default:
                return $this->generic->create()->setItem($item);
        }
    }

    /**
     * Determine the product's type based on the
     * item/product passed
     *
     * @param OrderItem | Product | QuoteItem $item
     *
     * @return string - e.g. "bundle", "configurable", "simple", "grouped"
     * @throws \Exception
     */
    private function getProductType($item)
    {
        $type = '';
        if ($item instanceof OrderItem) {
            $type = $item->getInternalOrderInfo()->getItemType();
        } elseif ($item instanceof Product) {
            $type = $item->getTypeId();
        } elseif ($item instanceof QuoteItem) {
            $type = $item->getProduct()->getTypeId();
        }

        if (empty($type)) {
            throw new \Exception('Did not recognize the passed object: ' . get_class($item));
        }

        return $type;
    }
}
