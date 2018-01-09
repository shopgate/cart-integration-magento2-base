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

namespace Shopgate\Base\Block\Adminhtml\Form\Field;

use Magento\Cms\Model\ResourceModel\Page\Collection as PageCollection;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;

class CmsMap extends Select
{
    /** @var StoreManagerInterface */
    private $storeManager;
    /** @var PageCollection */
    private $cmsPageCollection;

    /**
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     * @param PageCollection        $cmsPageCollection
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        PageCollection $cmsPageCollection,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager      = $storeManager;
        $this->cmsPageCollection = $cmsPageCollection;
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     *
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setData('name', $value);
    }

    /**
     * Retrieves all the pages that are allowed to be
     * viewed in the current context
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @codingStandardsIgnoreStart
     */
    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            $cmsPages = $this->cmsPageCollection->addStoreFilter($this->getStoreFromContext());
            foreach ($cmsPages as $cmsPage) {
                /** @var \Magento\Cms\Model\Page $cmsPage */
                $this->addOption($cmsPage->getId(), $cmsPage->getTitle());
            }
        }

        return parent::_toHtml();
    }
    //@codingStandardsIgnoreEnd

    /**
     * Retrieves the stores that are allowed in the context
     * E.g. Store X will just return itself
     * E.g. Website Y will return an array of all stores under it
     * E.g. Default will return all store ids
     *
     * @return array - e.g. [1] or [1,3]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStoreFromContext()
    {
        $params = $this->getRequest()->getParams();

        if (isset($params['store'])) {
            return [$params['store']];
        } elseif (isset($params['website'])) {
            /** @var Website $website */
            $website = $this->storeManager->getWebsite($params['website']);

            return $website->getStoreIds();
        }

        return array_keys($this->storeManager->getStores());
    }
}
