<?php
namespace Thecoachsmb\Mymodule\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
class Extension extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Thecoachsmb\Mymodule\Model\ResourceModel\Extension');
    }
}
