<?php
return array(
	'view_manager' => array(
		'template_map' => array(
			'error/404'               => __DIR__ . '/../view/error/404.phtml',
			'error/403'            	  => __DIR__ . '/../view/error/403.phtml',
		),
		'template_path_stack' => array(
			__DIR__ . '/../view',
		),
	),
);
