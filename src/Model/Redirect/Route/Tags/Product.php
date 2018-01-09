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

namespace Shopgate\Base\Model\Redirect\Route\Tags;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product as MagentoProduct;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Model\Config as TaxConfig;
use Magento\Tax\Model\TaxCalculation;
use Shopgate\Base\Api\Config\CoreInterface;
use Shopgate\Base\Api\ExportInterface;
use Shopgate\Base\Model\Utility\SgLoggerInterface;
use Shopgate_Helper_Redirect_TagsGenerator as TagGenerator;

class Product extends Generic
{
    /** @var StoreManagerInterface */
    protected $storeManager;
    /** @var TaxConfig */
    protected $taxConfig;
    /** @var TaxConfig */
    protected $taxCalculation;
    /** @var SgLoggerInterface */
    private $logger;
    /** @var Registry */
    private $registry;

    /**
     * @inheritdoc
     *
     * @param TaxConfig         $taxConfig
     * @param TaxCalculation    $taxCalculation
     * @param SgLoggerInterface $logger
     * @param Registry          $registry
     */
    public function __construct(
        CoreInterface $config,
        StoreManagerInterface $storeManager,
        TaxConfig $taxConfig,
        TaxCalculation $taxCalculation,
        SgLoggerInterface $logger,
        Registry $registry
    ) {
        parent::__construct($config, $storeManager);
        $this->taxConfig      = $taxConfig;
        $this->taxCalculation = $taxCalculation;
        $this->logger         = $logger;
        $this->registry       = $registry;
    }

    /**
     * Generates page specific tags + generic tags
     *
     * @param string $pageTitle
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generate($pageTitle)
    {
        $tags    = parent::generate($pageTitle);
        $product = $this->registry->registry('current_product');

        if (!$product instanceof MagentoProduct) {
            $this->logger->error('Could not retrieve mage product from registry');

            return $tags;
        }
        /** @var \Magento\Store\Model\Store $store */
        $store           = $this->storeManager->getStore();
        $image           = $product->getMediaGalleryImages()->getFirstItem();
        $imageUrl        = is_object($image) ? $image->getData('url') : '';
        $name            = $product->getName();
        $description     = $product->getDescription();
        $availableText   = $product->isInStock() ? 'instock' : 'oos';
        $categoryName    = $this->getCategoryName();
        $defaultCurrency = $store->getCurrentCurrency()->getCode();

        $eanIdentifier = $this->config->getConfigByPath(ExportInterface::PATH_PROD_EAN_CODE)->getValue();
        $ean           = (string) $product->getData($eanIdentifier);

        $taxClassId   = $product->getTaxClassId();
        $storeId      = $store->getId();
        $taxRate      = $this->taxCalculation->getDefaultCalculatedRate($taxClassId, null, $storeId);
        $priceIsGross = $this->taxConfig->priceIncludesTax($this->storeManager->getStore());
        $price        = $product->getFinalPrice();
        $priceNet     = $priceIsGross ? round($price / (1 + $taxRate), 2) : round($price, 2);
        $priceGross   = !$priceIsGross ? round($price * (1 + $taxRate), 2) : round($price, 2);

        $productTags = [
            TagGenerator::SITE_PARAMETER_PRODUCT_IMAGE             => $imageUrl,
            TagGenerator::SITE_PARAMETER_PRODUCT_NAME              => $name,
            TagGenerator::SITE_PARAMETER_PRODUCT_DESCRIPTION_SHORT => $description,
            TagGenerator::SITE_PARAMETER_PRODUCT_EAN               => $ean,
            TagGenerator::SITE_PARAMETER_PRODUCT_AVAILABILITY      => $availableText,
            TagGenerator::SITE_PARAMETER_PRODUCT_CATEGORY          => $categoryName,
            TagGenerator::SITE_PARAMETER_PRODUCT_PRICE             => $priceGross,
            TagGenerator::SITE_PARAMETER_PRODUCT_PRETAX_PRICE      => $priceNet
        ];

        if ($priceGross || $priceNet) {
            $productTags[TagGenerator::SITE_PARAMETER_PRODUCT_CURRENCY]        = $defaultCurrency;
            $productTags[TagGenerator::SITE_PARAMETER_PRODUCT_PRETAX_CURRENCY] = $defaultCurrency;
        }
        $tags = array_merge($tags, $productTags);

        return $tags;
    }

    /**
     * Helps pulling category name from registry
     *
     * @return string
     */
    public function getCategoryName()
    {
        $category = $this->registry->registry('current_category');

        return ($category instanceof Category) ? $category->getName() : '';
    }
}
