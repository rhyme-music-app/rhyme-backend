# Contributing to the Project

If you want to contribute to this project, first look through the file
[`README.md`](../README.md).

- [Contributing to the Project](#contributing-to-the-project)
  - [Setup](#setup)
    - [The Docker way](#the-docker-way)
    - [The Symfony way](#the-symfony-way)
  - [API Documentation](#api-documentation)
  - [Running GitHub Actions locally for testing purpose](#running-github-actions-locally-for-testing-purpose)
  - [Deploying to Production](#deploying-to-production)

## Setup

You can choose whether to run the Docker-containerized Apache server with
MySQL built-in (the Docker way), or to run the Symfony local development
server (the Symfony way).

### The Docker way

**Pros**: You don't have to setup MySQL yourself. The database data
is saved in `<projectroot>/db-data`, separate from your
system's MySQL.

**Cons**: Response time is too slow on Windows (~10s/request).

To go the Docker way: [click here](./setup/Docker.md).

It is highly recommended to go the Symfony way, since the response time
is much faster.

### The Symfony way

This is the recommended approach.

**Pros**: Response time is very quick (instant). Also, the server is
lightweight, i.e. it can be run on sluggish systems.

**Cons**: You have to configure MySQL yourself.

To go the Symfony way: [click here](./setup/Symfony.md).

## API Documentation

Once you get the app up and running, figure out how to use the
API backend by referring to the [API Documentation](./API.md).

## Running GitHub Actions locally for testing purpose

First, install [`nektos act`](https://nektosact.com/installation/index.html)
to run GitHub Actions locally like this,
for testing purpose:

```sh
act -P ubuntu-22.04=shivammathur/node:2204
```

## Deploying to Production

On production server, beside the required extensions
mentioned [here](./setup/php-notes.md), one has to
enable the following extensions as well in order
to run Composer:

1. dom
2. phar
3. xmlwriter
4. yaml

Then, open an SSH session into the production
server, `cd` to `/var/www/html` or the top-level
directory of your hosted domain (i.e. a
directory that if we were to make a simple
PHP website, we would put a `index.php` into
it.). Make sure the directory is empty (containing
no files and subdirectories), then run the
following commands:

```sh
git clone https://github.com/rhyme-music-app/rhyme-backend.git .
composer install --no-dev
```

**After that, you will no longer need to**
**do all the above stuff again.** Whenever
the master branch has production-ready
changes, run the following commands:

```sh
git pull origin master
composer install --no-dev
```

Note that the `.env` file on production
must have `APP_ENV=prod`, otherwise the
command `composer install --no-dev` would
yield errors.

The production server should be an Apache server,
due to various use of `.htaccess` throughout
this application. Note that the top-level `.htaccess`
is omitted since it is specific to the hosting
platform being used. Nevertheless, there are
some common rules that need to be specified in
the top-level `.htaccess`. Refer to the file
`.htaccess.example` (in the project root) to
know how to properly write a top-level `.htaccess`
file that suits your hosting environment and use cases.
