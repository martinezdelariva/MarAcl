<?php

namespace MarAcl;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceManager;

use MarAcl\Service\Acl;
use MarAcl\Listener\AclListener;
use MarAcl\Model\RulesDao;
use MarAcl\Model\RulesMapper;
use MarAcl\Model\Resource as MarAclResource;
use MarAcl\Model\Role as MarAclRole;
use MarAcl\Model\Rule as MarAclRule;

class Module implements
    AutoloaderProviderInterface,
    BootstrapListenerInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
	public function onBootstrap(EventInterface $e)
	{
		/* @var ServiceManager $sm */
		$sm = $e->getApplication()->getServiceManager();

		/* @var EventManager $eventManager */
		$eventManager = $e->getApplication()->getEventManager();

		/* @var AclListener $aclListener */
		// Attach aclListener to eventManager
		$aclListener = $sm->get('maracl_acl_listener');
		$aclListener->attach($eventManager);
	}

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

	public function getServiceConfig()
	{
		return array(
			'invokables' 	=> array(
				'maracl_acl_listener' => 'MarAcl\Listener\AclListener',
			),
			'factories' 	=> array(
				'maralc_acl' => function(\Zend\ServiceManager\ServiceManager $sm) {
					$acl = new Acl($sm->get('maracl_rules_dao'));
					return $acl;
				},
				'maracl_auth_service' => function(\Zend\ServiceManager\ServiceManager $sm) {
					$config = $sm->get('config');
					$authorizeClass = $config['MarAcl']['authorize_provider'];
					return new $authorizeClass;
				},
				'maracl_rules_dao' => function(\Zend\ServiceManager\ServiceManager $sm) {
					$rulesMapper = $sm->get('maracl_rules_mapper');
					return new RulesDao($rulesMapper);
				},
				'maracl_rules_mapper' => function(\Zend\ServiceManager\ServiceManager $sm) {
					$config = $sm->get('config');
					return new RulesMapper($config['MarAcl']['data']);
				},
			),
		);
	}
}
