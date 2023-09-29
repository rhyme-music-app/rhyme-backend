# Test Project Setup (Temporary)

0. Follow all the steps in the **Get Started** section in file [`README.md`](/README.md).

1. Go to URL <http://localhost:8000/> and verify that the page displays:

        Welcome to the INDEX page
        Environment setup OK

2. Go to URL <http://localhost:8000/api/sample.php> and verify that the page displays:

        Hello World from class RhymeMusicApp\RhymeMusicApp\api\A
        Environment setup OK
        Database setup OK.

    Then refresh the page and verify that it displays:

        Hello World from class RhymeMusicApp\RhymeMusicApp\api\A
        Environment setup OK
        The table may have already been created.

3. Go to the following URLs:

     - <http://localhost:8000/.git/>
     - <http://localhost:8000/.gitignore>
     - <http://localhost:8000/lib/_global.php>
     - <http://localhost:8000/vendor/autoload.php>
     - <http://localhost:8000/.env>
     - <http://localhost:8000/.ENV>
     - <http://localhost:8000/.env.example>
     - <http://localhost:8000/composer.json>
     - <http://localhost:8000/composer.lock>
     - <http://localhost:8000/docker-compose.yml>
     - <http://localhost:8000/Dockerfile>

    and verify that they all display the following message:

        Rhyme

        The requested URL was not found on this server.
        Note: We can edit this error page content in file /404.php !


That's all ! If all the above work as expected, our setup is stable enough to be under sustainable development.
