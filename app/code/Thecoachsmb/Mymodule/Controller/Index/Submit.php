<?php

namespace Thecoachsmb\Mymodule\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;

class Submit extends Action
{
    protected $resultPageFactory;


    public function __construct(
        Context $context,
        PageFactory $resultPageFactory

    )
    {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $data = (array)$this->getRequest()->getPost();
            if ($data) {


                $this->messageManager->addSuccessMessage(__("Data Saved Successfully."));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e, __("We can\'t submit your request, Please try again."));
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;

    }
}
