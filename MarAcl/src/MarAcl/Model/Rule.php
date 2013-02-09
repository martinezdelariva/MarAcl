<?php

namespace MarAcl\Model;

use MarAcl\Model\Role as MarAclRole;
use MarAcl\Model\Resource as MarAclResource;

class Rule
{
	protected $_id;
	protected $_permission;
	protected $_privilege;
	protected $_active;

	/**
	 * @var null|MarAclRole
	 */
	protected $_role;

	/**
	 * @var null|MarAclResource
	 */
	protected $_resource;

	// Privilege
	const PRIVILEGE_READ = 'read';
	const PRIVILEGE_WRITE = 'readwrite';

	protected $_allowPrivileges = array(
		self::PRIVILEGE_READ,
		self::PRIVILEGE_WRITE,
		null,
	);

    /**
     * Used by ResultSet to pass each database row to the entity
     */
    public function exchangeArray($data)
    {
        $this->setId((isset($data['id'])) ? $data['id'] : null);
        $this->setPermission((isset($data['permission'])) ? $data['permission'] : null);
        $this->setPrivilege((isset($data['privilege'])) ? $data['privilege'] : null);
        $this->setActive((isset($data['active'])) ? $data['active'] : null);

		if (isset($data['role'])) {
			$role = new MarAclRole();
			$role->exchangeArray(array('role' => $data['role']));
			$this->setRole($role);
		} else {
			$this->setRole(null);
		}

		if (isset($data['controller'])) {
			$resource = new MarAclResource();
			$resource->exchangeArray(array(
				'controller' => $data['controller'],
				'action' 	 => $data['action'],
			));

			$this->setResource($resource);
		} else {
			$this->setResource(null);
		}
    }

	/**
	 * Return if the rule is active or not
	 *
	 * @return bool
	 */
	public function isActive()
	{
		return $this->getActive() ?  true : false;
	}

	/**
	 * Return all privileges depend on current privilege:
	 * 	'read' 		: 'read'
	 *	'readwrite' : 'read' 'readwrite'
	 * @return string|array
	 */
	public function getPrivilegesSet()
	{
		if ($this->getPrivilege() == self::PRIVILEGE_WRITE) {
			return array(self::PRIVILEGE_WRITE, self::PRIVILEGE_READ);
		}

		return $this->getPrivilege();
	}

	/* \/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\ */
	/* 				 			Getters and Setter 							 */
	/* \/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\ */

	public function setId($id)
	{
		$this->_id = $id;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setPermission($permission)
	{
		$this->_permission = $permission;
	}

	public function getPermission()
	{
		return $this->_permission;
	}

	public function setPrivilege($privilege)
	{
		if (!in_array($privilege, $this->_allowPrivileges)) {
			throw new \Exception(
				"'$privilege' is not in the allow list :" . var_export($this->_allowPrivileges, true)
			);
		}

		$this->_privilege = $privilege;
	}

	public function getPrivilege()
	{
		return $this->_privilege;
	}

	/**
	 * @param \MarAcl\Model\Resource $resource
	 */
	public function setResource($resource)
	{
		$this->_resource = $resource;
	}

	/**
	 * @return \MarAcl\Model\Resource
	 */
	public function getResource()
	{
		if ($this->_resource instanceof \Closure) {
			$this->_resource = call_user_func($this->_resource);
		}
		return $this->_resource;
	}

	/**
	 * @param \MarAcl\Model\Role $role
	 */
	public function setRole($role)
	{
		$this->_role = $role;
	}

	/**
	 * Returns aggregation (of roles)
	 *
	 * @return \MarAcl\Model\Role
	 */
	public function getRole()
	{
		if ($this->_role instanceof \Closure) {
			$this->_role = call_user_func($this->_role);
		}
		return $this->_role;
	}

	public function setActive($active)
	{
		$this->_active = $active;
	}

	public function getActive()
	{
		return $this->_active;
	}
}
