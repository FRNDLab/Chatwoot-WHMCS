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

use WHMCS\Database\Capsule;
use WHMCS\Authentication\CurrentUser;

class Chatwoot
{
    private $isAuthenticatedUser;
    private $userid;
    private $name;
    private $email;
    private $BASE_URL;
    private $websiteToken;
    private $identifier_hash;
    private $secret;
    private $notes;
    private $phonenumber;
    private $country;
    private $city;
    private $displayNameFormatted;
    private $gen_avatar;
    private $logindetect;
    private $darkmode;

    public function __construct($vars)
    {
        $this->BASE_URL = Capsule::table('tbladdonmodules')->where('setting','chatwoot_whmcs_hostname')->value('value');
        $this->websiteToken = Capsule::table('tbladdonmodules')->where('setting','chatwoot_whmcs_website_token')->value('value');
        $this->secret = Capsule::table('tbladdonmodules')->where('setting','chatwoot_whmcs_website_secret')->value('value');
        $this->logindetect = Capsule::table('tbladdonmodules')->where('setting','chatwoot_whmcs_logindetect')->value('value');
        $this->darkmode = Capsule::table('tbladdonmodules')->where('setting','chatwoot_whmcs_darkmode')->value('value');

        if (!$this->BASE_URL) {
            return;
        }elseif (!$this->websiteToken) {
            return;
        }elseif (!$this->secret) {
            return;
        }

        $this->BASE_URL = "https://" . $this->BASE_URL;
                
        $currentUser = new CurrentUser();            
        $this->isAuthenticatedUser = $currentUser->isAuthenticatedUser();
        $authUser = $currentUser->client();   
        $this->userid = $authUser->id;
        $this->email = $authUser->email;
        $this->name = $authUser->firstname . ' ' . $authUser->lastname;
        $this->phonenumber = $this->cleanphone($authUser->phonenumber);
        $this->notes = $authUser->notes;
        $this->country = $authUser->country;
        $this->city = $authUser->city;
        $this->displayNameFormatted = $authUser->displayNameFormatted;
        $this->gen_avatar =  $this->gen_avatar();
        $this->identifier_hash = $this->gen_identifier_hash();               
    }    

    private function gen_avatar()
    {
        $avatarUrl = "https://www.gravatar.com/avatar/";
        $avatarType = "?d=monsterid";
        $email = trim($this->email);
        $email = strtolower( $email );
        $email = md5( $email );

        return $avatarUrl . $email . $avatarType;
    }

    private function cleanphone($cadena)
    {
        $cadena = str_replace("-","",$cadena);        
        $cadena = str_replace(".","",$cadena); 
        return $cadena;    
    }

    private function gen_identifier_hash()
    {
        return hash_hmac('sha256', $this->userid, $this->secret);
    }

    private function logedinchat()
    {
        $chatwoot = '$chatwoot';
        return <<<HTML
        <script>        
        
          (function() {
          
              window.addEventListener("chatwoot:ready", function() {
          
                  function set_cookie(name, value) {
                      var cookie = [name, '=', JSON.stringify(value), '; domain=.', window.location.host.toString(), '; path=/;'].join('');
                      document.cookie = cookie;
                  }
          
                  function get_cookie(name) {
                      var result = document.cookie.match(new RegExp(name + '=([^;]+)'));
                      result && (result = JSON.parse(result[1]));
                      return result;
                  }
          
                  let cookie = get_cookie('chatwoot_clean');
          
                  if (cookie) {
                      window.$chatwoot.reset();
                      set_cookie('chatwoot_clean', false);
                  }
          
              });
          
          })(window, addEventListener);

          (function(d,t) {
            var BASE_URL='$this->BASE_URL';
            var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src=BASE_URL+"/packs/js/sdk.js";
            g.defer = true;
            g.async = true;
            s.parentNode.insertBefore(g,s);
            g.onload=function(){
                window.chatwootSettings = {
                    darkMode: "$this->darkmode"
                }                 
                window.chatwootSDK.run({
                    websiteToken: '$this->websiteToken',
                    baseUrl: BASE_URL
                })               
                window.addEventListener("chatwoot:ready", function () {
                    window.$chatwoot.setUser('$this->userid', {
                        name: "$this->name",
                        email: "$this->email",
                        identifier_hash: "$this->identifier_hash",
                        phone_number: "$this->phonenumber",
                        description: "$this->notes",
                        country_code: "$this->country",
                        city: "$this->city",
                        company_name: "$this->displayNameFormatted",
                        avatar_url: "$this->gen_avatar"
                    });   
                })               
            }
          })(document,"script");
        </script>            
       HTML;        

    }

    private function guestchat()
    {
        $chatwoot = '$chatwoot';
        return <<<HTML
        <script>
          (function(d,t) {
            var BASE_URL='$this->BASE_URL';
            var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src='$this->BASE_URL'+"/packs/js/sdk.js";
            g.defer = true;
            g.async = true;
            s.parentNode.insertBefore(g,s);
            g.onload=function(){
                window.chatwootSettings = {
                    darkMode: "$this->darkmode"
                }                 
                window.chatwootSDK.run({
                    websiteToken: '$this->websiteToken',
                    baseUrl: '$this->BASE_URL'
                })              
            }
          })(document,"script");

          (function(window, addEventListener) {
          
              window.addEventListener("chatwoot:ready", function() {
          
          
                  function set_cookie(name, value) {
                      var cookie = [name, '=', JSON.stringify(value), '; domain=.', window.location.host.toString(), '; path=/;'].join('');
                      document.cookie = cookie;
                  }
          
                  function get_cookie(name) {
                      var result = document.cookie.match(new RegExp(name + '=([^;]+)'));
                      result && (result = JSON.parse(result[1]));
                      return result;
                  }
          
          
                  let cookie = get_cookie('chatwoot_clean');
          
                  if (!cookie) {
                      window.$chatwoot.reset();
                      set_cookie('chatwoot_clean', true);   
                  }
          
              });
          
          })(window, addEventListener);
          
      
        </script>            
       HTML;        

    }    

    private function without_logindetect()
    {
        $chatwoot = '$chatwoot';
        return <<<HTML
        <script>
          (function(d,t) {
            var BASE_URL='$this->BASE_URL';
            var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src='$this->BASE_URL'+"/packs/js/sdk.js";
            g.defer = true;
            g.async = true;
            s.parentNode.insertBefore(g,s);
            g.onload=function(){
                window.chatwootSettings = {
                    darkMode: "$this->darkmode"
                }                 
                window.chatwootSDK.run({
                    websiteToken: '$this->websiteToken',
                    baseUrl: '$this->BASE_URL'
                })              
            }
          })(document,"script");          
      
        </script>            
       HTML;        

    }      

    public function renderchat()
    {        

        if (!$this->logindetect) {
            return $this->without_logindetect();
        } else {
            return $this->isAuthenticatedUser ? $this->logedinchat() : $this->guestchat();
        }
        
    }

}

add_hook("ClientAreaFooterOutput", 1, function($vars) 
{
    $LoadScripts = new Chatwoot($vars);
	return $LoadScripts->renderchat();

});