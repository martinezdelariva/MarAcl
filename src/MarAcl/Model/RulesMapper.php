<?php

namespace MarAcl\Model;

use MarAcl\Model\Rule as MarRule;
use MarAcl\Model\Resource as MarResource;
use MarAcl\Model\Role as MarRole;

class RulesMapper
{
	/**
	 * @var array
	 */
	protected $_data;

	public function __construct(array $data)
	{
		$this->_data = $data;
	}

	public function findRulesByType($type, $onlyActive = true)
	{
		$rules = array();
		foreach ($this->_data['rules'][$type] as $row) {
			// add permission provided
			$row['permission'] = $type;
			foreach ($this->mapRulesRowToObject($row) as $rule) {
				if ($onlyActive && $rule->isActive()) {
					$rules[] = $rule;
				}
			}
		}
		return $rules;
	}

	public function findRoles()
	{
		$roles = array();
		foreach ($this->_data['roles'] as $row) {
			$roles[] = $this->mapRoleRowToObject($row, $this);
		}

		return $roles;
	}

	public function findResources()
	{
		$resources = array();
		foreach ($this->_data['resources'] as $row) {
			foreach ($this->mapResourcesRowToObject($row) as $resource ) {
				$resources[] = $resource;
			}
		}
		return $resources;
	}

	/**
	 * @param string $name
	 * @return null|array
	 */
	public function findRoleByName($name)
	{
		foreach ($this->_data['roles'] as $item) {
			if ($name == $item['name']) {
				return $item;
			}
		}

		return null;
	}

	/**
	 * @param string $controller
	 * @return null|array
	 */
	public function findResourceByController($controller)
	{
		foreach ($this->_data['resources'] as $item) {
			if ($controller == $item['controller']) {
				return $item;
			}
		}

		return null;
	}

	/**
	 * @param array $row
	 * @return Role
	 */
	public function mapRoleRowToObject(array $row)
	{
		static $roles = array();

		if (!array_key_exists($row['name'], $roles)) {
			$role = new MarRole();
			$role->setId(isset($row['id']) ? $row['id'] : null);
			$role->setName($row['name']);

			if (isset($row['parents'])) {
				$parents = array();
				foreach ($row['parents'] as $item) {
					// Get parent row
					$rowParent = $this->findRoleByName($item);

					$roleParent = new MarRole();
					$roleParent->setId(isset($rowParent['id']) ? $rowParent['id'] : null);
					$roleParent->setName($rowParent['name']);

					$parents[] = $roleParent;
				}
				$role->setParents($parents);
			}

			$roles[$row['name']] = $role;
		}

		return $roles[$row['name']];
	}

	/**
	 * @param array $row
	 *
	 * @throws \Exception
	 * @return Resource[]
	 */
	public function mapResourcesRowToObject(array $row)
	{
		// 'actions' key is
		// - undefined
		if (!isset($row['actions'])) {
			$actions = array(null);
		// - string
		} elseif (is_string($row['actions'])) {
			$actions = array($row['actions']);
		// - array
		} elseif (is_array($row['actions'])) {
			$actions = $row['actions'];
		} else {
			throw new \Exception(
				"Error: type of var 'action' " . var_export($row['actions'], true). " not supported"
			);
		}

		// Holds resources created
		$resources = array();

		// Create a resource for each action
		foreach ($actions as $action) {
			$resource = new MarResource();
			$resource->setId(isset($row['id']) ? $row['id'] : null);
			$resource->setController($row['controller']);
			$resource->setAction($action);

			if (isset($row['parents'])) {
				$parents = array();
				foreach ($row['parents'] as $item) {
					// Find parent resource
					$rowParent = $this->findResourceByController($item);

					$resourceParent = new MarResource();
					$resourceParent->setId(isset($rowParent['id']) ? $rowParent['id'] : null);
					$resourceParent->setController($rowParent['controller']);
					$resourceParent->setAction($rowParent['action']);

					$parents[] = $resourceParent;
				}
				$resource->setParents($parents);
			}

			$resources[] = $resource;
		}

		return $resources;
	}

	/**
	 * @param array $row
	 * @return Rule[]
	 */
	public function mapRulesRowToObject(array $row)
	{
		// Holds set of rules
		$rules = array();
		// Create a rule for each resource
		foreach ($this->mapResourcesRowToObject($row) as $resource) {
			// New rule
			$rule = new MarRule();
			$rule->setId(isset($row['id']) ? $row['id'] : null);
			$rule->setPermission($row['permission']);
			$rule->setResource($resource);
			$rule->setRole($this->mapRoleRowToObject(array('name' => $row['role'])));
			$rule->setActive(isset($row['active'])? $row['active'] : 1); // Default active

			// Privilege
			if (isset($row['privilege'])) {
				if (is_array($row['privilege'])) {
					foreach ($row['privilege'] as $item){
						$privileges = mb_strtoupper($item, 'UTF-8');
					}
				} else {
					$privileges = array(mb_strtoupper($row['privilege'], 'UTF-8'));
				}

			// Default 'GET' privilege
			} else {
				$privileges = array(Rule::PRIVILEGE_GET);
			}
			$rule->setPrivileges($privileges);

			// Hold single rule
			$rules[] = $rule;
		}

		return $rules;
	}
}
