<?php
/*
 * Copyright (c) 2014 - 2021 TBD, SAS. All rights reserved.
 * Developer: Pierre-Louis HUBERT <pierre-louis.hubert@tbdgroup.com>
 */

namespace Origines\Config\Setup\Patch;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use TBD\Core\Setup\Tools\CmsBlock;
use TBD\Core\Setup\Tools\CmsPage;
use TBD\Core\Setup\Tools\Config;
use TBD\Core\Setup\Tools\Website;
use TBD\Core\Setup\Tools\StoreGroup;
use TBD\Core\Setup\Tools\Storeview;
use TBD\Core\Setup\Tools\Theme;

abstract class AbstractPatchData implements DataPatchInterface
{
    /**
     * @var $cmsBlock CmsBlock
     */
    protected $tbdCmsBlock;

    /**
     * @var CmsPage
     */
    protected $tbdCmsPage;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $dataSetup;

    /**
     * @var Config
     */
    protected $tbdConfig;

    /**
     * @var Website
     */
    protected $tbdWebsite;

    /**
     * @var StoreGroup
     */
    protected $tbdStoreGroup;

    /**
     * @var Storeview
     */
    protected $tbdStoreview;

    /**
     * @var Theme
     */
    protected $tbdTheme;

    public function __construct(
        ModuleDataSetupInterface $dataSetup, 
        CmsBlock $cmsBlock, CmsPage $cmsPage, 
        Config $tbdConfig, 
        Website $tbdWebsite, StoreGroup $tbdStoreGroup, Storeview $tbdStoreview,
        Theme $tbdTheme)
    {
        $this->tbdCmsBlock = $cmsBlock;
        $this->tbdCmsPage = $cmsPage;
        $this->dataSetup = $dataSetup;
        $this->tbdConfig = $tbdConfig;
        $this->tbdWebsite = $tbdWebsite;
        $this->tbdStoreGroup = $tbdStoreGroup;
        $this->tbdStoreview = $tbdStoreview;
        $this->tbdTheme = $tbdTheme;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

}
