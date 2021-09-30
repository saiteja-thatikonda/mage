<?php
namespace Thecoachsmb\Mymodule\Model\ResourceModel\Extension;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Thecoachsmb\Mymodule\Model\Extension', 'Thecoachsmb\Mymodule\Model\ResourceModel\Extension');
    }
}
