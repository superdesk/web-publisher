#### Installing and Configuring Supervisor

On Ubuntu it can be installed with:

`apt-get install supervisor` 

Once this has completed, the supervisor daemon should already be started, as the prebuilt packages come with an init script that will also ensure the Supervisor is restarted after a system reboot. You can ensure this is the case by running:

`service supervisor restart`

The program configuration files for Supervisor programs are found in the `/etc/supervisor/conf.d` directory, normally with one program per file and a .conf extension. 
We prepared ready to use configuration files for publisher consumers. You can find them in `scripts/supervisor` directory. Copy  them to supervisor configs directory and run `supervisorctl reload`.

More detailed configuration tutorial can be found [here](https://www.digitalocean.com/community/tutorials/how-to-install-and-manage-supervisor-on-ubuntu-and-debian-vps).

#### Running RabbitMQ Consumers

Be sure to adjust (to your real publisher location) directory and logs paths inf config files.

Good configuration should give you similar output from command `supervisorctl status`:

```bash
messenger-consume      RUNNING   pid 77111, uptime 0:00:04
publisher-websocket    RUNNING   pid 77112, uptime 0:00:04
```

#### Running WebSocket Server

Websocket server is using amqp library and RabbitMQ. The Exchange (swp_websocket_exchange) and Queue (swp_websocket) is created automatically but you need to manually bind the swp_websocket queue to swp_websocket_exchange from server admin panel.

Binding Queue to the Exchange:

```bash
sudo rabbitmq-plugins enable rabbitmq_management
wget http://127.0.0.1:15672/cli/rabbitmqadmin
chmod +x rabbitmqadmin
sudo mv rabbitmqadmin /etc/rabbitmq

/etc/rabbitmq/rabbitmqadmin --vhost=/ declare binding source="swp_websocket_exchange" destination="swp_websocket"
```

Supervisor config file for running WebSocket server can be found in [scripts/supervisor/supervisor_websocket.conf](scripts/supervisor/supervisor-websocket.conf)
