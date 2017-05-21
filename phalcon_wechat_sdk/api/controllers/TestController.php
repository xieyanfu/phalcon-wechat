<?php
/**
User: pangxb
 * Date: 16/9/13
 * Time: 下午1:05
 */
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Model\Query;
trait TestController
{
	public function indexAction(){
		test::test();
		echo 'testController indexAction';
	}
	
	public function testAction(){
		test::test2();
		echo 'testController testAction';
	}
	
}


?>
