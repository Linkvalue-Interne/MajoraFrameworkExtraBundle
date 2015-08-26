/**
 * Location server class
 *
 * @param object config
 */
var WebSocketServer = function (api, config, handlers) {

    /**
     * @type {Object}
     */
    var _config = {
        'host': null,
        'protocol': 'ws',
        'reconnect_route': null,
        'max_reconnect_tries': 5,
        'reconnect_timeout': 2000
    };
    _.merge(_config, config);

    var _api = api;
    var _handlers = handlers;
    var _socket = null;
    var _reconnectTries = 0;
    var _reconnectTimeout = 0;

    /**
     * websocket reconnection handler
     */
    var _reconnect = function() {
        if (_config['max_reconnect_tries'] - _reconnectTries < 0) {
            return new Promise(function (resolve, reject) {
                reject('Max connection attempt reached.');
            });
        }

        _reconnectTries++;
        _reconnectTimeout += _config['reconnect_timeout'];

        if (!_config['reconnect_route']) {
            return _.delay(_connect, _reconnectTimeout);
        }

        return api.get(_config['reconnect_route'])
            .then(function(success, error) {
                _.delay(_connect, _reconnectTimeout);
            })
        ;
    };

    /**
     * connect websocket
     */
    var _connect = function() {
        return new Promise(function(success, error) {
            if (_socket && _socket.readyState == WebSocket.OPEN) {
                return success();
            }

            try {
                _socket = new WebSocket(_config.protocol + '://' + _config.host);

                // connection handler
                _socket.onopen = function () {
                    console.log('Connected on web socket server.');

                    // register listened events
                    _socket.send(JSON.stringify(_createEventMessage(
                        'subscribe', { 'events': _.keys(_handlers) }
                    )));

                    return success();
                };

                // disconnection handler
                _socket.onclose = function () {
                    console.log('Disconnected from web socket server.');

                    _socket = null;
                    _reconnect();

                    return;
                };

                _socket.onmessage = function (msg) {
                    var event = JSON.parse(msg.data);

                    console.log('Event recieved.', event);

                    _.forEach([event.event, '*'], function(handlerKey) {
                        if (!_handlers[handlerKey]) {
                            return;
                        }

                        _handlers[handlerKey](event.data);
                    });
                };
            } catch (e) {
                console.log('Error on web socket server connection: ' + e.message);
                _reconnect()
                    .then(success)
                    .catch(reject)
                ;
            }
        });
    };

    /**
     * Push given message to location server
     *
     * @param {string} event
     * @param {Object} data
     * @param {Object} metadata
     */
    var _createEventMessage = function(name, data, metadata) {
        return {
            event: name,
            data: data,
            metadata: _.merge({}, metadata)
        };
    }

    /**
     * Push given message to location server
     *
     * @param {string} event
     * @param {Object} data
     * @param {Object} metadata
     */
    var _push = function(name, data, metadata) {
        try {
            _connect().then(function() {
                var event = _createEventMessage(name, data, metadata);

                console.log('Event sent.', event);

                _socket.send(JSON.stringify(event));
            });
        } catch (e) {
            console.log(e);
        }
    };

    return {
        push: _push,
        connect: _connect
    };
};
