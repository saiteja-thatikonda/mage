<?php
 
namespace webkul\hello\Block\Customer;
 
use Magento\Framework\View\Element\Template;
 
use Magento\Backend\Block\Template\Context;
 
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
 
class Form extends Template
 
{
 
&nbsp;&nbsp;&nbsp;protected $_countryCollectionFactory;
 
public function __construct(CollectionFactory $countryCollectionFactory,Context $context, array $data = [])
 
{
 
&nbsp;&nbsp;&nbsp;$this->_countryCollectionFactory = $countryCollectionFactory;
 
&nbsp;&nbsp;&nbsp;parent::__construct($context, $data);
 
}
 
public function getCountryCollection()
 
{
 
&nbsp;&nbsp;&nbsp;$collection = $this->_countryCollectionFactory->create()->loadByStore();
 
&nbsp;&nbsp;&nbsp;return $collection;
 
}
 
public function getCountries()
 
{
 
&nbsp;&nbsp;&nbsp;return $this->getCountryCollection()->toOptionArray();
 
}
 
}
 
?>
