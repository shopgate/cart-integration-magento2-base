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

namespace Shopgate\Base\Model\Redirect;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\Header;
use Shopgate\Base\Model\Config as MainConfig;
use Shopgate\Base\Model\Utility\Registry;
use Shopgate\Base\Model\Utility\SgLoggerInterface;
use Shopgate_Helper_Redirect_Type_Http;
use Shopgate_Helper_Redirect_Type_Js;
use ShopgateBuilder;

/**
 * Class helps initialize the Shopgate builder
 * and assign tags to it
 */
class Builder
{

    /** @var MainConfig */
    private $config;
    /** @var Shopgate_Helper_Redirect_Type_Http */
    private $http;
    /** @var Shopgate_Helper_Redirect_Type_Js */
    private $js;
    /** @var Header */
    private $header;
    /** @var SgLoggerInterface */
    private $logger;
    /** @var RequestInterface */
    private $request;
    /** @var Registry */
    private $registry;

    /**
     * @param MainConfig        $config
     * @param Header            $header
     * @param SgLoggerInterface $logger
     * @param RequestInterface  $request
     * @param Registry          $registry
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        MainConfig $config,
        Header $header,
        SgLoggerInterface $logger,
        RequestInterface $request,
        Registry $registry
    ) {
        $this->config   = $config;
        $this->header   = $header;
        $this->logger   = $logger;
        $this->request  = $request;
        $this->registry = $registry;
    }

    /**
     * Instantiates the HTTP redirect object
     *
     * @return Shopgate_Helper_Redirect_Type_Http | null
     */
    public function buildHttpRedirect()
    {
        if (!is_null($this->http)) {
            return $this->http;
        }

        $builder   = new ShopgateBuilder($this->initConfig());
        $userAgent = $this->header->getHttpUserAgent();

        try {
            $this->http = $builder->buildHttpRedirect($userAgent, $this->request->getParams(), $_COOKIE);
        } catch (\ShopgateMerchantApiException $e) {
            $this->logger->error('HTTP > oAuth access token for store not set: ' . $e->getMessage());

            return null;
        } catch (\Exception $e) {
            $this->logger->error('HTTP > error in HTTP redirect: ' . $e->getMessage());

            return null;
        }

        return $this->http;
    }

    /**
     * @return MainConfig
     */
    private function initConfig()
    {
        $this->registry->flagRedirect();
        $configs = $this->config->toArray();
        $this->config->loadArray($configs);
        $this->config->loadConfig();

        return $this->config;
    }

    /**
     * Instantiates the JS script builder object
     *
     * @return Shopgate_Helper_Redirect_Type_Js | null
     */
    public function buildJsRedirect()
    {
        if (!is_null($this->js)) {
            return $this->js;
        }

        $builder = new ShopgateBuilder($this->initConfig());

        try {
            $this->js = $builder->buildJsRedirect($this->request->getParams(), $_COOKIE);
        } catch (\ShopgateMerchantApiException $e) {
            $this->logger->error('JS > oAuth access token for store not set: ' . $e->getMessage());

            return null;
        } catch (\Exception $e) {
            $this->logger->error('JS > error in HTTP redirect: ' . $e->getMessage());

            return null;
        }

        return $this->js;
    }
}
