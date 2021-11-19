<?php

namespace Origines\FooterSeo\Model\FooterSeo;

use Magento\Framework\Data\OptionSourceInterface;

class OptionsType implements OptionSourceInterface
{
    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => 'Fiche produit', 'value' => 'catalog_product_view'],
            ['label' => 'Liste/Marque', 'value' => 'catalog_category_view']
        ];
    }
}
