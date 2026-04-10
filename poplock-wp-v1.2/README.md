# Popup Redirect Countdown

A lightweight WordPress plugin that displays a full-screen popup overlay with a custom image. If the visitor doesn't dismiss it, a countdown timer redirects them to a target URL.

**Version:** 1.0

## Features

- Full-screen lightbox overlay with a configurable image
- Countdown timer with automatic redirect when it reaches zero
- Animated progress bar showing remaining time
- Dismiss by clicking the close button, clicking outside the image, or pressing Escape
- Cookie-based tracking to avoid showing the popup again after dismissal
- Display on homepage only or on all pages
- Adjustable overlay opacity
- WordPress media library integration for image selection
- Vanilla JavaScript — no jQuery dependency on the frontend

## Installation

1. Download or clone this repository.
2. Upload the `popup-redirect-countdown` folder to `/wp-content/plugins/`.
3. Activate the plugin through the **Plugins** menu in WordPress.
4. Go to **Popup Redirect** in the admin sidebar to configure settings.

## Settings

| Setting | Description | Default |
|---------|-------------|---------|
| Image URL | The image displayed in the popup. Use the media picker or paste a URL. | — |
| Image Alt Text | Alt text for the popup image (accessibility). | — |
| Redirect URL | Where the visitor is sent when the countdown ends. | — |
| Countdown Seconds | How many seconds before redirecting. Minimum: 1. | 10 |
| Display On | Show the popup on the homepage only or on all pages. | Homepage only |
| Cookie Duration | Days to remember dismissal. Set to 0 to always show. | 7 |
| Overlay Opacity | Background darkness level (0.0 transparent – 1.0 opaque). | 0.7 |

## How It Works

1. When a visitor lands on a configured page, the plugin checks for a dismissal cookie.
2. If no cookie is found, a dark overlay fades in after a brief delay (~300ms) with the configured image centered on screen.
3. A countdown ("Redirecting in X seconds...") and a shrinking progress bar are shown.
4. If the visitor closes the popup, the countdown stops and a cookie is set to prevent the popup from appearing again.
5. If the countdown reaches zero, the visitor is redirected to the target URL.

## Requirements

- WordPress 5.0 or later
- PHP 7.0 or later

## License

This plugin is licensed under the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.html).

## Author

[Baris Ozyurt](https://mirket.io)
