# Notes on using the Symfony PHP Framework

## Symfony Commands

To make a new API controller:

```sh
php bin/console make:controller API\ControllerClassName
```

or a new Web controller:

```sh
php bin/console make:controller Web\ControllerClassName
```

Controller files are located under the `/src/Controller`
directory.

To make an event subscriber:

```sh
php bin/console make:subscriber ExceptionSubscriber kernel.exception
```
