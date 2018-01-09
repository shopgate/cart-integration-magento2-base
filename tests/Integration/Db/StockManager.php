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

namespace Shopgate\Base\Tests\Integration\Db;

use Magento\CatalogInventory\Api\Data\StockStatusInterface;
use Magento\CatalogInventory\Api\StockStatusRepositoryInterface;
use Magento\CatalogInventory\Model\Stock\Status;
use Magento\TestFramework\Helper\Bootstrap;

class StockManager
{
    /**
     * @param int | string $productId
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setStockWebsite($productId)
    {
        /** @var StockStatusRepositoryInterface $factory */
        $factory = Bootstrap::getObjectManager()->get('Magento\CatalogInventory\Model\Stock\StockItemRepository');
        /** @var StockStatusInterface | Status $item */
        $item = $factory->get($productId);
        if ($item->getWebsiteId() == 1) {
            $item->setWebsiteId(0);
            $factory->save($item);
        }
    }
}
