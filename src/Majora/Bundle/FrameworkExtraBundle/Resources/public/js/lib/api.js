/**
 * REST API client class with OAuth2 connection handling.
 *
 * @param {Object} config See default configuration to know what is configurable.
 *
 * Depends:
 * jQuery as $
 * FOSJsRoutingBunble as Routing
 *
 * @todo replace hard dependencies with injected dependencies
 */
function Api(config) {
    var _getAccessTokenData;
    var _currentGrantType;

    // Default configuration
    var _config = {
        grantTypes: {
            refresh_token: 'refresh_token',
            password: 'password',
            anonymous: 'anonymous'
        },
        getAccessTokenRoute: 'api_oauth_token',
        clientId : '',
        clientSecret: '',
        accessToken: '',
        refreshToken: '',
        username: '',
        password: '',
        onApiRequestError: $.noop,
        onGetAccessTokenError: $.noop
    };
    // Extend default configuration with given configuration
    $.extend(true, _config, config);

    // Set getAccessToken grant_type
    detectAndUseGrantTypeFromConfig();

    /**
     * Automatic grant_type detection to getAccessToken
     */
    function detectAndUseGrantTypeFromConfig(){
        if(_config.refreshToken){
            // Use refresh_token if available
            useRefreshTokenGrantType();
        } else if(_config.username && _config.password){
            // Seems really dangerous to have password in Javascript client code... but let's make it possible anyway
            usePasswordGrantType();
        } else {
            useAnonymousGrantType();
        }
    }

    /**
     * Use anonymous grant type to getAccessToken
     */
    function useAnonymousGrantType(){
        _currentGrantType = _config.grantTypes.anonymous;
        _getAccessTokenData = {
            client_id: _config.clientId,
            client_secret: _config.clientSecret,
            grant_type: _currentGrantType
        };
    }

    /**
     * Use refresh_token grant type to getAccessToken
     */
    function useRefreshTokenGrantType(){
        _currentGrantType = _config.grantTypes.refresh_token;
        _getAccessTokenData = {
            client_id: _config.clientId,
            client_secret: _config.clientSecret,
            grant_type: _currentGrantType,
            refresh_token: _config.refreshToken
        };
    }

    /**
     * Use password grant type to getAccessToken
     */
    function usePasswordGrantType(){
        _currentGrantType = _config.grantTypes.password;
        _getAccessTokenData = {
            client_id: _config.clientId,
            client_secret: _config.clientSecret,
            grant_type: _currentGrantType,
            username: _config.username,
            password: _config.password
        };
    }

    /**
     * Retrieve access_token.
     *
     * @return {Promise} with access_token string passed to resolved method
     */
    function getAccessToken() {
        return new Promise(function(success, error, progress) {
            if (_config.accessToken) {
                return success(_config.accessToken);
            }

            $.ajax({
                url: Routing.generate(_config.getAccessTokenRoute),
                method: 'POST',
                data: _getAccessTokenData || {},
                success: function(oauthData) {
                    // Store access_token and use refresh_token for next access_token retrieval
                    _config.accessToken = oauthData.access_token;

                    // If we retrieved a refresh_token with our last access_token,
                    // use refresh_token grant type
                    if(oauthData.refresh_token){
                        _config.refreshToken = oauthData.refresh_token;
                        useRefreshTokenGrantType();
                    }

                    // Remove stored access_token 10sec before access_token expiration
                    setTimeout(
                        function() { _config.accessToken = null; },
                        (oauthData.expires_in-10)*1000
                    );

                    return success(_config.accessToken);
                },
                error: function(xhr, textStatus, e) {
                    _config.onGetAccessTokenError(xhr, textStatus, e);
                    return error(e);
                }
            });
        });
    }

    /**
     * perform a post call on given route with given query / data
     *
     * @param {string} route
     * @param {Object} query
     * @param {Object} data
     *
     * @return {Promise}
     */
    function post(route, query, data) {
        return request('POST', route, query, data);
    }

    /**
     * perform a put call on given route with given query / data
     *
     * @param {string} route
     * @param {Object} query
     * @param {Object} data
     *
     * @return {Promise}
     */
    function put(route, query, data) {
        return request('PUT', route, query, data);
    }

    /**
     * perform a patch call on given route with given query / data
     *
     * @param {string} route
     * @param {Object} query
     * @param {Object} data
     *
     * @return {Promise}
     */
    function patch(route, query, data) {
        return request('PATCH', route, query, data);
    }

    /**
     * perform a get call on given route with given query parameters
     *
     * @param {string} route
     * @param {Object} query
     *
     * @return {Promise}
     */
    function get(route, query) {
        return request('GET', route, query);
    }

    /**
     * Request REST API
     *
     * @return {Promise}
     */
    function request(method, route, query, data) {
        return new Promise(function(success, error, progress) {
            getAccessToken()
                .then(function (accessToken) {
                    $.ajax({
                        method: method,
                        url: Routing.generate(route, query || {}),
                        data: data || {},
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + accessToken);
                            xhr.setRequestHeader('Content-type', 'application/json');
                        },
                        success: function(data) {
                            return success(data);
                        },
                        error: function(xhr, textStatus, e) {
                            // If unauthorized exception, the access_token probably expired,
                            // retry with a new access_token
                            if (xhr.status === 401) {
                                _config.accessToken = null;
                                detectAndUseGrantTypeFromConfig();
                                return request(method, route, query, data);
                            }

                            _config.onApiRequestError(xhr, textStatus, e);
                            return error(e);
                        }
                    });
                })
            ;
        });
    }

    // Define public properties
    return {
        get: get,
        post: post,
        put: put,
        patch: patch,
        getAccessToken: getAccessToken
    };
}
