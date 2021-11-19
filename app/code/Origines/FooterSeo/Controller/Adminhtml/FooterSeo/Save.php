<?php

namespace Origines\FooterSeo\Controller\Adminhtml\FooterSeo;

use Magento\Backend\App\Action\Context;
use Origines\FooterSeo\Model\FooterSeoFactory;
use Origines\FooterSeo\Model\FooterSeoRepository;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\CouldNotSaveException;

class Save extends Action
{
    /**
     * @var FooterSeoFactory
     */
    protected $footerSeoFactory;

    /**
     * @var FooterSeoRepository
     */
    protected $footerSeoRepository;

    /**
     * Save constructor.
     * @param Context $context
     * @param FooterSeoFactory $footerSeoFactory
     * @param FooterSeoRepository $footerSeoRepository
     */
    public function __construct(
        Context $context,
        FooterSeoFactory $footerSeoFactory,
        FooterSeoRepository $footerSeoRepository
    ) {
        $this->footerSeoFactory = $footerSeoFactory;
        $this->footerSeoRepository = $footerSeoRepository;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return ResponseInterface|ResultInterface
     * @throws CouldNotSaveException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $data = $this->prepareDataBeforeSave($data);
        $model = $this->footerSeoFactory->create();
        if (isset($data['entity_id'])) {
            $model = $this->footerSeoFactory->create();
            $model->load($data['entity_id']);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This footer does not exist.'));
                return $this->_redirect('footerseo/footerSeo/index');
            }
        }

        try {
            $model->setData($data);
            $model->save();
            $this->messageManager->addSuccessMessage(__('You saved the footer.'));
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Impossible to save.'));
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $this->_redirect('footerseo/footerSeo/index');
    }

    /**
     * Prepare data before save
     *
     * @param $data
     * @return mixed
     */
    public function prepareDataBeforeSave($data)
    {
        $dataCategory = $data['type_id'];
        $categoryIdsString = "";
        if (is_array($dataCategory)) {
            foreach ($dataCategory as $value) {
                $categoryIdsString .= $value . ',';
            }
            $data['type_id'] = rtrim($categoryIdsString, ',');
        }
        $data['store_view'] = $data['store_view'][0];

        return $data;
    }
}
