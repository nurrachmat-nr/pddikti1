<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define(G_SESSION, 'PDPT_GATE');

$config['iterasi'] = 2;
$config['iterasi_internal_error'] = 3;
$config['iterasi_timeout'] = 2;
$config['sleep'] = 15;
$config['ip_server'] = '118.98.235.129';
$config['port_server'] = 80;
$config['url_ws'] = 'http://118.98.235.129/ws/newsyncserver.php';
$config['url_last_update'] = 'http://118.98.235.129/ws/last_update.php';
$config['timeout'] = 1800;
$config['connect_timeout'] = 5;

$config['base_url']	= '';
$config['index_page'] = '';//index.php';

$config['upload_path'] = 'd:/www/pdpt/upload';

$domainName = $_SERVER['HTTP_HOST'];

$config['akad_url'] = 'http://localhost/pdpt/';
$config['gate_key'] = 'PDpt';

# schema: schema lain
$config['schema_gate'] = 'gate';
$config['schema_akad'] = 'public';
$config['schema_referensi'] = 'ref';
$config['schema_akses'] = 'man_akses';

# date default
$config['date_default'] = '1901-01-01 00:00:00';

# hanya ip tertentu yang boleh akses gate
$config['gate_ip_white_list'] = array('localhost');

# show_ora: menampilkan pesan ORA bila ada error
$config['show_ora'] = true;

# log_db: log setiap insert/update/delete
$config['log_db'] = true;
$config['log_db_table_exception'] = array('sc_usersession', 'sc_userlog', 'sc_deletesession');

# debug_db: menampilkan semua query di halaman, matikan debug_db jika sudah production
$config['debug_db'] = false; // true;

/* Bila menggunakan sendmail */
$config['mailpath'] = '/usr/sbin/sendmail';

$config['uri_protocol']	= 'AUTO';
$config['url_suffix'] = '';
$config['language']	= 'id';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = TRUE;
$config['subclass_prefix'] = 'Base_';
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
$config['allow_get_array']		= TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger']	= 'c';
$config['function_trigger']		= 'm';
$config['directory_trigger']	= 'd'; // experimental not currently in use

$config['log_threshold'] = 0;

$config['log_path'] = '';
$config['log_date_format'] = 'Y-m-d H:i:s';

$config['cache_path'] = '';

$config['encryption_key'] = 'ae8a7a587a5a3e5738025b3b43392ab6';

$config['sess_cookie_name']		= 'PDPT_GATE';
$config['sess_expiration']		= 3600; // 1 jam
$config['sess_expire_on_close']	= FALSE;
$config['sess_encrypt_cookie']	= TRUE;
$config['sess_use_database']	= FALSE;
$config['sess_table_name']		= 'ci_sessions';
$config['sess_match_ip']		= FALSE;
$config['sess_match_useragent']	= TRUE;
$config['sess_time_to_update']	= 300;

$config['sess_db_delete_time']	= 60; // dalam detik, mengurangi operasi delete table sc_deletesession
$config['sess_db_update_time']	= 30; // dalam detik, mengurangi operasi update session

$config['cookie_prefix']	= "";
$config['cookie_domain']	= "";
$config['cookie_path']		= "/";
$config['cookie_secure']	= FALSE;

$config['global_xss_filtering'] = FALSE;



/*
|--------------------------------------------------------------------------
| Cross Site Request Forgery
|--------------------------------------------------------------------------
| Enables a CSRF cookie token to be set. When set to TRUE, token will be
| checked on a submitted form. If you are accepting user data, it is strongly
| recommended CSRF protection be enabled.
|
| 'csrf_token_name' = The token name
| 'csrf_cookie_name' = The cookie name
| 'csrf_expire' = The number in seconds the token should expire.
*/
$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;


$config['compress_output'] = FALSE;

/*
|--------------------------------------------------------------------------
| Master Time Reference
|--------------------------------------------------------------------------
|
| Options are 'local' or 'gmt'.  This pref tells the system whether to use
| your server's local time as the master 'now' reference, or convert it to
| GMT.  See the 'date helper' page of the user guide for information
| regarding date handling.
|
*/
$config['time_reference'] = 'local';

$config['rewrite_short_tags'] = FALSE;

$config['id_separator'] = '___';

/*
|--------------------------------------------------------------------------
| Reverse Proxy IPs
|--------------------------------------------------------------------------
|
| If your server is behind a reverse proxy, you must whitelist the proxy IP
| addresses from which CodeIgniter should trust the HTTP_X_FORWARDED_FOR
| header in order to properly identify the visitor's IP address.
| Comma-delimited, e.g. '10.0.1.200,10.0.1.201'
|
*/
$config['proxy_ips'] = '';


/* End of file config.php */
/* Location: ./application/config/config.php */
