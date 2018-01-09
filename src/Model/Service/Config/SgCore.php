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

use Magento\Config\Model\ResourceModel\Config\Data\Collection;
use Magento\Framework\App\Config\Value;
use Magento\Store\Model\ScopeInterface;
use Shopgate\Base\Api\Config\SgCoreInterface;
use ShopgateLibraryException;

class SgCore extends Core implements SgCoreInterface
{

    /**
     * @inheritdoc
     */
    public function getStoreId($shopNumber)
    {
        $configItem = $this->getShopNumberCollection($shopNumber)->getFirstItem();
        $scopeId    = $configItem->getData('scope_id');

        switch ($configItem->getData('scope')) {
            case ScopeInterface::SCOPE_STORES:
                return (int)$scopeId;
            case ScopeInterface::SCOPE_WEBSITES:
                /** @var \Magento\Store\Model\Website $website */
                $website = $this->storeManager->getWebsite($scopeId);

                return $website->getDefaultStore()->getStoreId();
            default:
                return $this->storeManager->getDefaultStoreView()->getId();
        }
    }

    /**
     * @inheritdoc
     * @return \Magento\Config\Model\ResourceModel\Config\Data\Collection
     */
    public function getShopNumberCollection($shopNumber)
    {
        return $this->configFactory->create()
                                   ->addFieldToFilter('path', self::PATH_SHOP_NUMBER)
                                   ->addValueFilter($shopNumber)
                                   ->setOrder('scope_id');
    }

    /**
     * @inheritdoc
     */
    public function getSaveScope($path, $shopNumber)
    {
        /**
         * Check if current store has value in provided path
         */
        $storeConfig = $this->getConfigByPath($path);
        if ($storeConfig->getData('value')) {
            return $storeConfig;
        }

        $shopConfig = $this->retrievePathScope($path, $shopNumber);

        return $shopConfig;
    }

    /**
     * Edge case checker, handles scenarios when multiple
     * scope id's have the same shop number.
     * Traverses the given collection of shopConfigs
     * to check whether a value in a given path exists.
     * If not, returns the first item of the collection.
     *
     * @param string $path
     * @param string $shopNumber
     *
     * @return Value | \Magento\Framework\DataObject
     * @throws ShopgateLibraryException
     */
    private function retrievePathScope($path, $shopNumber)
    {
        $shopConfigs = $this->getShopNumberCollection($shopNumber);

        if (!$shopConfigs->getSize()) {
            throw new ShopgateLibraryException(ShopgateLibraryException::PLUGIN_API_UNKNOWN_SHOP_NUMBER, false, true);
        }

        $firstItem = $shopConfigs->getFirstItem();

        /**
         * todo-sg: should be improved to use a single query, e.g. (scope = x AND scope_id = x) OR (scope = ...)
         *
         * @var Value $shopConfig
         */
        foreach ($shopConfigs as $shopConfig) {
            /** @var Collection $collection */
            $collection = $this
                ->getCollectionByPath($path)
                ->addFieldToFilter('scope', $shopConfig->getScope())
                ->addFieldToFilter('scope_id', $shopConfig->getScopeId());

            if ($collection->getItems()) {
                /** @var Value $propertyConfig */
                $propertyConfig = $collection->setOrder('scope_id')->getFirstItem();
                if ($propertyConfig->getData('value')) {
                    return $propertyConfig;
                }
            }
        }

        return $firstItem;
    }

    /**
     * @inheritdoc
     */
    public function isValid()
    {
        if (!empty($this->getConfigByPath(self::PATH_ACTIVE)->getValue())
            && !empty($this->getConfigByPath(self::PATH_API_KEY)->getValue())
            && !empty($this->getConfigByPath(self::PATH_SHOP_NUMBER)->getValue())
            && !empty($this->getConfigByPath(self::PATH_CUSTOMER_NUMBER)->getValue())
        ) {
            return true;
        }

        return false;
    }
}
