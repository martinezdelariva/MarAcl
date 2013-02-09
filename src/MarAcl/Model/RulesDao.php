<?php

namespace MarAcl\Model;

use MarAcl\Model\RulesMapper;

class RulesDao
{
	/**
	 * @var RulesMapper
	 */
	protected $_dataMapper;

	public function __construct(RulesMapper $dataMapper)
	{
		$this->_dataMapper = $dataMapper;
	}

	public function findByType($type)
	{
		return $this->_dataMapper->findRulesByType($type);
	}

	public function findRoles()
	{
		return $this->_dataMapper->findRoles();
	}

	public function findResources()
	{
		return $this->_dataMapper->findResources();
	}

}
