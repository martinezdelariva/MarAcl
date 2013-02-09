<?php
namespace MarAclTest\Acl;

use PHPUnit_Framework_TestCase;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManager;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Mvc\ApplicationInterface;

use MarAcl\Service\Acl as MarAcl;
use MarAcl\Listener\AclListener as MarAclListener;
use MarAcl\Model\Resource as MarAclResource;
use MarAcl\Model\Rule as MarAclRule;
use MarAclTest\Bootstrap;

error_reporting(E_ALL);

class MarAclTest extends PHPUnit_Framework_TestCase
{
	protected $controller;
	protected $request;
	protected $response;
	protected $routeMatch;
	protected $event;
	protected $application;

	/**
	 * @var MarAcl
	 */
	protected $_marAcl;

    public function setUp()
    {
		// Service Manager
		$serviceManager = Bootstrap::getServiceManager();
		$this->_marAcl = $serviceManager->get('maralc_acl');

		// Mock Application
		$this->application = $this->getMock('\Zend\Mvc\ApplicationInterface');
		$this->application
			->expects($this->any())
			->method('getServiceManager')
			->will($this->returnValue($serviceManager));
    }

	public function testRulesDeny()
	{
		$this->assertFalse($this->_marAcl->isAllowed('guest', 'controller_deny::action_deny'));
		$this->assertFalse($this->_marAcl->isAllowed('user', 'controller_deny::action_deny'));
		$this->assertFalse($this->_marAcl->isAllowed('admin', 'controller_deny::action_deny'));
	}

	public function testRulesPublic()
	{
		$this->assertTrue($this->_marAcl->isAllowed('guest', 'controller_public::action_public_1', 'read'));
		$this->assertTrue($this->_marAcl->isAllowed('user', 'controller_public::action_public_1', 'read'));
		$this->assertTrue($this->_marAcl->isAllowed('admin', 'controller_public::action_public_1', 'read'));
	}

	public function testRulesUser()
	{
		$this->assertFalse($this->_marAcl->isAllowed('guest', 'controller_user::action_user_1', 'read'));
		$this->assertTrue($this->_marAcl->isAllowed('user', 'controller_user::action_user_1', 'read'));
		$this->assertTrue($this->_marAcl->isAllowed('admin', 'controller_user::action_user_1', 'read'));

	}

	public function testRulesAdmin()
	{
		$this->assertFalse($this->_marAcl->isAllowed('guest', 'controller_admin::action_admin', 'read'));
		$this->assertFalse($this->_marAcl->isAllowed('user', 'controller_admin::action_admin', 'read'));
		$this->assertTrue($this->_marAcl->isAllowed('admin', 'controller_admin::action_admin', 'read'));
		$this->assertTrue($this->_marAcl->isAllowed('admin', 'controller_admin::action_admin', 'readwrite'));
	}

	public function testPrivilegesReadWrite()
	{
		$this->assertFalse($this->_marAcl->isAllowed('guest', 'controller_public::action_public_1', 'readwrite'));
		$this->assertTrue($this->_marAcl->isAllowed('user', 'controller_public::action_public_1', 'readwrite'));
		$this->assertTrue($this->_marAcl->isAllowed('admin', 'controller_public::action_public_1', 'readwrite'));
	}

	public function testPrivilegeUndefined()
	{
		$this->assertFalse($this->_marAcl->isAllowed('guest', 'controller_public::action_public_1'));
		$this->assertFalse($this->_marAcl->isAllowed('user', 'controller_public::action_public_1'));
	}

	public function testRulesInactive()
	{
		$this->assertFalse($this->_marAcl->isAllowed('guest', 'controller_inactive::action_inactive'));
		$this->assertFalse($this->_marAcl->isAllowed('user', 'controller_inactive::action_inactive'));

		// Admin must always has access but always indicating the privilege
		$this->assertTrue($this->_marAcl->isAllowed('admin', 'controller_inactive::action_inactive', 'readwrite'));
	}

	public function test404StatusCode()
	{
		// SM
		$serviceManager = Bootstrap::getServiceManager();

		// RouteMatch, Response
		$routeMatch = new RouteMatch(array('controller' => 'undefined_controller'));
		$response = new Response();

		// MvcEvent
		$mvcEvent = new MvcEvent();
		$mvcEvent->setRouteMatch($routeMatch);
		$mvcEvent->setApplication($this->application);
		$mvcEvent->setResponse($response);

		// Attach AclListener to EventManager
		/* @var $eventManager EventManager */
		$eventManager = $serviceManager->get('EventManager');
		/* @var $aclListener MarAclListener */
		$aclListener = $serviceManager->get('maracl_acl_listener');
		$aclListener->attach($eventManager);

		// Trigger Dispatch
		$eventManager->trigger(MvcEvent::EVENT_DISPATCH, $mvcEvent);

		// Response have to be 404
		$this->assertEquals($mvcEvent->getResponse()->getStatusCode(), '404');
	}

	public function test403StatusCode()
	{
		// SM
		$serviceManager = Bootstrap::getServiceManager();

		// RouteMatch, Response
		$routeMatch = new RouteMatch(array(
			'controller' => 'controller_deny',
			'action' => 'action_deny',
		));

		// Response and Request
		$response = new Response();
		$request = new Request();

		// MvcEvent
		$mvcEvent = new MvcEvent();
		$mvcEvent->setRouteMatch($routeMatch);
		$mvcEvent->setApplication($this->application);
		$mvcEvent->setResponse($response);
		$mvcEvent->setRequest($request);

		// Attach AclListener to EventManager
		/* @var $eventManager EventManager */
		$eventManager = $serviceManager->get('EventManager');
		/* @var $aclListener MarAclListener */
		$aclListener = $serviceManager->get('maracl_acl_listener');
		$aclListener->attach($eventManager);

		// Trigger Dispatch
		$eventManager->trigger(MvcEvent::EVENT_DISPATCH, $mvcEvent);

		// Response have to be 403
		$this->assertEquals($mvcEvent->getResponse()->getStatusCode(), '403');
	}
}
