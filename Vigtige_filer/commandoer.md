# Useful commands for symfony and php
symfony server:start
php bin/console asset-map:compile    
php bin/console debug:router  

## Commands for the database
php bin/console make:migration
php bin/console doctrine:migrations:migrate


# For website serveren
sudo systemctl status apache2
sudo ufw status
sudo tail -f /var/log/apache2/error.log

# Adgang til serveren med SSH
ssh ubuntu@129.151.199.162

Du skal bruge din private nøgle til at logge ind på serveren. Den private nøgle er gemt i din .ssh mappe på din computer. Som på windows ligger i C:\Users\dit_brugernavn\.ssh\ og på mac ligger i /Users/dit_brugernavn/.ssh/.

# Den private nøgle får du af Emilie, Skriv til mig hvis du vil have den.

