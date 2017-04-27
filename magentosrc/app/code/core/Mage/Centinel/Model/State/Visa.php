<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Centinel
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Abstract Validation State Model for Visa
 */
class Mage_Centinel_Model_State_Visa extends Mage_Centinel_Model_StateAbstract
{
    /**
     * Analyse lookup`s results. If it has require params for authenticate, return true
     *
     * @return bool
     */
    public function isAuthenticateAllowed()
    {
        return $this->_isLookupStrictSuccessful() && is_null($this->getAuthenticateEciFlag());
    }

    /**
     * Analyse authenticate`s results. If authenticate is successful return true and false if it failure
     * Result depends from flag self::getIsModeStrict()
     *
     * @return bool
     */
    public function isAuthenticateSuccessful()
    {
        $paResStatus = $this->getAuthenticatePaResStatus();
        $eciFlag = $this->getAuthenticateEciFlag();
        $xid = $this->getAuthenticateXid();
        $cavv = $this->getAuthenticateCavv();
        $errorNo = $this->getAuthenticateErrorNo();
        $signatureVerification = $this->getAuthenticateSignatureVerification();

        //Test cases 1-5, 11
        if ($this->_isLookupStrictSuccessful()) {

            if ($paResStatus == 'Y' && $eciFlag == '05' && $xid != '' && $cavv != '' && $errorNo == '0') {
                //Test case 1
                if ($signatureVerification == 'Y') {
                    return true;
                }
                //Test case 2
                if ($signatureVerification == 'N') {
                    return false;
                }
            }

            //Test case 3
            if ($paResStatus == 'N' && $signatureVerification == 'Y' && $eciFlag == '07' &&
                $xid != '' && $cavv == '' && $errorNo == '0') {
                return false;
            }

            //Test case 4
            if ($paResStatus == 'A' && $signatureVerification == 'Y' && $eciFlag == '06' &&
                $xid != '' && $cavv != '' && $errorNo == '0') {
                if ($this->getIsModeStrict()) {
                    return false;
                } else {
                    return true;
                }
            }

            //Test case 5
            if ($paResStatus == 'U' && $signatureVerification == 'Y' && $eciFlag == '07' &&
                $xid != '' && $cavv == '' && $errorNo == '0') {
                if ($this->getIsModeStrict()) {
                    return false;
                } else {
                    return true;
                }
            }

            //Test case 11
            if ($paResStatus == 'U' && $signatureVerification == '' && $eciFlag == '07' &&
                $xid == '' && $cavv == '' && $errorNo == '1050') {
                if ($this->getIsModeStrict()) {
                    return false;
                } else {
                    return true;
                }
            }

        }

        //Test cases 6-10
        if (!$this->getIsModeStrict() && $this->_isLookupSoftSuccessful()) {
            if ($paResStatus == '' && $signatureVerification == '' && $eciFlag == '' &&
                $xid == '' && $cavv == '' && $errorNo == '0') {
                return true;
            } elseif ($paResStatus == false && $signatureVerification == false && $eciFlag == false &&
                $xid == false && $cavv == false && $errorNo == false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Analyse lookup`s results. If lookup is strict successful return true
     *
     * @return bool
     */
    protected function _isLookupStrictSuccessful()
    {
        //Test cases 1-5, 11
        if ($this->getLookupEnrolled() == 'Y' &&
            $this->getLookupAcsUrl() != '' &&
            $this->getLookupPayload() != '' &&
            $this->getLookupErrorNo() == '0') {
            return true;
        }
        return false;
    }

    /**
     * Analyse lookup`s results. If lookup is soft successful return true
     *
     * @return bool
     */
    protected function _isLookupSoftSuccessful()
    {
        $acsUrl = $this->getLookupAcsUrl();
        $payload = $this->getLookupPayload();
        $errorNo = $this->getLookupErrorNo();
        $enrolled = $this->getLookupEnrolled();

        //Test cases 7,8
        if ($acsUrl == '' && $payload == '' && $errorNo == '0' && ($enrolled == 'N' || $enrolled == 'U')) {
            return true;
        }

        //Test case 6
        if ($enrolled == '' && $acsUrl == '' && $payload == '' && $errorNo == 'Timeout number') {
            return true;
        }

        //Test cases 9,10
        if ($enrolled == 'U' && $acsUrl == '' && $payload == '' && $errorNo == '1001') {
            return true;
        }

        return false;
    }
}
