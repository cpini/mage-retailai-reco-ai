<?php

namespace RetailAI\Recommendations\Block\Recommendation;

use Magento\Framework\App\ObjectManager;
use RetailAI\Recommendations\Helper\Data as GoogleHelper;

class View extends \Magento\Catalog\Block\Product\View
{
    const RECOMMENDATION_URL = 'googleretail/recommendation/index';

    /**
     * @return string
     */
    public function getAjaxUrl(): string
    {
        return sprintf("%s%s", $this->getBaseUrl(), self::RECOMMENDATION_URL);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getData('title') ?? false;
    }

    /**
     * @return string
     */
    public function getUserEventId(): string
    {
        return ObjectManager::getInstance()->create(GoogleHelper::class)->getVisitorId();
    }

    /**
     * @return int
     */
    public function getProductPageId(): int
    {
        $product = $this->getProduct();
        if (!is_null($product)) {
            return $product->getId();
        }
        return 0;
    }

    /**
     * Get key pieces for caching block content
     * @return array
     */
    public function getCacheKeyInfo(): array
    {
        $keyInfo     =  parent::getCacheKeyInfo();
        $keyInfo[]   =  'GOOGLE_PRODUCT_RECOMMENDATION_VIEW';
        //$keyInfo[]   = $this->getPriceCurrency()->getCurrency()->getCode();
        $keyInfo[]   =  $this->_storeManager->getStore()->getId();
        $keyInfo[]   =  $this->_design->getDesignTheme()->getId();
        $keyInfo[]   =  (int)$this->getRequest()->getParam($this->getData('page_var_name'), 1);
        $keyInfo[]   =  $this->getProductsPerPage();
        $keyInfo[]   =  $this->getProductsCount();
        $keyInfo[]   =  $this->getTemplate();
        $keyInfo[]   =  $this->getTitle();
        return $keyInfo;
    }

    /**
     * Get Key for caching block content
     * @return string
     */
    public function getRecommendationCacheKey(): string
    {
        $placementId = $this->getData('placement-id');
        $eventType = $this->getData('event-type');
        return sprintf("RECOMMENDATION_%s", hash('sha256', $placementId . '_'.
            $eventType .'_'. $this->getUserEventId().'_'. $this->getProductPageId()));
    }
}
