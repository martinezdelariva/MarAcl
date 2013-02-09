<?php

namespace MarAcl\Model;

use Zend\Permissions\Acl\Role\RoleInterface;

class Role implements RoleInterface
{
	/**
	 * @var int
	 */
	protected $_id;

	/**
	 * @var string
	 */
	protected $_name;

	/**
	 * @var null|Role[]
	 */
	protected $_parents;

//    /**
//     * Used by ResultSet to pass each database row to the entity
//     */
//    public function exchangeArray($data)
//    {
//        $this->setId((isset($data['id'])) ? $data['id'] : null);
//        $this->setName((isset($data['name'])) ? $data['name'] : null);
//        $this->setParents((isset($data['parents'])) ? $data['parents'] : null);
//    }

	public function setParents($parents)
	{
		$this->_parents = $parents;
	}

	public function getParents()
	{
		return $this->_parents;
	}

	public function setId($id)
	{
		$this->_id = $id;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setName($name)
	{
		$this->_name = $name;
	}

	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Returns the string identifier of the Role
	 *
	 * @return string
	 */
	public function getRoleId()
	{
		return $this->getName();
	}
}
