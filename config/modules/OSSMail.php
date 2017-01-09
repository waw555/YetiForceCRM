<?php
/* {[The file is published on the basis of YetiForce Public License that can be found in the following directory: licenses/License.html]} */
$config['db_dsnw'] = 'mysql://' . $dbconfig['db_username'] . ':' . $dbconfig['db_password'] . '@' . $dbconfig['db_server'] . ':' . $dbconfig['db_port'] . '/' . $dbconfig['db_name'];
$config['db_prefix'] = 'roundcube_';
$config['default_host'] = ['ssl://imap.gmail.com' => 'ssl://imap.gmail.com',];
$config['validate_cert'] = false;
$config['default_port'] = 993;
$config['smtp_server'] = 'ssl://smtp.gmail.com';
$config['smtp_port'] = 465;
$config['smtp_user'] = '%u';
$config['smtp_pass'] = '%p';
$config['support_url'] = 'http://yetiforce.com';
$config['des_key'] = 'rGOQ26hR%gxlZk=QA!$HMOvb';
$config['username_domain'] = 'gmail.com';
$config['product_name'] = 'YetiForce';
$config['plugins'] = array('identity_smtp', 'ical_attachments', 'yetiforce', 'thunderbird_labels', 'zipdownload', 'archive');
$config['language'] = 'en_US';
$config['mime_param_folding'] = 0;
$config['skin_logo'] = array('*' => '/images/null.png');
$config['ip_check'] = false;
$config['enable_spellcheck'] = true;
$config['identities_level'] = '0';
$config['auto_create_user'] = true;
$config['mail_pagesize'] = 25;
$config['addressbook_pagesize'] = 50;
$config['prefer_html'] = true;
$config['preview_pane'] = false;
$config['htmleditor'] = '1';
$config['draft_autosave'] = 300;
$config['mdn_requests'] = '0';
$config['session_lifetime'] = 10;
$config['sendmail_delay'] = 0;
$config['date_long'] = 'Y-m-d H:i';
$config['date_format'] = 'Y-m-d';
$config['time_format'] = 'H:i';
$config['show_images'] = '0';
$config['imap_cache'] = 'db';
$config['messages_cache'] = 'db';
$config['debug_level'] = AppConfig::debug('ROUNDCUBE_DEBUG_LEVEL');
$config['per_user_logging'] = AppConfig::debug('ROUNDCUBE_PER_USER_LOGGING');
$config['smtp_log'] = AppConfig::debug('ROUNDCUBE_SMTP_LOG');
$config['log_logins'] = AppConfig::debug('ROUNDCUBE_LOG_LOGINS');
$config['log_session'] = AppConfig::debug('ROUNDCUBE_LOG_SESSION');
$config['sql_debug'] = AppConfig::debug('ROUNDCUBE_SQL_DEBUG');
$config['imap_debug'] = AppConfig::debug('ROUNDCUBE_IMAP_DEBUG');
$config['ldap_debug'] = AppConfig::debug('ROUNDCUBE_LDAP_DEBUG');
$config['smtp_debug'] = AppConfig::debug('ROUNDCUBE_SMTP_DEBUG');
$config['log_dir'] = RCUBE_INSTALL_PATH . '/../../../cache/logs/';
$config['temp_dir'] = RCUBE_INSTALL_PATH . '/../../../cache/mail/';
$config['devel_mode'] = AppConfig::debug('ROUNDCUBE_DEVEL_MODE');
$config['imap_conn_options'] = [
	'ssl' => [
		'verify_peer' => false,
		'verfify_peer_name' => false,
	],
];
$config['smtp_timeout'] = 5;
$config['smtp_conn_options'] = [
	'ssl' => [
		'verify_peer' => false,
		'verfify_peer_name' => false,
	],
];
$config['smtp_helo_host'] = 'YetiForceCRM';
$config['root_directory'] = ROOT_DIRECTORY . DIRECTORY_SEPARATOR;
$config['site_URL'] = $site_URL;
$config['imap_open_add_connection_type'] = true;
$config['enable_variables_in_signature'] = false;
$config['skin'] = 'yetiforce';
