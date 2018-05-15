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
publisher_analytics              RUNNING   pid 77111, uptime 0:00:04
publisher_webhooks               RUNNING   pid 77112, uptime 0:00:04
```

#### Running WebSocket Server

Supervisor config file for running WebSocket server can be found in [scripts/supervisor/supervisor_websocket.conf](scripts/supervisor/supervisor_websocket.conf)
