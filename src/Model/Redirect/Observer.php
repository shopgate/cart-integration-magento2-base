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

use Magento\Framework\Event\ObserverInterface;
use Shopgate\Base\Helper\Redirect as RedirectHelper;
use Shopgate\Base\Model\Redirect\Type;
use Shopgate\Base\Model\Storage\Session;

class Observer implements ObserverInterface
{

    /** @var Type\Js */
    protected $jsRedirect;
    /** @var Type\Http */
    protected $httpRedirect;
    /** @var RedirectHelper */
    private $redirect;
    /** @var Session */
    private $session;

    /**
     * @param Type\Js        $js
     * @param Type\Http      $http
     * @param RedirectHelper $redirect
     * @param Session        $session
     */
    public function __construct(
        Type\Js $js,
        Type\Http $http,
        RedirectHelper $redirect,
        Session $session
    ) {
        $this->jsRedirect   = $js;
        $this->httpRedirect = $http;
        $this->redirect     = $redirect;
        $this->session      = $session;
    }

    /**
     * Runs the redirect list
     *
     * @inheritdoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->redirect->isAllowed()) {
            $this->session->unsetScript();

            return;
        }

        foreach ($this->getRedirects() as $redirect) {
            $redirect->run();
        }
    }

    /**
     * By default will run HTTP redirect with JS fallback if HTTP
     * does not work. If JS is set as the main redirect, it will
     * just run the JS redirect
     *
     * @return Type\TypeInterface[]
     */
    private function getRedirects()
    {
        $httpWithJsBackup = [
            $this->httpRedirect,
            $this->jsRedirect
        ];

        return $this->redirect->isTypeJavaScript() ? [$this->jsRedirect] : $httpWithJsBackup;
    }
}
