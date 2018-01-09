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

namespace Shopgate\Base\Model\Redirect\Route\Type;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreManagerInterface;
use Shopgate\Base\Model\Redirect\Route\Tags\Product as ProductTags;

class Product extends Generic
{
    const CONTROLLER_KEY = 'product';
    /** @var ProductTags */
    protected $tags;
    /** @var ProductFactory */
    private $productFactory;

    /**
     * @inheritdoc
     *
     * @param ProductFactory $productFactory
     * @param ProductTags    $tags
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ProductFactory $productFactory,
        ProductTags $tags
    ) {
        parent::__construct($context, $storeManager, $tags);
        $this->productFactory = $productFactory;
    }

    /**
     * @inheritdoc
     */
    public function callScriptBuilder(\Shopgate_Helper_Redirect_Type_TypeInterface $redirect)
    {
        return $redirect->loadProduct($this->getSpecialId());
    }

    /**
     * Returns the ID of the page
     *
     * @inheritdoc
     */
    protected function getSpecialId()
    {
        return $this->context->getRequest()->getParam('id');
    }

    /**
     * Returns the page name
     *
     * @inheritdoc
     */
    protected function getTitle()
    {
        $product = $this->productFactory->create()->load($this->getSpecialId());
        $title   = $product->getName() ? : parent::getTitle();

        return $title;
    }
}
