# Linemini admin side (Laravel)

## Default login

username/userid/email: admin/admin@chronostep.com
password: password

## Setup
locate linemini html folder or main folder
then git clone this repository inside using the command
```
git clone curiokids@curiokids.git.backlog.com:/LINEMINI/line-mini-laravel-admin.git
```

a folder will be created naming "line-mini-laravel-admin"
rename this folder to corresponding admin folder to be access by the user
example: manage
-> html -> manage

inside the manage folder run this commands:

install dependencies

```
composer install
```

if  an error occurs like this
```
  [Symfony\Component\Process\Exception\ProcessTimedOutException]
  The process "'/usr/bin/unzip' -qq '/var/www/linemini/html/manage/vendor/composer/tmp-191dc4378e3f61be0ef1db7995297a
  bb' -d '/var/www/linemini/html/manage/vendor/composer/68b3f2b7'" exceeded the timeout of 300 
```

then please remove existing composer.lock.json

then please run the command
```
composer config --global process-timeout 2000
composer update
```

copy .env.example file as .env file
```
cp .env.example .env
```

update corresponding environment variables in .env file

APP_DIR=admin

DB_HOST=mariadb
DB_DATABASE=coop
DB_USERNAME=root
DB_PASSWORD=root


generate laravel unique key for the application (can be found in .env APP_KEY
```
php artisan key:generate
```

run npm installation
```
npm install
```

compile npm resources
```
npm run dev
```

run migration
```
php artisan migrate --seed
```

link storage
```
php artisan storage:link
```

## Database

When migrating database avoid using commands
```
php artisan migrate:fresh
```
or
```
php artisan migrate:refresh
```
this will remove all existing tables in the current database which is not included in the admin laravel migration

## AWS S3 Bucket Setup

Note: you can use the existing configuration under the .env.example file for local development and testing

Requirement
- AWS IAM user
- AWS Bucket 

For steps AWS IAM user and bucket creation please refer to this tutorial
Note: bucket creation
1. Block public access = off
2. Access control list (ACL) = enabled
https://www.itsolutionstuff.com/post/laravel-amazon-s3-file-upload-tutorialexample.html

After creating a bucket update its permission policy
- click your bucket name
- go to permissions
- edit bucket policy

```
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "Statement1",
            "Effect": "Allow",
            "Principal": "*",
            "Action": [
                "s3:GetObject",
                "s3:GetObjectVersion"
            ],
            "Resource": "arn:aws:s3:::rynebucketskie/*"
        }
    ]
}
```

Then update your .env configuration with its corresponding value

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false
