<?php

namespace Webidea24\PayPalMessaging\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class MessagingBanner extends Template
{

    protected function _toHtml(): string
    {
        return $this->isEnabled() ? parent::_toHtml() : '';
    }

    private function isEnabled(): bool
    {
        return $this->_scopeConfig->isSetFlag('wi24_paypal_messaging/general/enabled', ScopeInterface::SCOPE_STORE);
    }

    public function getJsLayout(): string
    {
        $this->jsLayout = array_merge_recursive([
            'components' => [
                'paypalMessaging' => [
                    'component' => 'Magento_Paypal/js/view/paylater',
                    'config' => [
                        'sdkUrl' => $this->getPayPalSdkUrl(),
                        'displayAmount' => true,
                        'amountComponentConfig' => [
                            'component' => 'Magento_Paypal/js/view/amountProviders/product',
                        ],
                    ]
                ]
            ]
        ], $this->jsLayout ?? []);

        return parent::getJsLayout();
    }

    private function getPayPalSdkUrl(): string
    {
        /** @var Store $store */
        $store = $this->_storeManager->getStore();

        return 'https://www.paypal.com/sdk/js?' . http_build_query([
                'client-id' => $this->_scopeConfig->getValue('wi24_paypal_messaging/general/paypal_client_id', ScopeInterface::SCOPE_WEBSITE),
                'currency' => $store->getCurrentCurrency()->getCode(),
                'components' => 'messages'
            ]);
    }
}
