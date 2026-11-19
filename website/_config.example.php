<?php
/**
 * Website settings. Copy this file to _config.php on the server and fill it in.
 * _config.php is git-ignored, so updates from GitHub never overwrite your values.
 * The anon key is public by design (it is also inside the app); NEVER put the service_role key here.
 */
const SUPABASE_URL      = '';   // https://<project-ref>.supabase.co
const SUPABASE_ANON_KEY = '';   // Project Settings → API → anon / publishable key

const SITE_URL     = 'https://www.chatimpu.com';
const PORTAL_URL   = 'https://newsroom.chatimpu.com/reporter';   // reporter portal (the CMS on Hostinger)
const PLAY_URL     = '';   // Google Play listing, once the app is published
const APK_URL      = '';   // optional direct download, e.g. '/download/chatimpu.apk' (upload the file to public_html/download/)

const EMAIL_SUPPORT   = 'support@chatimpu.com';
const EMAIL_REPORTERS = 'reporters@chatimpu.com';
const EMAIL_GRIEVANCE = 'grievance@chatimpu.com';
