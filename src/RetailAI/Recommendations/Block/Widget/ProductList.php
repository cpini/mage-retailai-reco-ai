<?php

namespace RetailAI\Recommendations\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class ProductList extends Template implements BlockInterface
{
    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = "widget/product_list.phtml";
}
