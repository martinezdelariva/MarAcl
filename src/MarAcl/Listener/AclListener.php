<?php
namespace MarAcl\Listener;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

use MarAcl\Service\Acl as MarAcl;
use MarAcl\Model\Resource as MarAclResource;
use MarAcl\Model\Role as MarAclRole;
use MarAcl\Model\Rule as MarAclRule;

class AclListener implements  ListenerAggregateInterface
{
	protected $_listeners = array();

	/**
	 * Attach one or more listeners
	 *
	 * Implementors may add an optional $priority argument; the EventManager
	 * implementation will pass this to the aggregate.
	 *
	 * @param EventManagerInterface $events
	 */
	public function attach(EventManagerInterface $events)
	{
		$this->_listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'initAcl'), 100);
	}

	/**
	 * Detach all previously attached listeners
	 *
	 * @param EventManagerInterface $events
	 */
	public function detach(EventManagerInterface $events)
	{
		foreach ($this->_listeners as $index => $listener) {
			if ($events->detach($listener)) {
				unset($this->_listeners[$index]);
			}
		}
	}

	public function initAcl(MvcEvent $e)
	{
		/* @var \Zend\Mvc\Application $app */
		$app = $e->getApplication();

		// Get SM
		$sm = $app->getServiceManager();

		/* @var MarAcl $marAcl*/
		$marAcl = $sm->get('maralc_acl');

		// Get params 'controller', 'action' and 'privilege' from route match
		$matches = $e->getRouteMatch();

		// Resource based on request params
		$resource = new MarAclResource();
		$resource->setController($matches->getParam('controller'));
		$resource->setAction($matches->getParam('action', 'index'));

		// 404 response if resource does not exist
		if (!$marAcl->hasResource($resource, true)) {
			$e->getResponse()->setStatusCode(404);
			return;
		}

		// Get config for MarAcl
		$config = $sm->get('config');;
		$configMarAcl = $config['MarAcl'];

		// Role
		$auth = $sm->get('maracl_auth_service');

		$role = new MarAclRole();
		$role->setName($auth->hasIdentity() ?
			$auth->getIdentity()->$configMarAcl['field_role'] :
				$configMarAcl['default_role']
		);

		// Query ACL
		$result = $marAcl->isAllowed($role, $resource, $e->getRequest()->getMethod());

		// 403 Unauthorized
		if ($result === false) {
			// Create ViewModel
			$model = new \Zend\View\Model\ViewModel();
			$model->setTemplate('error/403');
			$model->setVariable('reason', $marAcl::ERROR_UNAUTHORIZED);

			// Add $model as a child and set 403 status code
			$e->getViewModel()->addChild($model);
			$e->getResponse()->setStatusCode(403);

			// Stop propagation
			$e->stopPropagation();
			return;
		}
	}
}
