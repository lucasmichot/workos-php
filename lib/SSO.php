<?php

namespace WorkOS;

/**
 * Class SSO
 * 
 * This class facilitates the use of WorkOS SSO.
 * 
 * $piKey and $projectId must be configured for the package to use SSO.
 */
class SSO
{
    const PATH_AUTHORIZATION = "sso/authorize";
    const PATH_PROFILE = "sso/token";

    private static $instance;

    /**
     * SSO constructor.
     * 
     * Verifies that $apiKey and $projectId are configured.
     * 
     * @throws \WorkOS\Exception\ConfigurationException if the required settings are not configured
     */
    private function __construct()
    {
        Util\Validator::validateSettings(Util\Validator::MODULE_SSO);
    }

    /**
     * @return \WorkOS\SSO
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Generates an OAuth 2.0 authorization URL used to initiate the SSO flow with WorkOS.
     * 
     * @param null|string $domain Domain of the user that will be going through SSO
     * @param null|string $redirectUri URI to direct the user to upon successful completion of SSO
     * @param null|array $state Associative array containing state that will be returned to the server
     * @param null|\WorkOS\Resource\ConnectionType $provider Service provider that handles the identity of the user
     * 
     * @return string
     */
    public function getAuthorizationUrl($domain, $redirectUri, $state, $provider)
    {
        if (!isset($domain) && !isset($provider)) {
            $msg = "Either \$domain or \$provider is required";

            throw new Exception\UnexpectedValueException($msg);
        }

        $params = [
            "client_id" => WorkOS::getProjectId(),
            "response_type" => "code"
        ];

        if (isset($domain)) {
            $params["domain"] = $domain;
        }

        if (isset($redirectUri)) {
            $params["redirect_uri"] = $redirectUri;
        }

        if (isset($state) and !empty($state)) {
            $params["state"] = $state;
        }

        if (isset($provider)) {
            $params["provider"] = $provider;
        }

        return Util\Curl::generateUrl(self::PATH_AUTHORIZATION, $params);
    }

    /**
     * Verify that SSO has been completed successfully and retrieve the identity of the user.
     * 
     * @param string $code Code returned by WorkOS on completion of OAuth 2.0 flow
     * 
     * @return \WorkOS\Resource\Profile
     */
    public function getProfile($code)
    {
        $params = [
            "client_id" => WorkOS::getProjectId(),
            "client_secret" => WorkOS::getApikey(),
            "code" => $code,
            "grant_type" => "authorization_code"
        ];
        $response = Util\Curl::request(Util\Curl::METHOD_POST, self::PATH_PROFILE, $params);

        return Resource\Profile::constructFromResponse($response);
    }
}
