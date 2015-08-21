/**
 * OAuth connection class
 * @param object config
 */
var Api = function (config) {
    var _cookieName = 'drop_access_token';
    var _config = {
        'grant_type': 'http://api.drop-dont-park.com/grant/anonymous',
        'client_id': config['client_id'],
        'client_secret': config['client_secret']
    };
    var _accessToken = config['access_token'] ? config['access_token'] : null ;

    /**
     * access token requested method
     *
     * @return Promise
     */
    var getAccessToken = function() {
        return new Promise(function(success, error, progress) {
            if (_accessToken) {
                return success(_accessToken);
            }

            $.ajax({
                "url": Routing.generate('drop_api_oauth_token'),
                "method": "POST",
                "data": _config,
                "success": function(oauthData) {
                    _accessToken = oauthData.access_token;
                    setTimeout(
                        function() { _accessToken = null; },
                        (oauthData.expires_in-10)*1000
                    );

                    return success(_accessToken);
                },
                "error": function(req, status, e) {
                    return error(e);
                }
            });
        });
    };

    /**
     * requesting method
     */
    var _request = function (method, route, query, data) {
        return new Promise(function(success, error, progress) {
            getAccessToken()
                .then(function (accessToken) {
                    query = query ? query : {};
                    $.ajax({
                        method: method,
                        url: Routing.generate(route, query),
                        data: data ? JSON.stringify(data) : null,
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('Authorization', 'Bearer ' + accessToken);
                            xhr.setRequestHeader('Content-type', 'application/json');
                        },
                        success: function(data) {
                            return success(data);
                        }
                    });
                })
            ;
        });
    };

    /**
     * perform a post call on given route with given query / data
     *
     * @param string route
     * @param Object query
     * @param Object data
     *
     * @return Promise
     */
    var post = function(route, query, data) {
        return _request('POST', route, query, data);
    };

    /**
     * perform a put call on given route with given query / data
     *
     * @param string route
     * @param Object query
     * @param Object data
     *
     * @return Promise
     */
    var put = function(route, query, data) {
        return _request('PUT', route, query, data);
    };

    /**
     * perform a patch call on given route with given query / data
     *
     * @param string route
     * @param Object query
     * @param Object data
     *
     * @return Promise
     */
    var patch = function(route, query, data) {
        return _request('PATCH', route, query, data);
    };

    /**
     * perform a get call on given route with given query parameters
     *
     * @param string route
     * @param Object query
     *
     * @return Promise
     */
    var get = function(route, query) {
        return _request('GET', route, query);
    };

    return {
        get: get,
        post: post,
        put: put,
        patch: patch,
        getAccessToken: getAccessToken
    };
};
