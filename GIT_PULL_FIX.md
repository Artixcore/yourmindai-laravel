# Quick Fix for Git Pull Conflict on EC2

## Option 1: Run the Automated Script (Recommended)

If you have the `scripts/resolve-git-pull.sh` script available:

```bash
cd ~/yourmindai-laravel
chmod +x scripts/resolve-git-pull.sh
./scripts/resolve-git-pull.sh
```

## Option 2: Run Commands Manually

Copy and paste these commands on your EC2 instance:

```bash
# Navigate to project directory
cd ~/yourmindai-laravel

# Stash local changes
git stash save "Local EC2 changes before pull"

# Pull remote changes
git pull origin main

# Reapply stashed changes
git stash pop

# Check status
git status

# If conflicts occurred, resolve them manually:
# 1. Edit the conflicted files
# 2. Remove conflict markers (<<<<<<, ======, >>>>>>)
# 3. Stage resolved files: git add <file>
# 4. Drop the stash: git stash drop
```

## Option 3: Create Script Manually on EC2

If the script isn't available, create it on EC2:

```bash
cd ~/yourmindai-laravel
cat > scripts/resolve-git-pull.sh << 'EOF'
#!/bin/bash
set -e
cd ~/yourmindai-laravel
echo "Stashing local changes..."
git stash save "Local EC2 changes before pull"
echo "Pulling remote changes..."
git pull origin main
echo "Reapplying stashed changes..."
git stash pop
echo "Done! Check git status for any conflicts."
git status
EOF

chmod +x scripts/resolve-git-pull.sh
./scripts/resolve-git-pull.sh
```

## What to Do If Conflicts Occur

If `git stash pop` shows conflicts:

1. **View conflicted files:**
   ```bash
   git diff --name-only --diff-filter=U
   ```

2. **Edit each conflicted file** and look for conflict markers:
   ```
   <<<<<<< Updated upstream
   (remote changes)
   =======
   (your local changes)
   >>>>>>> Stashed changes
   ```

3. **Resolve by choosing** which version to keep or merge both

4. **After resolving:**
   ```bash
   git add <resolved-file>
   git stash drop  # Remove the stash
   ```

5. **Verify:**
   ```bash
   git status
   ```
