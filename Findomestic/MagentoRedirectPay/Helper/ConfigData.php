<?php
namespace Findomestic\MagentoRedirectPay\Helper;

class ConfigData extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getConfig($field, $path = 'payment/findomesticpayment/')
    {
        return $this->scopeConfig->getValue(
            $path.$field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getLogo($location = ''){
        $mode = $this->getConfig('paymentMode');
        $option = $this->getConfig('logoColor', 'payment/findomesticpayment_'.$mode.'/');
        $logo = 'green-logo.svg';
        switch ($option){
            case 'green':
                $logo = 'green-logo.svg';
                break;
            case 'negative':
                $logo = 'negative-logo.svg';
                break;
        }
        return $logo;
    }

    public function getIcon(){
        $mode = $this->getConfig('paymentMode');
        $option = $this->getConfig('iconColor', 'payment/findomesticpayment_'.$mode.'/');

        $logo = 'green-icon.svg';
        switch ($option){
            case 'green':
                $logo = 'green-icon.svg';
                break;
            case 'negative':
                $logo = 'negative-icon.svg';
                break;
        }
        return $logo;
    }

    public function getWebAppUrl($mode, $amount, $simulator = false, $cart_id = null, $user_data = null, $urlRedirect = null, $callBackUrl = null)
    {
        $array = [];
        $amount = round($amount, 2)*100;
        $codiceFinalita = $this->getConfig('CodiceFinalita', 'payment/findomesticpayment_'.$mode.'/');

        if ($simulator) {
            $array['tvei'] = $this->getConfig('tvei', 'payment/findomesticpayment_'.$mode.'/');
            $array['prf'] = $this->getConfig('prf', 'payment/findomesticpayment_'.$mode.'/');
            if($codiceFinalita != ''){
                $array['CodiceFinalita'] = $codiceFinalita;
            }
            $array['importo'] = $amount;
            $array['versione'] = 'L';
        } else {
            $array = $user_data;
            $array['tvei'] = $this->getConfig('tvei', 'payment/findomesticpayment_'.$mode.'/');
            $array['prf'] = $this->getConfig('prf', 'payment/findomesticpayment_'.$mode.'/');
            if($codiceFinalita != ''){
                $array['CodiceFinalita'] = $codiceFinalita;
            }
            $array['importo'] = $amount;
            $array['cartId'] = $cart_id;
            $array['urlRedirect'] = $urlRedirect;
            $array['callBackUrl'] = $callBackUrl;
        }
        return $this->getConfig('findomesticUrl', 'payment/findomesticpayment_'.$mode.'/') . $this->urlArgsToString($array); // GET url should return something like https://b2ctest.ecredit.it/clienti/pmcrs/eprice/mcommerce/pages/simulatore.html?tvei=100394259&prf=7079&importo=200&versione=L
    }

    /**
     * @param $args
     * @return false|string
     *
     * CONVERTS ARRAY TO URL ARGUMENTS (no leading "?")
     */
    public function urlArgsToString($args)
    {
        if (count($args)  == 0) {
            return false;
        }
        $string = '';
        foreach ($args as $key => $value) {
            if($string == ''){
                $string .= '?';
            }else{
                $string .= '&';
            }


            $string.= $key.'='.$value;
        }

        return $string;
    }


    public function getFrontendOption($option, $amount){
        $mode = $this->getConfig('paymentMode');
        $minAmount = $this->getConfig('minAmount', 'payment/findomesticpayment_'.$mode.'/');

        if($amount < $minAmount){
            return 'no';
        }
        if($mode == '' || $mode == null){
            return 'no';
        }
        //return $this->helper->getConfig('findomesticUrl', 'payment/findomesticpayment_'.$mode.'/');
        return $this->getConfig($option, 'payment/findomesticpayment_'.$mode.'/');
    }

    public function checkPopup(){
        // HANDLE AUTHORIZATION TO SHOW ELEMENT
        return false;
    }
}
