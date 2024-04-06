## Required
- framework laravel
- ssh access to gitlab
- php
- composer
- nodejs

## Add to nginx config 
root /home/forge/$host/public;

## Running
bash make-org.sh --short_org_name=some --domain=test.com --projects_path=/home/forge --repository=git@gitlab.com:some/some.git
