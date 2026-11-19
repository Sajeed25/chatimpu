# www.chatimpu.com: public website

A small PHP site for Hostinger, with no build step. Pages:

| Address | What |
|---|---|
| `/` | Landing page in Telugu and English, with a language switch. Shows the latest stories once Supabase is configured. |
| `/s/<story-id>`, `/s/v/<video-id>` | Story and News Reel links shared from the app. Rendered on the server, so WhatsApp and Facebook previews show the real headline and photo. |
| `/grievance` | Complaints and corrections: the grievance officer's details (from CMS settings) and a form that files a grievance (24-hour / 15-day deadlines). |
| `/privacy`, `/terms` | Plain-language starting points. **Have a lawyer review them before launch.** |

## Deploy on Hostinger

1. **hPanel → Websites → `chatimpu.com` → File Manager → `public_html`**: upload the contents of this
   `website/` folder (zip it, upload, then **Extract**), including the hidden `.htaccess`.
2. Copy `_config.example.php` to `_config.php` in the same folder and fill in:
   - `SUPABASE_URL` and `SUPABASE_ANON_KEY`: the same values as the app (never the service_role key).
   - `PORTAL_URL`: your CMS address plus `/reporter`.
   - `PLAY_URL` once the app is on Google Play. Until then, the button says "Coming soon".
   - Optionally `APK_URL`: upload the APK to `public_html/download/` and use `/download/<file>.apk`.
3. **Security → SSL**: install the free certificate for `chatimpu.com` and `www.chatimpu.com`.
4. Check that these work:
   - `https://chatimpu.com` redirects to `https://www.chatimpu.com`.
   - `/privacy` loads.
   - `/_config.php` is refused (403).
   - A story link shared from the app opens its story.

The site needs PHP 7.4 or newer (Hostinger's default is 8.x). Pages work without Supabase: the latest-stories
section hides itself, and the complaint form asks people to email instead.

To update the site, upload the changed files. `_config.php` isn't in git, so updates never overwrite it.
