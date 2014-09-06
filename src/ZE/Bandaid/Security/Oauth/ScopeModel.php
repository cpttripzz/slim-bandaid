<?php
namespace ZE\Bandaid\Security\Oauth;

use \League\OAuth2\Server\Storage\ScopeInterface;
class ScopeModel implements ScopeInterface {


    public function getScope($scope, $clientId = null, $grantType = null)
    {

            return array(
                'id'	=>	1,
                'scope'	=>	'',
                'name'	=>	'default',
                'description'	=>	'Default'
            );

    }

}