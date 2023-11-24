# PHP Notes

Regardless of which way you want to go, the Docker way
or the Symfony one, you have to install PHP Interpreter
first. Rhyme requires **PHP version 8.2 or above**.

Setup guide on Windows: <https://www.geeksforgeeks.org/how-to-install-php-in-windows-10/>. Note that you may
extract the software's content to a directory other
than Program Files.

On Linux: <https://www.geeksforgeeks.org/how-to-install-php-on-linux/>.

Then, you have to enable required PHP extensions:

1. curl
2. fileinfo
3. gd
4. intl
5. mbstring
6. mysqli
7. openssl
8. pdo
9. pdo_mysql
10. sodium
11. xml
12. zip
13. opcache (optional, but highly recommended)

To enable those extensions on Linux, you may `cd` to the project root, then run:

```sh
sudo chmod +x linux-enable-php-extensions.sh
sudo        ./linux-enable-php-extensions.sh
```

On Windows, find the `php.ini` file and uncomment the `extension=X` lines,
where `X` is the name of a required extension. (Google for more.)

Finally, to allow the application to work with Algolia-based search,
you have to follow the instructions [here](https://stackoverflow.com/a/31830614/13680015).
