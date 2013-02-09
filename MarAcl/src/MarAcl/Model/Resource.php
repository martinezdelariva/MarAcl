<?php

namespace MarAcl\Model;

use Zend\Permissions\Acl\Resource\ResourceInterface;

class Resource implements ResourceInterface
{
	const RESOURCE_SEPARATOR = '::';

	/**
	 * @var int
	 */
	protected $_id;

	/**
	 * @var string
	 */
	protected $_controller;

	/**
	 * @var string
	 */
	protected $_action;

	/**
	 * Array of resource code parents
	 *
	 * @var null|Resource[]
	 */
	protected $_parents;

	/**
	 * Number of level that current resource is downgrade.
	 * Sample:
	 * Level 0: resourceId is [controller]::[action]
	 * Level 1: resourceId is [controller]
	 *
	 * @var int
	 */
	protected $_downgradeLevels = 0;

    /**
     * Used by ResultSet to pass each database row to the entity
     */
    public function exchangeArray($data)
    {
        $this->setId((isset($data['id'])) ? $data['id'] : null);
        $this->setController((isset($data['controller'])) ? $data['controller'] : null);
        $this->setAction((isset($data['action'])) ? $data['action'] : null);

		if (isset($data['parents'])) {
			foreach ($data['parents'] as $item) {
				$resource = new Resource();
				$resource->exchangeArray($item);
				$this->_parents[] = $resource->getResourceCode();
			}
		} else {
			$this->_parents = null;
		}
    }

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string|null
	 */
	public function getResourceId()
	{
		// null resource
		if (is_null($this->getController())) {
			return null;
		}

		// Add Controller to resourceId
		$resourceId = mb_strtolower($this->getController(), 'UTF-8');

		// Add Action if exists and if not is downgraded
		if ($this->getDowngradeLevels() === 0 && $this->getAction()) {
			$resourceId .= self::RESOURCE_SEPARATOR . mb_strtolower($this->getAction(), 'UTF-8');
		}

		return $resourceId;
	}

	/**
	 * Increase level of resourceId
	 */
	public function downgrade()
	{
		$this->_downgradeLevels++;
	}

	public function setAction($action)
	{
		$this->_action = $action;
	}

	public function getAction()
	{
		return $this->_action;
	}

	public function setController($controller)
	{
		$this->_controller = $controller;
	}

	public function getController()
	{
		return $this->_controller;
	}

	public function setId($id)
	{
		$this->_id = $id;
	}

	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @param array $parents
	 */
	public function setParents($parents)
	{
		$this->_parents = $parents;
	}

	/**
	 * @return array
	 */
	public function getParents()
	{
		return $this->_parents;
	}

	/**
	 * @return int
	 */
	public function getDowngradeLevels()
	{
		return $this->_downgradeLevels;
	}
}
