<?php

namespace XCNetworks\PagandoPayment\Controller\Checkout;

use Magento\Framework\App\Action\Action;
use XCNetworks\PagandoPayment\Model\PagandoPayment;
use Magento\Sales\Model\Order;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\OrderFactory;
use Psr\Log\LoggerInterface;

class Cancel extends AbstractAction {
    
    public function execute() {

        $orderId = $this->getRequest()->get('orderId');
        $order =  $this->getOrderById($orderId);

        if ($order && $order->getId()) {
            $this->getLogger()->debug('Requested order cancellation by customer. OrderId: ' . $order->getIncrementId());
            $this->getCheckoutHelper()->cancelCurrentOrder("Pagando: ".($order->getId())." was cancelled by the customer.");
            $this->getCheckoutHelper()->restoreQuote(); //restore cart
            $this->getMessageManager()->addWarningMessage(__("You have successfully canceled your Pagando payment. Please click on 'Update Shopping Cart'."));
        }
        $this->_redirect('checkout/cart');
    }

}
