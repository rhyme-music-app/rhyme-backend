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

1. **POST /auth/login**

    Returns access token of a user, given their credentials.

     - Payload: `{ email, password }`.
     - Response on success: `{ accessToken }`.

2. **POST AUTH /auth/logout**

    Invalidates an access token of an authenticated user. The invalidated token can no longer be used.

     - Empty payload.
     - Empty response on success.

### User API

1. **GET /user/{user_id}**

    Returns information of a user, given their ID.

     - Empty payload.
     - Response on success: `UserInfo`.

2. **POST /user/signup** OR **POST /user**

    Registers a new user.

     - Payload: `UserUpdate`.
     - Response on success: `UserInfo`.

3. **PUT AUTH /user/{user_id}**

    Updates information of a user, given their ID and the updated information.

    Every user, including every admin, cannot update information of another user.

     - Payload: `UserUpdate`.
     - Response on success: `UserInfo`.

4. **DELETE AUTH /user/{user_id}**

    Deregisters, or deletes a user account, given their ID.

    Normal users can only delete their own accounts, not others'.

    Admin users can delete any account.

     - Empty payload.
     - Empty response on success.

### Playlist API

1. **GET /playlist/{playlist_id}**

    Returns information of a playlist, given its ID. If the playlist is not owned by the currently-authenticated user, then it must be public.

     - Empty payload.
     - Response on success: `PlaylistInfo`.

2. **POST AUTH /playlist**

    Creates a new playlist.

     - Payload: `PlaylistUpdate`.
     - Response on success: `PlaylistInfo`.

3. **PUT AUTH /playlist/{playlist_id}**

    Updates an existing playlist, given its ID. The playlist must be owned by the currently-authenticated user, with one exception: an admin can change playlists of anyone, regardless of whether the playlists are public or private.

     - Payload: `PlaylistUpdate`.
     - Response on success: `PlaylistInfo`.

4. **DELETE AUTH /playlist/{playlist_id}**

    Deletes an existing playlist, given its ID. Same restrictions as PUT method above are applied here.

     - Empty payload.
     - Empty response on success.

### Genre API

1. **GET /genre/{genre_id}**

    Returns information of a genre.

     - Empty payload.
     - Response on success: `GenreInfo`.

2. **POST AUTH ADMIN /genre**

    Creates a new genre.

     - Payload: `GenreUpdate`.
     - Response on success: `GenreInfo`.

3. **PUT AUTH ADMIN /genre/{genre_id}**

    Updates an existing genre.

     - Payload: `GenreUpdate`.
     - Response on success: `GenreInfo`.

4. **DELETE AUTH ADMIN /genre/{genre_id}**

    Deletes an existing genre.

     - Empty payload.
     - Empty response on success.

### Artist API

1. **GET /artist/{artist_id}**

    Returns information of an artist, given their ID.

     - Empty payload.
     - Response on success: `ArtistInfo`.

2. **POST AUTH ADMIN /artist**

    Adds a new artist.

     - Payload: `ArtistUpdate`.
     - Response on success: `ArtistInfo`.

3. **PUT AUTH ADMIN /artist/{artist_id}**

    Updates an existing artist.

    - Payload: `ArtistUpdate`.
    - Response on success: `ArtistInfo`.

4. **DELETE AUTH ADMIN /artist/{artist_id}**

    Deletes an existing artist.

     - Empty payload.
     - Empty response on success.

### Song API

1. **GET /song/{song_id}**

    Returns information of a song. Do not increment streams.

     - Empty payload.
     - Response on success: `SongInfo`.

2. **POST AUTH ADMIN /song**

    Adds a new song.

     - Payload: `SongUpdate`.
     - Response on success: `SongInfo`.

3. **PUT AUTH ADMIN /song/{song_id}**

    Updates information of a song.

     - Payload: `SongUpdate`.
     - Response on success: `SongInfo`.

4. **DELETE AUTH ADMIN /song/{song_id}**

    Deletes a song.

     - Empty payload.
     - Empty response on success.

## JSON Object Schemas
