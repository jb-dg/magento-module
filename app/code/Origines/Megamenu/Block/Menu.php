<?php

/*
 * Copyright (c) 2014 - 2021 TBD, SAS. All rights reserved.
 * Developer: Pierre-Louis HUBERT <pierre-louis.hubert@tbdgroup.com>
 */

namespace Origines\Megamenu\Block;

use Blackbird\MenuManager\Api\MenuRepositoryInterface;
use Blackbird\MenuManager\Api\NodeRepositoryInterface;
use Blackbird\MenuManager\Block\Menu as BlackbirdMenu;
use Blackbird\MenuManager\Helper\MenuSingleton;
use Blackbird\MenuManager\Model\NodeTypeProvider;
use Magento\Framework\View\Element\Template\Context;

class Menu extends BlackbirdMenu
{
    /**
     * Menu constructor.
     *
     * @param Context $context
     * @param \Blackbird\MenuManager\Api\MenuRepositoryInterface $menuRepository
     * @param \Blackbird\MenuManager\Api\NodeRepositoryInterface $nodeRepository
     * @param \Blackbird\MenuManager\Model\NodeTypeProvider $nodeTypeProvider
     * @param MenuSingleton $singletonMenu
     * @param array $data
     */
    public function __construct(
        Context $context,
        MenuRepositoryInterface $menuRepository,
        NodeRepositoryInterface $nodeRepository,
        NodeTypeProvider $nodeTypeProvider,
        MenuSingleton $singletonMenu,
        array $data = []
    ) {
        parent::__construct($context, $menuRepository, $nodeRepository, $nodeTypeProvider, $singletonMenu, $data);
        $this->setData('cache_lifetime', 3600);
    }
}
