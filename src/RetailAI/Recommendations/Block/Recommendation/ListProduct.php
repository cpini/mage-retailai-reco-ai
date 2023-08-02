<?php

namespace RetailAI\Recommendations\Block\Recommendation;

use Exception;
use RetailAI\Recommendations\Block\Product\ListProduct as CoreListProduct;
use RetailAI\Recommendations\Helper\Data as GoogleHelper;
use Google\Cloud\Retail\V2\PredictionServiceClient;
use Google\Cloud\Retail\V2\Product;
use Google\Cloud\Retail\V2\ProductDetail;
use Google\Cloud\Retail\V2\UserEvent;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Url\Helper\Data;
use Psr\Log\LoggerInterface;
use Zend_Db_Expr as ZendDbExpression;

class ListProduct extends CoreListProduct
{
    /**
     * Product collection factory
     *
     * @var CollectionFactory
     */
    protected CollectionFactory $_productCollectionFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var GoogleHelper
     */
    protected GoogleHelper $googleHelper;

    /**
     * @var Visibility
     */
    private Visibility $visibility;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param CollectionFactory $productCollectionFactory
     * @param GoogleHelper $googleHelper
     * @param Visibility $visibility
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        CollectionFactory $productCollectionFactory,
        GoogleHelper $googleHelper,
        Visibility $visibility,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->logger = $logger;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->googleHelper = $googleHelper;
        $this->visibility = $visibility;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * @return Collection
     */
    protected function _getProductCollection(): Collection
    {
        $entities = $this->getProductRecommendationEntities();
        $collection = $this->_productCollectionFactory->create();
        if ($this->getData('store_id') !== null) {
            $collection->setStoreId($this->getData('store_id'));
        }
        $collection->setVisibility($this->visibility->getVisibleInCatalogIds());
        $collection->addAttributeToFilter('entity_id', $entities);
        // Sort by order returned by Google Reco AI
        $orderList = join(',', $entities);
        $collection->getSelect()->order(new ZendDbExpression("FIELD(e.entity_id,$orderList)"));
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->setPageSize(5);
        /**
         * Prevent retrieval of duplicate records. This may occur when multiselect product attribute matches
         * several allowed values from condition simultaneously
         */
        $collection->distinct(true);
        return $collection;
    }

    /**
     * @return array
     */
    private function getProductRecommendationEntities(): array
    {
        $topFive = [-1];
        $predictClient = $this->getGooglePredictionServiceClient();
        try {
            $validateOnly = $this->googleHelper->getRecommendationsValidateOnly();
            $placement = $this->getPlacementData();
            $userEvent = $this->getUserEventData();
            $results = $predictClient->predict($placement, $userEvent, ['validateOnly' => $validateOnly]);
            $predictClient->close();
            $entityIds = [];
            if (!empty($results->getResults())) {
                foreach ($results->getResults()->getIterator() as $item) {
                    $entityIds[] = $item->getId();
                }
                $topFive = array_slice($entityIds, 0, 5, true);
            }
        } catch (Exception $e) {
            $this->logger->critical("Recs: " . $e->getMessage());
        }

        return $topFive;
    }

    /**
     * Return identifiers for produced content
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        return array_merge([], ...$identities);
        ;
    }


    /**
     * @return PredictionServiceClient
     */
    private function getGooglePredictionServiceClient(): PredictionServiceClient
    {
        $pid = getmypid();
        if (!isset($this->client[$pid])) {
            $this->client[$pid] = (new PredictionServiceClient);
        }
        return $this->client[$pid];
    }

    /**
     * @return UserEvent
     */
    private function getUserEventData(): UserEvent
    {
        $userEvent = (new UserEvent);
        try {
            $visitorId = $this->googleHelper->getVisitorId();
            $session_id = $this->googleHelper->getStorefrontSessionId();
            $event_type = $this->getData('event-type');
            $userEvent->setVisitorId($visitorId);
            $userEvent->setEventType($event_type); // For now either 'home-page' OR 'detail-page-view'
            $userEvent->setSessionId($session_id);
            if (!is_null($this->getData('product-id'))) {
                $prdId = $this->getData('product-id');
                $gProduct = (new Product);
                $productDetails = (new ProductDetail);
                $gProduct->setPrimaryProductId($prdId);
                $gProduct->setId($prdId);
                $productDetails->setProduct($gProduct);
                $userEvent->setProductDetails([$productDetails]);
            }
        } catch (LocalizedException $e) {
            $this->_logger->error("Google Recs - issue getting visitorId: " . $e->getMessage());
        }
        return $userEvent;
    }

    /**
     * @return string
     */
    public function getPlacementData():string
    {
        $placement_id = $this->getData('placement-id');
        $project_id = $this->googleHelper->getGoogleRetailProjectId();
        $catalog = $this->googleHelper->getGoogleRetailCatalogId();
        return "projects/$project_id/locations/global/catalogs/$catalog/placements/$placement_id";
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        return $this;
    }

    /**
     * Get key pieces for caching block content
     * @return array
     */
    public function getCacheKeyInfo(): array
    {
        $keyInfo     =  parent::getCacheKeyInfo();
        $keyInfo[]   =  'GOOGLE_PRODUCT_RECOMMENDATION_LIST';
        return $keyInfo;
    }

    /**
     * Get Key for caching block content
     * @return string
     */
    public function getCacheKey(): string
    {
        return parent::getCacheKey()
            . ($this->getData('placement-id'))
            . ($this->getData('event-type'))
            . ($this->getData('product-id'))
            . ($this->googleHelper->getVisitorId());
    }
}
