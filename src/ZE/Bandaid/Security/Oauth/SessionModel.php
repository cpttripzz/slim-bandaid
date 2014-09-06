<?php
namespace ZE\Bandaid\Security\Oauth;

use \League\OAuth2\Server\Storage\SessionInterface;
class SessionModel implements SessionInterface {

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createSession($clientId, $redirectUri, $type = 'user', $typeId = null, $authCode = null, $accessToken = null, $refreshToken = null, $accessTokenExpire = null, $stage = 'requested')
    {
        $this->db->insert('
            INSERT INTO oauth_sessions (
                client_id,
                redirect_uri,
                owner_type,
                owner_id,
                auth_code,
                access_token,
                refresh_token,
                access_token_expires,
                stage,
                first_requested,
                last_updated
            )
            VALUES (
                :clientId,
                :redirectUri,
                :type,
                :typeId,
                :authCode,
                :accessToken,
                :refreshToken,
                :accessTokenExpire,
                :stage,
                UNIX_TIMESTAMP(NOW()),
                UNIX_TIMESTAMP(NOW())
            )', array(
            ':clientId' =>  $clientId,
            ':redirectUri'  =>  $redirectUri,
            ':type' =>  $type,
            ':typeId'   =>  $typeId,
            ':authCode' =>  $authCode,
            ':accessToken'  =>  $accessToken,
            ':refreshToken' =>  $refreshToken,
            ':accessTokenExpire'    =>  $accessTokenExpire,
            ':stage'    =>  $stage
        ));

        return $this->db->getInsertId();
    }

    public function updateSession($sessionId, $authCode = null, $accessToken = null, $refreshToken = null, $accessTokenExpire = null, $stage = 'requested')
    {
        $this->db->update('
            UPDATE oauth_sessions SET
                auth_code = :authCode,
                access_token = :accessToken,
                refresh_token = :refreshToken,
                access_token_expires = :accessTokenExpire,
                stage = :stage,
                last_updated = UNIX_TIMESTAMP(NOW())
            WHERE id = :sessionId',
            array(
                ':authCode' =>  $authCode,
                ':accessToken'  =>  $accessToken,
                ':refreshToken' =>  $refreshToken,
                ':accessTokenExpire'    =>  $accessTokenExpire,
                ':stage'    =>  $stage,
                ':sessionId'    =>  $sessionId
            ));
    }

    public function deleteSession($clientId, $type, $typeId)
    {
        $this->db->delete('
                DELETE FROM oauth_sessions WHERE
                client_id = :clientId AND
                owner_type = :type AND
                owner_id = :typeId',
            array(
                ':clientId' =>  $clientId,
                ':type'  =>  $type,
                ':typeId' =>  $typeId
            ));
    }

    public function validateAuthCode($clientId, $redirectUri, $authCode)
    {
        $result = $this->db->select('
                SELECT * FROM oauth_sessions WHERE
                    client_id = :clientId AND
                    redirect_uri = :redirectUri AND
                    auth_code = :authCode',
            array(
                ':clientId' =>  $clientId,
                ':redirectUri'  =>  $redirectUri,
                ':authCode' =>  $authCode
            ));

        while ($row = $result->fetch())
        {
            return (array) $row;
        }

        return false;
    }

    public function validateAccessToken($accessToken)
    {
        // Not needed for this demo
        die(var_dump('validateAccessToken'));
    }

    public function getAccessToken($sessionId)
    {
        // Not needed for this demo
    }

    public function validateRefreshToken($refreshToken, $clientId)
    {
        // Not needed for this demo
    }

    public function updateRefreshToken($sessionId, $newAccessToken, $newRefreshToken, $accessTokenExpires)
    {
        // Not needed for this demo
    }

    public function associateScope($sessionId, $scopeId)
    {
        $this->db->insert('INSERT INTO oauth_session_scopes (session_id, scope_id) VALUE (:sessionId, :scopeId)', array(
            ':sessionId'    =>  $sessionId,
            ':scopeId'  =>  $scopeId
        ));
    }

    public function getScopes($accessToken)
    {
        // Not needed for this demo
    }

    /**
     * Associate a redirect URI with a session
     *
     * Example SQL query:
     *
     * <code>
     * INSERT INTO oauth_session_redirects (session_id, redirect_uri) VALUE (:sessionId, :redirectUri)
     * </code>
     *
     * @param  int $sessionId The session ID
     * @param  string $redirectUri The redirect URI
     * @return void
     */
    public function associateRedirectUri($sessionId, $redirectUri)
    {
        // TODO: Implement associateRedirectUri() method.
    }

    /**
     * Associate an access token with a session
     *
     * Example SQL query:
     *
     * <code>
     * INSERT INTO oauth_session_access_tokens (session_id, access_token, access_token_expires)
     *  VALUE (:sessionId, :accessToken, :accessTokenExpire)
     * </code>
     *
     * @param  int $sessionId The session ID
     * @param  string $accessToken The access token
     * @param  int $expireTime Unix timestamp of the access token expiry time
     * @return int                 The access token ID
     */
    public function associateAccessToken($sessionId, $accessToken, $expireTime)
    {
        // TODO: Implement associateAccessToken() method.
    }

    /**
     * Associate a refresh token with a session
     *
     * Example SQL query:
     *
     * <code>
     * INSERT INTO oauth_session_refresh_tokens (session_access_token_id, refresh_token, refresh_token_expires,
     *  client_id) VALUE (:accessTokenId, :refreshToken, :expireTime, :clientId)
     * </code>
     *
     * @param  int $accessTokenId The access token ID
     * @param  string $refreshToken The refresh token
     * @param  int $expireTime Unix timestamp of the refresh token expiry time
     * @param  string $clientId The client ID
     * @return void
     */
    public function associateRefreshToken($accessTokenId, $refreshToken, $expireTime, $clientId)
    {
        // TODO: Implement associateRefreshToken() method.
    }

    /**
     * Assocate an authorization code with a session
     *
     * Example SQL query:
     *
     * <code>
     * INSERT INTO oauth_session_authcodes (session_id, auth_code, auth_code_expires)
     *  VALUE (:sessionId, :authCode, :authCodeExpires)
     * </code>
     *
     * @param  int $sessionId The session ID
     * @param  string $authCode The authorization code
     * @param  int $expireTime Unix timestamp of the access token expiry time
     * @return int                The auth code ID
     */
    public function associateAuthCode($sessionId, $authCode, $expireTime)
    {
        // TODO: Implement associateAuthCode() method.
    }

    /**
     * Remove an associated authorization token from a session
     *
     * Example SQL query:
     *
     * <code>
     * DELETE FROM oauth_session_authcodes WHERE session_id = :sessionId
     * </code>
     *
     * @param  int $sessionId The session ID
     * @return void
     */
    public function removeAuthCode($sessionId)
    {
        // TODO: Implement removeAuthCode() method.
    }

    /**
     * Removes a refresh token
     *
     * Example SQL query:
     *
     * <code>
     * DELETE FROM `oauth_session_refresh_tokens` WHERE refresh_token = :refreshToken
     * </code>
     *
     * @param  string $refreshToken The refresh token to be removed
     * @return void
     */
    public function removeRefreshToken($refreshToken)
    {
        // TODO: Implement removeRefreshToken() method.
    }

    /**
     * Associate scopes with an auth code (bound to the session)
     *
     * Example SQL query:
     *
     * <code>
     * INSERT INTO `oauth_session_authcode_scopes` (`oauth_session_authcode_id`, `scope_id`) VALUES
     *  (:authCodeId, :scopeId)
     * </code>
     *
     * @param  int $authCodeId The auth code ID
     * @param  int $scopeId The scope ID
     * @return void
     */
    public function associateAuthCodeScope($authCodeId, $scopeId)
    {
        // TODO: Implement associateAuthCodeScope() method.
    }

    /**
     * Get the scopes associated with an auth code
     *
     * Example SQL query:
     *
     * <code>
     * SELECT scope_id FROM `oauth_session_authcode_scopes` WHERE oauth_session_authcode_id = :authCodeId
     * </code>
     *
     * Expected response:
     *
     * <code>
     * array(
     *     array(
     *         'scope_id' => (int)
     *     ),
     *     array(
     *         'scope_id' => (int)
     *     ),
     *     ...
     * )
     * </code>
     *
     * @param  int $oauthSessionAuthCodeId The session ID
     * @return array
     */
    public function getAuthCodeScopes($oauthSessionAuthCodeId)
    {
        // TODO: Implement getAuthCodeScopes() method.
    }
}