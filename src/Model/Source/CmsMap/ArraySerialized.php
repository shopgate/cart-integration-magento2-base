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

namespace Shopgate\Base\Model\Source\CmsMap;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized as MageArraySerialized;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Serialize;
use Shopgate\Base\Model\Source\CmsMap;
use Shopgate\Base\Model\Storage\Cache;

class ArraySerialized extends MageArraySerialized
{
    /** @var Serialize */
    private $serialize;
    /** @var array */
    private $list;
    /** @var ManagerInterface */
    private $messageManager;
    /** @var Cache */
    private $sgCache;

    /**
     * Sets our Shopgate config validator
     *
     * @inheritdoc
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ManagerInterface $messageManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        Cache $sgCache,
        Serialize $serialize,
        array $data = []
    ) {
        $this->messageManager = $messageManager;
        $this->sgCache        = $sgCache;
        $this->serialize      = $serialize;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Converts pre v2.2.0 serialized database data to display properly
     *
     * @inheritdoc
     */
    public function _afterLoad()
    {
        $value        = $this->getValue();
        $isSerialized = $this->isSerialized($value);
        if ($isSerialized) {
            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $this->setValue(empty($value) ? false : $this->serialize->unserialize($value));
        }
        parent::_afterLoad();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        /** @var array $oldValues */
        $oldValues = $this->getValue();
        $newValues = $this->removeDuplicates($oldValues);

        if (count($oldValues) !== count($newValues)) {
            /** @noinspection PhpParamsInspection */
            $this->setValue($newValues);
            $this->messageManager->addWarningMessage(
                __('Same page was assigned a URL Key multiple times! Duplicate entries were removed.')
            );
        }

        return parent::beforeSave();
    }

    /**
     * Invalidate our cache to make sure the user
     * understands why the CMS mapping might not
     * be updated in the redirect script
     *
     * @inheritdoc
     */
    public function afterSave()
    {
        $this->sgCache->invalidate();

        return parent::afterSave();
    }

    /**
     * Searches for duplicate CMS pages before saving
     *
     * @param array $values
     *
     * @return array
     */
    public function removeDuplicates($values)
    {
        foreach ($values as $key => $option) {
            $value = $this->getCmsPageValue($option);
            if ($value) {
                if ($this->listHasValue($value)) {
                    unset($values[$key]);
                } else {
                    $this->setListValue($value);
                }
            }
        }

        return $values;
    }

    /**
     * @param string | array $option
     *
     * @return bool
     */
    private function getCmsPageValue($option)
    {
        if ($this->isOptionCmsPage($option)) {
            return $option[CmsMap::INPUT_ID_CMS_PAGE];
        }

        return false;
    }

    /**
     * @param string | array $option
     *
     * @return bool
     */
    private function isOptionCmsPage($option)
    {
        return is_array($option) && !empty($option[CmsMap::INPUT_ID_CMS_PAGE]);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function listHasValue($value)
    {
        return isset($this->list[$value]);
    }

    /**
     * Creates a list map of page keys to figure
     * out if there are duplicates in it
     *
     * @param string $value
     */
    private function setListValue($value)
    {
        $this->list[$value] = 1;
    }

    /**
     * Check if value is serialized string
     *
     * @param string $value
     *
     * @return boolean
     */
    private function isSerialized($value)
    {
        return (boolean) preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
    }
}
