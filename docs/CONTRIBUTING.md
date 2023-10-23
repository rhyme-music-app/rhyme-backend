# Contributing to the Project

If you want to contribute to this project, first look through the file
[`README.md`](../README.md).

## Setup

Your machine must have the following software installed (**in order**):

1. Git <https://git-scm.com/downloads>
2. PHP Interpreter. Setup guide:
   <https://www.geeksforgeeks.org/how-to-install-php-in-windows-10/>.
   Rhyme requires **PHP version 8.2 or above**.
   Note that you may extract the software's content to a directory other
   than Program Files.
3. Composer <https://getcomposer.org/download/>
4. Docker Desktop <https://www.docker.com/products/docker-desktop/>

The following steps are applicable on both Windows and Linux/Ubuntu. Other
Linux distros should work out of the box. If you encountered any errors
while following the steps, take a look at the [Troubleshooting](#troubleshooting)
section below.

0. Clone the repository to your local machine using Git. Then `cd` to
   the project root.

1. Run the following commands to install required PHP libraries.
   After that, a new directory called `vendor` should appear inside
   the `/src` directory:

   ```sh
   composer install
   composer dump-autoload
   ```

2. Copy content in `.env.example` into a new file named `.env` in the
   project root, and modify the values in it if necessary.

3. Make sure the Docker service (daemon) has started.

   **On Windows**, open Docker Desktop, then close the window. (If it
   displays "Docker Desktop starting...", wait for that message to
   disappear, then close the window.)

   Next, run **the following command to check Docker service status**:

   ```powershell
   powershell -c "docker version | Out-Null"
   ```

   If the command does not output anything, Docker service has started.
   If it outputs something like:

   ```txt
   error during connect: This error may indicate that the docker daemon is not running.: Get ...
   ```

   or:

   ```txt
   Error response from daemon: open \\.\pipe\docker_engine_linux: The system cannot find the file specified.
   ```

   then Docker service has not started properly. Wait some more seconds
   then retry the check command above. If the problem persists, open
   Docker Desktop again as instructed initially.

    **On Linux**, just run the following command instead:

    ```sh
    sudo service docker start
    ```

4. Still in the project root, to build Docker image, run the following
   command **on Windows**:

   ```powershell
   docker compose build
   ```

   or, **on Linux**:

   ```sh
   sudo docker compose build
   ```

5. **Now the setup completed. You don't have to repeat the above steps**
   **anymore, unless one of the Docker files (`docker-compose.yml` and**
   **`Dockerfile`) has been changed.**

   From now on, you just have to do this step to switch the application on.

   Still in the project root, run the following command **on Windows**:

   ```powershell
   docker compose up
   ```

   or, **on Linux**:

   ```sh
   sudo docker compose up
   ```

   (Note that there is no dash between `docker` and `compose`. This is
   because the project demands one of the most recent Docker versions,
   which deprecates the old `docker-compose` command, and replaces it
   with `docker compose`.)

   Then, the local web application should be available at address
   <http://localhost:8000>. To shut down this Docker container,
   simply press `Ctrl-C` in the terminal.

   Note that while the Docker container is up, when any file is
   modified, you can simply reload it in browser to see the changes.
   You do not need to restart the whole Docker container.

Database data is saved into a folder named `db_data` in the project root.
So to clear database data, first shut down the container, then delete that
folder, and turn the container back on.

## API Documentation

Once you get the app up and running, figure out how to use the
API backend by referring to the [API Documentation](./API.md).

## Troubleshooting

1. Docker Error: No configuration file provided: not found

   Perhaps you are running `docker compose up` outside of the project root.
   You have to `cd` into the project root before turning on the app's Docker
   container.

2. Docker Error: Permission denied while trying to connect to the Docker
   daemon socket at unix:///...

   This is because you forgot the `sudo` magic word while executing Docker
   commands on Linux. For example, you have to run:

   ```sh
   sudo docker compose up
   ```

   instead of just `docker compose up` as on Windows.

   If the problem persists, maybe you haven't started the Docker daemon yet.
   Do that by running:

   ```sh
   sudo service docker start
   ```

   then retry.

3. Docker Error: Error during connect: This error may indicate that the docker daemon is not running...

   Reread the [Setup](#setup) section.

4. PHP Error: Failed to open stream: No such file or directory

   This error is displayed in the browser when you load a page of the web app.

   If the error message starts with "Warning: require_once(...
   **/vendor/autoload.php**): Failed to open stream...",
   then maybe you forgot to run Composer commands before launching the Docker
   container. Reread the [Setup](#setup) section.

   If the problem persists: First and foremost, check the paths in `include`
   and `require` statements, if any: Are the directories' names are written
   correctly ? Note that directories's names are case-sensitive !!!

   If the problem is still there, try other measures at
   <https://stackoverflow.com/a/36577021/13680015>.
