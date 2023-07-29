<?php

namespace RetailAI\Recommendations\Test\Integration\Controller\Recommendation;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\TestFramework\TestCase\AbstractController;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;

class IndexTest extends AbstractController
{
    const PDP_PI_PATH = 'catalog/google_retail/google_pdp_recommendation_placement_id';

    const PDP_ET_PATH = 'catalog/google_retail/google_pdp_recommendation_event_type';

    private mixed $scopeConfig;

    private mixed $serializer;

    protected string $httpMethod = HttpRequest::METHOD_POST;

    protected string $uri = 'googleretail/recommendation/index';

    protected function setUp(): void
    {
        parent::setUp();
        $this->scopeConfig = $this->_objectManager->get(ScopeConfigInterface::class);
        $this->serializer = $this->_objectManager->get(Json::class);
    }

    public function testExecuteAction()
    {
        $this->prepareRequest();
        $this->dispatch($this->uri);
        $json = $this->serializer->unserialize($this->getResponse()->getBody());
        $this->assertStringContainsString('products wrapper grid products-grid', $json['output']);
    }


    private function prepareRequest()
    {
        $placement  = $this->scopeConfig->getValue(self::PDP_PI_PATH, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $event_type = $this->scopeConfig->getValue(self::PDP_ET_PATH, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $this->getRequest()->setParams([
            'recommendation-key' => md5(microtime().rand()),
            'placement-id' => $placement,
            'event-type' => $event_type,
            'product-id' => 1840
        ]);
        $this->getRequest()->setMethod(HttpRequest::METHOD_POST);
    }
}
