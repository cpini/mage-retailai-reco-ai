<?php

namespace RetailAI\Recommendations\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\Session\SessionManagerInterface;

class Recommendation implements SectionSourceInterface
{
    /**
     * @var SessionManagerInterface
     */
    protected SessionManagerInterface $sessionManager;

    /**
     * constructor
     * @param SessionManagerInterface $session
     */
    public function __construct(
        SessionManagerInterface $session
    ) {
        $this->sessionManager = $session;
    }

    /**
     * @inheritdoc
     * return array
     */
    public function getSectionData(): array
    {
        $this->sessionManager->start();
        $recs = $this->sessionManager->getGoogleRecommendations();
        if (is_array($recs) && !empty($recs)) {
            $retArr = [];
            $retArr['default'] = __('MAINTAIN');
            foreach ($recs as $k => $v) {
                if ($k !== "" && !is_array($k)) {
                    $retArr[$k] = gzdecode(base64_decode($v));
                }
            }
            return $retArr;
        }
        $this->sessionManager->unsGoogleRecommendations();
        return ['default' => __('RESET') ];
    }
}
