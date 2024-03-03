# Setup - The Docker way

Your machine must have the following software installed (**in order**):

1. Git <https://git-scm.com/downloads>
2. PHP Interpreter: please [read this file](./php-notes.md)
3. Composer <https://getcomposer.org/download/>
4. Docker Desktop <https://www.docker.com/products/docker-desktop/>

The following steps are applicable on both Windows and Linux/Ubuntu. Other
Linux distros should work out of the box. If you encountered any error
while following the steps, read file [`TROUBLESHOOTING.md`](../TROUBLESHOOTING.md).

0. Clone the repository to your local machine using Git. Then `cd` to
   the project root.

1. Run the following commands to install required PHP libraries.
   After that, a new directory called `vendor` should appear inside
   the `/src` directory:

   ```sh
   composer install
   composer dump-autoload
   ```

2. Still in the project root, run the following command to setup
   fresh environment variables automatically:

   ```sh
   php bin/console app:refresh-env-docker
   ```

3. Setup Algolia credentials. [More details here](./algolia-notes.md).

4. Make sure the Docker service (daemon) has started.

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

5. Still in the project root, to build Docker image, run the following
   command **on Windows**:

   ```powershell
   docker compose build
   ```

   or, **on Linux**:

   ```sh
   sudo docker compose build
   ```

6. **Now the setup completed. You don't have to repeat the above steps**
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

Database data is saved into a folder named `db-data` in the project root.
So to clear database data, first shut down the container, then delete that
folder, and turn the container back on.
