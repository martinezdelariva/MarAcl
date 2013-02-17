<?php

namespace MarAcl\Model;

use MarAcl\Model\Role as MarAclRole;
use MarAcl\Model\Resource as MarAclResource;

class Rule
{
	protected $_id;
	protected $_permission;
	protected $_privileges;
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
	const PRIVILEGE_GET = 'get';
	const PRIVILEGE_POST = 'post';
	const PRIVILEGE_PUT = 'put';
	const PRIVILEGE_HEAD = 'head';
	const PRIVILEGE_TRACE = 'trace';
	const PRIVILEGE_OPTIONS = 'options';

	protected $_allowPrivileges = array(
		self::PRIVILEGE_GET,
		self::PRIVILEGE_POST,
		self::PRIVILEGE_PUT,
		self::PRIVILEGE_HEAD,
		self::PRIVILEGE_TRACE,
		self::PRIVILEGE_OPTIONS,
	);

    /**
     * Used by ResultSet to pass each database row to the entity
     */
    public function exchangeArray($data)
    {
        $this->setId((isset($data['id'])) ? $data['id'] : null);
        $this->setPermission((isset($data['permission'])) ? $data['permission'] : null);
        $this->setPrivileges((isset($data['privilege'])) ? $data['privilege'] : null);
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

	public function setPrivileges(array $privileges)
	{
		foreach($privileges as $item) {
			if (!in_array($item, $this->_allowPrivileges)) {
				throw new \Exception(
					"'$item' is not in the allow list :" . var_export($this->_allowPrivileges, true)
				);
			}
		}

		$this->_privileges = $privileges;
	}

	public function getPrivileges()
	{
		return $this->_privileges;
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
