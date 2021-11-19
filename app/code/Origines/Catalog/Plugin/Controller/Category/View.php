<?php
/**
 * Detection d'une page nommée "landing-marque-CategoryID", puis redirection si elle existe
 * Necessite une page par store
 */

namespace Origines\Catalog\Plugin\Controller\Category;

use Magento\Catalog\Controller\Category\View as CategoryView;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Helper\Page;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Store\Model\StoreManagerInterface;

class View
{
    protected $pageRepository;
    protected $searchCriteriaBuilder;
    protected $helperPage;
    protected $storeManager;
    protected $resultRedirectFactory;
    protected $sortOrderBuilder;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Page $helperPage,
        StoreManagerInterface $storeManager,
        RedirectFactory $resultRedirectFactory,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->pageRepository = $pageRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->helperPage = $helperPage;
        $this->storeManager = $storeManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    public function aroundExecute(CategoryView $categoryView, callable $proceed) 
    {
        if (!empty($categoryView->getRequest()->getParam('id'))) {
            $identifier = 'landing-category-' . $categoryView->getRequest()->getParam('id');
            $storeId = $this->storeManager->getStore()->getId();
            $sortOrder = $this->sortOrderBuilder->setField('store_id')->setDirection('DESC')->create();
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('identifier', $identifier, 'eq')
                ->addFilter('store_id',[0,$storeId],'in')->setSortOrders([$sortOrder])->create();
            $cmsPages = $this->pageRepository->getList($searchCriteria)->getItems();
            if (count($cmsPages) > 0) {
                // Keep only first one found
                $cmsPage = reset($cmsPages);
                $url = $this->helperPage->getPageUrl($cmsPage->getId());
                return $this->resultRedirectFactory->create()->setUrl($url);
            }
        }
        return $proceed();
    }
}
