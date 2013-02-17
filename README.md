# MarAcl

A ZF2 module for "ACL" made easy.

## Overview
Configure

In most ZF2 applications, you'll have at least differents roles that you need to manage which resources they can see.

This module allow you to configure ACL depending on:

- Roles
- Resources: Controller and Actions.
- Privileges: HTTP methods (GET, POST…)

When the Request doesn't pass the ACL then the Response could be:
- 403 Unathorized: curret role is not allowed to `controller` and `action` extracted from Request.
- 404 Not found: Resource with `controller` and `action` extracted from Request doesn't exists. 


## Installation

### Source download

Grab a source download:

- https://github.com/martinezdelariva/MarAcl/archive/master.zip

Unzip it in your `vendor` directory, and rename the resulting directory:

```sh
cd vendor
unzip /path/to/MarAcl.git
mv MarAcl-master MarAcl
```

### Git submodule

Add the repository as a git submodule in your project.

```sh
git submodule add git://github.com/martinezdelariva/MarAcl.git vendor/MarAcl
```

## Enable the module

Once you've installed the module, you need to enable it. You can do this by 
adding it to your `config/application.config.php` file:

```php
<?php
return array(
    'modules' => array(
        'MarAcl',
        'Application',
    ),
);
```

## Usage

Create configuration in your application. You can copy sample file from `vendor/MarAcl/config/maracl.global.php` to `config/autoload/maracl.global.php`:

```php
	
	return array(
	'MarAcl' => array(	
		// Holds the full qualified class name.
		// Must implement AuthenticationService class.
		// By default "Zend\Authentication\AuthenticationService"
		'authorize_provider' => 'Zend\Authentication\AuthenticationService',

		// String
		// Default role to use when there is no identity on 'authorize_provider'
		// By default "anonymous"
		'default_role' => 'anonymous',
		
		// Holds information about Roles, Resources and Rules
		'data'	=> array(
					
			// Holds array of Roles
			// Singles role is defined by:
			// 'name' 		string 		Indicates the name of role
			// 'parents' 	array 		Indicates the parents of roles. OPTIONAL
			'roles'		=> array(
				array(
					'name' 		=> 'anonymous',
					// 'parents'	=>	array('parent_1', 'parent_2')
				),
			),

			// Holds array of Resources.
			// All controller and action used on Rules section must be defined here.
			// Single resource is defined by:
			// 	'controller' 	string 			Indicates fully controller name
			// 	'actions' 		string|array 	Indicates name of actions belong in this controller
			'resources'	=> array(
				array(
					'controller' => 'application\controller\index',
					'actions' 	 => array('index', 'other'),
				),
				// ...
			),

			// Holds array of Rules divided in two groups: allow and deny
			// Single rule is defined by:
			// 	'role'			string
			// 	'controller'	string			Indicates fully controller name
			// 	'actions'		string|array	Optional. Indicates name of actions of this controller
			// 									If it's not provided then applies to all actions
			// 	'privilege'		string|array	Optional. HTTP method (GET, POST, HEAD, TRACE, DELETE)
			//									By default is 'GET'
			// 	'active'		int				Optional. 1 enable. 2 disable.
			//									By default is '1'
			'rules'	=> array(
				'allow' => array(
					array(
						'role'			=> 'guest',
						'controller'	=> 'application\controller\index',
						'actions'		=> array('index'),
						'privilege'		=> 'read',
						'active' 		=> 1,
					),
					// …
				),
				'deny' => array(),
			),
		),
	),
	);
```

## TODO
- Ability choose a redirect (302) instead of 403.
- Ability install with Composer
- Create View Helper and Controller Plugin.