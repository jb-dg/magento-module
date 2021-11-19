<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Origines\Customer\Plugin;

use Magento\Customer\Block\Widget\AbstractWidget;
use Magento\Customer\Block\Widget\Dob;
/**
 * Class Dob
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class PluginDob extends AbstractWidget
{
    /**
     * Return data-validate rules
     *
     * @return string
     */
    public function afterGetHtmlExtraParams(Dob $subject, $result)
    {
        $validators = [];
        if ($subject->isRequired()) {
            $validators['required'] = true;
        }
        $validators['validate-date'] = [
            'dateFormat' => $subject->getDateFormat()
        ];  
        $validators['validate-dob'] = [
            'dateFormat' => $subject->getDateFormat()
        ];

        return 'data-validate="' . $subject->_escaper->escapeHtml(json_encode($validators)) . '"';
    }
}