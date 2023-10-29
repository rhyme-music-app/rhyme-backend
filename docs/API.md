# API Documentation

## Conventions

### URL Format (query params)

### JSON Body/Payload and Optional Fields

### Empty Payload

### Default Format of Response

### Authentication Workflow

### Legend

1. **GET, POST, PUT, PATCH**: HTTP methods to be used with each endpoint.

2. **AUTH**: When accessing endpoints marked AUTH, the client must provide the access token of the user.

3. **ADMIN**: When accessing endpoints marked ADMIN, the authenticated user must be an admin. Endpoints not marked ADMIN can be consumed by any authenticated user regardless of they are a normal or an admin user.

4. **UserInfo, UserUpdate, SongInfo, SongUpdate, GenreInfo, GenreUpdate, PlaylistInfo, PlaylistUpdate**: Go to section [JSON Object Schemas](#json-object-schemas) to see what they are.

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

     - Response on success:

        ```json
        {
            "success": true,
            "token": "..."
        }
        ```

3. **POST AUTH /api/auth/logout**

    Invalidates a token of an authenticated user. The invalidated token can no longer be used.

     - Empty payload.
     - Empty response on success.

### User API

1. **GET /api/users/{user_id}**

    Returns information of a user, given their ID.

     - Empty payload.
     - Response on success: [`UserInfo`](#userinfo).

2. **POST /api/users/signup** OR **POST /users**

    Same as `/api/auth/register`.

3. **PUT AUTH /api/users/{user_id}**

    Updates information of a user, given their ID and the updated information.

    Every user, including every admin, cannot update information of another user.

     - Payload: [`UserUpdate`](#userupdate).
     - Response on success: [`UserInfo`](#userinfo).

4. **DELETE AUTH /api/users/{user_id}**

    Deregisters, or deletes a user account, given their ID.

    Normal users can only delete their own accounts, not others'.

    Admin users can delete any account.

     - Empty payload.
     - Empty response on success.

### Playlist API

1. **GET /api/playlists/{playlist_id}**

    Returns information of a playlist, given its ID. If the playlist is not owned by the currently-authenticated user, then it must be public.

     - Empty payload.
     - Response on success: [`PlaylistInfo`](#playlistinfo).

2. **POST AUTH /api/playlists**

    Creates a new playlist.

     - Payload: [`PlaylistUpdate`](#playlistupdate).
     - Response on success: [`PlaylistInfo`](#playlistinfo).

3. **PUT AUTH /api/playlists/{playlist_id}**

    Updates an existing playlist, given its ID. The playlist must be owned by the currently-authenticated user, with one exception: an admin can change playlists of anyone, regardless of whether the playlists are public or private.

     - Payload: [`PlaylistUpdate`](#playlistupdate).
     - Response on success: [`PlaylistInfo`](#playlistinfo).

4. **DELETE AUTH /api/playlists/{playlist_id}**

    Deletes an existing playlist, given its ID. Same restrictions as PUT method above are applied here.

     - Empty payload.
     - Empty response on success.

### Genre API

1. **GET /api/genres/{genre_id}**

    Returns information of a genre.

     - Empty payload.
     - Response on success: [`GenreInfo`](#genreinfo).

2. **POST AUTH ADMIN /api/genres**

    Creates a new genre.

     - Payload: [`GenreUpdate`](#genreupdate).
     - Response on success: [`GenreInfo`](#genreinfo).

3. **PUT AUTH ADMIN /api/genres/{genre_id}**

    Updates an existing genre.

     - Payload: [`GenreUpdate`](#genreupdate).
     - Response on success: [`GenreInfo`](#genreinfo).

4. **DELETE AUTH ADMIN /api/genres/{genre_id}**

    Deletes an existing genre.

     - Empty payload.
     - Empty response on success.

### Artist API

1. **GET /api/artists/{artist_id}**

    Returns information of an artist, given their ID.

     - Empty payload.
     - Response on success: [`ArtistInfo`](#artistinfo).

2. **POST AUTH ADMIN /api/artists**

    Adds a new artist.

     - Payload: [`ArtistUpdate`](#artistupdate).
     - Response on success: [`ArtistInfo`](#artistinfo).

3. **PUT AUTH ADMIN /api/artists/{artist_id}**

    Updates an existing artist.

    - Payload: [`ArtistUpdate`](#artistupdate).
    - Response on success: [`ArtistInfo`](#artistinfo).

4. **DELETE AUTH ADMIN /api/artists/{artist_id}**

    Deletes an existing artist.

     - Empty payload.
     - Empty response on success.

### Song API

1. **GET /api/songs/{song_id}**

    Returns information of a song. Do not increment streams.

     - Empty payload.
     - Response on success: [`SongInfo`](#songinfo).

2. **POST AUTH ADMIN /api/songs**

    Adds a new song.

     - Payload: [`SongUpdate`](#songupdate).
     - Response on success: [`SongInfo`](#songinfo).

3. **PUT AUTH ADMIN /api/songs/{song_id}**

    Updates information of a song.

     - Payload: [`SongUpdate`](#songupdate).
     - Response on success: [`SongInfo`](#songinfo).

4. **DELETE AUTH ADMIN /api/songs/{song_id}**

    Deletes a song.

     - Empty payload.
     - Empty response on success.

## JSON Object Schemas

The following object schemas are listed in their alphabetical order.

### ArtistInfo

### ArtistUpdate

### GenreInfo

### GenreUpdate

### PlaylistInfo

### PlaylistUpdate

### SongInfo

### SongUpdate

### UserInfo

```json
{
    "success": true,
    "id": "13680015",
    "email": "any@email.com",
    "name": "User's Full Name",
    "is_admin": false, // or true
    "deleted": false // or true
}
```

### UserUpdate

```json
{
    "email": "any@email.com",
    "password": "123456789",
    "name": "User's Full Name"
}
```

In case of registering a new user, all the fields are required.
Otherwise, specify the updated fields only.
