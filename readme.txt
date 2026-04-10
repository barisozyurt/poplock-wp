=== Popup Redirect Countdown ===
Contributors: barisozyurt
Tags: popup, redirect, countdown, overlay, lightbox
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.0
Stable tag: 1.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Shows an animated image overlay on configurable pages. If the user doesn't close it, a countdown redirects them to a target URL.

== Description ==

A lightweight WordPress plugin that displays a full-screen popup overlay with a custom image. If the visitor doesn't dismiss it, a countdown timer redirects them to a target URL.

**Features:**

* Full-screen lightbox overlay with a configurable image
* Countdown timer with automatic redirect
* Animated progress bar showing remaining time
* Dismiss by clicking close button, clicking outside, or pressing Escape
* Cookie-based tracking to prevent re-display after dismissal
* Display on homepage, all pages, or specific pages
* Redirect to an internal WordPress page or external URL
* Adjustable overlay opacity
* WordPress media library integration
* Vanilla JavaScript — no frontend jQuery dependency

== Installation ==

1. Upload the `popup-redirect-countdown` folder to `/wp-content/plugins/`.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Popup Redirect** in the admin sidebar to configure settings.

== Frequently Asked Questions ==

= How do I disable the popup? =

Click the "Remove Popup" button on the settings page to clear all settings and disable the popup.

= Can I show the popup on specific pages only? =

Yes. Under "Display On", select "Specific pages" and check the pages you want.

= What happens when the countdown reaches zero? =

The visitor is automatically redirected to the configured URL.

== Screenshots ==

1. Admin settings page

== Changelog ==

= 1.1 =
* Added internal WordPress page selection for redirect URL
* Added specific page selection for display targeting
* Added "Remove Popup" button to clear all settings

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.1 =
New page selector for redirect and display targeting options.
