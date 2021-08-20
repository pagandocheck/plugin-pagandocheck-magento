<?php

namespace XCNetworks\PagandoPayment\Controller\Checkout;

use Magento\Sales\Model\Order;

class Success extends AbstractAction {


    public function execute() {


        $request = $this->getObjectManager()->get('Magento\Framework\App\Request\Http');  
        $orderId = $request->getParam('orderId');

        if(!$orderId) {
            $this->_redirect('checkout/onepage/error', array('_secure'=> false));
            return;
        }

        $orderInfo = $this->getPagandoPayment()->getEcommerceOrderData($orderId);
        
        if(!$orderInfo) {
            $this->_redirect('checkout/onepage/error', array('_secure'=> false));
            return;
        }

        $transactionId = $orderInfo->transactionId;

        $order = $this->getOrderById($orderInfo->orderIdECommerce);

        if(!$order) {
            $this->_redirect('checkout/onepage/error', array('_secure'=> false));
            return;
        }

        if ($orderInfo->payResponse == "APPROVED") {

            $orderState = Order::STATE_PROCESSING;

            $orderStatus = 'pagando_processed';
            if (!$this->statusExists($orderStatus)) {
                $orderStatus = $order->getConfig()->getStateDefaultStatus($orderState);
            }

            $order->setState($orderState)
                ->setStatus($orderStatus)
                ->addStatusHistoryComment("Pagando authorization success. Transaction #$transactionId")
                ->setIsCustomerNotified(true);

	        $payment = $order->getPayment();

	        $payment->setTransactionId($transactionId);
	        $payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE, null, true);
            $order->save();

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $emailSender = $objectManager->create('\Magento\Sales\Model\Order\Email\Sender\OrderSender');
            $emailSender->send($order);

            
            $this->getMessageManager()->addSuccessMessage(__("Your payment with Pagando is complete"));
            $this->_redirect('checkout/onepage/success', array('_secure'=> false));
        } else {
            $this->getCheckoutHelper()->cancelCurrentOrder("Order #".($order->getId())." was rejected by Pagando. Transaction #$transactionId.");
            $this->getCheckoutHelper()->restoreQuote(); //restore cart
            $this->getMessageManager()->addErrorMessage(__("There was an error in the Pagando payment"));
            $this->_redirect('checkout/cart', array('_secure'=> false));
        }
    }

    private function statusExists($orderStatus)
    {
        $statuses = $this->getObjectManager()
            ->get('Magento\Sales\Model\Order\Status')
            ->getResourceCollection()
            ->getData();
        foreach ($statuses as $status) {
            if ($orderStatus === $status["status"]) return true;
        }
        return false;
    }

}
