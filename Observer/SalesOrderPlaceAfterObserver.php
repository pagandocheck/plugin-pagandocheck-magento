<?php

namespace XCNetworks\PagandoPayment\Observer;

use XCNetworks\PagandoPayment\Model\PagandoPayment;
use Magento\Framework\App\ActionFlag;

class SalesOrderPlaceAfterObserver implements \Magento\Framework\Event\ObserverInterface
{

    protected $actionFlag;
    protected $response;
    protected $_redirect;
    protected $_objectManager;


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $url,
        PagandoPayment $paymentFactory,
        ActionFlag $actionFlag,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\App\Response\Http $redirect,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->cart = $cart;
        $this->url = $url;
        $this->logger = $logger;
        $this->currency = $currency;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
        $this->responseFactory = $responseFactory;
        $this->paymentFactory = $paymentFactory;
        $this->actionFlag = $actionFlag;
        $this->response = $response;
        $this->_redirect = $redirect;
        $this->_objectManager = $objectManager;
        $this->_checkoutSession = $checkoutSession;
    }

    private function postToCheckout($checkoutUrl)
    {
        echo
        "<html>
            <body>
            <form id='form' action='$checkoutUrl' method='post'>";
       
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

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        try {
            // $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            $event = $observer->getEvent();
            $order = $event->getOrder();
            $paymentOrder = $order->getPayment();
            $paymentMethod = $paymentOrder->getMethodInstance()->getCode();

            if (empty($paymentOrder)) {
                    $message = "No payment method valid";
                    $this->redirectError($message);   
                }
                
            if ($paymentMethod != 'pagandoPayment') {
                return $this;   
            }

            // $redirectionUrl = $this->url->getUrl('pagando/checkout/index');

            // $this->_checkoutSession->setOrderId($this->paymentFactory->orderId);
            // $this->_checkoutSession->setToken($this->paymentFactory->token);
            // $this->response->setRedirect($redirectionUrl)->sendResponse();
            // exit();


        } catch(\Exception $e) {
            $this->logger->info(print_r('error-> '.$e->getMessage(),true));
            $message = "An error occurs";
            $this->redirectError($message);
        }

        return $this;
    }

    protected function redirectError($returnMessage) {
        $this->messageManager->addError($returnMessage);
        $cartUrl = $this->url->getUrl('checkout/cart/index');
        $this->responseFactory->create()->setRedirect($cartUrl)->sendResponse();            
        exit;
    }
}
