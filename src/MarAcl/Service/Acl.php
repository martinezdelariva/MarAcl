<?php
namespace MarAcl\Service;

use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\EventManager\EventManager;

use MarAcl\Model\Role as MarAclRole;
use MarAcl\Model\Resource as MarAclResource;
use MarAcl\Model\Rule as MarAclRule;

use MarAcl\Model\RulesDao;

class Acl extends ZendAcl
{
	/**
	 * @var EventManager
	 */
	protected $_eventManager;

	/**
	 * @var RulesDao
	 */
	protected $_rulesDao;

	const ERROR_UNAUTHORIZED = 'error-unauthorized';

	/**
	 * Initialize roles, resources and rules
	 *
	 * @param \MarAcl\Model\RulesDao $rulesDao
	 */
	public function __construct(RulesDao $rulesDao)
	{
		// Hold RulesDao
		$this->_rulesDao = $rulesDao;

		// add roles
		foreach ($this->getRulesDao()->findRoles() as $role) {
			/* @var MarAclRole $role */
			$this->addRole($role, $role->getParents());
		}

		// add resources
		foreach ($this->getRulesDao()->findResources() as $resource) {
			/* @var MarAclResource $resource */
			$this->addResource($resource, $resource->getParents());
		}

		// Rules allow
		foreach ($this->getRulesDao()->findByType('allow') as $rule) {
			/* @var MarAclRule $rule */
			$this->allow(
				$rule->getRole()->getRoleId(),
				$rule->getResource() ? $rule->getResource()->getResourceId() : null,
				$rule->getPrivilegesSet()
			);
		}

		// Rules deny
		foreach ($this->getRulesDao()->findByType('deny') as $rule) {
			/* @var MarAclRule $rule */
			$this->deny(
				$rule->getRole()->getRoleId(),
				$rule->getResource() ? $rule->getResource()->getResourceId() : null,
				$rule->getPrivilegesSet()
			);
		}
	}

	/**
	 * Override in order to use lower case
	 *
	 * @param null|string|MarAclRole $role
	 * @param null|string|MarAclResource $resource
	 * @param null|string $privilege
	 * @return bool
	 */
	public function isAllowed($role = null, $resource = null, $privilege = null)
	{
		// Check is resource exists
		if (!$this->hasResource($resource, true)) {
			return false;
		}

		// Return
		return parent::isAllowed($role,	$resource,	$privilege);
	}

	/**
	 * Returns true if and only if the Resource exists in the ACL
	 *
	 * Resource can be downgrade optionally
	 *
	 * @param MarAclResource $resource
	 * @param bool $downgrade
	 * @return bool
	 */
	public function hasResource($resource, $downgrade = false)
	{
		// Exist $resource?
		$result = parent::hasResource($resource);

		// Return when $resource exist or cannot downgrade
		if ($result === true || $downgrade === false) {
			return $result;
		}

		// Downgrade $resource
		$resource->downgrade();

		// Try again with $resource downgrade
		return parent::hasResource($resource);
	}

	/**
	 * @return \MarAcl\Model\RulesDao
	 */
	public function getRulesDao()
	{
		return $this->_rulesDao;
	}
}
