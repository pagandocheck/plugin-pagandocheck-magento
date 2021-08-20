<?php

namespace XCNetworks\PagandoPayment\Model;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Catalog\Helper\Image;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as customerSession;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\Method\Logger;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\ScopeInterface;

class PagandoPayment extends AbstractMethod
{
    const PAYMENT_METHOD_PAGANDOPAYMENT_CODE = 'pagandoPayment';
    protected const API_URI = 'http://localhost:3000/api-v2/pagando/';
    protected const CHECKOUT_URI = 'http://192.168.1.74:8090/';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_PAGANDOPAYMENT_CODE;
    protected $_apiUri = self::API_URI;
    public $_checkoutUri = self::CHECKOUT_URI;

    public $error_msg, $error, $id, $token, $orderId;
    protected $api_user, $api_pass, $logger ;

    public function __construct(
        Image $helperImage,
        Session $checkoutSession,
        customerSession $customerSession,
        OrderFactory $orderFactory,
        UrlInterface $urlBuilder,
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        array $data = [],
        \Psr\Log\LoggerInterface $customLogger
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            null,
            null,
            $data
        );

        $this->_helperImage = $helperImage;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_orderFactory = $orderFactory;
        $this->_urlBuilder = $urlBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->api_user = $this->_scopeConfig->getValue('payment/pagandoPayment/user', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->api_pass = $this->_scopeConfig->getValue('payment/pagandoPayment/public_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->logger = $customLogger;

    }


    function getToken(){
        $data = [
            'user' => $this->api_user,
            'password' => $this->api_pass
        ];

        $res = $this->post('get-token', $data);

        if(!$res->error) {
            $this->token = $res->data->token;
            $this->_checkoutSession->setToken($this->token);
        }
        
        return $res;
    }

    function createEcommerceOrder($cartId, $amount, $incrementalId) {
        // Get service params
        $data = $this->getEcommerceData($cartId, $amount, $incrementalId);

        $res = $this->post('orders/create-ecommerce-order', $data);

        if(!$res->error) {
            $this->orderId = $res->data->orderId;
            $this->_checkoutSession->setOrderId($this->orderId);

        }

        return $res;
    }

    protected function getEcommerceData($cartId, $amount, $incrementalId) {
        $data['cartId'] = $cartId;
        $data['total'] = $amount;
        $data['orderIdECommerce'] = $incrementalId;
        $data['paymentToken'] = md5($this->api_pass.$data['cartId'].$data['total']);;
        $data['originECommerce'] = 'MAGENTO';

        return $data;

    }

    function getEcommerceOrderData($orderId) {

        $res = $this->request('orders/get-order-info?orderId='.$orderId);

        if(!$res->error)
            return $res->data;

        return $res->error;
    }

    public function getRedirectURIForPagandoCheckout() {
        return $this->_checkoutUri."_external-payment?orderId=".$this->orderId."&token=".$this->token;
    }

    function post($path, $data)
    {
        return $this->request($path, $data, "POST");
    }

    function request( $path, $data = [], $type = "GET" )
    {
        $url = $this->_apiUri.$path;

        $headers[] = "Content-Type: application/x-www-form-urlencoded";

        if(!empty($this->_checkoutSession->getToken())){
            $headers[] = "Authorization: ".$this->_checkoutSession->getToken();
        }

        $settings = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => $headers,
        );

        if($type != "GET")
        {
            $settings[CURLOPT_CUSTOMREQUEST] = $type;

            if(!empty($data)){
                $settings[CURLOPT_POSTFIELDS] = http_build_query($data);
            }
            
        }

        $curl = curl_init();
        curl_setopt_array($curl, $settings);
        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response);

        $this->error_msg = $result->message;

        $return = new \stdClass();

        if(!empty($result->data)){
            $this->error = 0;
            $return->error = 0;
            $return->data = $result->data;
        } else {
            $this->error = 1;
            $return->error = 1;
            $return->message = $result->message;
        }

        return $return;
    }
}