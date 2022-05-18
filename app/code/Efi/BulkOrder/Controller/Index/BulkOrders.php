<?php

namespace Efi\BulkOrder\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class BulkOrders extends \Magento\Framework\App\Action\Action
{
    protected $orderManagementFactory;
    protected $resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context      $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Efi\BulkOrder\Model\OrderManagement       $orderManagementFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->orderManagementFactory = $orderManagementFactory
        parent::__construct($context);
    }

    public function execute()
    {
        $orderInfo=$this->orderManagementFactory->orderInfo();
        $this->orderManagementFactory->createOrder($orderInfo);
    }
}
