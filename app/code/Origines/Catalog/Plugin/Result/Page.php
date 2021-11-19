<?php
namespace Origines\Catalog\Plugin\Result;

use Magento\Framework\App\ResponseInterface;

class Page
{
  private $context;
  private $registry;

  public function __construct(
  \Magento\Framework\View\Element\Context $context,
  \Magento\Framework\Registry $registry
  ) {
     $this->context = $context;
     $this->registry = $registry;
  }

  public function beforeRenderResult(
  \Magento\Framework\View\Result\Page $subject,
  ResponseInterface $response
  ){

  

  if($this->context->getRequest()->getFullActionName() == 'catalog_product_view'){
   
     $_product = $this->registry->registry('current_product');

     $_manufacturerId = $_product->getManufacturer();

     $subject->getConfig()->addBodyClass('category-brand-'.$_manufacturerId);

     if($this->registry->registry('current_category')){
        $_category = $this->registry->registry('current_category');
        $subject->getConfig()->addBodyClass('product-category-'.$_category->getId());
     }
  }

  return [$response];
  }
}