<?php
namespace Origines\CustomNewsletters\Block\Newsletter\Adminhtml\Template\Grid\Renderer;

use Magento\Framework\DataObject;

class ContactName extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    const TYPE_GEST = 1;

    public function render(\Magento\Framework\DataObject $row)
    {
        $contactNameFull = '';

        if($row->getData('type') == self::TYPE_GEST){
            if (!empty($row->getData('subscriber_firstname'))) {
                $contactNameFull = $row->getData('subscriber_gender').' '.
                    $row->getData('subscriber_firstname').' '.
                    $row->getData('subscriber_lastname');
            } else {
                $contactNameFull = 'non renseigné';
            }
        }else{
           $contactNameFull  = $row->getData('firstname').' '.$row->getData('lastname');
        }

        return $contactNameFull;
    }
}
