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
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\StoreManagerInterface;
use Shopgate\Base\Model\Redirect\Route\Tags\Generic as GenericTags;
use Shopgate\Base\Model\Redirect\Route\TypeInterface;

class Generic implements TypeInterface
{
    const CONTROLLER_KEY = 'default';

    /** @var Context */
    protected $context;
    /** @var StoreManagerInterface */
    protected $storeManager;
    /** @var GenericTags */
    protected $tags;

    /**
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     * @param GenericTags           $tags
     */
    public function __construct(Context $context, StoreManagerInterface $storeManager, GenericTags $tags)
    {
        $this->context      = $context;
        $this->storeManager = $storeManager;
        $this->tags         = $tags;
    }

    /**
     * @inheritdoc
     */
    public function getCacheKey()
    {
        /** @var Http $request */
        $request = $this->context->getRequest();
        $storeId = $this->storeManager->getStore()->getId();

        return $storeId . '_' . $request->getRouteName() . '_' . $this::CONTROLLER_KEY . '_' . $this->getSpecialId();
    }

    /**
     * Returns the ID of the page, by default
     * there is no ID
     *
     * @return string
     */
    protected function getSpecialId()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function callScriptBuilder(\Shopgate_Helper_Redirect_Type_TypeInterface $redirect)
    {
        return $redirect->loadDefault();
    }

    /**
     * @inheritdoc
     */
    public function getTags()
    {
        return $this->tags->generate($this->getTitle());
    }

    /**
     * @inheritdoc
     */
    protected function getTitle()
    {
        return $this->storeManager->getWebsite()->getName();
    }
}
