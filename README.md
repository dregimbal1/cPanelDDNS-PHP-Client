# cPanel Dynamic DNS Client
Just like NO-IP you can use this application on your local machine and when your public IP address changes it will reflect in your A record within cPanel. You do not need root access to the server to run this application.
### Fully Functional!
Run the script as much as you like and it will keep track of your public IP and update it accordingly. Once your IP does change this script will remove the old A Record prior to adding the new one. It is self-sufficent. Please star / follow if you find this useful.
### Version
0.0.1
### Installation
  - Download and unzip
  - Modify /Core/config.php
  - Run update.php to start
```sh
$ php update.php
```
License
----
GNU GENERAL PUBLIC LICENSE
