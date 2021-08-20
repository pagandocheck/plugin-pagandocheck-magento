<?php

namespace XCNetworks\PagandoPayment\Controller\Checkout;

use Magento\Sales\Model\Order;

class Index extends AbstractAction {

    private function postToCheckout($checkoutUrl, $orderId, $token)
    {
        echo
        "<html>
            <body>
            <form id='form' action='$checkoutUrl' method='get'>
            <input type='hidden' name='orderId' value='$orderId'>
            <input type='hidden' name='token' value='$token'>";
        
        echo
            '</form>
            </body>';
        echo
            '<script>
                var form = document.getElementById("form");
                form.submit();
            </script>
        </html>';
    }

    public function execute() {

        try {
            // $this->getLogger()->info('ORDER PAGANDO BEFORE  -> ' . json_encode($this->getCheckoutSession()->getOrderId()));
            // $this->getLogger()->info('ORDER PAGANDO INCREMENTAL ID BEFORE  -> ' . json_encode($this->getCheckoutSession()->getOrderIncrementalId()));
            // $order = $this->getOrder();
            // $this->getLogger()->info('ORDER PAGANDO -> ' . json_encode($order));
            // $this->getLogger()->info('ORDER PAGANDO STATE-> ' . json_encode($order->getState()));

            // if ($order->getState() === Order::STATE_PENDING_PAYMENT) {
            //     $payload = $this->getPayload($order);
                $this->postToCheckout($this->getPagandoPayment()->_checkoutUri."_external-payment", $this->getCheckoutSession()->getOrderId(), $this->getCheckoutSession()->getToken());
            // } else if ($order->getState() === Order::STATE_CANCELED) {
            //     $errorMessage = $this->getCheckoutSession()->getPagandoErrorMessage(); //set in InitializationRequest
            //     if ($errorMessage) {
            //         $this->getMessageManager()->addWarningMessage($errorMessage);
            //         $errorMessage = $this->getCheckoutSession()->unsPagandoErrorMessage();
            //     }
            //     $this->getCheckoutHelper()->restoreQuote(); //restore cart
            //     $this->_redirect('checkout/cart');
            // } else {
            //     $this->getLogger()->debug('Order in unrecognized state: ' . $order->getState());
            //     $this->getCheckoutHelper()->restoreQuote(); //restore cart
            //     $this->_redirect('checkout/cart');
            // }



            // $this->postToCheckout($this->getPagandoPayment()->_checkoutUri."_external-payment", $this->getCheckoutSession()->getOrderId(), $this->getCheckoutSession()->getToken());
        } catch (Exception $ex) {
            $this->getLogger()->debug('An exception was encountered in pagando/checkout/index: ' . $ex->getMessage());
            $this->getLogger()->debug($ex->getTraceAsString());
            $this->getMessageManager()->addErrorMessage(__('Unable to start Pagando Checkout.'));
        }
    }

}