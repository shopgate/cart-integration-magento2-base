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

namespace Shopgate\Base\Helper\Quote;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\CouponManagement;
use Magento\Quote\Model\Quote;
use Shopgate\Base\Model\Shopgate\Extended\Base;
use Shopgate\Base\Model\Shopgate\Extended\ExternalCoupon;
use Shopgate\Base\Model\Utility\SgLoggerInterface;
use ShopgateLibraryException;

class Coupon
{
    /** @var Quote */
    private $quote;
    /** @var Base */
    private $cart;
    /** @var SgLoggerInterface */
    private $log;
    /** @var CouponManagement */
    private $couponManagement;
    /** @var int - counts the amount of valid coupons in cart */
    private $validCoupons = 0;

    /**
     * @param Quote             $quote
     * @param Base              $cart
     * @param SgLoggerInterface $log
     * @param CouponManagement  $couponManagement
     */
    public function __construct(
        Quote $quote,
        Base $cart,
        SgLoggerInterface $log,
        CouponManagement $couponManagement
    ) {
        $this->quote            = $quote;
        $this->cart             = $cart;
        $this->log              = $log;
        $this->couponManagement = $couponManagement;
    }

    /**
     * Sets coupon to quote
     *
     * @return Quote
     */
    public function setCoupon()
    {
        foreach ($this->cart->getExternalCoupons() as $coupon) {
            $this->setCouponToQuote($coupon);
        }

        return $this->quote;
    }

    /**
     * @param ExternalCoupon $coupon
     */
    public function setCouponToQuote(ExternalCoupon $coupon)
    {
        $couponId = $this->quote->getEntityId();
        try {
            if ($this->couponManagement->set($couponId, $coupon->getCode()) && $this->validCoupons > 0) {
                throw new ShopgateLibraryException(ShopgateLibraryException::COUPON_TOO_MANY_COUPONS);
            }
            $this->quote->loadActive($couponId);
            $this->validCoupons++;
        } catch (ShopgateLibraryException $e) {
            $coupon->setErrorByCode($e->getCode());
        } catch (NoSuchEntityException $e) {
            $this->log->debug($e->getMessage());
            $coupon->setErrorByCode(ShopgateLibraryException::COUPON_NOT_VALID);
        } catch (CouldNotSaveException $e) {
            $coupon->setErrorByCode(ShopgateLibraryException::COUPON_NOT_VALID);
        } catch (\Exception $e) {
            $this->log->debug($e->getMessage());
            $coupon->setErrorByCode(ShopgateLibraryException::UNKNOWN_ERROR_CODE);
        }
    }
}
