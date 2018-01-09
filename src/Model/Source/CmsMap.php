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

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Shopgate\Base\Block\Adminhtml\Form\Field\CmsMap as CmsMapField;

class CmsMap extends AbstractFieldArray
{
    const INPUT_ID_CMS_PAGE = 'cms_page';
    const INPUT_ID_URL_KEY  = 'url_key';

    /**
     * @inheritdoc
     * @codingStandardsIgnoreStart
     */
    protected $_columns = [];
    /** @inheritdoc */
    protected $_addAfter = true;
    /** @inheritdoc */
    protected $_addButtonLabel;
    /** @var CmsMapField */
    protected $cmsPageRenderer;

    /**
     * @inheritdoc
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName === self::INPUT_ID_URL_KEY) {
            $this->_columns[$columnName]['class'] = 'input-text required-entry validate-identifier';
            $this->_columns[$columnName]['style'] = 'width:250px';
        }

        return parent::renderCellTemplate($columnName);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $options = [];
        $cmsPage = $row->getData('cms_page');
        if ($cmsPage) {
            $options['option_' . $this->getCmsPageRenderer()->calcOptionHash($cmsPage)] =
                'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }

    /**
     * Returns a rendered for cms page mapping
     *
     * @codingStandardsIgnoreEnd
     * @return CmsMapField
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCmsPageRenderer()
    {
        if (!$this->cmsPageRenderer) {
            $this->cmsPageRenderer = $this->getLayout()->createBlock(
                'Shopgate\Base\Block\Adminhtml\Form\Field\CmsMap',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->cmsPageRenderer;
    }
    /** @codingStandardsIgnoreEnd */

    /**
     * @inheritdoc
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            self::INPUT_ID_CMS_PAGE,
            [
                'label'    => __('Page'),
                'renderer' => $this->getCmsPageRenderer(),
            ]
        );
        $this->addColumn(self::INPUT_ID_URL_KEY, ['label' => __('URL Key')]);
        $this->_addAfter = false;
    }
}
