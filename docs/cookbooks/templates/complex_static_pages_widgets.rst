Complex static pages with Containers and Widgets
================================================

Web site almost always need a way to work with static pages (e.g. About us, Contact, Impressum).
For simple 'static' pages such as these, it is usually enough to create page-like articles in CMS back-end - title and one big content field editable in html editor.

But how to approach complex static pages that have several different elements such as boxes, info with fancy changeable backgrounds, custom size graphic elements etc?

In Publisher for such complex, presentation-like static pages we use combination of containers and widgets linked to them - in :doc:`LiveSite Editing </manual/editorial_tools/livesite_editor>` mode, editors can then change content of widgets, their order inside parent container, or can turn widgets on or off. And because it's content editing on front-end, visualization of the change is not an obstacle, editors see in real time how their change will affect web page.

First step is defining containers on your page - try to identify zone with specific characteristics and then turn them into containers. For example, page header can be one container, main content bar another, sidebar third, footer fourth container.

It is done in template, with command like this

.. code-block:: twig

    {% container 'frontpage_sidebar' with {
        'styles': 'border: solid 1px red',
        'cssClass': 'css_class_name',
        'data': {'custom-key': value}
    } %}
    ...
    {% endcontainer %}

Of course, parameters are not mandatory, so your container definition can be as simple as this:

.. code-block:: twig

    {% container 'frontpage_sidebar'%}
    ...
    {% endcontainer %}

This will generate block-level element on web page (div) with special class - it is used in live editing to define dynamic canvas where editors can work with widgets.

Containers can have none, one or more widgets - for example, think of frontpage sidebar; if it is defined as container in template, then editors can create several widgets in that container. From time to time they can turn on widget that highlights for example some recent interview; or can put some important announcement on top of the sidebar; etc - and
all that without any changes in templates, only by playing wiith :doc:`LiveSite Editing </manual/editorial_tools/livesite_editor>` mode.

Default widget that we have in mind here is html widget; it's content is markup that can be prepared externally (even in some html editor), and then just pasted as widget content. so when editors need to change something inside the widget, they will actually apply change in html.
