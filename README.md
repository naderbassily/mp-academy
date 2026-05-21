# MP Academy Theme

Custom WordPress theme for MP Academy, built around LearnDash and the Franklin Design System.

## Repository Status

- `main` is the clean `v1` baseline and the branch intended to deploy to WP Engine staging.
- Feature and cleanup work should happen in short-lived branches and merge into `main` only after review and testing.
- Production deployment is deferred until the live 20i workflow is defined.

## Stack

- WordPress theme
- LearnDash
- Franklin Design System
- GitHub Actions for staging deployment to WP Engine

## Local Workflow

1. Create a feature branch from `main`.
2. Make and test theme changes locally.
3. Update this README:
   - revise structure notes if files move
   - add a changelog entry under `Unreleased`
4. Commit and push the branch.
5. Merge into `main` once the change is approved.
6. GitHub Actions deploys `main` to WP Engine staging.
7. Validate staging before any future production deployment.

## Staging Deployment

The repo is configured for a theme-only deploy to WP Engine staging using WP Engine's official GitHub Action.

- Environment: `mpacademystg`
- Remote target path: `wp-content/themes/mp-academy/`
- Workflow file: `.github/workflows/deploy-to-wpe.yml`
- Required GitHub secret: `WPE_SSHG_KEY_PRIVATE`

### Required GitHub Secret

Add this repository secret before enabling staging deploys:

- `WPE_SSHG_KEY_PRIVATE`: private SSH key for the WP Engine SSH Gateway user with access to `mpacademystg`

## WordPress Theme Updates From GitHub Releases

MP Academy includes the Plugin Update Checker library in `inc/plugin-update-checker/` so WordPress can detect and install theme updates from GitHub.

The updater is initialized in `functions.php` and points at:

```text
https://github.com/naderbassily/mp-academy/
```

The stable branch is `main`. WordPress compares the installed theme version from the `Version:` header in `style.css` against GitHub releases, tags, or the configured stable branch. If GitHub has a higher version, WordPress shows an update notification in Appearance -> Themes and supports the standard one-click theme update flow.

### Repository Shape

This repository must contain only the theme files at the repository root:

```text
mp-academy/
├── style.css
├── functions.php
├── assets/
├── inc/
└── template-parts/
```

Do not wrap the theme in `wp-content/`, `wordpress/`, or another project folder. GitHub release source ZIPs must expose `style.css` and `functions.php` directly inside the extracted top-level theme folder so WordPress can update `wp-content/themes/mp-academy/` correctly.

### Release Workflow

1. Develop locally on a feature branch.
2. Bump `Version:` in `style.css`.
3. Keep `MP_ACADEMY_VERSION` in `functions.php` in sync with `style.css`.
4. Commit and push the branch.
5. Merge the branch into `main` after review and testing.
6. Create a GitHub Release from `main` with a matching tag, for example `v1.0.2`.
7. WordPress checks for updates periodically and shows Update Available when the release version is higher than the installed version.
8. Use Appearance -> Themes to run the one-click update.

Recommended tag and release names:

```text
v1.0.2
v1.0.3
v1.1.0
```

Avoid prereleases for production updates. Plugin Update Checker ignores GitHub releases marked as prerelease, which is useful for staging tests but not for live update prompts.

### Private Repository Support

The GitHub repository is private, so production/staging WordPress installs need a GitHub token to check releases. Define the token outside the theme code.

Preferred `wp-config.php` setup:

```php
define( 'MP_ACADEMY_GITHUB_TOKEN', 'github_pat_or_classic_token_here' );
```

Environment variable alternative:

```text
MP_ACADEMY_GITHUB_TOKEN=github_pat_or_classic_token_here
```

Use a token with the minimum read-only repository access needed to read releases and source archives. Never commit a real token to this repository.

### Testing Updates Locally

To test the update flow:

1. Install an older packaged copy of the theme, for example version `1.0.1`.
2. Confirm `style.css` in GitHub `main` has a higher version, for example `1.0.2`.
3. Create a GitHub Release from `main` using a matching tag such as `v1.0.2`.
4. In WordPress admin, go to Dashboard -> Updates and click Check again, or wait for the normal update check.
5. Confirm Appearance -> Themes shows Update Available for MP Academy.
6. Run the update.
7. Confirm the theme remains active and the frontend/admin load without PHP errors.

Plugin Update Checker can also be inspected with the Debug Bar plugin. After installing Debug Bar, open the PUC panel in the admin toolbar and trigger Check Now for `mp-academy`.

### Rollback Workflow

1. Keep a ZIP of the currently working theme release before applying updates.
2. If an update fails, upload the previous release ZIP through Appearance -> Themes -> Add New -> Upload Theme.
3. Alternatively, restore the previous release from hosting backups or deploy the previous Git tag to the server.
4. Clear object/page caches after rollback.
5. Confirm the active theme version in `style.css` matches the intended rollback version.

### Hosting Requirements

- WordPress must be able to make outbound HTTPS requests to GitHub.
- The web server must be able to write to `wp-content/themes/` during theme updates.
- ZIP extraction must be available in PHP/WordPress.
- WP Engine, cPanel, and other managed hosts may require correct filesystem credentials or write permissions.
- Caches should be cleared after updates when CSS, JS, or templates change.

### Troubleshooting

- No update appears: confirm the GitHub Release is published, not a prerelease, and its version/tag is higher than the installed `style.css` version.
- Private repository returns no update: confirm `MP_ACADEMY_GITHUB_TOKEN` is configured on the WordPress host and has read access to `naderbassily/mp-academy`.
- Update downloads but fails to install: confirm the repository ZIP contains the theme at the root and not nested inside `wp-content/` or another project folder.
- Permission errors: confirm WordPress can write to `wp-content/themes/`.
- Authentication errors for private repos: confirm the token is valid, read-only, and available without being committed.
- Theme appears inactive after update: reinstall the previous release ZIP or restore from backup, then check the package folder structure before releasing again.

## Theme Structure

```text
mp-academy/
├── assets/
│   ├── css/                 Page and component styles
│   ├── fonts/               Inter font assets
│   ├── images/              Theme image assets
│   └── js/                  Frontend behavior
├── inc/
│   ├── customizer.php       Theme customizer integration
│   ├── menu-walker.php      Custom menu walker
│   ├── template-functions.php LearnDash/theme helpers
│   └── template-tags.php    Generic template helpers
├── languages/               Translation files
├── learndash/               LearnDash template overrides
├── template-parts/
│   ├── components/          Shared UI components
│   ├── content/             Generic post/page templates
│   ├── course/              Course archive and single-course parts
│   ├── home/                Homepage sections
│   ├── lesson/              Lesson/topic hero components
│   └── video/               Video library components
├── .github/workflows/       Deployment automation
├── functions.php            Theme setup and consolidated asset loading
├── front-page.php           Homepage shell
├── archive-sfwd-courses.php LearnDash course archive
├── single-sfwd-courses.php  Single course shell
├── single-sfwd-lessons.php  Lesson shell
├── single-sfwd-topic.php    Topic shell
├── page-videos-library.php  Video library shell
├── single-video.php         Single video shell
├── style.css                Theme metadata and root stylesheet
└── style-rtl.css            RTL stylesheet
```

## Key Theme Templates

- `front-page.php`: homepage assembly
- `archive-sfwd-courses.php`: LearnDash course archive
- `single-sfwd-courses.php`: single course experience
- `single-sfwd-lessons.php`: lesson template
- `single-sfwd-topic.php`: topic template
- `page-videos-library.php`: video library
- `single-video.php`: single video

## Cleanup Notes

- Removed unused starter or orphaned files that were not loaded by the theme bootstrap.
- Consolidated frontend asset registration into one place to reduce duplicate logic.
- Kept only the project README as the source of truth for structure and workflow documentation.

## Known Risks / Follow-up Items

- A full translation file regeneration has not been run in this branch.
- The staging workflow still depends on the correct WP Engine SSH key being configured in GitHub and WP Engine.

## Changelog

### 1.0.2

- Added GitHub Release-based WordPress theme updates using Plugin Update Checker.
- Documented the release, testing, rollback, and hosting requirements for one-click theme updates.

### 1.0.1

- Replaced starter-theme metadata in theme, package, composer, PHPCS, and translation headers.
- Consolidated theme asset loading into `functions.php` and removed duplicate enqueue logic from `inc/template-functions.php`.
- Fixed the Customizer preview script path and removed debug logging from the single-course accordion script.
- Removed unused files that were not loaded by the active theme bootstrap.
- Cleaned header and breadcrumb integration, including the missing corporate menu registration.
- Hardened the LearnDash topic template by replacing regex-based content extraction with DOM-based parsing.
- Replaced the old SFTP workflow with a WP Engine staging deployment workflow for the `main` branch.
- Documented repository workflow, theme structure, staging deployment, and follow-up risks.

### 1.0.0

- Reset repository history and established the current theme as the fresh `v1` baseline.
