# Repository Split Instructions

This document explains how to extract the `/yourmindai-laravel` folder into a new, standalone Git repository.

## Steps to Create New Repository

### 1. Create New Repository

Create a new Git repository on GitHub/GitLab/Bitbucket (or your preferred Git hosting service).

### 2. Copy Laravel Project

```bash
# Navigate to parent directory
cd /path/to/yourmindai-web

# Copy the Laravel project to a new location
cp -r yourmindai-laravel /path/to/new-repo-location
cd /path/to/new-repo-location
```

### 3. Initialize New Git Repository

```bash
# Remove existing .git if present (it's part of parent repo)
rm -rf .git

# Initialize new repository
git init

# Add all files
git add .

# Create initial commit
git commit -m "Initial commit: Laravel backend for YourMindAI"

# Add remote repository
git remote add origin <your-new-repo-url>

# Push to new repository
git branch -M main
git push -u origin main
```

### 4. What to Exclude

The following files/directories from the parent repository should NOT be copied:
- `apps/` (old Node.js API)
- `packages/` (old shared packages)
- `app.yaml` (old DigitalOcean config)
- `docker-compose.yml` (if it references old services)
- Any TypeScript/Node.js specific files

### 5. What to Include

Make sure these are included:
- `/yourmindai-laravel` - The entire Laravel application
- All Laravel-specific files (composer.json, artisan, etc.)
- Dockerfile and .dockerignore
- README.md
- .env.example

### 6. Verify Setup

After splitting, verify:
- `composer install` works
- `.env` file is created from `.env.example`
- `php artisan key:generate` works
- Docker build works: `docker build -t yourmindai-laravel .`

## Alternative: Use Git Subtree

If you want to maintain a connection to the original repository:

```bash
# From the parent repository root
git subtree push --prefix=yourmindai-laravel origin laravel-backend
```

This creates a `laravel-backend` branch in the original repo that contains only the Laravel code.

## Post-Split Checklist

- [ ] Update README.md with new repository URL
- [ ] Update any CI/CD configurations
- [ ] Update DigitalOcean App Platform to point to new repository
- [ ] Test deployment from new repository
- [ ] Archive or remove old Node.js API code from original repo (optional)
