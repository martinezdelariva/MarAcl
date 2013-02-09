<?php
return array(
    'modules' => array(
        'MarAcl',
    ),
	'module_listener_options' => array(
		'config_glob_paths'    => array(
			__DIR__ . '/MarAclConfig.php',
		),
	),
);
