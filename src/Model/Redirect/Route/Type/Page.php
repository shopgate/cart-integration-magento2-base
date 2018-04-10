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
use Magento\Cms\Model\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Shopgate\Base\Api\Config\CoreInterface;
use Shopgate\Base\Api\Config\SgCoreInterface;
use Shopgate\Base\Helper\Encoder;
use Shopgate\Base\Model\Redirect\Route\Tags\Generic as GenericTags;
use Shopgate\Base\Model\Source\CmsMap;
use Shopgate\Base\Model\Utility\SgLoggerInterface;

class Page extends Generic
{
    const CONTROLLER_KEY = 'page';

    /** @var PageFactory */
    private $pageFactory;
    /** @var CoreInterface */
    private $config;
    /** @var Encoder */
    private $encoder;
    /** @var SgLoggerInterface */
    private $sgLogger;

    /**
     * @inheritdoc
     *
     * @param PageFactory   $pageFactory
     * @param CoreInterface $config
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        PageFactory $pageFactory,
        GenericTags $tags,
        CoreInterface $config,
        Encoder $encoder,
        SgLoggerInterface $sgLogger
    ) {
        parent::__construct($context, $storeManager, $tags);
        $this->pageFactory = $pageFactory;
        $this->config      = $config;
        $this->encoder     = $encoder;
        $this->sgLogger    = $sgLogger;
    }

    /**
     * @inheritdoc
     */
    public function callScriptBuilder(\Shopgate_Helper_Redirect_Type_TypeInterface $redirect)
    {
        try {
            $key = $this->getUrlKey();
        } catch (\Exception $exception) {
            $this->sgLogger->error($exception->getMessage());
            $key = $this->getCurrentPage()->getIdentifier();
        }

        return $redirect->loadCms($key);
    }

    /**
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getUrlKey()
    {
        $pageId       = $this->getSpecialId();
        $cmsMapConfig = $this->config->getConfigByPath(SgCoreInterface::PATH_CMS_MAP)->getValue();

        if (!empty($cmsMapConfig)) {
            $cmsMap = (array) $this->encoder->decode($cmsMapConfig);

            foreach ($cmsMap as $map) {
                if ($map[CmsMap::INPUT_ID_CMS_PAGE] === $pageId) {
                    return $map[CmsMap::INPUT_ID_URL_KEY];
                }
            }
        }

        return $this->getCurrentPage()->getIdentifier();
    }

    /**
     * Returns the ID of the page
     *
     * @inheritdoc
     */
    protected function getSpecialId()
    {
        return $this->context->getRequest()->getParam('page_id');
    }

    /**
     * @return \Magento\Cms\Model\Page
     */
    private function getCurrentPage()
    {
        return $this->pageFactory->create()->load($this->getSpecialId());
    }

    /**
     * Returns the page name
     *
     * @inheritdoc
     */
    protected function getTitle()
    {
        return $this->getCurrentPage()->getTitle();
    }
}
