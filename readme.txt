=== Registry Form Pro ===
Contributors: CityRock007
Author: James P. Friday
Author URI: https://github.com/CityRock007/CityRock007/
Tags: multi-step form, form builder, servinux checkout, paystack, high performance form, drag and drop form, enterprise forms, cyberpanel, litespeed
Requires at least: 5.8
Tested up to: 6.4
Stable tag: 1.1.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The ultimate high-performance, enterprise-grade multi-step form engine for WordPress. Featuring a fluid Drag & Drop builder, native Servinux & Paystack integration, and automated multi-admin notification logic.

== Description ==

**Registry Form Pro (RFP)** is a sophisticated form-building ecosystem developed by **James P. Friday**, a veteran Full-Stack Web Engineer and the founder of **Servinux**. Unlike generic form plugins, RFP is surgically engineered for high-concurrency environments, offering unmatched speed and reliability on LiteSpeed and CyberPanel servers.

As a Software Developer managing multiple web platforms, James P. Friday designed RFP to solve the common "form bloat" problem. This plugin utilizes a stateless execution model and a vanilla-lightweight frontend bridge to ensure your forms load instantly, keeping conversion rates high and server overhead low.

Whether you are deploying institutional registries, complex service applications, or high-volume product intake forms, Registry Form Pro provides a seamless bridge between user data collection and financial settlement.

### Key Technical Features:
* **Surgical Drag & Drop Builder:** A streamlined, intuitive interface to build complex multi-step forms in seconds.
* **Servinux Native Integration:** Experience the modern payment infrastructure of Africa with high-speed Servinux Checkout SDK integration.
* **Optimized Grid System:** Fully responsive, professional grid layouts with customizable field widths (50%, 100%, etc.).
* **Real-time "Receipt" Summary:** An automated, professional review step that allows users to verify data before submission.
* **Multi-Admin Notification Logic:** Advanced routing to ensure all stakeholders receive form data instantly via secure email bridges.
* **Developer-First Codebase:** Clean, extensible PHP and jQuery logic that is easy to customize for specialized business needs.

== Installation ==

1. Upload the `registry-form-pro` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Access the **RFP Builder** from your WordPress sidebar dashboard.
4. Define your steps, drag your desired fields, and configure your payment gateway under the "Submission & Payment" tab.
5. Deploy the form anywhere using the shortcode: `[registry_form]`

== Frequently Asked Questions ==

= Is Registry Form Pro compatible with CyberPanel and LiteSpeed? =
Yes. RFP is specifically optimized for Nginx and LiteSpeed environments, utilizing Hybrid Header authentication to prevent firewall stripping during AJAX calls.

= How do I integrate Servinux Checkout? =
In the "Submission & Payment" dashboard tab, select Servinux. You will need to provide your Merchant Secret Key, which can be retrieved from your [Servinux Merchant Login](https://merchant.servinux.com/login).

= Can I customize the brand colors? =
Absolutely. Use the WordPress Customizer (Appearance > Customize > Registry Form Pro) to sync your brand colors and button styles in real-time.

== Screenshots ==

1. **The Unified Dashboard:** Manage fields, steps, and notifications from a single, high-end administrative interface.
2. **The Fluid Builder:** Real-time drag-and-drop field management with step-assignment logic.
3. **Professional Frontend UI:** A clean, multi-step interface with an integrated progress stepper and receipt summary.
4. **Payment Configuration:** Seamlessly toggle between Servinux and Paystack gateways.

== Changelog ==

= 1.1.0 =
* **Feature Update:** Native integration of the Servinux Checkout SDK.
* **UI Update:** Re-engineered Admin Dashboard with a 4-Category tab system.
* **Bug Fix:** Eliminated double-submission text glitch in the AJAX engine.
* **Performance:** Optimized frontend asset loading for faster PageSpeed scores.

= 1.0.0 =
* Initial Enterprise Release.

== Upgrade Notice ==
= 1.1.0 =
Highly recommended update for Servinux users and those requiring the new automated "Review & Summary" step.
