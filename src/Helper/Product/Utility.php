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

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product as MageProduct;
use Magento\Store\Model\StoreManager;

class Utility
{
    /** @var StoreManager */
    protected $storeManager;
    /** @var ProductRepositoryInterface */
    protected $productRepository;

    /**
     * @param StoreManager               $storeManager
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(StoreManager $storeManager, ProductRepositoryInterface $productRepository)
    {
        $this->storeManager      = $storeManager;
        $this->productRepository = $productRepository;
    }

    /**
     * @param string | int $productId
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface | MageProduct
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function loadById($productId)
    {
        return $this->productRepository->getById($productId, false, $this->storeManager->getStore()->getId(), true);
    }
}
