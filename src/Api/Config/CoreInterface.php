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

namespace Shopgate\Base\Api\Config;

interface CoreInterface
{
    /**
     * Retrieves all database rows that matched
     * the value of the configuration entry
     *
     * @param string $value - value column
     *
     * @return \Magento\Config\Model\ResourceModel\Config\Data\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCollectionByValue($value);

    /**
     * @param string $path
     *
     * @return \Magento\Config\Model\ResourceModel\Config\Data\Collection
     */
    public function getCollectionByPath($path);

    /**
     * Retrieves the config object starting with
     * store first, then website, then default
     *
     * @param string   $path
     * @param int|null $storeId
     *
     * @return \Magento\Framework\App\Config\Value | \Magento\Framework\DataObject
     */
    public function getConfigByPath($path, $storeId = null);

    /**
     * Helps trickle down the collection until a store value is found.
     * Will search for store Value, then search for website value, then return default value
     *
     * @param \Magento\Config\Model\ResourceModel\Config\Data\Collection $collection
     * @param int|string                                                 $storeId
     *
     * @return \Magento\Framework\App\Config\Value | \Magento\Framework\DataObject
     */
    public function findConfigByStoreId($collection, $storeId);
}
