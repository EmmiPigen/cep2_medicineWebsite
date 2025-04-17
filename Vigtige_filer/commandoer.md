# Useful commands for symfony and php
symfony server:start
php bin/console asset-map:compile      

## Commands for the database
php bin/console make:migration
php bin/console doctrine:migrations:migrate


# For website serveren
sudo systemctl status apache2
sudo ufw status
sudo tail -f /var/log/apache2/error.log

