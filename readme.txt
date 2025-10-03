=== AdX Ad Inserter ===
Contributors: monetiscopeadx
Donate link: https://monetiscope.com/
Tags: ads, adsense, ad-manager, popup, ads.txt
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Insert Google AdX, Ad Manager, popup, rewarded, interstitial, offerwall, sticky, in-content, header & footer ads — plus built-in ads.txt editor.

== Description ==

AdX Ad Inserter by Monetiscope is a lightweight, publisher-friendly Google Ad Exchange (AdX) ad placement plugin. It supports all major ad formats and gives you precise controls to insert ads in the header, footer, before/after paragraphs or images, on scroll, or on user actions—without touching code.

It also includes a built-in ads.txt manager so you can easily add or update your authorized sellers file directly from WordPress.

✅ **Supported Ad Formats**
- Google AdX ads  
- Google Ad Manager (DFP) ads  
- Popup ads (scroll %, delay, exit intent)  
- Rewarded ads (trigger on button/link click)  
- Sticky / Anchor ads (top or bottom)  
- In-content ads (before/after Nth paragraph or image, with offsets)  
- Header & Footer ads (custom scripts/async codes)  
- Interstitial / Vignette ads (via AdSense/Ad Manager or custom code)  
- Offerwall ads (via third-party networks or iframe/script)  
- Native ads (in-article / in-feed)  
- Video ads (including offerwall/rewarded video via network code)  

🧾 **ads.txt Manager (built-in)**
- Create or edit `/ads.txt` from your WordPress admin  
- Add multiple authorized seller lines  
- If host blocks root write access, plugin shows your ads.txt content so you can copy & paste manually  

🎛️ **Placement & Targeting**
- Insert ads before/after Nth paragraph or image  
- Device targeting (desktop, mobile)  
- Page targeting (homepage, posts, categories, tags, archive)  
- Popup session logic (once per session / once per page)  
- Global enable/disable switch + per-slot toggles  
- Offerwall Ad with custom logo option  

⚡ **Performance & Safety**
- Async loading for faster pages  
- Lightweight code, no bloat  
- Escaping/sanitization on settings  
- Fully GPL-compliant  

🔍 **SEO-Optimized Keywords**  
AdX Ad Inserter, adx ad tool, best ad inserter tool, ad placement, ads.txt, AdSense inserter, Ad Manager inserter, popup ads, rewarded ads, interstitial ads, vignette ads, offerwall ads, sticky ads, floating ads, flying carpet, in-content ads, header ads, footer ads, monetize WordPress sites.

== Installation ==

1. Upload `adx-ad-inserter` to the `/wp-content/plugins/` directory.  
2. Activate via **Plugins → Installed Plugins**.  
3. Go to **Settings → AdX Ad Inserter**.  
4. Enable ad formats and paste your ad codes.  
5. (Optional) Configure your ads.txt entries in the plugin’s ads.txt tab.  

== Frequently Asked Questions ==

= Does AdX Ad Inserter insert any internal ads? =  
No. AdX Ad Inserter never inserts internal or third-party ads on its own. The plugin only displays the ad codes that you configure. 100% control remains with you.

= Is AdX Ad Inserter compatible with Google AdX and Ad Manager (DFP)? =  
Yes. You can paste your AdX or Ad Manager ad slot line directly to show ads on your sites, and the plugin handles safe placement and async loading.

= Can I show popup, rewarded, or interstitial (vignette) ads? =  
Yes. Popup ads can be triggered on scroll. Rewarded ads can be triggered when users click a link or button. Interstitial/Vignette ads can be placed via AdX/Ad Manager or custom scripts.

= Does the plugin support Offerwall ads? =  
Yes. You can integrate Offerwall ads by pasting your Offerwall ad slot line from Google Ad Manager.

= Can I insert ads in the header and footer? =  
Yes. The plugin provides dedicated slots to insert header and footer codes (scripts or styles).

= Can I control placement inside post content? =  
Yes. You can insert ads before or after specific paragraphs or images, with precise offsets.

= How does the ads.txt feature work? =  
The plugin includes a built-in ads.txt manager where you can create and edit your `/ads.txt` file directly from WordPress. If your host blocks root file writing, the plugin will display the content for you to copy and upload manually.

= Does this plugin track my visitors? =  
No. The plugin itself does not track visitors or users. It only loads the ad scripts you provide. However, the ad networks you use (AdSense, Ad Manager, Offerwall, etc.) may collect data according to their privacy policies.

= Privacy Policy – Plugin Usage Tracking =  
This plugin does not send any usage or tracking data to Monetiscope or third parties. All ads and scripts are user-configured. Any analytics, targeting, or cookies come from your ad networks, not from the plugin.

= How to display a GDPR-compliant cookie message? =  
AdX Ad Inserter does not include a cookie consent popup. We recommend using a GDPR/CCPA cookie consent plugin alongside AdX Ad Inserter. You can configure it to work with your ad network scripts for compliance.

= Does the plugin slow down my site? =  
No. All ad scripts are loaded asynchronously, and the plugin itself is lightweight with no unnecessary bloat.

= Can I enable or disable the plugin globally? =  
Yes. There is a global enable/disable switch that controls whether ads render at all. Each slot also has its own enable/disable toggle.

= Is it free to use? =  
Yes, AdX Ad Inserter is completely free. There are no hidden charges.
== External Services ==

This plugin loads the following third-party script:

1) Google Publisher Tag (GPT.js)
- Service: Google Ad Manager (Google Publisher Tag)
- Purpose: Requests and renders ad slots configured in the plugin.
- When it loads: On pages where ad slots are enabled via the plugin’s settings.
- URL: https://securepubads.g.doubleclick.net/tag/js/gpt.js
- Data sent/received: The script communicates with Google’s ad servers. It may transmit the page URL, referrer, device/user agent, ad unit identifiers, ad sizes, and any targeting parameters configured in the plugin. Google may set or read cookies/local storage to deliver, measure, and limit ads.
- Terms: https://policies.google.com/terms
- Privacy Policy: https://policies.google.com/privacy


== Screenshots ==

1. Display Ad with multiple sub slots  
2. Popup ad preview  
3. Custom Ad slot preview.

== Changelog ==

= 1.0.0 =  
* Initial release  
* Added AdX & Ad Manager support  
* Popup, Rewarded, Sticky/Anchor, Interstitial (Vignette), Offerwall, Native/In-feed ads  
* In-content placements (paragraph/image with offsets)  
* Header & Footer custom scripts  
* Adsense ads via custom code  
* Built-in ads.txt editor  
* Global enable/disable + per-slot toggles  
* Chat Support  

== Upgrade Notice ==

= 1.0.0 =  
First public release of AdX Ad Inserter. Includes AdX ad placement option, header/footer, ads.txt editor and all major ad formats.
