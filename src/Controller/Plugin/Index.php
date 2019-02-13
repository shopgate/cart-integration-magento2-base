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

namespace Shopgate\Base\Controller\Plugin;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Module\ResourceInterface;
use Shopgate\Base\Model\Forwarder;
use Shopgate\Base\Model\Utility\Registry;

/**
 * Merchant API to Magento2 call router
 */
class Index extends Action
{
    /** @var Forwarder */
    private $forwarder;
    /** @var ResourceInterface */
    private $versionInfo;
    /** @var Registry */
    private $registry;

    /**
     * @param Context           $context
     * @param Forwarder         $forwarder
     * @param ResourceInterface $versionInfo
     * @param Registry          $registry
     */
    public function __construct(
        Context $context,
        Forwarder $forwarder,
        ResourceInterface $versionInfo,
        Registry $registry
    ) {
        $this->forwarder   = $forwarder;
        $this->versionInfo = $versionInfo;
        $this->registry    = $registry;

        // CsrfAwareAction Magento2.3 compatibility
        if (interface_exists('\Magento\Framework\App\CsrfAwareActionInterface')) {
            $request = $context->getRequest();
            if ($request instanceof Http && $request->isPost()) {
                $request->setParam('isAjax', true);
            }
        }
        parent::__construct($context);
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        define('SHOPGATE_PLUGIN_VERSION', $this->versionInfo->getDbVersion('Shopgate_Base'));
        $this->registry->flagApi();
        $this->registry->setAction($this->getRequest()->getParam('action'));

        try {
            $this->forwarder->handleRequest($this->getRequest()->getParams());
        } catch (\ShopgateLibraryException $e) {
            $response = new \ShopgatePluginApiResponseAppJson(
                $this->getRequest()->getParam('trace_id')
            );
            $response->markError($e->getCode(), $e->getMessage());
            $response->setData([]);
            $response->send();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}

