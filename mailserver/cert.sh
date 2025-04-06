sudo apt install certbot
sudo certbot certonly --standalone -d <subdomain>.<domain>
cp /etc/letsencrypt/live/<subdomain>.<domain>/fullchain.pem ./docker-data/ssl/
cp /etc/letsencrypt/live/<subdomain>.<domain>/privkey.pem ./docker-data/ssl/

#add mail
./setup.sh email add noreply@<domain> mypassword123
