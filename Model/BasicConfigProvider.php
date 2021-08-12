<?php

namespace XCNetworks\PagandoPayment\Model;

use Exception;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\Action\Context;
use XCNetworks\PagandoPayment\Model\PagandoPayment;

/**
 * Class BasicConfigProvider
 * @package XCNetworks\PagandoPayment\Model
 */
class BasicConfigProvider implements ConfigProviderInterface
{
    protected $methodCode = 'pagandoPayment';
    protected $_scopeConfig;
    protected $_methodInstance;
    protected $_checkoutSession;
    protected $_assetRepo;
    protected $_productMetaData;
    protected $_coreHelper;
    protected $_context;
    protected $_paymentFactory;
    protected $logger;

    /**
     * BasicConfigProvider constructor.
     * @param Context $context
     * @param PaymentHelper $paymentHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $checkoutSession
     * @param Repository $assetRepo
     * @param ProductMetadataInterface $productMetadata
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        Context $context,
        Repository $assetRepo,
        Session $checkoutSession,
        PaymentHelper $paymentHelper,
        ScopeConfigInterface $scopeConfig,
        ProductMetadataInterface $productMetadata,
        PagandoPayment $paymentFactory,
        \Psr\Log\LoggerInterface $customLogger

    )
    {
        $this->_context = $context;
        $this->_assetRepo = $assetRepo;
        $this->_scopeConfig = $scopeConfig;
        $this->_methodInstance = $paymentHelper->getMethodInstance($this->methodCode);
        $this->_checkoutSession = $checkoutSession;
        $this->_productMetaData = $productMetadata;
        $this->_paymentFactory = $paymentFactory;
        $this->logger = $customLogger;

    }

    /**
     * @return array
     */
    public function getConfig()
    {
        try {
            if (!$this->_methodInstance->isAvailable()) {
                return [];
            }
            
            $data = [
                'payment' => [
                    $this->methodCode => [
                        'active' => $this->_scopeConfig->getValue(
                            'payment/pagandoPayment/active',
                            ScopeInterface::SCOPE_STORE
                        ),
                        'order_status' => $this->_scopeConfig->getValue(
                            'payment/pagandoPayment/order_status',
                            ScopeInterface::SCOPE_STORE
                        ),
                        'logoUrl' => $this->_assetRepo->getUrl("XCNetworks_PagandoPayment::images/logo-color.png"),
                        'platform_version' => $this->_productMetaData->getVersion(),
                        'allowed_countries' => $this->_scopeConfig->getValue(
                            'payment/pagandoPayment/specificcountry',
                            ScopeInterface::SCOPE_STORE
                        )
                    ],
                ],
            ];

            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

}
