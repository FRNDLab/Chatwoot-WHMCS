<?php

/**
 *
 * Permite utilizar chatwoot con whmcs y detectar cuando un cliente ha iniciado sesión.
 * https://github.com/mariofernandu
 * 
 * @package    ChatwootWHMCS
 * @author     Fernando Torres <fernando@clotr.com>
 * @version    1.1.1
 * 
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function chatwoot_whmcs_config()
{
    return [
        // Display name for your module
        'name' => 'Chatwoot WHMCS',
        // Description displayed within the admin interface
        'description' => 'Permite utilizar chatwoot con whmcs y detectar cuando un cliente ha iniciado sesión.',
        // Module author name
        'author' => '<a href="http://clotr.com" targer="_blank">clotr.com</a>',
        // Default language
        'language' => 'english',
        // Version number
        'version' => '1.1.1',
        'fields' => [
            // a text field type allows for single line text input
            'chatwoot_whmcs_hostname' => [
                'FriendlyName' => 'Chatwoot Hostname',
                'Type' => 'text',
                'Size' => '25',
                'Default' => '',
                'Description' => 'Chatwoot hostname, ejemplo: <b>chat.yourdomain.com</b>',
            ],
            // a password field type allows for masked text input
            'chatwoot_whmcs_website_token' => [
                'FriendlyName' => 'Chatwoot Website Token',
                'Type' => 'password',
                'Size' => '25',
                'Default' => '',
                'Description' => 'Busquelo en Ajustes >> Entradas >> [seleccione la entrada] >> Configuracion',
            ],
            // a password field type allows for masked text input
            'chatwoot_whmcs_website_secret' => [
                'FriendlyName' => 'Chatwoot Website Secret',
                'Type' => 'password',
                'Size' => '25',
                'Default' => '',
                'Description' => 'Busquelo en Ajustes >> Entradas >> [seleccione la entrada] >> Configuracion',
            ],            
            // the yesno field type displays a single checkbox option
            'chatwoot_whmcs_logindetect' => [
                'FriendlyName' => 'Login Detect?',
                'Type' => 'yesno',
                'Default' => true,
                'Description' => 'Tick to enable',
            ],            
            // the yesno field type displays a single checkbox option
            'chatwoot_whmcs_darkmode' => [
                'FriendlyName' => 'Modo oscuro?',
                'Type' => 'yesno',
                'Default' => true,
                'Description' => 'Tick to enable',
            ],
            // the dropdown field type renders a select menu of options
            'chatwoot_whmcs_darkmode' => [
                'FriendlyName' => 'Tema',
                'Type' => 'dropdown',
                'Options' => [
                    'light' => 'Modo claro',
                    'auto' => 'Auto(acoplar al tema del usuario)',
                ],
                'Default' => 'auto',
                'Description' => 'Los sitios web modernos permiten a los usuarios cambiar entre los modos claro y oscuro. Por lo tanto, un chat en vivo que funcione con ambos temas es importante.',
            ]    
        ]
    ];
}
