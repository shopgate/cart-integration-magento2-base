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

namespace Shopgate\Base\Helper\Initializer;

use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use Shopgate\Base\Api\Config\CoreInterface;
use Shopgate\Base\Api\Config\SgCoreInterface;
use Shopgate\Base\Helper\Config as ConfigHelper;
use Shopgate\Base\Model\Utility\Registry;
use Shopgate\Base\Model\Utility\SgLoggerInterface;

/**
 * The main purpose of this class is to initialize classes and pass them along.
 * This hack is needed due to the non-rewritable final constructor on ShopgateConfig lib class
 */
class Config
{
    /** @var StoreManagerInterface */
    private $storeManager;
    /** @var SgLoggerInterface */
    private $logger;
    /** @var DirectoryList */
    private $directory;
    /** @var CoreInterface */
    protected $coreConfig;
    /** @var sgCoreInterface */
    private $sgCoreConfig;
    /** @var CacheInterface */
    private $cache;
    /** @var ConfigResource */
    private $configResource;
    /** @var array - see global di.xml */
    private $configMapping;
    /** @var array - see global di.xml */
    private $configMethods;
    /** @var Registry */
    private $registry;
    /** @var ConfigHelper */
    private $helper;

    /**
     * @param StoreManagerInterface $storeManager
     * @param SgLoggerInterface     $logger
     * @param DirectoryList         $directory
     * @param SgCoreInterface       $sgCoreConfig
     * @param CoreInterface         $coreConfig
     * @param CacheInterface        $cache
     * @param ConfigResource        $configResource
     * @param Registry              $registry
     * @param ConfigHelper          $helper
     * @param array                 $configMethods - config methods to enable
     * @param array                 $configMapping - configs to retrieve from the database
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        SgLoggerInterface $logger,
        DirectoryList $directory,
        SgCoreInterface $sgCoreConfig,
        CoreInterface $coreConfig,
        CacheInterface $cache,
        ConfigResource $configResource,
        Registry $registry,
        ConfigHelper $helper,
        array $configMethods = [],
        array $configMapping = []
    ) {
        $this->configMapping  = $configMapping;
        $this->storeManager   = $storeManager;
        $this->logger         = $logger;
        $this->directory      = $directory;
        $this->configMapping  = $configMapping;
        $this->sgCoreConfig   = $sgCoreConfig;
        $this->coreConfig     = $coreConfig;
        $this->cache          = $cache;
        $this->configResource = $configResource;
        $this->configMethods  = $configMethods;
        $this->registry       = $registry;
        $this->helper         = $helper;
    }

    /**
     * @codeCoverageIgnore
     * @return StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->storeManager;
    }

    /**
     * @codeCoverageIgnore
     * @return SgLoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @codeCoverageIgnore
     * @return DirectoryList
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function getConfigMapping()
    {
        return $this->configMapping;
    }

    /**
     * @codeCoverageIgnore
     * @return CoreInterface
     */
    public function getCoreConfig()
    {
        return $this->coreConfig;
    }

    /**
     * @codeCoverageIgnore
     * @return SgCoreInterface
     */
    public function getSgCoreConfig()
    {
        return $this->sgCoreConfig;
    }

    /**
     * @codeCoverageIgnore
     * @return CacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @codeCoverageIgnore
     * @return ConfigResource
     */
    public function getConfigResource()
    {
        return $this->configResource;
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function getConfigMethods()
    {
        return $this->configMethods;
    }

    /**
     * @codeCoverageIgnore
     * @return Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @codeCoverageIgnore
     * @return ConfigHelper
     */
    public function getHelper()
    {
        return $this->helper;
    }
}
