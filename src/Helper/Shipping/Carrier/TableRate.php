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

namespace Shopgate\Base\Helper\Shipping\Carrier;

use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\Collection;
use Magento\Store\Model\StoreManagerInterface;

class TableRate
{
    const CODE = 'tablerate';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Collection
     */
    private $tableRateCollection;

    /**
     * @param StoreManagerInterface $storeManager
     * @param Collection            $tableRateCollection
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Collection $tableRateCollection
    ) {
        $this->storeManager        = $storeManager;
        $this->tableRateCollection = $tableRateCollection;
    }

    /**
     * Retrieve the table rate collection filtered by store's website
     *
     * @param null|string|int $storeId - will use this store's website to pull the collection
     *
     * @return Collection
     */
    public function getTableRateCollection($storeId = null)
    {
        return $this->tableRateCollection->setWebsiteFilter($this->storeManager->getStore($storeId)->getWebsiteId());
    }
}
