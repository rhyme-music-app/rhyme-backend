# Contributing to the Project

If you want to contribute to this project, first look through the file
[`README.md`](../README.md).

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
to run GitHub Actions locally like this:

```sh
act -P ubuntu-22.04=shivammathur/node:2204 --secret-file act.secrets
```

where `act.secrets` is a file that provides GitHub secrets that
the GitHub Actions scripts require. Those secrets will be made
available by `nektos act` when running the scripts locally.

To create your own `act.secrets`, take a look at the file
`act.secrets.example`.
