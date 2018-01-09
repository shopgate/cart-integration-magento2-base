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

namespace Shopgate\Base\Tests;

class Bootstrap extends \Magento\TestFramework\Helper\Bootstrap
{
    /** @var array - holds loaded Shopgate DI preferences */
    protected static $loaded = [];
    /** @var bool */
    protected static $libraryLoaded = false;

    /**
     * Object manager rewrite to include our DI's & Library
     *
     * @return \Magento\Framework\ObjectManagerInterface | \Magento\TestFramework\ObjectManager
     */
    public static function getObjectManager()
    {
        $objManager = parent::getObjectManager();

        self::initializeDi($objManager, 'Shopgate_Base');
        self::initializeDi($objManager, 'Shopgate_Export');
        //self::initializeDi($objManager, 'Shopgate_Import'); //todo-sg: need a way to pass it on per module basis

        return $objManager;
    }

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objManager
     * @param  string                                   $pluginName
     */
    public static function initializeDi($objManager, $pluginName)
    {
        if (!in_array($pluginName, self::$loaded)) {
            $reader      = $objManager->get('Magento\Framework\Module\Dir\Reader');
            $di          = $reader->getModuleDir('etc', $pluginName) . '/di.xml';
            $parser      = $objManager->get('Magento\Framework\Xml\Parser');
            $xml         = $parser->load($di)->xmlToArray();
            $preferences = [];

            foreach ($xml['config']['_value']['preference'] as $preference) {
                $preferences[$preference['_attribute']['for']] = $preference['_attribute']['type'];
            }
            $objManager->configure(['preferences' => $preferences]);
            self::$loaded[] = $pluginName;
        }
    }
}
