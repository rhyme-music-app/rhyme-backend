# Algolia Notes

Looking at the generated `.env` file you will see empty `ALGOLIA_APP_ID`,
`ALGOLIA_WRITE_KEY` and `ALGOLIA_INDEX` environment variables.

To obtain those Algolia credentials, create a new Algolia application,
then create a new index inside it. Finally, copy the credentials and
the index name from the Algolia website into the vacancies.

`ALGOLIA_WRITE_KEY` is actually Admin API key.

To make the songs searchable, go to the Song Manager in [Administrator Dashboard](https://localhost:8000/dashboard).
