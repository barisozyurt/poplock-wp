# Mirket Popup Redirect Countdown

A lightweight WordPress plugin that displays a full-screen popup overlay with a custom image. If the visitor doesn't dismiss it, a countdown timer redirects them to a target URL.

**Version:** 1.4
**Tested up to:** WordPress 7.0
**Requires:** WordPress 5.0+ · PHP 7.0+

## Features

- Full-screen lightbox overlay with a configurable image
- Countdown timer with automatic redirect when it reaches zero
- Animated progress bar showing remaining time
- Dismiss by clicking the close button, clicking outside the image, or pressing Escape
- Cookie-based tracking to avoid showing the popup again after dismissal
- Display targeting: homepage only, all pages, or specific pages
- Redirect to an internal WordPress page or an external URL
- Adjustable overlay opacity
- WordPress media library integration for image selection
- "Remove Popup" button to clear all settings in one click
- Vanilla JavaScript — no jQuery dependency on the frontend

## Installation

1. Download the latest `mirket-popup-redirect-countdown-vX.Y.zip` from the [Releases](https://github.com/barisozyurt/poplock-wp/releases) page (or build it yourself — see [Development](#development)).
2. In WordPress, go to **Plugins → Add New → Upload Plugin** and upload the zip.
3. Activate the plugin through the **Plugins** menu.
4. Go to **Popup Redirect** in the admin sidebar to configure settings.

## Settings

| Setting | Description | Default |
|---------|-------------|---------|
| Image URL | The image displayed in the popup. Use the media picker or paste a URL. | — |
| Image Alt Text | Alt text for the popup image (accessibility). | — |
| Redirect | Send the visitor to an internal WordPress page or an external URL. | External URL |
| Countdown Seconds | How many seconds before redirecting. Minimum: 1. | 10 |
| Display On | Show the popup on the homepage only, all pages, or specific pages. | Homepage only |
| Cookie Duration (days) | Days to remember dismissal. Set to 0 to always show. | 7 |
| Overlay Opacity | Background darkness level (0.0 transparent – 1.0 opaque). | 0.7 |

The popup only renders when both an **image** and a resolvable **redirect target** are configured.

## How It Works

1. When a visitor lands on a configured page, the plugin checks for a dismissal cookie.
2. If no cookie is found, a dark overlay fades in after a brief delay (~300ms) with the configured image centered on screen.
3. A countdown ("Redirecting in X seconds…") and a shrinking progress bar are shown.
4. If the visitor closes the popup, the countdown stops and a cookie is set to prevent the popup from appearing again.
5. If the countdown reaches zero, the visitor is redirected to the target URL.

## Project Structure

```
mirket-popup-redirect-countdown.php        Main plugin file (headers, bootstrap, activation)
includes/
  class-mirketprc-admin.php                Settings page, options, enqueuing of admin assets
  class-mirketprc-frontend.php             Display logic, asset enqueuing, overlay markup
assets/
  css/mirketprc-frontend.css               Overlay styles
  css/mirketprc-admin.css                  Settings page styles
  js/mirketprc-frontend.js                 Countdown, redirect, dismissal logic
  js/mirketprc-admin.js                    Media picker, field toggles, reset confirm
readme.txt                                 WordPress.org readme
build.ps1                                  Builds the distributable plugin zip
```

All declarations (functions, classes, constants, options, handles, nonces, CSS classes and JS globals) are prefixed with `mirketprc_` / `MIRKETPRC_` to avoid collisions. All admin and frontend scripts and styles are registered through WordPress's enqueue APIs — there is no inline JS or CSS.

## Development

To build the distributable plugin zip on Windows:

```powershell
powershell -File build.ps1 -Version 1.4
```

This produces `mirket-popup-redirect-countdown-v1.4.zip` with Unix-compatible (forward-slash) ZIP paths. Do **not** use `Compress-Archive` or Windows Explorer's "Send to → Compressed folder", as Windows PowerShell writes backslash path separators that break extraction on WordPress.org and other Linux hosts.

## Requirements

- WordPress 5.0 or later
- PHP 7.0 or later

## License

This plugin is licensed under the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.html).

## Author

[Baris Ozyurt](https://mirket.io) · [mirket.io](https://mirket.io)
