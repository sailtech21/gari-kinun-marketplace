# 🚀 Gari Kinun - Deploy to Vercel Guide

## Prerequisites
- GitHub account
- Vercel account (sign up at https://vercel.com)
- Git installed on your computer

## Step 1: Initialize Git Repository

```bash
cd /Users/tushar/Desktop/MarketplaceLaravel/website
git init
git add .
git commit -m "Initial commit - Gari Kinun Marketplace"
```

## Step 2: Create GitHub Repository

1. Go to https://github.com/new
2. Repository name: `gari-kinun-marketplace` (or your choice)
3. Description: "Gari Kinun - Bangla Vehicle Marketplace"
4. Choose: **Public** or **Private**
5. **Do NOT** initialize with README (we already have files)
6. Click "Create repository"

## Step 3: Push to GitHub

```bash
# Add your GitHub repository (replace YOUR_USERNAME)
git remote add origin https://github.com/YOUR_USERNAME/gari-kinun-marketplace.git
git branch -M main
git push -u origin main
```

## Step 4: Deploy to Vercel

### Option A: Using Vercel Website (Recommended)

1. **Login to Vercel**
   - Go to https://vercel.com
   - Click "Sign Up" or "Login"
   - Choose "Continue with GitHub"

2. **Import Project**
   - Click "Add New..." → "Project"
   - Click "Import Git Repository"
   - Find your `gari-kinun-marketplace` repo
   - Click "Import"

3. **Configure Project**
   - **Framework Preset:** Vite (auto-detected)
   - **Root Directory:** `./` (leave as default)
   - **Build Command:** `npm run build` (auto-detected)
   - **Output Directory:** `dist` (auto-detected)
   - **Install Command:** `npm install` (auto-detected)

4. **Add Environment Variables**
   Click "Environment Variables" and add these:

   ```
   VITE_FIREBASE_API_KEY = AIzaSyBLYSfaIuE0t2mPHaqPr7hNgycIDFP2NDQ
   VITE_FIREBASE_AUTH_DOMAIN = garikinun-bb120.firebaseapp.com
   VITE_FIREBASE_PROJECT_ID = garikinun-bb120
   VITE_FIREBASE_STORAGE_BUCKET = garikinun-bb120.firebasestorage.app
   VITE_FIREBASE_MESSAGING_SENDER_ID = 949156489690
   VITE_FIREBASE_APP_ID = 1:949156489690:web:2ca69388b367adb87078e3
   ```

5. **Deploy**
   - Click "Deploy"
   - Wait 2-3 minutes
   - Your site will be live at: `https://your-project.vercel.app`

### Option B: Using Vercel CLI

```bash
# Install Vercel CLI globally
npm install -g vercel

# Login to Vercel
vercel login

# Deploy from website directory
cd /Users/tushar/Desktop/MarketplaceLaravel/website
vercel

# Follow prompts:
# Set up and deploy? Yes
# Which scope? (select your account)
# Link to existing project? No
# Project name? gari-kinun-marketplace
# In which directory is your code located? ./
# Auto-detected: Vite
# Override settings? No

# Add environment variables
vercel env add VITE_FIREBASE_API_KEY
# Enter value: AIzaSyBLYSfaIuE0t2mPHaqPr7hNgycIDFP2NDQ

vercel env add VITE_FIREBASE_AUTH_DOMAIN
# Enter value: garikinun-bb120.firebaseapp.com

# (Repeat for all environment variables)

# Deploy to production
vercel --prod
```

## Step 5: Custom Domain (Optional)

1. Go to your Vercel project dashboard
2. Click "Settings" → "Domains"
3. Add your domain: `garikinun.com`
4. Follow DNS configuration instructions
5. Update DNS records in your domain registrar:
   - Add CNAME record: `www` → `cname.vercel-dns.com`
   - Add A record: `@` → Vercel's IP

## Step 6: Automatic Deployments

✅ **Every time you push to GitHub main branch:**
- Vercel automatically rebuilds and deploys
- Preview deployments for pull requests
- Instant rollback if needed

```bash
# Make changes
git add .
git commit -m "Updated homepage"
git push origin main
# Vercel will auto-deploy!
```

## Project Structure

```
website/
├── dist/              # Build output (auto-generated)
├── public/            # Static assets
├── src/               # Source code
│   ├── components/    # React components
│   ├── contexts/      # Context providers
│   └── config.js      # API configuration
├── .env.local         # Local environment variables
├── .gitignore         # Git ignore rules
├── vercel.json        # Vercel configuration
├── package.json       # Dependencies
└── vite.config.js     # Vite configuration
```

## Important Files

### vercel.json
- Framework: Vite
- Build command: `npm run build`
- Output: `dist/`
- SPA routing configured

### package.json
- React 18.3.1
- Vite 6.0.3
- TailwindCSS 3.4.17
- Firebase 12.9.0

## Environment Variables

All `VITE_*` variables are:
- ✅ Embedded in build (client-side accessible)
- ✅ Required for Firebase authentication
- ✅ Must be added to Vercel dashboard

## Deployment URLs

After deployment, you'll get:
- **Production:** `https://gari-kinun-marketplace.vercel.app`
- **Preview:** `https://gari-kinun-marketplace-git-[branch].vercel.app`
- **Custom Domain:** `https://garikinun.com` (if configured)

## Troubleshooting

### Build Fails
```bash
# Test build locally first
npm run build
npm run preview
```

### Environment Variables Not Working
- Make sure all variables start with `VITE_`
- Redeploy after adding new variables
- Check Vercel dashboard → Settings → Environment Variables

### 404 Errors on Routes
- Already fixed with `vercel.json` rewrites
- All routes redirect to `index.html` for React Router

### API Connection Issues
- API is still at `https://admin.garikinun.com/api`
- Check `src/config.js` for API URLs
- CORS must be enabled on backend

## Performance Optimization

Vercel automatically provides:
- ✅ Global CDN distribution
- ✅ Automatic HTTPS
- ✅ Image optimization
- ✅ Edge caching
- ✅ Brotli compression
- ✅ HTTP/2 support

## Monitoring

View in Vercel Dashboard:
- Build logs
- Deployment history
- Analytics
- Performance metrics
- Error tracking

## Support

- Vercel Docs: https://vercel.com/docs
- Vite Docs: https://vitejs.dev
- Support: https://vercel.com/support

---

## Quick Deploy Commands

```bash
# One-time setup
cd /Users/tushar/Desktop/MarketplaceLaravel/website
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/YOUR_USERNAME/gari-kinun-marketplace.git
git push -u origin main

# Then go to vercel.com and import the GitHub repo!
```

✅ **That's it! Your website will be live in 3 minutes!** 🚀
