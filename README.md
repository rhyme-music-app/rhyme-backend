# Rhyme

Rhyme is a music web app featuring basic functionality.

This is a team project.

## Who we are

At the time of initiating this project, we are all students at **University of Engineering and Technology (UET)**, which is a member of **Vietnam National University (VNU)** in Hanoi. Our team:

| Student ID |     Full Name     |          GitHub Profile         |
| :--------: | ----------------- | ------------------------------- |
|  22028235  | Vũ Tùng Lâm       | <https://github.com/laam-egg>   |
|  22028286  | Nguyễn Hữu Phương | <https://github.com/png261>     |
|  22028213  | Đỗ Thái Sơn       | <https://github.com/tsun165>    |

## Introduction

Databases are the general means of storing data in large-scale applications as well as small- and medium-sized ones.
In the **Database course (INT2211E-25)** at UET-VNU, we are taught the importance roles of databases and how to take advantage of them.
To acquire a better understanding of databases and their applications, we together formed a team to make this web app, **Rhyme**, which is a music web service with basic functionality, using a **MySQL database** under the hood.

## Features

(Write this later)

## Database Schema

(Write this later)

## Project Filesystem Structure
 - `/`: Contains PHP scripts that dump views (user interface) to the end-users, as well as some necessary files.
 - `/api`: Contains PHP scripts that handle API endpoints.
 - `/lib`: Contains private and common PHP scripts that are included/required in forefront scripts, which are view scripts in `/` and API scripts in `/api` folder.

## Get Started

Your machine must have the following software installed:
 - Docker Desktop <https://www.docker.com/products/docker-desktop/>
 - Git <https://git-scm.com/downloads>
 - Composer <https://getcomposer.org/download/>

The following steps are applicable on both Windows and Linux/Ubuntu. Other Linux distros should work out of the box.

0. Clone the repository to your local machine using Git. Then `cd` to the project root.

1. Run the following commands to install required PHP libraries. After that, a new directory called `vendor` should appear inside the `/src` directory:

	```sh
	composer install
	composer dump-autoload
	```

2. Copy content in `.env.example` into a new file named `.env` in the project root, and modify the values in it if necessary.

3. On Windows, open Docker Desktop and make sure the service (daemon) has started. On Linux, run the following command instead:

	```sh
	sudo service docker start
	```

4. To switch the application on as a Docker container, run the following command on Windows:

	```sh
	docker compose up
	```

	or, on Linux:

	```sh
	sudo docker compose up
	```

	(Notice there is no dash between `docker` and `compose`. This is because the project demands one of the most
	recent Docker versions, which deprecates the old `docker-compose` command, and replaces it with `docker compose`.)

	Then, the local web application should be available at <http://localhost:8000>. To shut down this Docker container, simply press `Ctrl-C` in the terminal.

	Note that while the Docker container is up, when any file is modified, you can simply reload it in browser to see the changes. You do not need to restart the whole Docker container.


Database data is saved into a folder named `db_data` in the project root. So to clear database data, first shut down the container, then delete that folder, and turn the container back on.

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

then retry your previous attempt.

### Docker Error: Error during connect: This error may indicate that the docker daemon is not running...

Now run Docker Desktop, make sure the service has started, then retry.

### PHP Error: Failed to open stream: No such file or directory

This error is displayed in the browser when you load a page of the web app.

If the error message starts with "Warning: require_once(...**/vendor/autoload.php**): Failed to open stream...", then maybe you forgot to run Composer commands before launching the Docker container. Reread the [Get Started](#get-started) section.

If the problem persists: First and foremost, check the paths in `include` and `require` statements: Are the directories' names and order are written correctly ? Note that directories's names are case-sensitive !!!

If the problem is still there, try other measures at <https://stackoverflow.com/a/36577021/13680015>.

## License
Copyright (C) 2023-now Vũ Tùng Lâm, Nguyễn Hữu Phương and Đỗ Thái Sơn.

This app is licensed under the **3-clause BSD license**. Refer to the [`LICENSE.md`](/LICENSE.md) file for details.
