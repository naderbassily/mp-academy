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

- The LearnDash topic template currently strips some native LearnDash markup with regex; replacing that with hook-based customization would be safer.
- A full translation file regeneration has not been run in this branch.
- The staging workflow still depends on the correct WP Engine SSH key being configured in GitHub and WP Engine.

## Changelog

### Unreleased

- Replaced starter-theme metadata in theme, package, composer, PHPCS, and translation headers.
- Consolidated theme asset loading into `functions.php` and removed duplicate enqueue logic from `inc/template-functions.php`.
- Fixed the Customizer preview script path and removed debug logging from the single-course accordion script.
- Removed unused files that were not loaded by the active theme bootstrap.
- Replaced the old SFTP workflow with a WP Engine staging deployment workflow for the `main` branch.
- Documented repository workflow, theme structure, staging deployment, and follow-up risks.

### 1.0.0

- Reset repository history and established the current theme as the fresh `v1` baseline.
