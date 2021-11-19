<?php

namespace Origines\Manufacturer\Controller\Adminhtml\Manufacturer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Origines\Manufacturer\Model\ManufacturerRepository;
use Origines\Manufacturer\Service\ManufacturerImageService;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\App\ResponseInterface;

class Delete extends Action
{
    /** @var ManufacturerRepository $manufacturerRepository */
    private $manufacturerRepository;

    /** @var ManufacturerImageService $manufacturerImageService */
    private $manufacturerImageService;

    /**
     * @param Context $context
     * @param ManufacturerRepository $manufacturerRepository
     * @param ManufacturerImageService $manufacturerImageService
     */
    public function __construct(
        Context $context,
        ManufacturerRepository $manufacturerRepository,
        ManufacturerImageService $manufacturerImageService
    ) {
        $this->manufacturerRepository = $manufacturerRepository;
        $this->manufacturerImageService = $manufacturerImageService;

        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            try {
                $manufacturer = $this->manufacturerRepository->getById($id);
                $this->manufacturerImageService->deleteImage($manufacturer->getImage());
                $this->manufacturerRepository->deleteById($id);

                $this->messageManager->addSuccessMessage(__('Manufacturer deleted'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('Manufacturer does not exist'));
        return $resultRedirect->setPath('*/*/');
    }
}
