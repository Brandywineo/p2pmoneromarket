``` nano /etc/systemd/system/monero-deposit-daemon.service ```sh

paste this

 GNU nano 7.2          /etc/systemd/system/monero-deposit-daemon.service
```
[Unit]
Description=Monero Deposit Scanner Daemon
After=network.target monero-wallet-rpc.service
Requires=monero-wallet-rpc.service
[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/var/www/moneromarket/daemon
ExecStart=/usr/bin/php deposit_daemon.php
Restart=always
RestartSec=5
# Hardening
NoNewPrivileges=true
PrivateTmp=true
ProtectSystem=full
ProtectHome=true
ReadWritePaths=/var/www/moneromarket
# Logging
StandardOutput=journal
StandardError=journal
[Install]
WantedBy=multi-user.target
```
```  nano /etc/systemd/system/monerod.service```
```
[Unit]
Description=Monero Daemon
After=network.target
[Service]
Type=simple
User=root
ExecStart=/root/monero-x86_64-linux-gnu-v0.18.4.5/monerod \
  --data-dir=/var/lib/monero/.bitmonero \
  --non-interactive
Restart=always
RestartSec=10
[Install]
WantedBy=multi-user.target
```
``` nano /etc/systemd/system/monero-wallet-rpc.service```
```
[Unit]
Description=Monero Wallet RPC Service
After=network.target monerod.service
Wants=monerod.service
[Service]
User=root
Group=root
Type=simple
ExecStart=/root/monero-x86_64-linux-gnu-v0.18.4.5/monero-wallet-rpc \
    --wallet-file /root/monerowallet/walletName \
    --password "07911617" \
    --rpc-bind-port 18083 \
    --rpc-bind-ip 127.0.0.1 \
    --disable-rpc-login \
    --confirm-external-bind \
    --trusted-daemon \
    --daemon-address 127.0.0.1:18081
Restart=always
RestartSec=5
LimitNOFILE=4096
[Install]
WantedBy=multi-user.target
```
