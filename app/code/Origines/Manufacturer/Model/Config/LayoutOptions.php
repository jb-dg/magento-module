<?php

namespace Origines\Manufacturer\Model\Config;

use Magento\Catalog\Model\Category\Attribute\Source\Layout;

class LayoutOptions implements \Magento\Framework\Option\ArrayInterface
{
    /** @var Layout $source */
    private $source;

    /**
     * LayoutOption constructor.
     *
     * @param Layout $source
     */
    public function __construct(Layout $source)
    {
        $this->source = $source;
    }

    public function toOptionArray()
    {
        return $this->source->toOptionArray();
    }
}
