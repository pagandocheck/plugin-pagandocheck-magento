<?php

namespace XCNetworks\PagandoPayment\Observer;

use XCNetworks\PagandoPayment\Model\PagandoPayment;
use Magento\Framework\App\ActionFlag;

class SalesOrderPlaceBeforeObserver implements \Magento\Framework\Event\ObserverInterface
{


    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\UrlInterface $url,
        PagandoPayment $paymentFactory,
        ActionFlag $actionFlag
    ) {
        $this->url = $url;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->responseFactory = $responseFactory;
        $this->paymentFactory = $paymentFactory;
        $this->actionFlag = $actionFlag;

    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        try {

            $event = $observer->getEvent();
            $order = $event->getOrder();
            $paymentOrder = $order->getPayment();
            $paymentMethod = $paymentOrder->getMethodInstance()->getCode();
            
            if (empty($paymentOrder)) {
                $message = "No paymenth method detected.";
                $this->redirectError($message);   
            }
            
            if ($paymentMethod != 'pagandoPayment') {
                return $this;   
            }
                    
            $orderObserverData = $order->getData();
            $quote = $event->getQuote();
            $cartId = $order->getQuoteId();
            $incrementalId = $order->getIncrementId();
            $amount = $orderObserverData['grand_total'];

            $jwt_token = $this->paymentFactory->getToken();
            
            if($jwt_token->error){
                $this->redirectError($this->paymentFactory->error_msg);
            }
            
            $result = $this->paymentFactory->createEcommerceOrder($cartId, $amount, $incrementalId);
            
            if ($result->error) {
                $this->redirectError($result->error_msg);
            }
            
        } catch(\Exception $e) {
            $message = $e->getMessage();
            $this->redirectError($message);
        }

        return $this;
    }

    protected function redirectError($returnMessage) {
        $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);

        $this->messageManager->addError($returnMessage);
       $cartUrl = $this->url->getUrl('checkout/cart/index');
       $this->responseFactory->create()->setRedirect($cartUrl)->sendResponse(); 
       
       exit();
    }

}
