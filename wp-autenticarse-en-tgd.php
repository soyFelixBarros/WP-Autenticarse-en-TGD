<?php
/*******************************************************************************
** Plugin Name: WP Autenticarse en TGD
**
** Description: Plugin para autenticarse en Tu Gobierno Abierto.
**
** Author: Felix Barros
** Author URI: https://felixbarros.blog
** Plugin URI: https://github.com/soyFelixBarros/WP-Autenticarse-en-TGD
** Version: 1.0.0
** Text Domain: wpaet
*******************************************************************************/

define('BASE_PATH', plugin_dir_path(__FILE__));
define('BASE_URL', plugin_dir_url(__FILE__));

require BASE_PATH . 'vendor/autoload.php';

use WpAutenticarseEnTGD\Forms;
use WpAutenticarseEnTGD\Plugin;

$formTgd = new Forms\Tgd();
$plugin = new Plugin();

$plugin->init();