# GitHub Pages Setup

This repository now includes a static portfolio site in the `docs/` folder.

## Important Limit

GitHub Pages can publish the public project presentation page, but it cannot run the full Laravel application itself.

This app needs:

- PHP on a server
- a database
- background queue workers
- scheduled jobs
- persistent file storage

So GitHub Pages is being used here as a clean portfolio showcase page.

## Expected Public URL

Once Pages is enabled for this repository, the project page URL will be:

```text
https://kyawmyohtay1.github.io/academic-portal/
```

## Recommended GitHub Pages Source

Use:

- Branch: `deployment`
- Folder: `/docs`

## What To Click In GitHub

1. Open the repository settings.
2. Open `Pages`.
3. Under `Build and deployment`, choose `Deploy from a branch`.
4. Select branch `deployment`.
5. Select folder `/docs`.
6. Save.

GitHub will then publish the site to:

```text
https://kyawmyohtay1.github.io/academic-portal/
```

## Files Used

- `docs/index.html`
- `docs/styles.css`
- `docs/assets/*`

## If You Want The Real Interactive System

Use the backend hosting setup instead:

- [RAILWAY_DEPLOYMENT.md](RAILWAY_DEPLOYMENT.md)
- [DEPLOYMENT.md](DEPLOYMENT.md)
