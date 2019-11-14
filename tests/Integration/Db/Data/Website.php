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

namespace Shopgate\Base\Tests\Integration\Db\Data;

use Magento\Store\Api\Data\GroupInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\TestFramework\Helper\Bootstrap;

class Website
{
    /** @var WebsiteInterface */
    private $website;
    /** @var  GroupInterface */
    private $group;
    /** @var StoreInterface */
    private $store;

    /**
     * @param null|string $websiteCode
     * @return WebsiteInterface
     *
     * @throws \Exception
     */
    public function createWebsite($websiteCode = null)
    {
        if (!$websiteCode) {
            $websiteCode = 'website_' . random_int(0, 99999);
        }

        return $this->website = Bootstrap::getObjectManager()
                                         ->create('Magento\Store\Api\Data\WebsiteInterface')
                                         ->setCode($websiteCode)
                                         ->setName('WebsiteName')
                                         ->save();
    }

    /**
     * @param null|int    $websiteId
     * @param int         $rootCatId
     * @param string|null $groupName
     *
     * @return GroupInterface
     *
     * @throws \Exception
     */
    public function createGroup($websiteId = null, $rootCatId = 0, $groupName = null)
    {
        if (!$groupName) {
            $groupName = 'group_' . random_int(0, 99999);
        }
        if (!$websiteId) {
            $websiteId = $this->getWebsite()->getId();
        }

        return $this->group = Bootstrap::getObjectManager()
                                       ->create('Magento\Store\Api\Data\GroupInterface')
                                       ->setWebsiteId($websiteId)
                                       ->setName($groupName)
                                       ->setRootCategoryId($rootCatId)
                                       ->save();
    }

    /**
     * @return WebsiteInterface
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param WebsiteInterface $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @param int         $websiteId
     * @param int         $storeGroupId
     * @param string|null $storeCode
     * @param string|null $storeName
     * @param int         $isActive
     *
     * @return StoreInterface
     */
    public function createStore(
        $websiteId = null,
        $storeGroupId = null,
        $storeCode = null,
        $storeName = null,
        $isActive = 1
    ) {
        $id = rand(0, 99999);

        if (!$storeName) {
            $storeName = 'Store ' . $id;
        }

        if (!$storeCode) {
            $storeCode = 'store_' . $id;
        }

        if (!$websiteId) {
            $websiteId = $this->getWebsite()->getId();
        }

        if (!$storeGroupId) {
            $storeGroupId = $this->getGroup()->getId();
        }

        return $this->store = Bootstrap::getObjectManager()
                                       ->create('Magento\Store\Api\Data\StoreInterface')
                                       ->setCode($storeCode)
                                       ->setWebsiteId($websiteId)
                                       ->setGroupId($storeGroupId)
                                       ->setName($storeName)
                                       ->setIsActive($isActive)
                                       ->save();
    }

    /**
     * @return GroupInterface
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param GroupInterface $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * Removes Website/Group/Store from DB
     *
     * @return $this
     */
    public function delete()
    {
        /** @var \Magento\Store\Model\ResourceModel\Store\Interceptor $resource */
        $resource = $this->getStore()->getResource();
        $resource->getConnection()->delete(
            $resource->getMainTable(),
            [$resource->getIdFieldName() . ' = ' . $this->getStore()->getId()]
        );
        /** @var \Magento\Store\Model\ResourceModel\Group\Interceptor $resource */
        $resource = $this->getGroup()->getResource();
        $resource->getConnection()->delete(
            $resource->getMainTable(),
            [$resource->getIdFieldName() . ' = ' . $this->getGroup()->getId()]
        );
        /** @var \Magento\Store\Model\ResourceModel\Website\Interceptor $resource */
        $resource = $this->getWebsite()->getResource();
        $resource->getConnection()->delete(
            $resource->getMainTable(),
            [$resource->getIdFieldName() . ' = ' . $this->getWebsite()->getId()]
        );

        unset($this->website, $this->store, $this->group);

        return $this;
    }

    /**
     * @return StoreInterface
     */
    public function getStore()
    {
        return $this->store;
    }
}
