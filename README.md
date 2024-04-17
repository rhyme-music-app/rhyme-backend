# Rhyme

[Go to project's GitHub](https://github.com/rhyme-music-app/rhyme-backend)

Rhyme is a music web app featuring basic functionality.

This is a team project.

## Who we are

At the time of initiating this project, we are all students at
**University of Engineering and Technology (UET)**, which is a
member of **Vietnam National University (VNU) in Hanoi**. Our team
(hereafter referred to as **Rhyme Creators and Contributors**):

| Student ID |     Full Name     |          GitHub Profile              |
| :--------: | ----------------- | ------------------------------------ |
|  22028235  | Vũ Tùng Lâm       | <https://github.com/laam-egg>        |
|  22028167  | Hoàng Văn Phi     | <https://github.com/hoangvanphi2004> |
|  22028286  | Nguyễn Hữu Phương | <https://github.com/png261>          |
|  22028213  | Đỗ Thái Sơn       | <https://github.com/tsun165>         |
|  22028182  | Nguyễn Văn Thiện  | <https://github.com/nvt18624>        |

## Introduction

Databases are the general means of storing data in large-scale applications
as well as small- and medium-sized ones. In the **Database course (INT2211E 25)**
at UET-VNU, we have learnt the importance roles of databases and how to take
advantage of them. Furthermore, in the **Software Engineering course**
**(INT2208E 25)**, we've got to know the principles of software engineering,
including the software development lifecycle, attributes of good software,
the challenges and issues involved in software development in general.

To acquire a better understanding of databases and their
applications, as well as to apply knowledge of software
engineering in terms of practicality, we've together
formed a team to make this web app, **Rhyme**, which
is a music web service with basic functionality, using
a **MySQL database** under the hood.

This repository holds the backend of the app. The frontend is housed at:
<https://github.com/rhyme-music-app/rhyme-frontend>

## Features

🔐 Authentication.

🎵 Music Player.

🔍 Search Songs.

❤️ Like Songs.

🎶 Create Playlists.

➕ Add Songs to Playlists.

💻 Fully responsive.

## Database Schema

![erd](./docs/images/erd.png)

## Technologies

Rhyme API backend is based on the
**Symfony PHP Framework**.

Additionally, it makes use of the
following libraries:

- `algolia/algoliasearch-client-php`:
   to power the app's search engine,
   which is used to search songs
   based on their titles.
- `league/commonmark`: to convert
   Markdown to HTML, which is used
   in the backend's homepage to
   display API Reference Documentation.
- `firebase/php-jwt`: used to generate
   JWT-based authentication tokens for
   the app's register/login feature.
- `nelmio/cors-bundle`: to implement
   Cross Origin Resource Sharing (CORS)
   policies which are made mandatory
   by modern Web specifications.

It also uses other libraries. The full
list of this app's dependencies are
present in the file [`composer.json`](/composer.json).

## How to Run

Open the file [`docs/CONTRIBUTING.md`](/docs/CONTRIBUTING.md) for details.

## API Documentation

API documentation is present in the file [`docs/API.md`](/docs/API.md).

## License

Copyright (C) 2023-now **Rhyme Creators and Contributors**.

This app is licensed under the **3-clause BSD license**. Refer to the
[`LICENSE.txt`](/LICENSE.txt) file for details.
