<?php

namespace Origines\Manufacturer\Controller\Adminhtml\Manufacturer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Origines\Manufacturer\Model\Manufacturer;
use Origines\Manufacturer\Model\ManufacturerFactory;
use Origines\Manufacturer\Model\ManufacturerRepository;
use Origines\Manufacturer\Service\ManufacturerImageService;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\App\ResponseInterface;

class Save extends Action
{
    /** @var ManufacturerRepository $manufacturerRepository */
    private $manufacturerRepository;

    /** @var ManufacturerFactory $manufacturerFactory */
    private $manufacturerFactory;

    /** @var ManufacturerImageService $manufacturerImageService */
    private $manufacturerImageService;

    /**
     * Save constructor.
     * @param Context $context
     * @param ManufacturerRepository $manufacturerRepository
     * @param ManufacturerFactory $manufacturerFactory
     * @param ManufacturerImageService $manufacturerImageService
     */
    public function __construct(
        Context $context,
        ManufacturerRepository $manufacturerRepository,
        ManufacturerFactory $manufacturerFactory,
        ManufacturerImageService $manufacturerImageService
    ) {
        $this->manufacturerRepository = $manufacturerRepository;
        $this->manufacturerFactory = $manufacturerFactory;
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
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->filterPostData($data);
            if (isset($data['entity_id'])) {
                $manufacturer = $this->manufacturerRepository->getById($data['entity_id']);
            } else {
                $manufacturer = $this->manufacturerFactory->create();
            }

            /** @var Manufacturer $manufacturer */
            $manufacturer->setData($data);
            try {
                $this->manufacturerRepository->save($manufacturer);
                if ($manufacturer->getImage()) {
                    $this->manufacturerImageService->moveImageFromTmp($manufacturer->getImage());
                }

                $this->messageManager->addSuccessMessage(__('Manufacturer saved'));
                $this->_getSession()->setFormData(false);

                return $resultRedirect->setPath('*/*/');
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->getRequest()->getParam('id')]);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Filter post data
     *
     * @param array $rawData
     * @return array
     */
    private function filterPostData(array $rawData)
    {
        $data = $rawData;
        if (isset($data['image']) && is_array($data['image'])) {
            if (!empty($data['image']['delete'])) {
                $data['image'] = null;
            } else {
                if (isset($data['image'][0]['name']) && isset($data['image'][0]['tmp_name'])) {
                    $data['image'] = $data['image'][0]['name'];
                } else {
                    unset($data['image']);
                }
            }
        } else {
            $data['image'] = null;
        }

        return $data;
    }
}
