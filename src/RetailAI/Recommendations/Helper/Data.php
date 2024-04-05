<?php

namespace RetailAI\Recommendations\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResourceModel;
use Magento\Customer\Model\SessionFactory;


class Data
{
    public const COOKIE_NAME = "_ga";

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * @var DirectoryList
     */
    protected DirectoryList $_dir;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $_storeManager;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    /**
     * @var SessionManagerInterface
     */
    private SessionManagerInterface $sessionManager;

    /**
     * @var CustomerFactory
     */
    private CustomerFactory $customerFactory;

    /**
     * @var SessionFactory
     */
    private SessionFactory $customerSession;

    /**
     * @var CustomerResourceModel
     */
    private CustomerResourceModel $customerResourceModel;

    /**
     * @var ScopeConfigInterface
     * @since 100.1.0
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @param CookieManagerInterface $cookieManager,
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     * @param DirectoryList $dir
     * @param Filesystem $filesystem
     * @param SessionManagerInterface $sessionManager
     * @param SessionFactory $customerSession
     * @param CustomerResourceModel $customerResourceModel
     * @param CustomerFactory $customerFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        LoggerInterface         $logger,
        StoreManagerInterface   $storeManager,
        DirectoryList           $dir,
        Filesystem              $filesystem,
        SessionManagerInterface $sessionManager,
        SessionFactory          $customerSession,
        CustomerResourceModel   $customerResourceModel,
        CustomerFactory         $customerFactory,
        ScopeConfigInterface    $scopeConfig
    ) {
        $this->_storeManager = $storeManager;
        $this->logger = $logger;
        $this->_dir = $dir;
        $this->filesystem = $filesystem;
        $this->sessionManager = $sessionManager;
        $this->customerSession = $customerSession;
        $this->customerResourceModel = $customerResourceModel;
        $this->customerFactory = $customerFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_cookieManager = $cookieManager;
    }

    /**
     * The GA ID or the sessionId
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getVisitorId(): string
    {
        /**
         * Assumes the use of GA for tracking and looks for the GA Client ID (as recommended by Google
         * https://cloud.google.com/retail/docs/reference/rest/v2/projects.locations.catalogs.userEvents).
         * However, this must match the visitorId captured in the user events being captured
         */
        $analyticsId = $this->_cookieManager->getCookie(self::COOKIE_NAME);
        $visitorId = $analyticsId;
        return (string)$visitorId;
    }

    /**
     * Essentially the customer increment id for to use for Reco API.
     * The user events also need to capture the userInfo.UserId information i.e. the incrementId
     * as this will allow for a deeper level personalisation for logged in users
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getUserId(): string
    {
        $userId = null;
        $customerSession = $this->customerSession->create()->getCustomerData();
        if ($customerSession !== null) {
            $customerModel = $this->customerFactory->create();
            $this->customerResourceModel->load($customerModel, $customerSession->getId());
            $userId = $customerModel->getIncrementId();
        }
        return (string)$userId;
    }

    /**
     * @return string
     */
    public function getStorefrontSessionId(): string
    {
        return $this->sessionManager->getSessionId();
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return mixed
     */
    public function getGoogleRetailProjectId(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ) {
        return $this->scopeConfig->getValue(
            'catalog/google_retail/google_recommendations_project_id',
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return mixed
     */
    public function getGoogleRetailCatalogId(
        string $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        $scopeCode = null
    ): mixed {
        return $this->scopeConfig->getValue(
            'catalog/google_retail/google_recommendations_catalogs',
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @return mixed
     */
    public function getPdpPlacementId(): mixed
    {
        return $this->scopeConfig->getValue(
            'catalog/google_retail/google_pdp_recommendation_placement_id',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    /**
     * @return mixed
     */
    public function getPdpEventType(): mixed
    {
        return $this->scopeConfig->getValue(
            'catalog/google_retail/google_pdp_recommendation_event_type',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    /**
     * @return mixed
     */
    public function getPdpTitle(): mixed
    {
        return $this->scopeConfig->getValue(
            'catalog/google_retail/google_pdp_recommendation_title',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    /**
     * @return bool
     */
    public function getRecommendationsValidateOnly(): bool
    {
        return $this->scopeConfig->getValue(
            'catalog/google_retail/google_recommendation_validate_only',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }
}
