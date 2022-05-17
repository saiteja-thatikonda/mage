<?php

namespace Efi\BulkOrder\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class BulkOrders extends \Magento\Framework\App\Action\Action
{   
  protected $orderManagementFactory;
   
  protected $resultPageFactory;
    
   public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Efi\BulkOrder\Model\OrderManagement $orderManagementFactory
    ) {
         $this->resultPageFactory = $resultPageFactory;
         $this->orderManagementFactory = $orderManagementFactory;

         return parent::__construct($context);
    }

    public function execute()
    {
       $orderManagement = $this->orderManagementFactory->create();
   // echo "hi";
       $orderManagement->createOrder($orderInfo); 
        
    }
}
