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

namespace Shopgate\Base\Model\Service\Config;

use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Shopgate\Base\Api\Config\CoreInterface;

class Core implements CoreInterface
{
    /** @var CollectionFactory - core_config_data table collection */
    protected $configFactory;
    /** @var StoreManagerInterface */
    protected $storeManager;
    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /**
     * @param CollectionFactory     $configCollection
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface  $scopeConfig
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        CollectionFactory $configCollection,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->configFactory = $configCollection;
        $this->storeManager  = $storeManager;
        $this->scopeConfig   = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function getCollectionByValue($value)
    {
        return $this->configFactory->create()
                                   ->addFieldToSelect('*')
                                   ->addFieldToFilter('value', ['eq' => $value])
                                   ->addOrder('scope_id');
    }

    /**
     * @inheritdoc
     * @codeCoverageIgnore
     */
    public function getConfigByPath($path, $storeId = null)
    {
        $storeId = $storeId ? : $this->storeManager->getStore()->getId();
        $item    = $this->findConfigByStoreId($this->getCollectionByPath($path), $storeId);
        if (!$item->getValue()) {
            $type = $storeId ? ScopeInterface::SCOPE_STORES : ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $item->setValue($this->scopeConfig->getValue($path, $type, $storeId));
        }

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function findConfigByStoreId($collection, $storeId)
    {
        /** @var \Magento\Framework\App\Config\Value[] $storeConfigs */
        $storeConfigs = $collection->getItemsByColumnValue('scope', ScopeInterface::SCOPE_STORES);
        foreach ($storeConfigs as $store) {
            if ($store->getData('scope_id') == $storeId) {
                return $store;
            }
        }

        /** @var \Magento\Framework\App\Config\Value[] $websites */
        $websites  = $collection->getItemsByColumnValue('scope', ScopeInterface::SCOPE_WEBSITES);
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        foreach ($websites as $website) {
            if ($website->getData('scope_id') == $websiteId) {
                return $website;
            }
        }
        $item = $collection->getItemByColumnValue('scope', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);

        return $item ? : new DataObject;
    }

    /**
     * @inheritdoc
     * @return \Magento\Config\Model\ResourceModel\Config\Data\Collection
     */
    public function getCollectionByPath($path)
    {
        return $this->configFactory->create()
                                   ->addFieldToSelect('*')
                                   ->addFieldToFilter('path', ['like' => $path])
                                   ->addOrder('scope_id');
    }
}
