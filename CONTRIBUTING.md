# Contributing to the Project

If you want to contribute to this project, first look through the **Get Started** and **Project Filesystem Structure** sections in the file `README.md`. Once you understand all parts of the project and are able to get the app up and running, you can develop it further.

## View and API routes/route handlers

Routes are basically the paths in the URL that the server can expose to the client. For example, in development, the app listens at <http://localhost:8000>. Consider the following URLs:

1. <http://localhost:8000/>
2. <http://localhost:8000/index.php>
3. <http://localhost:8000/api/sample.php>
4. <http://localhost:8000/.env>

then URL 1 refer to the **route** `/`, URL 2 **route** `/index.php`, and URL 3 **route** `/api/sample.php`. URL 4 is not a route, since the server *does not expose* that file to any client. (If you access that URL you will get a 404 - URL not found response).

The PHP script that serves a route is called the **route handler** of that route. In PHP, a route with just the directory name (no file name) is handled by the file `index.php` or `index.html` under that directory. Therefore:

1. Route `/` is handled by the **route handler** `<projectroot>/index.php`.
2. Route `/index.php` is handled by the **route handler** `<projectroot>/index.php` too.
3. Route `/api/sample.php` is handled by the **route handler** `<projectroot>/api/sample.php`, that is, the `sample.php` file under the `api` directory under the project root directory.

Routes fall into two categories: view routes and API routes.

 - **View routes** are the routes whose handlers are not inside the `/api` directory. For example, `/login.php`, `/signup.php`...

 - **API routes** are the routes whose handlers are inside the `/api` directory, e.g. `/api/sample.php`.

End-users can directly access the view routes to be served a webpage/user interface (UI) which they can interact with easily. Meanwhile, API routes represent API endpoints that the client-side Javascript code in the UI webpages can access to obtain data and render it to the pages dynamically, without the need to reload the page.

## Coding Convention

0. Always use PHP file extension in favor of HTML.

1. Any view route handler, as well as API route handler and class file, must declare its namespace at the beginning of the PHP file, and include the appropriate begin code:

    ```php
    <?php
        namespace RhymeMusicApp\RhymeMusicApp\...;
        /* where ... is path to the file as relative to the project root, case-sensitive*/

        require_once /*begin code*/;
    ?>
    ```

    For view route handlers: `/*begin code*/ = $_SERVER['DOCUMENT_ROOT'] . '/lib/view_begin.php'`

    For API route handlers: `/*begin code*/ = $_SERVER['DOCUMENT_ROOT'] . '/lib/api_begin.php'`

    For class files: `/*begin code*/ = $_SERVER['DOCUMENT_ROOT'] . '/lib/class_begin.php'`

    For example, inside the API route handler `/api/sample.php`, we have to place the following statement on top:

    ```php
    <?php
        namespace RhymeMusicApp\RhymeMusicApp\api;

        require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/api_begin.php';
    ?>
    ```

    Another example: Inside the view route handler `/index.php`, we have to place the following statement on top:

    ```php
    <?php
        namespace RhymeMusicApp\RhymeMusicApp;

        require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/view_begin.php';
    ?>
    ```

    Again, note that the namespace names correspond to the filesystem directories of the project, on a **case sensitive** basis.

2. Always place this code at the end of a route handler or class file:

    ```php
    <?php
        require_once /*end code*/;
    ?>
    ```

    For view route handlers: `/*begin code*/ = $_SERVER['DOCUMENT_ROOT'] . '/lib/view_end.php'`

    For API route handlers: `/*begin code*/ = $_SERVER['DOCUMENT_ROOT'] . '/lib/api_end.php'`

    For class files: `/*begin code*/ = $_SERVER['DOCUMENT_ROOT'] . '/lib/class_end.php'`

3. View route handlers should be placed right in the project root directory, unless there is a very good reason not to do so.

4. API route handlers must be placed in the `<projectroot>/api` directory.

## Troubleshooting

### Docker Error: Permission denied while trying to connect to the Docker daemon socket at unix:///...

This is because you forgot the `sudo` magic word while executing Docker commands on Linux. For example, you have to run:

```sh
sudo docker compose up
```

instead of just `docker compose up` as on Windows.

If the problem persists, maybe you haven't started the Docker daemon yet. Do that by running:

```sh
sudo service docker start
```

then retry.

### Docker Error: Error during connect: This error may indicate that the docker daemon is not running...

On Windows: Run Docker Desktop, make sure the service has started, then retry.

On Linux: Run the following command:

```sh
sudo service docker start
```

then retry.

### PHP Error: Failed to open stream: No such file or directory

This error is displayed in the browser when you load a page of the web app.

If the error message starts with "Warning: require_once(...**/vendor/autoload.php**): Failed to open stream...", then maybe you forgot to run Composer commands before launching the Docker container. Reread the [**Get Started** section in `README.md`](/README.md#get-started).

If the problem persists: First and foremost, check the paths in `include` and `require` statements: Are the directories' names and order are written correctly ? Note that directories's names are case-sensitive !!!

If the problem is still there, try other measures at <https://stackoverflow.com/a/36577021/13680015>.
