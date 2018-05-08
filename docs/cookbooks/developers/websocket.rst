WebSocket Communication
=======================

There is a WebSocket server where the push notifications can be sent to the connected clients. These push notifications
are used to refresh the views or in other words, keeps everything synchronized.

In the background, the WebSocket server is using ZeroMQ queue (`WAMP sub-protocol and PubSub patterns<http://socketo.me/docs/wamp>`_)
and from there it sends everything to clients. There is no communication from client to server, all changes are handled via API.
For example, if the new content is pushed to Publisher, it is immediately sent to all the clients, meaning that the new content has been delivered.

How it works?
~~~~~~~~~~~~~

A client must connect to the WebSocket server and subscribe to the specific topic. In this it is ``package_created``.

If the new content will be sent to the Publisher, we will automatically receive info from the WebSocket server about newly
delivered package/content. Based on that info we can refresh or update existing view.

The default WebSocket server port is ``8080`` and host ``127.0.0.1``.

.. code-block:: html

  <script language="javascript" type="text/javascript" src="https://cdn.rawgit.com/cboden/fcae978cfc016d506639c5241f94e772/raw/e974ce895df527c83b8e010124a034cfcf6c9f4b/autobahn.js"></script>
  <script>
      var conn = new ab.Session('ws://127.0.0.1:8080',
          function() {
              conn.subscribe('package_created', function(topic, data) {
                  // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
                  console.log('New article published to "' + topic + '" : ' + data.title);
              });
          },
          function() {
              console.warn('WebSocket connection closed');
          },
          {'skipSubprotocolCheck': true}
      );
  </script>
