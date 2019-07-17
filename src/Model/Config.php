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

use Exception;
use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\Api\SimpleDataObjectConverter;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use ReflectionException;
use ReflectionProperty;
use Shopgate\Base\Api\Config\CoreInterface;
use Shopgate\Base\Api\Config\SgCoreInterface;
use Shopgate\Base\Helper\Config as ConfigHelper;
use Shopgate\Base\Model\Utility\Registry;
use Shopgate\Base\Model\Utility\SgLoggerInterface;
use ShopgateConfig;
use ShopgateLibraryException;

class Config extends ShopgateConfig
{
    /** @var CoreInterface */
    protected $coreConfig;
    /** @var int */
    private $storeViewId;
    /** @var array - blacklist set via injection in di.xml */
    private $blacklistConfig = [];
    /** @var StoreManagerInterface */
    private $storeManager;
    /** @var SgLoggerInterface */
    private $logger;
    /** @var DirectoryList */
    private $directory;
    /** @var array - see global di.xml */
    private $configMapping;
    /** @var sgCoreInterface */
    private $sgCoreConfig;
    /** @var CacheInterface */
    private $cache;
    /** @var ConfigResource */
    private $configResource;
    /** @var array */
    private $configMethods;
    /** @var Registry */
    private $registry;
    /** @var ConfigHelper */
    private $configHelper;
    /**
     * Will hold all the parameters loaded from DB, Library preset properties and DI
     *
     * @var array
     */
    private $configVars = [];

    /**
     * Assists with initializing
     *
     * @return bool
     */
    public function startup()
    {
        /** @var \Shopgate\Base\Helper\Initializer\Config $initializer */
        $manager              = ObjectManager::getInstance();
        $initializer          = $manager->get('Shopgate\Base\Helper\Initializer\Config');
        $this->storeManager   = $initializer->getStoreManager();
        $this->logger         = $initializer->getLogger();
        $this->directory      = $initializer->getDirectory();
        $this->sgCoreConfig   = $initializer->getSgCoreConfig();
        $this->coreConfig     = $initializer->getCoreConfig();
        $this->cache          = $initializer->getCache();
        $this->configResource = $initializer->getConfigResource();
        $this->configMapping  = $initializer->getConfigMapping();
        $this->configMethods  = $initializer->getConfigMethods();
        $this->registry       = $initializer->getRegistry();
        $this->configHelper   = $initializer->getHelper();
        $this->plugin_name    = 'magento2';
        $this->configMapping  += $this->configHelper->loadUndefinedConfigPaths();
        $this->loadArray($this->configMethods);

        return true;
    }

    /**
     * Prepares values to be saved.
     * Note that we use the += operator intentionally
     *
     * @param array|null $settings
     *
     * @throws ReflectionException
     */
    public function load(array $settings = null)
    {
        $classVars = array_keys(get_class_vars(get_class($this)));

        foreach ($settings as $name => $value) {
            if (in_array($name, $this->blacklistConfig, true)) {
                continue;
            }

            if (in_array($name, $classVars, true)) {
                $method = 'set' . SimpleDataObjectConverter::snakeCaseToUpperCamelCase($name);
                if (method_exists($this, $method)) {
                    $this->configMapping += $this->configHelper->getNewSettingPath($name);
                    $this->{$method}($this->castToType($value, $name));
                } else {
                    $this->logger->debug(
                        'The evaluated method "' . $method . '" is not available in class ' . __CLASS__
                    );
                }
            } else {
                if (array_key_exists($name, $this->additionalSettings)) {
                    $this->additionalSettings[$name] = $value;
                } else {
                    $this->logger->debug(
                        'The given setting property "' . $name . '" is not available in class ' . __CLASS__
                    );
                }
            }
        }
    }

    /**
     * Cast a given property value to the matching property type
     *
     * @param mixed  $value
     * @param string $property
     *
     * @return boolean|number|string|integer
     * @throws ReflectionException
     */
    protected function castToType($value, $property)
    {
        $type = $this->getPropertyType($property);

        switch ($type) {
            case 'array':
                return is_array($value) ? $value : explode(",", $value);
            case 'bool':
            case 'boolean':
                return (boolean) $value;
            case 'int':
            case 'integer':
                return (int) $value;
            case 'string':
                return (string) $value;
            default:
                return $value;
        }
    }

    /**
     * Fetches the property type described in phpdoc annotation
     *
     * @param string $property
     *
     * @return string
     * @throws ReflectionException
     */
    protected function getPropertyType($property): string
    {
        if (!array_key_exists($property, get_class_vars('ShopgateConfig'))) {
            return 'string';
        }

        $r   = new ReflectionProperty('ShopgateConfig', $property);
        $doc = $r->getDocComment();
        preg_match_all('#@var ([a-zA-Z-_]*(\[\])?)(.*?)\n#s', $doc, $annotations);

        $value = 'string';
        if (count($annotations) > 0 && isset($annotations[1][0])) {
            $value = $annotations[1][0];
        }

        return $value;
    }

    /**
     * Load general information and values.
     * Use shop number to determine store only when it is not a frontend call
     *
     * @throws FileSystemException
     * @throws ReflectionException
     */
    public function loadConfig()
    {
        $this->setGlobalStoreOfShopNumber($this->getShopNumber());
        $this->loadArray($this->toArray());
        $this->setExportTmpAndLogSettings();
    }

    /**
     * Retrieves the storeId of the shopNumber's config and sets that
     * store ID as global
     *
     * @param string $shopNumber
     */
    private function setGlobalStoreOfShopNumber(string $shopNumber)
    {
        if (!$this->registry->isRedirect()) {
            $storeId = $this->sgCoreConfig->getStoreId($shopNumber);
            $this->storeManager->setCurrentStore($storeId);
        }
    }

    /**
     * Retrieve the shop number of the current request
     *
     * @return string|null
     */
    public function getShopNumber()
    {
        if ($this->shop_number === null) {
            $this->shop_number = $this->configHelper->getShopNumber();
        }

        return $this->shop_number;
    }

    /**
     * Setup export, log and tmp folder and check if need to create them
     *
     * @throws FileSystemException
     */
    protected function setExportTmpAndLogSettings()
    {
        $this->setExportFolderPath(
            $this->directory->getPath(DirectoryList::TMP) . DS . 'shopgate' . DS . $this->getShopNumber()
        );
        $this->createFolderIfNotExist($this->getExportFolderPath());

        $this->setLogFolderPath(
            $this->directory->getPath(DirectoryList::LOG) . DS . 'shopgate' . DS . $this->getShopNumber()
        );
        $this->createFolderIfNotExist($this->getLogFolderPath());

        $this->setCacheFolderPath(
            $this->directory->getPath(DirectoryList::TMP) . DS . 'shopgate' . DS . $this->getShopNumber()
        );
        $this->createFolderIfNotExist($this->getCacheFolderPath());
    }

    /**
     * @param string $folderPath - folder path to check if exists and create
     */
    private function createFolderIfNotExist($folderPath)
    {
        if (!is_dir($folderPath) && mkdir($folderPath, 0777, true) && !is_dir($folderPath)) {
            $this->logger->error('Could not create path: ' . $folderPath);
        }
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function toArray(): array
    {
        /**
         * Blacklist check
         */
        if (!empty($this->configVars)) {
            $temp = $this->configVars;
            foreach ($this->blacklistConfig as $key) {
                unset($temp[$key]);
            }

            return $temp;
        }

        $classProperties  = get_class_vars(get_class($this));
        $classVarMap      = $this->methodLoader($classProperties);
        $coreConfigMap    = $this->getCoreConfigMap($this->configMapping);
        $this->configVars = array_merge($classVarMap, $coreConfigMap);

        return $this->configVars;
    }

    /**
     * Uses the keys of the array as camelCase methods,
     * calls them and retrieves the data
     *
     * @param array $classProperties - where array('hello' => '') calls $this->getHello()
     *
     * @return array - where we get $return['hello'] = $this->getHello()
     */
    private function methodLoader(array $classProperties)
    {
        $result = [];
        foreach ($classProperties as $propertyName => $nullVal) {
            $result[$propertyName] = $this->getPropertyValue($propertyName);
        }

        return $result;
    }

    /** ======= SAVE LOGIC ========= */

    /**
     * Calls the properties getter method to retrieve the value
     *
     * @param $property
     *
     * @return null
     */
    private function getPropertyValue($property)
    {
        $value  = null;
        $getter = 'get' . SimpleDataObjectConverter::snakeCaseToUpperCamelCase($property);

        if (method_exists($this, $getter)) {
            $value = $this->{$getter}();
        } elseif ($this->returnAdditionalSetting($property)) {
            $value = $this->returnAdditionalSetting($property);
        }

        return $value;
    }

    /**
     * Traverses the config and retrieves data from
     * magento core_config_data table
     *
     * @param array $map - where key is Library defined property name
     *                   & value is core_config_data path
     *
     * @return array - $return['key'] = (cast type) value
     * @throws ReflectionException
     */
    private function getCoreConfigMap(array $map): array
    {
        $result = [];

        foreach ($map as $key => $path) {
            $value = $this->coreConfig->getConfigByPath($path)->getData('value');
            if ($value !== null) {
                $result[$key] = $this->castToType($value, $key);
            }
        }

        return $result;
    }

    /**
     * Writes the given fields to magento
     *
     * @param array   $fieldList
     * @param boolean $validate
     *
     * @throws Exception
     * @throws ShopgateLibraryException
     */
    public function save(array $fieldList, $validate = true)
    {
        $this->logger->debug('# setSettings save start');

        if ($validate) {
            $this->validate($fieldList);
        }

        foreach ($fieldList as $property) {
            if (in_array($property, $this->blacklistConfig, true)) {
                continue;
            }

            if (isset($this->configMapping[$property])) {
                $config =
                    $this->sgCoreConfig->getSaveScope($this->configMapping[$property], $this->getShopNumber());
                $this->saveField(
                    $this->configMapping[$property],
                    $property,
                    $config->getScope(),
                    $config->getScopeId()
                );
            }
        }

        $this->clearCache();
        $this->logger->debug('# setSettings save end');
    }

    /**
     * Saves this property to magento core_config_data
     *
     * @param string $path
     * @param string $property
     * @param string $scope
     * @param int    $scopeId
     * @param mixed  $value
     *
     * @throws Exception
     */
    protected function saveField($path, $property, $scope, $scopeId, $value = null)
    {
        if ($value === null) {
            if (isset($this->configMapping[$property])) {
                $value = $this->getPropertyValue($property);
            } else {
                $this->logger->error('The specified property "' . $property . '" is not in the DI list');
            }
        }

        if ($value !== null) {
            $this->logger->debug(
                '    Saving config field \'' . $property . '\' with value \'' . $value . '\' to scope {\''
                . $scope . '\':\'' . $scopeId . '\'}'
            );
            $value = $this->prepareForDatabase($property, $value);
            $this->configResource->saveConfig($path, $value, $scope, $scopeId);
        }
    }

    /**
     * Converts values into a core_config_data compatible format
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return mixed
     * @throws ReflectionException
     */
    protected function prepareForDatabase($property, $value)
    {
        $type = $this->getPropertyType($property);

        if ($type === 'array' && is_array($value)) {
            return implode(',', $value);
        }
        if (is_bool($value)) {
            $value = (int) $value;
        }

        return $value;
    }

    /** ======= Getters / Setters =========== */

    /**
     * Clears config cache after saving altered configuration
     */
    protected function clearCache()
    {
        $result = $this->cache->clean([\Magento\Framework\App\Config::CACHE_TAG]);
        $this->logger->debug(
            ' Config cache cleared with result: ' . ($result ? '[OK]' : '[ERROR]')
        );
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreViewId(): int
    {
        if (!$this->storeViewId) {
            $this->setGlobalStoreOfShopNumber($this->getShopNumber());
            $this->storeViewId = $this->storeManager->getStore()->getId();
        }

        return $this->storeViewId;
    }

    /**
     * @param int $value
     */
    public function setStoreViewId($value)
    {
        $this->storeViewId = $value;
    }

    /**
     * @param array $keys
     */
    public function setBlacklistConfig($keys)
    {
        $this->blacklistConfig = $keys;
    }

    /**
     * Return array values so that the ping returns
     * an Array instead of a JSON object
     *
     * @return array
     */
    public function getSupportedFieldsCheckCart(): array
    {
        return array_values(parent::getSupportedFieldsCheckCart());
    }

    /**
     * todo-sg: must be implemented in MAGENTO2-6
     * di.xml controls which one it uses
     *
     * @see https://shopgate.atlassian.net/browse/MAGENTO2-6
     * @return null|string
     */
    public function getOauthAccessToken()
    {
        return null;
    }
}
