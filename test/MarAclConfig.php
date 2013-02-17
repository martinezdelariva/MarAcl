<?php
return array(
	'MarAcl' => array(
		'authorize_provider' => 'Zend\Authentication\AuthenticationService',
		'default_role' 	=> 'guest',
		'data' => array(
			// Roles
			'roles'		=> array(
				array(
					'name' 		=> 'guest',
				),
				array(
					'name' 		=> 'user',
					'parents'	=>	array('guest')
				),
				array(
					'name' 		=> 'admin',
				),
			),
			// Resources
			'resources' => array(
				array(
					'controller' => 'controller_public',
					'actions' 	 => array('action_public_1', 'action_public_2'),
//					'parents'	 => null,
				),
				array(
					'controller' => 'controller_user',
					'actions' 	 => 'action_user_1',
					'parents'	 => null,
				),
				array(
					'controller' => 'controller_admin',
					'actions' 	 => 'action_admin',
					'parents'	 => null,
				),
				array(
					'controller' => 'controller_inactive',
					'actions' 	 => 'action_inactive',
					'parents'	 => null,
				),
				array(
					'controller' => 'controller_deny',
					'actions' 	 => 'action_deny',
					'parents'	 => null,
				),
			),
			// Rules
			'rules' => array(
				'allow' => array(
					array(
						'role'			=> 'guest',
						'controller'	=> 'controller_public',
						'actions'		=> array('action_public_1', 'action_public_2'),
						'privilege'		=> array('get'),
					),
					array(
						'role'			=> 'user',
						'controller'	=> 'controller_public',
						'actions'		=> 'action_public_1',
					),
					array(
						'role'			=> 'user',
						'controller'	=> 'controller_user',
						'actions'		=> 'action_user_1',
						'privilege'		=> 'get',
					),
					array(
						'role'			=> 'guest',
						'controller'	=> 'controller_inactive',
						'actions'		=> 'action_inactive',
						'privilege'		=> 'get',
						'active' 		=> 0,
					),
					array(
						'role'			=> 'admin',
						'controller'	=> null,
						'actions'		=> null,
					),
				),
				'deny' => array(
					array(
						'role'			=> 'guest',
						'controller'	=> 'controller_deny',
						'actions'		=> 'action_deny',
						'privilege'		=> 'get',
					),
					array(
						'role'			=> 'user',
						'controller'	=> 'controller_deny',
						'actions'		=> 'action_deny',
						'privilege'		=> 'get',
					),
					array(
						'role'			=> 'admin',
						'controller'	=> 'controller_deny',
						'actions'		=> 'action_deny',
						'privilege'		=> 'get',
					),
				),
			),
		),
	),
);
