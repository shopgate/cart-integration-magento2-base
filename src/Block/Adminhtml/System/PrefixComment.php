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

namespace Shopgate\Base\Block\Adminhtml\System;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Config\Model\Config\CommentInterface;

class PrefixComment extends AbstractBlock implements CommentInterface
{
    public function getCommentText($elementValue): string
    {
        $url = sprintf(
            '<a href="%s" target="_blank">%s / %s</a>',
            $this->_urlBuilder->getUrl('adminhtml/system_config/edit/section/customer'),
            __('Name and Address Options'),
            __('Prefix Dropdown Options')
        );

        return sprintf(
            __('Note: only one entry per prefix allowed.<br/>Prefixes must be configured first at:<br/>%s'),
            $url
        );
    }
}
