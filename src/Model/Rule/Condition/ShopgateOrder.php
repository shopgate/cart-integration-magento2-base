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

namespace Shopgate\Base\Model\Rule\Condition;

use Magento\Rule\Model\Condition\AbstractCondition;
use ShopgateClient;
use Shopgate\Base\Model\Utility\Registry;

class ShopgateOrder extends AbstractCondition
{
    const CLIENT_ATTRIBUTE     = 'shopgate_client';
    const IS_SHOPGATE_ORDER    = 'is_shopgate_order';
    const APP_CLIENTS          = [
        ShopgateClient::TYPE_IPHONEAPP,
        ShopgateClient::TYPE_IPADAPP,
        ShopgateClient::TYPE_ANDROIDPHONEAPP,
        ShopgateClient::TYPE_ANDROIDTABLETAPP,
    ];

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $sourceYesno;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderFactory;

    /**
     * @var Registry
     */
    private $registry;


    /**
     * @param \Magento\Rule\Model\Condition\Context                      $context
     * @param \Magento\Config\Model\Config\Source\Yesno                  $sourceYesno
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderFactory
     * @param Registry                                                   $registry
     * @param array                                                      $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Config\Model\Config\Source\Yesno $sourceYesno,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderFactory,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sourceYesno  = $sourceYesno;
        $this->orderFactory = $orderFactory;
        $this->registry     = $registry;
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(
            [
                self::IS_SHOPGATE_ORDER => __('Shopgate Mobile App'),
            ]
        );

        return $this;
    }

    /**
     * Get input type
     *
     * @return string
     */
    public function getInputType()
    {
        return 'select';
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Get value select options
     *
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $this->setData(
                'value_select_options',
                $this->sourceYesno->toOptionArray()
            );
        }

        return $this->getData('value_select_options');
    }

    /**
     * Validate App Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     *
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $isShopgateOrder = $model->hasData(self::CLIENT_ATTRIBUTE)
            ? in_array($model->getData(self::CLIENT_ATTRIBUTE), self::APP_CLIENTS)
            : $this->registry->isApi();

        // TODO add validation for web checkout
        $model->setData(self::IS_SHOPGATE_ORDER, (int)$isShopgateOrder);

        return parent::validate($model);
    }
}
