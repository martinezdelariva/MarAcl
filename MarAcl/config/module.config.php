<?php
return array(
	'MarAcl' => array(
		'authorize_provider' => 'Zend\Authentication\AuthenticationService',
		'default_role' 	=> 'guest',
		'data'	=> array(
			'roles'		=> array(
				array(
					'name' 		=> 'guest',
				),
				array(
					'name' 		=> 'user',
					'parents'	=>	array('guest')
				),
			),
			'resources'	=> array(
				array(
					'controller' => 'application\controller\index',
					'actions' 	 => array('index', 'other'),
				),
				array(
					'controller' => 'album\controller\album',
					'actions' 	 => array('index', 'add'),
				),
				array(
					'controller' => 'marimage\controller\image',
					'actions' 	 => 'index',
					'parents'	 => null,
				),
			),
			'rules'	=> array(
				'allow' => array(
					array(
						'role'			=> 'guest',
						'controller'	=> 'application\controller\index',
						'actions'		=> array('index'),
						'privilege'		=> 'read',
						'active' 		=> 1,
					),
					array(
						'role'			=> 'guest',
						'controller'	=> 'album\controller\album',
						'actions'		=> 'index',
						'privilege'		=> 'read',
						'active' 		=> 1,
					),
					array(
						'role'			=> 'user',
						'controller'	=> 'album\controller\album',
						'actions'		=> 'add',
						'privilege'		=> 'read',
						'active' 		=> 1,
					),
					array(
						'role'			=> 'guest',
						'controller'	=> 'marimage\controller\image',
						'actions'		=> 'index',
						'privilege'		=> 'read',
						'active' 		=> 1,
					),
				),
				'deny' => array(),
			),
		),
	),
);
