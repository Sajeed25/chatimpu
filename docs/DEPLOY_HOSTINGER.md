# Deploying Chatimpu: Supabase + Hostinger

## What runs where

| Part | Where | Why |
|---|---|---|
| Database, logins, file storage, server functions, scheduled jobs | **Supabase** | Hostinger can't run Postgres, Supabase Auth or Edge Functions. Set it up with [SUPABASE_SETUP.md](SUPABASE_SETUP.md). |
| Newsroom CMS and reporter web portal (`admin/`) | **Hostinger** | A static website (HTML, JS, CSS). Any Hostinger plan with a domain can host it. |
| Mobile app (`app/`) | APK / Google Play | Built on your computer or with EAS. |

Suggested address: **`https://newsroom.chatimpu.com`** for the CMS, so the reporter portal is
`https://newsroom.chatimpu.com/reporter`. Keep `www.chatimpu.com` for a public website.

---

## 1. Supabase settings for the website (dashboard)

Do these after the database is set up ([SUPABASE_SETUP.md](SUPABASE_SETUP.md) steps 1–7).

**Authentication → URL Configuration**

| Setting | Value |
|---|---|
| Site URL | `https://newsroom.chatimpu.com` |
| Redirect URLs | `https://newsroom.chatimpu.com/**` |

**Authentication → Sign In / Providers**
- **Phone:** on. Before launch, connect an SMS provider with a DLT-registered template.
- **Email:** on, for staff who sign in with email and password.
- **Allow new users to sign up:** on. Reporters and readers sign up with their phone number. The CMS still shows "No newsroom access" to anyone without a staff role.

**Authentication → Rate Limits:** SMS about 30 per hour.

**Project Settings → API:** copy the **Project URL** and the **anon / publishable key**. Never put the
**service_role** key on Hostinger or in the app.

No CORS setting is needed. The server functions already accept requests from any website and check the user's login on every call.

---

## 2. Build the CMS (your computer)

You need Node 20+. The CMS reuses code from the app, so install both.

```bash
git clone https://github.com/Sajeed25/chatimpu
cd chatimpu
(cd app && npm ci)
cd admin && npm ci
```

Create `admin/.env.production`. It is git-ignored, so it never goes to GitHub:

```
VITE_SUPABASE_URL=https://<project-ref>.supabase.co
VITE_SUPABASE_ANON_KEY=<anon / publishable key>
```

Build:

```bash
npm run build
```

This creates the `admin/dist/` folder, including a hidden `.htaccess` file. Zip the **contents** of `dist`, not the folder itself:

```bash
cd dist && zip -r ../chatimpu-cms.zip . && cd ..
```

---

## 3. Upload to Hostinger (hPanel)

1. **Domains → Subdomains:** create `newsroom` under `chatimpu.com`. Hostinger creates a folder for it, usually
   `public_html/newsroom`. If the domain isn't registered at Hostinger, point its DNS to Hostinger first (**Domains → DNS / Nameservers**).
2. **Files → File Manager:** open that folder, delete any default `index.php` or `default.php`, upload
   `chatimpu-cms.zip`, right-click it → **Extract**, then delete the zip.
   - Check that `index.html`, `assets/` and `.htaccess` are **directly** in the folder, not in a `dist/` subfolder.
   - If you can't see `.htaccess`, turn on **Show hidden files** in File Manager settings.
3. **Security → SSL:** install the free SSL certificate for `newsroom.chatimpu.com`. It can take up to 15 minutes.
4. Open `https://newsroom.chatimpu.com`:
   - You should see the sign-in page.
   - The sidebar should show the green **Live data** badge after you sign in.
   - Open `https://newsroom.chatimpu.com/reporter/help` directly and refresh. It should load, not show a 404.

What the `.htaccess` file does:
- Forces HTTPS.
- Sends every page address to `index.html`, so `/reporter/...` and `/editorial` work after a refresh.
- Adds security headers.
- Caches built files for a year. `index.html` itself is never cached, so new uploads show up straight away.

---

## 4. Point the app at the website

Add to `app/.env` (with the two Supabase lines from SUPABASE_SETUP.md step 9):

```
EXPO_PUBLIC_REPORTER_PORTAL_URL=https://newsroom.chatimpu.com/reporter
```

Then rebuild the APK. The app's "Contributor agreement" button opens this address.

---

## 5. Updating the website later

Run `git pull`, then `npm run build` in `admin/`. Upload the new `dist` contents, replacing the old files.
- You don't need to empty the folder first: old `assets/` files are unused and harmless.
- Staff get the new version on their next page load.

Database changes are separate: run any new `supabase/migrations/00xx_*.sql` file in the Supabase SQL Editor
**before** uploading a CMS build that needs it.

---

## Not covered yet

- **Shared story links:** shared stories and reels link to `https://www.chatimpu.com/s/<id>`, but no public web page
  shows them yet. Until one is built, put a simple landing page on `www.chatimpu.com` with a Play Store link, and send `/s/*` there.
- **Automatic deploys:** each update is a manual upload. A GitHub Action can upload `admin/dist` over FTP on every push to `main`, using your Hostinger FTP details stored as GitHub secrets.
