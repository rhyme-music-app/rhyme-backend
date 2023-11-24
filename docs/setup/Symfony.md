# Setup - The Symfony way

Your machine must have the following software installed (**in order**):

1. Git <https://git-scm.com/downloads>
2. PHP Interpreter: please [read this file](./php-notes.md)
3. Composer <https://getcomposer.org/download/>
4. Symfony CLI <https://symfony.com/download>. **Remember to add it to PATH**.

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
   environment variables:

   ```sh
   php bin/console app:refresh-env-symfony --dbname=... --dbhost=... --dbport=... --dbuser=... --dbpass=...
   ```

   You have to determine the database environment variables yourself.
   Also, some of them are optional. If you are not sure what they mean, type:

   ```sh
   php bin/console --help app:refresh-env-symfony
   ```

3. Setup Algolia credentials. [More details here](./algolia-notes.md).

4. **Now the setup completed. You don't have to repeat the above steps**
   **anymore.**

   From now on, you just have to do this step to switch the application on.

   Still in the project root, run the following command to start the server:

   ```sh
   symfony server:start
   ```

   Carefully read the output message in the terminal to know what is
   the web address of the server. Usually the server will be listening
   at <http://localhost:8000>.
