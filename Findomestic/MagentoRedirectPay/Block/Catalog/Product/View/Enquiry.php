<?php

namespace Findomestic\MagentoRedirectPay\Block\Catalog\Product\View;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Findomestic\MagentoRedirectPay\Helper\ConfigData;

class Enquiry extends Template
{
    private $helper;

    private $mode;

    public function getConfig($field, $path = 'payment/findomesticpayment/')
    {
        return $this->helper->getConfig($field, $path);
    }

    public function __construct(
        ConfigData $helper,
        Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->mode = $this->helper->getConfig('paymentMode');
    }


    public function getLogo(){
        $image = $this->helper->getLogo();
        return $this->getViewFileUrl('Findomestic_MagentoRedirectPay::images/'.$image);
    }


    public function getCmsBlock($identifier)
    {
        $cmsBlock = $this->_layout->createBlock(\Magento\Cms\Block\Block::class)
            ->setBlockId($identifier)
            ->toHtml();
        return $cmsBlock;
    }

    public function getLocation(){
        return $this->getConfig('inProductPosition', 'payment/findomesticpayment_'.$this->mode.'/');
    }

    public function findomesticInProduct($amount, $location){
        if($this->getLocation() != $location){
            return $this->getLocation();
        }
        return $this->helper->getFrontendOption('inProduct', $amount);
    }
    public function findomesticInProductHtml($amount, &$extraClass, $location = ''){
        $buttonMode = $this->findomesticInProduct($amount, $location);
        return $this->getButtonHtml($buttonMode, $amount, $extraClass);
    }


    public function findomesticInCart($amount, $location){
        // TODO -> CHECK LOCATION
        return $this->helper->getFrontendOption('inCart', $amount);
    }
    public function findomesticInCartHtml($amount, &$extraClass, $location = ''){
        $buttonMode = $this->findomesticInCart($amount, $location);
        return $this->getButtonHtml($buttonMode, $amount, $extraClass);
    }

    private function getButtonHtml($buttonMode, $amount, &$extraClass){
        $simulatorUrl = $this->findomesticSimulatorUrl($amount);
        $html = '<div id="findomestic-info-tab-container">';
        $flag = false;
        $colorClass = $this->helper->getConfig('logoColor', 'payment/findomesticpayment_'.$this->mode.'/');
        $buttonText = 'Calcola la rata';
        switch($buttonMode){
            case 'horizontal-long':
                $flag = true;
                $html .= '
                <div id="findomestic-info-tab" class="'.$colorClass.'">
                    <div class="findomestic-simulator findomestic-two-col-container">
                        <div class="findomestic-column findomestic-column-1">
                            <p>Pagamento a rate con <span><img id="eps" src="' . $this->getLogo(). '" alt="Findomestic" /></span></p>
                        </div>
                        <div class="findomestic-column findomestic-column-2">
                            <a class=" findomestic-view-simulator '.$colorClass.'" href="' . $simulatorUrl. '" target="_blank">
                                '.$buttonText.'
                            </a>
                        </div>

                    </div>

                </div>';
                break;
            case 'vertical-long':
                $flag = true;
                $html .= '
                <div id="findomestic-info-tab" class="'.$colorClass.'">
                    <div class="findomestic-simulator findomestic-column-container">
                        <div class="findomestic-row">
                            <p>Pagamento a rate con <span><img id="eps" src="' . $this->getLogo(). '" alt="Findomestic" /></span></p>
                        </div>
                        <div class="findomestic-row">
                            <a class=" findomestic-view-simulator '.$colorClass.'" href="' . $simulatorUrl. '" target="_blank">
                                '.$buttonText.'
                            </a>
                        </div>
                    </div>
                </div>';
                break;
            case 'no-button-horizontal-long':
                $flag = true;
                $html .= '
                <div id="findomestic-info-tab" class="'.$colorClass.'">
                    <div class="findomestic-simulator findomestic-two-col-container">
                        <div class="findomestic-column findomestic-column-1">
                            <p>Pagamento a rate con <span><img id="eps" src="' . $this->getLogo(). '" alt="Findomestic" /></span></p>
                        </div>
                        <div class="findomestic-column findomestic-column-2">
                            <a class=" findomestic-view-simulator-text '.$colorClass.'" href="' . $simulatorUrl. '" target="_blank">
                                '.$buttonText.'
                            </a>
                        </div>

                    </div>

                </div>';
                break;
            case 'no-button-vertical-long':
                $flag = true;
                $html .= '
                <div id="findomestic-info-tab" class="'.$colorClass.'">
                    <div class="findomestic-simulator findomestic-column-container">
                        <div class="findomestic-row">
                            <p>Pagamento a rate con <span><img id="eps" src="' . $this->getLogo(). '" alt="Findomestic" /></span></p>
                        </div>
                        <div class="findomestic-row">
                            <a class=" findomestic-view-simulator-text '.$colorClass.'" href="' . $simulatorUrl. '" target="_blank">
                                '.$buttonText.'
                            </a>
                        </div>
                    </div>
                </div>';
                break;
            case 'no-button-no-bg-horizontal-long':
                $flag = true;
                $extraClass = 'findomestic-popup-link-no-bg';
                $html .= '
                <div id="findomestic-info-tab" class="findomestic-info-tab-no-bg '.$colorClass.'">
                    <div class="findomestic-simulator findomestic-two-col-container">
                        <div class="findomestic-column findomestic-column-1">
                            <p>Pagamento a rate con <span><img id="eps" src="' . $this->getLogo(). '" alt="Findomestic" /></span></p>
                        </div>
                        <div class="findomestic-column findomestic-column-2">
                            <a class=" findomestic-view-simulator-text '.$colorClass.'" href="' . $simulatorUrl. '" target="_blank">
                                '.$buttonText.'
                            </a>
                        </div>

                    </div>

                </div>';
                break;
            case 'no-button-no-bg-vertical-long':
                $flag = true;
                $extraClass = 'findomestic-popup-link-no-bg';
                $html .= '
                <div id="findomestic-info-tab" class="findomestic-info-tab-no-bg '.$colorClass.'">
                    <div class="findomestic-simulator findomestic-column-container">
                        <div class="findomestic-row">
                            <p>Pagamento a rate con <span><img id="eps" src="' . $this->getLogo(). '" alt="Findomestic" /></span></p>
                        </div>
                        <div class="findomestic-row">
                            <a class=" findomestic-view-simulator-text '.$colorClass.'" href="' . $simulatorUrl. '" target="_blank">
                                '.$buttonText.'
                            </a>
                        </div>
                    </div>
                </div>';
                break;
            case 'horizontal':
                $flag = true;
                $html .= '
                <div id="findomestic-info-tab" class="'.$colorClass.'">
                    <div class="findomestic-simulator findomestic-two-col-container">
                        <div class="findomestic-column">
                            <img id="eps" src="' . $this->getLogo(). '" alt="Findomestic" />
                        </div>
                        <div class="findomestic-column">
                            <a class=" findomestic-view-simulator '.$colorClass.'" href="' . $simulatorUrl. '" target="_blank">
                                '.$buttonText.'
                            </a>
                        </div>
                    </div>
                </div>';
                break;
            case 'vertical':
                $flag = true;
                $html .= '
                <div id="findomestic-info-tab" class="'.$colorClass.'">
                    <div class="findomestic-simulator findomestic-column-container">
                        <div class="findomestic-row">
                            <img id="eps" src="' . $this->getLogo(). '" alt="Findomestic" />
                        </div>
                        <div class="findomestic-row">
                            <a class=" findomestic-view-simulator '.$colorClass.'" href="' . $simulatorUrl. '" target="_blank">
                                '.$buttonText.'
                            </a>
                        </div>
                    </div>
                </div>';
                break;

        }
        $html .= '</div>';
        if($flag){
            return $html;
        }
        return '';
    }

    public function findomesticSimulatorUrl($price){
        return $this->helper->getWebAppUrl($this->mode, $price, 1);
    }

    public function getMoreInfoPopup($extraClass = ''){
        if(!$this->helper->checkPopup()){
            return '';
        }

        $html = '<div class="findomestic-simulator findomestic-column-container">
                <div class="findomestic-row">
                    <div id="findomestic-popup-link" class="'.$extraClass.'">Maggiori informazioni</div>
                    <div class="findomestic-popup">
                       <div class="findomestic-popup-logo"><img id="eps" src="'. $this->getLogo() . '" alt="Findomestic logo" /> </div>
                       <div class="findomestic-popup-text">
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce mollis nulla ac tempus rutrum. </p>
                        <p>Aenean sit amet accumsan lectus. Sed varius urna id urna sodales vulputate. </p>
                        <p>Nam a suscipit ante. Ut commodo pharetra ornare. Phasellus sit amet orci tortor.</p>
                        <p>Etiam lobortis est in purus tempor, sit amet maximus eros dignissim.</p>
                       </div>
                       <div class="findomestic-popup-close">Chiudi</div>
                    </div>
                </div>
            </div>';

        return $html;
    }

}
