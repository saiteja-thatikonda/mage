<?php

namespace Efi\BulkOrder\Controller\Index;

class BulkOrder  extends \Magento\Framework\App\Action\Action {

    
    protected $resultPageFactory;

    
    protected $orderRepository;

    
    protected $storeManager;

    
    protected $customerFactory;

    
    protected $productRepository;

    
    protected $customerRepository;

    
    protected $quote;

    
    protected $quoteManagement;

    
    protected $orderSender;

    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->orderRepository = $orderRepository;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->orderSender = $orderSender;
        parent::__construct($context);
    }
    
    public function execute() {
        $orderInfo =[
            'email'        => 'saiteja@gmail.com',
            'currency_id'  => 'USD',
            'address' =>[
                'firstname'    => 'sai',
                'lastname'     => 'teja',
                'prefix' => '',
                'suffix' => '',
                'street' => 'Test Street',
                'city' => 'Miami',
                'country_id' => 'US',
                'region' => 'Florida',
                'region_id' => '18', 
                'postcode' => '98651',
                'telephone' => '1234567890',
                'fax' => '1234567890',
                'save_in_address_book' => 1
            ],
            'items'=>
                [
                    
                    [
                        'product_id' => '1',
                        'qty' => 1
                    ],
                    
                    [
                        'product_id' => '2',
                        'qty' => 2,
                        'super_attribute' => [
                            93 => 52,
                            142 => 167
                        ]
                    ]
                ]
        ];
        $store = $this->storeManager->getStore();
        $storeId = $store->getStoreId();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customer = $this->customerFactory->create()
        ->setWebsiteId($websiteId)
        ->loadByEmail($orderInfo['email']); 
        if(!$customer->getId()){
            
            $customer->setStore($store)
                    ->setFirstname($orderInfo['address']['firstname'])
                    ->setLastname($orderInfo['address']['lastname'])
                    ->setEmail($orderInfo['email'])
                    ->setPassword('admin123');
            $customer->save();
        }
        $quote = $this->quote->create();
        $quote->setStore($store); 
        
        
        $customer = $this->customerRepository->getById($customer->getId());
        $quote->setCurrency();
        $quote->assignCustomer($customer); 
 
        
        foreach($orderInfo['items'] as $item){
            $product=$this->productRepository->getById($item['product_id']);
            if(!empty($item['super_attribute']) ) {
                
                $buyRequest = new \Magento\Framework\DataObject($item);
                $quote->addProduct($product,$buyRequest);
            } else {
                
                $quote->addProduct($product,intval($item['qty']));
            }
        }
 
        
        $quote->getBillingAddress()->addData($orderInfo['address']);
        $quote->getShippingAddress()->addData($orderInfo['address']);
 
        
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates()
                        ->setShippingMethod('freeshipping_freeshipping'); 
        $quote->setPaymentMethod('checkmo'); 
        $quote->setInventoryProcessed(false);
        $quote->save();
        $quote->getPayment()->importData(['method' => 'checkmo']);
 
        
        $quote->collectTotals()->save();
        
        $order = $this->quoteManagement->submit($quote);
        
        $this->orderSender->send($order);
       
        $orderId = $order->getIncrementId();
        if($orderId){
            $result['success'] = $orderId;
        } else {
            $result = [ 'error' => true,'msg' => 'Error occurs for Order placed'];
        }
        print_r($result);
    }

}
