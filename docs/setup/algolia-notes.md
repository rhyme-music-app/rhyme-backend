# Algolia Notes

Looking at the generated `.env` file you will see empty `ALGOLIA_APP_ID`
and `ALGOLIA_WRITE_KEY` environment variables.

To obtain those Algolia credentials, create a new Algolia application,
then create a new index named `rhyme_songs` inside it. Finally, copy
the credentials from the Algolia website into the vacancies.

`ALGOLIA_WRITE_KEY` is actually Admin API key.

To make the songs searchable, go to the Song Manager in [Administrator Dashboard](https://localhost:8000/dashboard).
