<?php

namespace Efi\BulkOrder\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteManagement;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Store\Model\StoreManagerInterface;

class OrderManagement

{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var   ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var CustomerFactory
     */
    protected $customerRepository;
    /**
     * @var QuoteFactory
     */
    protected $quote;
    /**
     * @var QuoteManagement
     */
    protected $quoteManagement;
    /**
     * @var OrderSender
     */
    protected $orderSender;
    /**
     * @param StoreManagerInterface $storeManager
     * @param CustomerFactory $customerFactory
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param QuoteFactory $quote
     * @param QuoteManagement $quoteManagement
     * @param OrderSender $orderSender
     */
    public function __construct(


        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
    ) {
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->orderSender = $orderSender;
    }

    /**
     * @return array
     */
    public function orderInfo()
    {
        $orderInfo = [
            'currency_id' => 'USD',
            'email' => 'saiteja@gmail.com',
            'address' => [
                'firstname' => 'sai',
                'lastname' => 'Teja',
                'prefix' => '',
                'suffix' => '',
                'street' => 'B1 Abcd street',
                'city' => 'Los Angeles',
                'country_id' => 'US',
                'region' => 'California',
                'region_id' => '12',
                'postcode' => '45454',
                'telephone' => '1234512345',
                'fax' => '12345',
                'save_in_address_book' => 1
            ],
            'items' =>
                [
                    ['product_id' => '1', 'qty' => 1],
                    ['product_id' => '3', 'qty' => 2, 'super_attribute' => array(93 => 52, 142 => 167)]
                ]
        ];
        return $orderInfo;
    }

    /**
     * @param $orderInfo
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createOrder($orderInfo)
    {
        $store = $this->storeManager->getStore();
        $storeId = $store->getStoreId();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($orderInfo['email']);
        if (!$customer->getId()) {
            $customer->setWebsiteId($websiteId)
                ->setStore($store)
                ->setFirstname($orderInfo['address']['firstname'])
                ->setLastname($orderInfo['address']['lastname'])
                ->setEmail($orderInfo['email'])
                ->setPassword($orderInfo['email']);
            $customer->save();
        }
        $quote = $this->quote->create();
        $quote->setStore($store);
        $customer = $this->customerRepository->getById($customer->getId());
        $quote->setCurrency();
        $quote->assignCustomer($customer);
        foreach ($orderInfo['items'] as $item) {
            $product = $this->productRepository->getById($item['product_id']);
            if (!empty($item['super_attribute'])) {
                $buyRequest = new \Magento\Framework\DataObject($item);
                $quote->addProduct($product, $buyRequest);
            } else {
                $quote->addProduct($product, intval($item['qty']));
            }
        }
        $quote->getBillingAddress()->addData($orderInfo['address']);
        $quote->getShippingAddress()->addData($orderInfo['address']);
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('flatrate_flatrate');
        $quote->setPaymentMethod('checkmo');
        $quote->setInventoryProcessed(false);
        $quote->save();
        $quote->getPayment()->importData(['method' => 'checkmo']);
        $quote->collectTotals()->save();
        $order = $this->quoteManagement->submit($quote);
        $this->orderSender->send($order);
        $orderId = $order->getIncrementId();
        if ($orderId) {
            $result['success'] = $orderId;
        } else {
            $result = ['error' => true, 'msg' => 'Error occurs for Order placed'];
        }
        return $result;
    }
}
