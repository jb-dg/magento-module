<?php
/*
 * Copyright (c) 2014 - 2021 TBD, SAS. All rights reserved.
 * Developer: Pierre-Louis HUBERT <pierre-louis.hubert@tbdgroup.com>
 */

namespace Origines\Config\Setup\Patch\Data;

use Origines\Config\Setup\Patch\AbstractPatchData;
use Magento\Widget\Model\Widget\InstanceFactory;
use Magento\Framework\App\State;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;

class TopBannerWidget extends AbstractPatchData
{
    /**
     * @var \Magento\Widget\Model\Widget\InstanceFactory
     */
    protected $widgetFactory;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Magento\Cms\Api\BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * TopBandeau constructor
     *
     * @param \TBD\Core\Setup\Tools\CmsBlock $cmsBlock
     * @param \TBD\Core\Setup\Tools\CmsPage $cmsPage
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $dataSetup
     * @param \TBD\Core\Setup\Tools\Config $tbdConfig
     * @param \TBD\Core\Setup\Tools\Website $tbdWebsite
     * @param \TBD\Core\Setup\Tools\StoreGroup $tbdStoreGroup
     * @param \TBD\Core\Setup\Tools\Storeview $tbdStoreview
     * @param \TBD\Core\Setup\Tools\Theme $tbdTheme
     * @param InstanceFactory $widgetFactory
     * @param State $state
     * @param BlockRepositoryInterface $blockRepository
     * @param ThemeProviderInterface $themeProvider
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $dataSetup,
        \TBD\Core\Setup\Tools\CmsBlock $cmsBlock, \TBD\Core\Setup\Tools\CmsPage $cmsPage,
        \TBD\Core\Setup\Tools\Config $tbdConfig,
        \TBD\Core\Setup\Tools\Website $tbdWebsite, \TBD\Core\Setup\Tools\StoreGroup $tbdStoreGroup, \TBD\Core\Setup\Tools\Storeview $tbdStoreview,
        \TBD\Core\Setup\Tools\Theme $tbdTheme,
        InstanceFactory $widgetFactory,
        State $state,
        BlockRepositoryInterface $blockRepository,
        ThemeProviderInterface $themeProvider
    )
    {
        parent::__construct(
            $dataSetup,
            $cmsBlock, $cmsPage,
            $tbdConfig,
            $tbdWebsite, $tbdStoreGroup, $tbdStoreview,
            $tbdTheme
        );
        $this->widgetFactory    = $widgetFactory;
        $this->state            = $state;
        $this->blockRepository  = $blockRepository;
        $this->themeProvider    = $themeProvider;
    }

    /**
     * @inheritDoc
     * @phpcs:disable Generic.CodeAnalysis.EmptyStatement.DetectedCatch
     */
    public function apply()
    {
        $this->dataSetup->startSetup();

        //--> Ajout des blocs CMS
        $this->tbdCmsBlock->saveCmsBlock(['banner-top-site'], 'Origines_Config');

        try {
            $this->state->setAreaCode('adminhtml');
        } catch (\Exception $e) {

        }

        $this->createWidget('Top Banner', 'banner-top-site', 'origines.header.top_banner');

        $this->dataSetup->endSetup();

        return $this;
    }

    /**
     * Créée le widget pour injecter le block cms
     *
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createWidget($title, $blockCode, $layoutBlock)
    {
        $cmsBlock   = $this->blockRepository->getById($blockCode);
        $theme      = $this->themeProvider->getThemeByFullPath('frontend/Sutunam/parfums');
        $widgetData = [
            'instance_type'     => \Magento\Cms\Block\Widget\Block::class,
            'instance_code'     => 'cms_static_block',
            'theme_id'          => $theme->getId(),
            'title'             => $title,
            'store_ids'         => '0',
            'widget_parameters' => '{"block_id":"' . $cmsBlock->getIdentifier() . '"}',
            'sort_order'        => 0,
            'page_groups'       => [
                [
                    'page_id'       => 1,
                    'page_group'    => 'all_pages',
                    'layout_handle' => 'default',
                    'for'           => 'all',
                    'all_pages' => [
                        'page_id'           => null,
                        'layout_handle'     => 'default',
                        'block'             => $layoutBlock,
                        'for'               => 'all',
                        'template'          => 'widget/static_block/default.phtml',
                    ],
                ],
            ],
        ];
        $this->widgetFactory->create()->setData($widgetData)->save();
    }
}
