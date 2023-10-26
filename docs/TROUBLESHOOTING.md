# Troubleshooting

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

   Reread the [Setup section in `CONTRIBUTING.md`](./CONTRIBUTING.md#setup).

4. PHP Error: Failed to open stream: No such file or directory

   This error is displayed in the browser when you load a page of the web app.

   If the error message starts with "Warning: require_once(...
   **/vendor/autoload.php**): Failed to open stream...",
   then maybe you forgot to run Composer commands before launching the Docker
   container. Reread the [Setup section in `CONTRIBUTING.md`](./CONTRIBUTING.md#setup).

   If the problem persists: First and foremost, check the paths in `include`
   and `require` statements, if any: Are the directories' names are written
   correctly ? Note that directories's names are case-sensitive !!!

   If the problem is still there, try other measures at
   <https://stackoverflow.com/a/36577021/13680015>.

5. Docker Error: max depth exceeded

   Run:

   ```sh
   docker rmi -f $(docker images -a -q)
   ```

   Source: <https://stackoverflow.com/a/58303779/13680015>.

6. PHP Error: SQLSTATE[HY000] [2002] No such file or directory / Connection refused

   Review your MySQL setup. Maybe you forgot to run a `refresh-env` command as
   instructed [in the setup guide](./CONTRIBUTING.md#setup).
