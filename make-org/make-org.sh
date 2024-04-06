#!/bin/bash

short_org_name=""
domain=""
projects_path=""
repository=""

while [[ "$#" -gt 0 ]]; do
    case "$1" in
        --short_org_name=*)
            short_org_name="${1#*=}"
            ;;
        --short_org_name)
            echo "Option --short_org_name must have a value"
            exit 1
            ;;
        --domain=*)
            domain="${1#*=}"
            ;;
        --domain)
            echo "Option --domain must have a value"
            exit 1
            ;;
        --projects_path=*)
            projects_path="${1#*=}"
            ;;
        --projects_path)
            echo "Option --projects_path must have a value"
            exit 1
            ;;
        --repository=*)
            repository="${1#*=}"
            ;;
        --repository)
            echo "Option --repository must have a value"
            exit 1
            ;;
        *)
            echo "Unknown option: $1"
            exit 1
            ;;
    esac
    shift
done

org_dir="$projects_path/$short_org_name.$domain"

echo -e "Making dir\n"
mkdir "$org_dir"

echo -e "Creating database\n"
php artisan db:create "$short_org_name"

cd "$org_dir" || { echo "Directory not found"; exit 1; }

echo -e "Clone repository\n"
git clone "$repository" .

echo -e "Coping .env\n"
cp /home/forge/x-max.space/.env .env

echo -e "Env configuration\n"
php configure_env.php --short_org_name="$short_org_name"

echo -e "Composer install\n"
composer install --ignore-platform-reqs

echo -e "Key generating\n"
php artisan key:generate

echo -e "NPM install\n"
npm i

echo -e "NPM build\n"
npm run build

echo -e "Run migration\n"
php artisan migrate --force

echo -e "Done\n"
