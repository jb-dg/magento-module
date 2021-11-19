<?php
/**
 * Ajout des infos du bloc CMS "top-category-CategoryID" en haut de la categorie si il existe
 */

namespace Origines\Catalog\Plugin\Block\Category;

use Magento\Catalog\Block\Category\View as CategoryView;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Model\Block;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Store\Model\StoreManagerInterface;

class View
{
    protected  $blockRepository;
    protected  $searchCriteriaBuilder;
    protected  $filterProvider;
    protected  $storeManager;
    protected  $sortOrderBuilder;

    public function __construct(
        BlockRepositoryInterface $blockRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        FilterProvider $filterProvider,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->blockRepository = $blockRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->filterProvider = $filterProvider;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    private function getCmsBlock($idBlock): ?Block
    {
        $block = null;
        $storeId = $this->storeManager->getStore()->getId();
        $sortOrder = $this->sortOrderBuilder->setField('store_id')->setDirection('DESC')->create();
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('identifier', $idBlock,'eq')
            ->addFilter('store_id',[0,$storeId],'in')->setSortOrders([$sortOrder])->create();
        $cmsBlocks = $this->blockRepository->getList($searchCriteria)->getItems();
        if (count($cmsBlocks)>0){
            // Get first one
            $block = reset($cmsBlocks);
        }
        return $block;
    }

    public function afterSetLayout(CategoryView $categoryView, $result, $layout)
    {
        $categoryId = $categoryView->getCurrentCategory()->getId();
        $cmsBlock = $this->getCmsBlock('top-category-'.$categoryId);
        if (!empty($cmsBlock)){
            if (!$layout->hasElement($cmsBlock->getIdentifier())){
                $block = $layout->createBlock(
                    \Magento\Cms\Block\Block::class,
                    $cmsBlock->getIdentifier(),
                    ['data'=>['block_id'=>$cmsBlock->getIdentifier()]]
                );

                if ($layout->getBlock('category.description')) {
                    $layout->getBlock('category.description')->append($block);
                }
            }
        }
        return $result;
    }
}
