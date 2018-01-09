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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\TestFramework\Helper\Bootstrap;

class ConfigManager
{
    /**
     * Holds config values to cleanup
     *
     * @var array
     */
    protected $cleanup = [];

    /**
     * Retrieve configuration node value
     *
     * @param string      $configPath
     * @param string      $scopeType
     * @param string|null $scopeCode
     *
     * @return string
     */
    public function getConfigValue(
        $configPath,
        $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ) {
        $objectManager = Bootstrap::getObjectManager();
        $result        = null;
        if ($scopeCode !== false) {
            /** @var ScopeConfigInterface $scopeConfig */
            $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
            $result      = $scopeConfig->getValue(
                $configPath,
                $scopeType,
                $scopeCode
            );
        }

        return $result;
    }

    /**
     * Assign configuration node value
     *
     * @param string      $configPath
     * @param string      $value
     * @param string      $scopeType
     * @param string|null $scopeId
     */
    public function setConfigValue(
        $configPath,
        $value,
        $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeId = null
    ) {
        $objectManager = Bootstrap::getObjectManager();
        /** @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface $scopeConfig */
        $scopeConfig = $objectManager->get(
            '\Magento\Framework\App\Config\ConfigResource\ConfigInterface'
        );
        if (empty($scopeId)) {
            $scopeId = 0;
            if (strpos($configPath, 'default/') === 0) {
                $configPath = substr($configPath, 8);
            }
            $scopeConfig->saveConfig(
                $configPath,
                $value,
                $scopeType,
                $scopeId
            );
        } else {
            $scopeConfig->saveConfig(
                $configPath,
                $value,
                $scopeType,
                $scopeId
            );
        }

        $this->cleanup[] = [$configPath, $scopeType, $scopeId];
    }

    /**
     * Cleans up all the deleted config params
     */
    public function removeConfigs()
    {
        /** @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configResource */
        $configResource = Bootstrap::getObjectManager()->get(
            '\Magento\Framework\App\Config\ConfigResource\ConfigInterface'
        );
        foreach ($this->cleanup as list($path, $scopeType, $scopeId)) {
            $configResource->deleteConfig($path, $scopeType, $scopeId);
        }
    }
}
