/**
 * Templates holder class
 * @param object config
 */
var Template = function (config) {
    var _templates = {};
    var _config = {
        'template_class': '.template',
        'template_alias': 'template'
    };
    _.merge(_config, config);

    /**
     * register a template
     *
     * @param string name
     * @param string content
     */
    var register = function(name, content) {
        _templates[name] = _.template(_.unescape(content));
    };

    /**
     * render template under "name" alias, with given data
     *
     * @param string name
     * @param object data
     * @return string
     */
    var render = function(name, data) {
        if (!_templates[name]) {
            throw "Unregistered template " + name;
        }

        return _templates[name](data);
    };

    if (_config.container) {
        $(_config.container)
            .find(_config.template_class + '[data-' + _config.template_alias + ']')
            .each(function(index, element) {
                var element = $(element);
                register(
                    element.data(_config.template_alias),
                    element.html()
                );
            })
        ;
    }

    return {
        render: render,
        templates: _templates
    };
};
