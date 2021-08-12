<?php

namespace XCNetworks\PagandoPayment\Controller\Checkout;

use Magento\Framework\App\Action\Action;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\OrderFactory;
use XCNetworks\PagandoPayment\Helper\Checkout;
use XCNetworks\PagandoPayment\Model\PagandoPayment;
use Psr\Log\LoggerInterface;

/**
 * @package
 */
abstract class AbstractAction extends Action {

    const LOG_FILE = 'pagando.log';

    private $_context;

    private $_checkoutSession;

    private $_orderFactory;

    private $_checkoutHelper;

    private $_messageManager;

    private $_logger;

    protected $_pagandoPayment;

    public function __construct(
        Session $checkoutSession,
        Context $context,
        OrderFactory $orderFactory,
        Checkout $checkoutHelper,
        PagandoPayment $pagandoPayment,
        LoggerInterface $logger
        ) {
        parent::__construct($context);
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_checkoutHelper = $checkoutHelper;
        $this->_messageManager = $context->getMessageManager();
        $this->_logger = $logger;
        $this->_pagandoPayment = $pagandoPayment;
    }
    
    protected function getContext() {
        return $this->_context;
    }

    protected function getCheckoutSession() {
        return $this->_checkoutSession;
    }

    protected function getOrderFactory() {
        return $this->_orderFactory;
    }

    protected function getCheckoutHelper() {
        return $this->_checkoutHelper;
    }

    protected function getMessageManager() {
        return $this->_messageManager;
    }

    protected function getLogger() {
        return $this->_logger;
    }
    
    protected function getPagandoPayment() {
        return $this->_pagandoPayment;
    }
    
    protected function getOrder()
    {
        $orderId = $this->_checkoutSession->getLastRealOrderId();

        $this->getLogger()->info('ORDER PAGANDO orderId -> ' . json_encode($this->_checkoutSession));

        if (!isset($orderId)) {
            return null;
        }

        return $this->getOrderById($orderId);
    }

    protected function getOrderById($orderId)
    {
        $order = $this->_orderFactory->create()->loadByIncrementId($orderId);

        if (!$order->getId()) {
            return null;
        }

        return $order;
    }

    protected function getObjectManager()
    {
        return \Magento\Framework\App\ObjectManager::getInstance();
    }

}
