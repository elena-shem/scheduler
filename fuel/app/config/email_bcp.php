<?php
return array(
	'defaults' => 
	array(
		'useragent' => 'Scheduler Automailer',
		'driver' => 'smtp',
		'is_html' => true,
		'charset' => 'utf-8',
		'encode_headers' => true,
		'encoding' => 'quoted-printable',
		'priority' => '1 (Highest)',
		'from' => 
		array(
			'email' => 'di.exam.scheduler@sblaxos.com',
			'name' => 'DI Exam Scheduler',
		),
		'return_path' => 'paskalis@di.uoa.gr',
		'validate' => true,
		'auto_attach' => true,
		'generate_alt' => true,
		'force_mixed' => false,
		'wordwrap' => 76,
		'sendmail_path' => '/usr/sbin/sendmail',
		'smtp' => 
		array(
			'host' => 'ssl://miltonkeynes.theukhost.net',
			'port' => '465',
			'username' => 'di.exam.scheduler@sblaxos.com',
			'password' => '!35Qw47@%56aB!35Qw47@%56aB',
			'timeout' => '5',
		),
		'newline' => '\n',
		'attach_paths' => 
		array(
			0 => '',
			1 => DOCROOT.'',
		),
	),
	'default_setup' => 'default',
	'setups' => 
	array(
		'default' => 
		array(
		),
	),
);
