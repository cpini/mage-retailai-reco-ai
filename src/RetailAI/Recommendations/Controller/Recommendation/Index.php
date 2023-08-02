<?php

namespace RetailAI\Recommendations\Controller\Recommendation;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\ViewModel\Product\OptionsData as ViewModel;
use Magento\Framework\Session\SessionManagerInterface;
use RetailAI\Recommendations\Helper\Data as GoogleHelper;
use RetailAI\Recommendations\Block\Recommendation\ListProduct;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected PageFactory $_resultPageFactory;

    /**
     * @var JsonFactory
     */
    protected JsonFactory $_resultJsonFactory;

    /**
     * @var ViewModel
     */
    protected ViewModel $optionsData;

    /**
     * @var GoogleHelper
     */
    protected GoogleHelper $googleHelper;

    /**
     * @var SessionManagerInterface
     */
    protected SessionManagerInterface $sessionManager;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param ViewModel $optionsData
     * @param GoogleHelper $googleHelper
     * @param SessionManagerInterface $session
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        ViewModel $optionsData,
        GoogleHelper $googleHelper,
        SessionManagerInterface $session
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->optionsData = $optionsData;
        $this->googleHelper = $googleHelper;
        $this->sessionManager = $session;
        parent::__construct($context);
    }

    /**
     * @return object
     */
    public function execute(): object
    {
        $result = $this->_resultJsonFactory->create();
        $resultPage = $this->_resultPageFactory->create();
        $placementId = $this->getRequest()->getParam('placement-id');
        $eventType = $this->getRequest()->getParam('event-type');
        $productId = $this->getRequest()->getParam('product-id');
        $recKey = $this->getRequest()->getParam('recommendation-key');
        $block = $resultPage->getLayout()
            ->createBlock(ListProduct::class)
            ->setTemplate('RetailAI_Recommendations::product/recommendation/list.phtml')
            ->setData('viewModel', $this->optionsData)
            ->setData('placement-id', $placementId)
            ->setData('event-type', $eventType)
            ->setData('product-id', $productId)
            ->setData('cache_lifetime', 43200) // 12 hours
            ->toHtml();
        $this->setSessionRecommendations($recKey, $block);
        $result->setData(['output' => $block]);
        return $result;
    }

    /**
     * Index constructor.
     * @param $recKey
     * @param $block
     */
    private function setSessionRecommendations($recKey, $block)
    {
        $this->sessionManager->start();
        $recommendations = [];
        if (is_array($this->sessionManager->getGoogleRecommendations())) {
            $recommendations = $this->sessionManager->getGoogleRecommendations();
        }
        $recommendations = array_merge($recommendations, [$recKey => base64_encode(gzencode($block))]);
        $this->sessionManager->setGoogleRecommendations($recommendations);
    }
}
