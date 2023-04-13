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

declare(strict_types=1);

namespace Shopgate\Base\Model\Source\GenderMap;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized as MageArraySerialized;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Shopgate\Base\Block\Adminhtml\Form\Field\GenderMap;

class ArraySerialized extends MageArraySerialized
{
    /** @var ManagerInterface */
    private $messageManager;
    /** @var string|null */
    private $mainColumnKey;

    /**
     * @inheritdoc
     *
     * @param ManagerInterface $messageManager - manages errors/warnings displayed to the user
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        ManagerInterface $messageManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->messageManager = $messageManager;
        $this->mainColumnKey  = GenderMap::INPUT_ID_GENDER_SHOPGATE;
    }

    /**
     * Removes duplicate entries with same key values
     *
     * @inheritdoc
     */
    public function beforeSave(): MageArraySerialized
    {
        $oldValues = $this->getValue();
        if (is_array($oldValues)) {
            $newValues = $this->removeDuplicates($oldValues);
            if (count($oldValues) !== count($newValues)) {
                /** @noinspection PhpParamsInspection */
                /** @noinspection PhpStrictTypeCheckingInspection */
                $this->setValue($newValues);
                $this->messageManager->addWarningMessage(__('Duplicate entries were removed.'));
            }
        }

        return parent::beforeSave();
    }

    /**
     * Searches for duplicate column values before saving
     *
     * @param array $values
     *
     * @return array
     */
    private function removeDuplicates($values): array
    {
        $list = [];
        foreach ($values as $key => $option) {
            $value = $this->getInputId($option);
            if ($value) {
                if (isset($list[$value])) {
                    unset($values[$key]);
                } else {
                    $list[$value] = 1;
                }
            }
        }

        return $values;
    }

    /**
     * @param string|array $option
     *
     * @return false|string
     */
    private function getInputId($option)
    {
        if ($this->isCorrectOptionColumn($option)) {
            return $option[$this->mainColumnKey];
        }

        return false;
    }

    /**
     * @param string|array $option
     *
     * @return bool
     */
    private function isCorrectOptionColumn($option): bool
    {
        return is_array($option) && !empty($option[$this->mainColumnKey]);
    }
}
