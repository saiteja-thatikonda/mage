<?php
namespace Request\Router\Controller\Test;

use Magento\Framework\App\Action\Context ;

class Index extends \Magento\Framework\App\Action\Action
{
	public function __construct(Context $context)
	{
		parent::__construct($context);
	}

	public function execute()
	{
		die('Custom new router');
	}
}
