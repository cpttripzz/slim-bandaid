<?php
namespace ZE\Bandaid\Security\Oauth;
use \League\OAuth2\Server\Storage\ClientInterface;
class ClientModel implements ClientInterface {

    public function getClient($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
    {
        return array(
            'client_id' => '1',
            'client secret' => 'jfJhfumdkJSDTBDkfbfkw4623n2',
            'redirect_uri' => 'http://foo/redirect',
            'name' => 'API Client'
        );
    }

}