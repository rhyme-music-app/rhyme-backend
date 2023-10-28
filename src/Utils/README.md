# Utils

Contains the utility classes, which are never direct
controllers or console commands.

Therefore, to prevent Symfony from accidentally
considering some of the classes here as a controller
or console command, add the path of this directory
(`Utils`) to `services > App\ > exclude` in
`<projectroot>/config/services.yaml`.

The [`reset_database.sql` file](./reset_database.sql)
is generated from [diagram file `rhyme.mwb`](./rhyme.mwb)
by MySQL Workbench Community.
