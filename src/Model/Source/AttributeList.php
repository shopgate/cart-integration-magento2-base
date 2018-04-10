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

namespace Shopgate\Base\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\Option\ArrayInterface;
use Shopgate\Base\Helper\Serializer;
use Shopgate\Base\Model\Storage\Session;
use Shopgate\Base\Model\Utility\SgLoggerInterface;

class AttributeList implements ArrayInterface
{
    /** Cache key to use to save the list */
    const CACHE_KEY = 'attribute_list';

    /** @var AttributeFactory */
    private $attributeFactory;
    /** @var Session */
    private $session;
    /** @var Serializer */
    private $serializer;
    /** @var SgLoggerInterface */
    private $sgLogger;

    /**
     * @param AttributeFactory  $attributeFactory
     * @param Session           $session
     * @param Serializer        $serializer
     * @param SgLoggerInterface $sgLogger
     */
    public function __construct(
        AttributeFactory $attributeFactory,
        Session $session,
        Serializer $serializer,
        SgLoggerInterface $sgLogger
    ) {
        $this->attributeFactory = $attributeFactory;
        $this->session          = $session;
        $this->serializer       = $serializer;
        $this->sgLogger         = $sgLogger;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $attributes = $this->session->getData(self::CACHE_KEY);
        if ($attributes) {
            try {
                $result = $this->serializer->decode($attributes);
            } catch (\InvalidArgumentException $exception) {
                $this->sgLogger->error($exception->getMessage());
                $result[] = ['value' => 0, 'label' => 'ERROR: please flush cache storage'];
            }

            return $result;
        }

        $list = $this->getAttributeList();
        $this->session->setData(self::CACHE_KEY, $this->serializer->encode($list));

        return $list;
    }

    /**
     * @return array - array(array('value' => 'Attribute Code', 'label' => 'Name of Attribute'), ...)
     */
    private function getAttributeList()
    {
        $collection = $this->attributeFactory->create()->getCollection();
        $collection->addFieldToFilter(Set::KEY_ENTITY_TYPE_ID, 4);
        $collection->getSelect()->where('frontend_label <> ?', null);
        $attributes = $collection->load()->getItems();

        $list[] = ['value' => 0, 'label' => 'Please Select'];
        foreach ($attributes as $attribute) {
            /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
            $list[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getDefaultFrontendLabel()
            ];
        }

        return $list;
    }
}
