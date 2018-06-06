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

namespace Shopgate\Base\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;
use Shopgate\Base\Api\CronInterface;
use Shopgate\Base\Api\ExportInterface;
use Shopgate\Base\Api\ImportInterface;
use Shopgate\Base\Api\SettingsInterface;
use ShopgateCart;
use ShopgateCustomer;
use ShopgateOrder;

/**
 * Forwards Shopgate Merchant API requests to the right functions
 */
class Forwarder extends \ShopgatePlugin
{
    /** @var Config */
    protected $config;
    /** @var ExportInterface */
    protected $exportApi;
    /** @var StoreManagerInterface */
    private $storeManager;
    /** @var SettingsInterface */
    private $settingsApi;
    /** @var ImportInterface */
    private $importApi;
    /** @var CronInterface */
    private $cronApi;

    /**
     * Gets called on initialization
     *
     * @codeCoverageIgnore
     */
    public function startup()
    {
        /** @var \Shopgate\Base\Helper\Initializer\Forwarder $forwarderInitializer */
        $manager              = ObjectManager::getInstance();
        $forwarderInitializer = $manager->get('Shopgate\Base\Helper\Initializer\Forwarder');
        $this->config         = $forwarderInitializer->getMainConfig();
        $this->settingsApi    = $forwarderInitializer->getSettingsInterface();
        $this->exportApi      = $forwarderInitializer->getExportInterface();
        $this->importApi      = $forwarderInitializer->getImportInterface();
        $this->cronApi        = $forwarderInitializer->getCronInterface();

        $configInitializer  = $forwarderInitializer->getConfigInitializer();
        $this->storeManager = $configInitializer->getStoreManager();
        $this->config->loadConfig();
    }

    /**
     * @inheritdoc
     */
    public function cron($jobname, $params, &$message, &$errorcount)
    {
        $this->cronApi->cron($jobname, $params, $message, $errorcount);
    }

    /**
     * @inheritdoc
     */
    public function getCustomer($user, $pass)
    {
        return $this->exportApi->getCustomerRaw($user, $pass);
    }

    /**
     * @inheritdoc
     */
    public function registerCustomer($user, $pass, ShopgateCustomer $customer)
    {
        $this->importApi->registerCustomerRaw($user, $pass, $customer);
    }

    /**
     * @inheritdoc
     */
    public function addOrder(ShopgateOrder $order)
    {
        return $this->importApi->addOrder($order);
    }

    /**
     * @inheritdoc
     */
    public function updateOrder(ShopgateOrder $order)
    {
        return $this->importApi->updateOrder($order);
    }

    /**
     * @inheritdoc
     */
    public function checkCart(ShopgateCart $cart)
    {
        return $this->exportApi->checkCartRaw($cart);
    }

    /**
     * @inheritdoc
     */
    public function checkStock(ShopgateCart $cart)
    {
        return $this->exportApi->checkStockRaw($cart);
    }

    /**
     * @inheritdoc
     */
    public function getSettings()
    {
        return $this->settingsApi->getSettings(null, null, null);
    }

    /**
     * @inheritdoc
     */
    public function getOrders(
        $customerToken,
        $customerLanguage,
        $limit = 10,
        $offset = 0,
        $orderDateFrom = '',
        $sortOrder = 'created_desc'
    ) {
        // TODO: Implement getOrders() method.
    }

    /**
     * @inheritdoc
     */
    public function syncFavouriteList($customerToken, $items)
    {
        // TODO: Implement syncFavouriteList() method.
    }

    /**
     * @inheritdoc
     */
    protected function createMediaCsv()
    {
        // TODO: Implement createMediaCsv() method.
    }

    /**
     * @inheritdoc
     */
    protected function createItems($limit = null, $offset = null, array $uids = [])
    {
        if ($this->splittedExport) {
            $limit  = is_null($limit) ? $this->exportLimit : $limit;
            $offset = is_null($offset) ? $this->exportOffset : $offset;
        }

        $items = $this->exportApi->getItemsRaw($limit, $offset, $uids);

        foreach ($items as $item) {
            $this->addItemModel($item);
        }

        return $items;
    }

    /**
     * @inheritdoc
     */
    protected function createCategories($limit = null, $offset = null, array $uids = [])
    {
        if ($this->splittedExport) {
            $limit  = is_null($limit) ? $this->exportLimit : $limit;
            $offset = is_null($offset) ? $this->exportOffset : $offset;
        }

        $categories = $this->exportApi->getCategoriesRaw($limit, $offset, $uids);

        foreach ($categories as $category) {
            $this->addCategoryModel($category);
        }

        return $categories;
    }

    /**
     * @inheritdoc
     */
    protected function createReviews($limit = null, $offset = null, array $uids = [])
    {
        if ($this->splittedExport) {
            $limit  = is_null($limit) ? $this->exportLimit : $limit;
            $offset = is_null($offset) ? $this->exportOffset : $offset;
        }

        $reviews = $this->exportApi->getReviewsRaw($limit, $offset, $uids);

        foreach ($reviews as $review) {
            $this->addReviewModel($review);
        }

        return $reviews;
    }
}
