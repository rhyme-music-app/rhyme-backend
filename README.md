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
 - `/`: Contains PHP scripts that dump views (user interface) to the end-users, as well as some necessary files needed for the whole project.
 - `/api`: Contains PHP scripts that handle API endpoints.
 - `/lib`: Contains private and common PHP scripts that are included/required in forefront scripts, which are view scripts in `/` and API scripts in `/api` folder.

## Get Started

Your machine must have the following software installed (**in order**):
 - Git <https://git-scm.com/downloads>
 - PHP Interpreter. Setup guide: <https://www.geeksforgeeks.org/how-to-install-php-in-windows-10/>. Note that you may extract the software's content to a directory other than Program Files.
 - Composer <https://getcomposer.org/download/>
 - Docker Desktop <https://www.docker.com/products/docker-desktop/>

The following steps are applicable on both Windows and Linux/Ubuntu. Other Linux distros should work out of the box. If you encountered any errors while following the steps, take a look at the [**Troubleshooting** section in the file `CONTRIBUTING.md`](/CONTRIBUTING.md#troubleshooting).

0. Clone the repository to your local machine using Git. Then `cd` to the project root.

1. Run the following commands to install required PHP libraries. After that, a new directory called `vendor` should appear inside the `/src` directory:

	```sh
	composer install
	composer dump-autoload
	```

2. Copy content in `.env.example` into a new file named `.env` in the project root, and modify the values in it if necessary.

3. Make sure the Docker service (daemon) has started.

	**On Windows**, open Docker Desktop, then close the window. (If it displays "Docker Desktop starting...", wait for that message to disappear, then close the window.) Next, run **the following command to check Docker service status**:

	```powershell
	powershell -c "docker version | Out-Null"
	```

	If the command does not output anything, Docker service has started. If it outputs something like:

		error during connect: This error may indicate that the docker daemon is not running.: Get ...

	or:

		Error response from daemon: open \\.\pipe\docker_engine_linux: The system cannot find the file specified.
	
	then Docker service has not started properly. Wait some more seconds then retry the check command above. If the problem persists, try opening Docker Desktop again as instructed initially.

	**On Linux**, just run the following command instead:

	```sh
	sudo service docker start
	```

4. Still in the project root, to switch the application on as a Docker container, run the following command on Windows:

	```powershell
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

## Contributing

Open the file [`CONTRIBUTING.md`](/CONTRIBUTING.md) for details.

## License
Copyright (C) 2023-now Vũ Tùng Lâm, Nguyễn Hữu Phương and Đỗ Thái Sơn.

This app is licensed under the **3-clause BSD license**. Refer to the [`LICENSE.md`](/LICENSE.md) file for details.
