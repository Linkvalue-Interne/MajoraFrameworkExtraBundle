'use strict';
/**
 * Templates holder class
 * @param object config
 */
function Template(config) {
    var _templates = {};
    var templatesHtml;
    var _config = {
        'template_alias': 'template'
    };
    _.merge(_config, config);

    if (_config.container) {
        templatesHtml = $(_config.container).html();
        $(templatesHtml).filter('[data-' + _config.template_alias + ']')
            .each(function(index, element) {
                register(
                    element.dataset[_config.template_alias],
                    _.unescape(element.innerHTML)
                );
            });
    }

    /**
     * register a template
     *
     * @param string name
     * @param string content
     */
    function register(name, content) {
        _.set(_templates, name, _.template(content));
    }

    /**
     * render template under "name" alias, with given data
     *
     * @param string name
     * @param object data
     * @return string
     */
    function render(name, data) {
        var template = _.get(_templates, name);
        if (!template) {
            throw "Unregistered template " + name;
        }
        return template(data);
    }

    return {
        render: render,
        templates: _templates
    };
}
