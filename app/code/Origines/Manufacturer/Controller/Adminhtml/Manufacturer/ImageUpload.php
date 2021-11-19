<?php

namespace Origines\Manufacturer\Controller\Adminhtml\Manufacturer;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Origines\Manufacturer\Service\ManufacturerImageService;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ResponseInterface;
use Exception;

class ImageUpload extends Action
{
    /** @var ManufacturerImageService $manufacturerImageService */
    private $manufacturerImageService;

    /**
     * @param Context $context
     * @param ManufacturerImageService $manufacturerImageService
     */
    public function __construct(
        Context $context,
        ManufacturerImageService $manufacturerImageService
    ) {
        $this->manufacturerImageService = $manufacturerImageService;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $imageId = $this->_request->getParam('param_name', 'image');

        try {
            $result = $this->manufacturerImageService->saveFileToTmp($imageId);

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
