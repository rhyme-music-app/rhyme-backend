# API Documentation

[Go to project's GitHub](https://github.com/rhyme-music-app/rhyme-backend)

- [API Documentation](#api-documentation)
  - [Conventions](#conventions)
  - [Endpoints](#endpoints)
    - [Authentication API](#authentication-api)
    - [User API](#user-api)
    - [Playlist API](#playlist-api)
    - [Genre API](#genre-api)
    - [Artist API](#artist-api)
    - [Song API](#song-api)
    - [Indexing API](#indexing-api)
  - [JSON Object Schemas](#json-object-schemas)
    - [ArtistInfo](#artistinfo)
    - [ArtistUpdate](#artistupdate)
    - [GenreInfo](#genreinfo)
    - [GenreUpdate](#genreupdate)
    - [PlaylistInfo](#playlistinfo)
    - [PlaylistUpdate](#playlistupdate)
    - [SongInfo](#songinfo)
    - [SongUpdate](#songupdate)
    - [UserInfo](#userinfo)
    - [UserUpdate](#userupdate)

## Conventions

1. **GET, POST, PUT, PATCH**: HTTP methods to be used with each endpoint.

2. **AUTH**: When accessing endpoints marked AUTH, the client must provide the access token of the user.

3. **ADMIN**: When accessing endpoints marked ADMIN, the authenticated user must be an admin. Endpoints not marked ADMIN can be consumed by any authenticated user regardless of whether they are a normal or an admin user.

4. **UserInfo, UserUpdate, SongInfo, SongUpdate, GenreInfo, GenreUpdate, PlaylistInfo, PlaylistUpdate**: Go to section [JSON Object Schemas](#json-object-schemas) to see what they are.

5. In this document, **empty response on success** actually means the following JSON response:

    ```json
    {
        "success": true
    }
    ```

    There's no data other than a field indicating the response's status,
    hence the name, even though it is not literally empty.

## Endpoints

### Authentication API

1. **POST /api/auth/register**

    Registers a new user.

     - Payload: [`UserUpdate`](#userupdate).
     - Response on success: [`UserInfo`](#userinfo).

2. **POST /api/auth/login**

    Returns token of a user, given their credentials.

     - Payload:

        ```json
        {
            "email": "any@email.com",
            "password": "12345678"
        }
        ```

     - Response on success: [`UserInfo`](#userinfo) plus one field
       named `token`, which is the authentication token used for
       AUTH routes.

3. **POST AUTH /api/auth/logout**

    Invalidates a token of an authenticated user. The invalidated token can no longer be used.

     - Empty payload.
     - Empty response on success.

### User API

1. **GET /api/users**

    Returns a list of all users in the system.

     - Empty payload.
     - Response on success:

        ```json
        {
            "list": [ ... ]
        }
        ```

        where `[ ... ]` is a list of [`UserInfo`](#userinfo)
        objects.

2. **GET /api/users/{user_id}**

    Returns information of a user, given their ID.

     - Empty payload.
     - Response on success: [`UserInfo`](#userinfo).

3. **POST /api/users/signup** OR **POST /users**

    Same as `/api/auth/register`.

4. **PUT AUTH /api/users/{user_id}**

    Updates information of a user, given their ID and the updated information.

    Every user, including every admin, cannot update information of another user.

     - Payload: [`UserUpdate`](#userupdate).
     - Response on success: [`UserInfo`](#userinfo).

5. **DELETE AUTH /api/users/{user_id}**

    Deregisters, or deletes a user account, given their ID.

    Normal users can only delete their own accounts, not others'.

    Admin users can delete any account.

     - Empty payload.
     - Empty response on success.

6. **GET AUTH /api/users/{user_id}/favorite/playlists**

    Retrieves a list of this user's favorite playlists.

    The specified user must be the currently-authenticated
    user.

     - Empty payload.
     - Response on success:

        ```json
        {
            "list": [ ... ]
        }
        ```

        where `[ ... ]` is a list of [`PlaylistInfo`](#playlistinfo)
        objects.

7. **POST AUTH /api/users/{user_id}/favorite/playlists/{playlist_id}**

    Marks an existing playlist as one of the user's favorites.

    The specified user must be the currently-authenticated
    user.

     - Empty payload.
     - Empty response on success.

8. **DELETE AUTH /api/users/{user_id}/favorite/playlists/{playlist_id}**

    Removes a playlist from the user's favorite list.

    The specified user must be the currently-authenticated
    user.

     - Empty payload.
     - Empty response on success.

9. **GET AUTH /api/users/{user_id}/favorite/songs**

    Retrieves a list of this user's favorite songs.

    The specified user must be the currently-authenticated
    user.

     - Empty payload.
     - Response on success:

        ```json
        {
            "list": [ ... ]
        }
        ```

        where `[ ... ]` is a list of [`SongInfo`](#songinfo)
        objects.

10. **POST AUTH /api/users/{user_id}/favorite/songs/{song_id}**

    Marks an existing song as one of the user's favorites.

    The specified user must be the currently-authenticated
    user.

     - Empty payload.
     - Empty response on success.

11. **DELETE AUTH /api/users/{user_id}/favorite/songs/{song_id}**

    Removes a song from the user's favorite list.

    The specified user must be the currently-authenticated
    user.

     - Empty payload.
     - Empty response on success.

12. **GET /api/users/{user_id}/own/playlists**

    If the specified user is the currently-authenticated
    user: returns all playlists that the user has
    created.

    Otherwise: returns only the playlists that the user
    has created **AND IS PUBLIC**.

     - Empty payload.
     - Response on success:

        ```json
        {
            "list": [ ... ]
        }
        ```

        where `[ ... ]` is a list of [`PlaylistInfo`](#playlistinfo)
        objects.

### Playlist API

1. **GET /api/playlists**

    Returns a list of all playlists in the system that the
    currently-authenticated user has the right to access.

    If the user has not logged in: only public playlists
    are returned.

    If the user has logged in: only public playlists and
    all of his playlists are returned.

    In neither case will other users' private playlists
    be returned.

     - Empty payload.
     - Response on success:

        ```json
        {
            "list": [ ... ]
        }
        ```

        where `[ ... ]` is a list of [`PlaylistInfo`](#playlistinfo)
        objects.

2. **GET /api/playlists/{playlist_id}**

    Returns information of a playlist, given its ID.

    If the playlist is not public, only the authenticated user
    who owns it can access its information. See details about
    playlist ownership in [`PlaylistInfo`](#playlistinfo).

     - Empty payload.
     - Response on success: [`PlaylistInfo`](#playlistinfo).

3. **POST AUTH /api/playlists**

    Creates a new playlist.

     - Payload: [`PlaylistUpdate`](#playlistupdate).
     - Response on success: [`PlaylistInfo`](#playlistinfo).

4. **PUT AUTH /api/playlists/{playlist_id}**

    Updates an existing playlist, given its ID.

    Only the authenticated user who owns the playlist can
    modify it. See details about playlist ownership in
    [`PlaylistInfo`](#playlistinfo).

     - Payload: [`PlaylistUpdate`](#playlistupdate).
     - Response on success: [`PlaylistInfo`](#playlistinfo).

5. **DELETE AUTH /api/playlists/{playlist_id}**

    Deletes an existing playlist, given its ID.

    Only the authenticated user who owns the playlist can
    delete it. See details about playlist ownership in
    [`PlaylistInfo`](#playlistinfo).

     - Empty payload.
     - Empty response on success.

6. **GET /api/playlists/{playlist_id}/songs**

    Retrieves all of this playlist's songs.

    If the playlist is not public, only the authenticated user
    who owns it can access its information. See details about
    playlist ownership in [`PlaylistInfo`](#playlistinfo).

     - Empty payload.
     - Response on success:

        ```json
        {
            "list": [ ... ]
        }
        ```

        where `[ ... ]` is a list of [`SongInfo`](#songinfo)
        objects.

7. **POST AUTH /api/playlists/{playlist_id}/songs/{song_id}**

    Adds a song to an existing playlist.

    Only the authenticated user who owns the playlist can
    modify it. See details about playlist ownership in
    [`PlaylistInfo`](#playlistinfo).

     - Empty payload.
     - Empty response on success.

8. **DELETE AUTH /api/playlists/{playlist_id}/songs/{song_id}**

    Removes a song from an existing playlist.

    Only the authenticated user who owns the playlist can
    modify it. See details about playlist ownership in
    [`PlaylistInfo`](#playlistinfo).

     - Empty payload.
     - Empty response on success.

### Genre API

1. **GET /api/genres**

    Returns names of all genres available in the system.

     - Empty payload.
     - Response on success:

        ```json
        {
            "list": [ "..." ]
        }
        ```

        where `[ ... ]` is a list of [`GenreInfo`](#genreinfo)
        objects.

2. **GET /api/genres/{genre_id}**

    Returns information of a genre.

     - Empty payload.
     - Response on success: [`GenreInfo`](#genreinfo).

3. **POST AUTH ADMIN /api/genres**

    Creates a new genre.

     - Payload: [`GenreUpdate`](#genreupdate).
     - Response on success: [`GenreInfo`](#genreinfo).

4. **PUT AUTH ADMIN /api/genres/{genre_id}**

    Updates an existing genre.

     - Payload: [`GenreUpdate`](#genreupdate).
     - Response on success: [`GenreInfo`](#genreinfo).

5. **DELETE AUTH ADMIN /api/genres/{genre_id}**

    Deletes an existing genre.

     - Empty payload.
     - Empty response on success.

6. **GET /api/genres/{genre_id}/songs**

    Retrieves a list of this genre's songs.

     - Empty payload.
     - Response on success:

        ```json
        {
            "list": [ ... ]
        }
        ```

        where `[ ... ]` is a list of [`SongInfo`](#songinfo) objects.

### Artist API

1. **GET /api/artists**

    Returns a list of all artists available in the system.

     - Empty payload.
     - Response on success:

        ```json
        {
            "list": [ ... ]
        }
        ```

        where `[ ... ]` is a list of [`ArtistInfo`](#artistinfo)
        objects.

2. **GET /api/artists/{artist_id}**

    Returns information of an artist, given their ID.

     - Empty payload.
     - Response on success: [`ArtistInfo`](#artistinfo).

3. **POST AUTH ADMIN /api/artists**

    Adds a new artist.

     - Payload: [`ArtistUpdate`](#artistupdate).
     - Response on success: [`ArtistInfo`](#artistinfo).

4. **PUT AUTH ADMIN /api/artists/{artist_id}**

    Updates an existing artist.

    - Payload: [`ArtistUpdate`](#artistupdate).
    - Response on success: [`ArtistInfo`](#artistinfo).

5. **DELETE AUTH ADMIN /api/artists/{artist_id}**

    Deletes an existing artist.

     - Empty payload.
     - Empty response on success.

6. **GET /api/artists/{artist_id}/songs**

    Retrieves a list of this artist's songs.

     - Empty payload.
     - Response on success:

        ```json
        {
            "list": [ ... ]
        }
        ```

        where `[ ... ]` is a list of [`SongInfo`](#songinfo)
        objects.

### Song API

1. **GET /api/songs**

    Returns a list of all songs in the system.

     - Empty payload.
     - Response on success:

        ```json
        {
            "list": [ ... ]
        }
        ```

        where `[ ... ]` is a list of [`SongInfo`](#songinfo)
        objects.

2. **GET /api/songs/{song_id}**

    Returns information of a song. Do not increment streams.

     - Empty payload.
     - Response on success: [`SongInfo`](#songinfo).

3. **POST AUTH ADMIN /api/songs**

    Adds a new song.

     - Payload: [`SongUpdate`](#songupdate).
     - Response on success: [`SongInfo`](#songinfo).

4. **PUT AUTH ADMIN /api/songs/{song_id}**

    Updates information of a song.

     - Payload: [`SongUpdate`](#songupdate).
     - Response on success: [`SongInfo`](#songinfo).

5. **DELETE AUTH ADMIN /api/songs/{song_id}**

    Deletes a song.

     - Empty payload.
     - Empty response on success.

6. **GET /api/songs/{song_id}/listen**

    Returns information of the song, and increment its stream count.

     - Empty payload.
     - Response on success: [`SongInfo`](#songinfo).

7. **GET /api/songs/{song_id}/artists**

    Retrieves a list of this song's artists.

     - Empty payload.
     - Response on success:

        ```json
        {
            "list": [ ... ]
        }
        ```

        where `[ ... ]` is a list of [`ArtistInfo`](#artistinfo)
        objects.

8. **POST AUTH ADMIN /api/songs/{song_id}/artists/{artist_id}**

    Adds an artist to this song's list of artists.

     - Empty payload.
     - Empty response on success.

9. **DELETE AUTH ADMIN /api/songs/{song_id}/artists/{artist_id}**

    Removes an artist from this song's list of artists.

     - Empty payload.
     - Empty response on success.

10. **GET /api/songs/{song_id}/genres**

    Retrieves a list of this song's genres.

     - Empty payload.
     - Response on success:

        ```json
        {
            "list": [ ... ]
        }
        ```

        where `[ ... ]` is a list of [`GenreInfo`](#genreinfo)
        objects.

11. **POST AUTH ADMIN /api/songs/{song_id}/genres/{genre_name}**

    Adds a genre to this song's list of genres.

     - Empty payload.
     - Empty response on success.

12. **DELETE AUTH ADMIN /api/songs/{song_id}/genres/{genre_name}**

    Removes a genre from this song's list of gerres.

     - Empty payload.
     - Empty response on success.

### Indexing API

This web project uses Algolia for searching (which is used by
the search bar in the frontend).

After adding several searchable objects, we have to access the
Indexing API endpoints to update Algolia records.

1. **PATCH AUTH ADMIN /api/index/songs, /api/index/songs?reset=true**

    Updates Algolia records for songs, so that the newly-added songs
    could appear in the frontend's search bar when being searched for.

    By default, this won't remove records of deleted songs. Add query
    paramater `?reset=true` if you want to also remove those unused
    records.

     - Empty payload.
     - Empty response on success.

## JSON Object Schemas

The following object schemas are listed in their alphabetical order.

### ArtistInfo

```json
{
    "success": true,
    "id": "123",
    "name": "Frederic Chopin",
    "image_link": "...",
    "added_at": "When was this artist added. See notes about datetimes.",
    "updated_at": "When was this artist last updated. See notes about datetimes.",
    "added_by": "ID of the user that added this artist",
    "updated_by": "ID of the user that last updated this artist"
}
```

### ArtistUpdate

```json
{
    "name": "Alan Walker",
    "image_link": "The link to image, or null"
}
```

When adding a new artist, all the fields are required.
Otherwise, specify the updated fields only.

### GenreInfo

```json
{
    "success": true,
    "id": 11,
    "name": "Classical",
    "image_link": "The link to image, or null",
    "added_at": "When was this genre added. See notes about datetimes.",
    "updated_at": "When was this genre last updated. See notes about datetimes.",
    "added_by": "ID of the user that added this genre",
    "updated_by": "ID of the user that last updated this genre"
}
```

### GenreUpdate

```json
{
    "name": "Classical Music",
    "image_link": "The link to image, or null"
}
```

When adding a new genre, all the fields are required.
Otherwise, specify the updated fields only.

### PlaylistInfo

```json
{
    "success": true,
    "id": 3003,
    "name": "My Playlist",
    "image_link": "The link to image, or null",
    "owned_by": "ID of the user that added this playlist, which is also the only one that could update it",
    "is_public": true, // or false
    "added_at": "When was this playlist added. See notes about datetimes.",
    "updated_at": "When was this playlist last updated. See notes about datetimes."
}
```

### PlaylistUpdate

```json
{
    "name": "Our Playlist",
    "image_link": "The link to image, or null",
    "is_public": false // or true
}
```

When adding a new playlist, all the fields are required.
Otherwise, specify the updated fields only.

### SongInfo

```json
{
    "success": true,
    "id": "123456",
    "name": "Happy Birthday",
    "image_link": "The link to image, or null",
    "audio_link": "A link that can be embedded into <audio> HTML tag",
    "added_at": "When was this song added. See notes about datetimes.",
    "updated_at": "When was this song last updated. See notes about datetimes.",
    "added_by": "ID of the user that added this song",
    "updated_by": "ID of the user that last updated this song",
    "streams": 15000
}
```

### SongUpdate

```json
{
    "name": "Happy Birthday to You",
    "audio_link": "See SongInfo",
    "image_link": "The link to image, or null"
}
```

When adding a new song, all the fields are required.
Otherwise, specify the updated fields only.

### UserInfo

```json
{
    "success": true,
    "id": "13680015",
    "email": "any@email.com",
    "name": "User's Full Name",
    "image_link": "The link to image, or null",
    "is_admin": false, // or true
    "deleted": false // or true
}
```

### UserUpdate

```json
{
    "email": "any@email.com",
    "password": "123456789",
    "name": "User's Full Name",
    "image_link": "The link to image, or null"
}
```

In case of registering a new user, all the fields are required.
Otherwise, specify the updated fields only.
