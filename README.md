# YourMindAI Laravel Backend

Laravel-based backend API for YourMindAI, replacing the Node.js/TypeScript API with MongoDB support, JWT authentication, and OpenAI integration.

## Features

- **MongoDB Integration**: Full MongoDB support using `mongodb/laravel-mongodb`
- **JWT Authentication**: Secure token-based authentication with role-based access control
- **OpenAI Integration**: Clinical note summarization and treatment suggestions
- **CORS Support**: Configured for web frontend communication
- **Docker Ready**: Includes Dockerfile for DigitalOcean deployment

## Requirements

- PHP 8.2 or higher
- Composer
- MongoDB (local or cloud instance)
- OpenAI API Key (for AI features)

## Local Development Setup

### 1. Install Dependencies

```bash
composer install
```

### 2. Environment Configuration

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 3. Configure Environment Variables

Edit `.env` and set the following:

```env
APP_NAME=YourMindAI
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# MongoDB
MONGODB_URI=mongodb://localhost:27017/yourmindai
MONGODB_DATABASE=yourmindai

# JWT
JWT_SECRET=your-secret-key-here
JWT_TTL=604800

# CORS
CORS_ORIGIN=http://localhost:3000

# OpenAI
OPENAI_API_KEY=your-openai-api-key
OPENAI_MODEL=gpt-4o-mini
```

### 4. Seed Database

Run the database seeder to create the admin user:

```bash
php artisan db:seed
```

This creates an admin account:
- **Email**: `admin@yourmindaid.com`
- **Password**: `Admin123456`
- **Role**: DOCTOR

### 5. Start Development Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## API Endpoints

### Authentication

- `POST /api/auth/register-doctor` - Register new doctor
- `POST /api/auth/login` - Login (returns JWT token)
- `POST /api/auth/register-patient` - Patient registration with invite code
- `POST /api/auth/register-parent` - Parent registration with invite code
- `GET /api/auth/me` - Get current user profile
- `POST /api/auth/logout` - Logout

### Doctor Routes (Requires DOCTOR/THERAPIST role)

- `GET /api/doctors/patients` - List all patients
- `POST /api/doctors/patients` - Create new patient
- `PUT /api/doctors/patients/{id}` - Update patient
- `DELETE /api/doctors/patients/{id}` - Delete patient
- `POST /api/doctors/patients/{id}/reset-password` - Reset patient password

### Patient Routes

- `GET /api/patients/{id}` - Get patient profile
- `GET /api/patients/{id}/notes` - Get clinical notes
- `POST /api/patients/{id}/notes` - Create clinical note (with AI summary)
- `GET /api/patients/{id}/goals` - Get treatment goals
- `POST /api/patients/{id}/goals` - Create goal
- `PUT /api/patients/{id}/goals/{goalId}` - Update goal
- `DELETE /api/patients/{id}/goals/{goalId}` - Delete goal
- `GET /api/patients/{id}/tasks` - Get tasks
- `POST /api/patients/{id}/tasks` - Create task
- `PUT /api/patients/{id}/tasks/{taskId}` - Update task
- `PUT /api/patients/{id}/tasks/{taskId}/complete` - Complete task (earn points)
- `DELETE /api/patients/{id}/tasks/{taskId}` - Delete task
- `GET /api/patients/me/dashboard` - Patient dashboard data
- `GET /api/patients/me/reminders` - Get reminders
- `POST /api/patients/me/reminders` - Create reminder
- `PUT /api/patients/me/reminders/{id}` - Update reminder
- `DELETE /api/patients/me/reminders/{id}` - Delete reminder
- `GET /api/patients/me/instructions` - Get doctor instructions

### AI Routes (Requires DOCTOR/THERAPIST role)

- `POST /api/ai/summarize-note` - Summarize clinical note text
- `POST /api/ai/treatment-suggestions` - Get AI treatment suggestions

### Appointments

- `GET /api/appointments/doctors` - List all doctors
- `GET /api/appointments/doctors/{id}/availability` - Get doctor availability
- `POST /api/appointments` - Create appointment (patient only)
- `GET /api/appointments/me` - Get patient's appointments
- `GET /api/appointments/{id}` - Get appointment details
- `PATCH /api/appointments/{id}/reschedule` - Reschedule appointment
- `PATCH /api/appointments/{id}/cancel` - Cancel appointment

### Assessments

- `POST /api/assessments/assign` - Assign assessment to patient
- `GET /api/assessments/me` - Get patient's assigned assessments
- `GET /api/assessments/patient/{patientId}` - Get patient assessments (doctor)
- `POST /api/assessments/{id}/complete` - Complete assessment
- `GET /api/assessments/{id}/result` - Get assessment result

### Health Check

- `GET /api/health` - API health status

## Authentication

All protected endpoints require a JWT token in the Authorization header:

```
Authorization: Bearer <your-jwt-token>
```

## Response Format

### Success Response

```json
{
  "success": true,
  "data": {
    // Response data
  }
}
```

### Error Response

```json
{
  "success": false,
  "error": "Error message"
}
```

## Docker Setup

### Build Docker Image

```bash
docker build -t yourmindai-laravel .
```

### Run with Docker Compose

A `docker-compose.yml` file is available at the repository root for local development:

```bash
docker-compose up
```

## DigitalOcean App Platform Deployment

### Prerequisites

1. DigitalOcean App Platform account
2. MongoDB database (DigitalOcean Managed Database or external)
3. OpenAI API key
4. GitHub repository with Laravel code pushed

### Why This Setup Prevents Errors

This Laravel backend is configured to avoid the runtime detection errors that occurred with the Node.js project:

- **No Spec Files**: No `app.yaml` or `project.yml` files that could trigger auto-detection
- **Docker-Only**: DigitalOcean auto-detects Dockerfile, bypassing runtime detection
- **No TypeScript**: PHP project, so no "typescript:default" errors possible
- **PORT Handling**: Dockerfile uses `PORT` environment variable (set automatically by DO)
- **Health Check**: Configured health check endpoint for DO monitoring

### Deployment Steps

#### Step 1: Create App in DigitalOcean

1. Go to [DigitalOcean App Platform](https://cloud.digitalocean.com/apps)
2. Click **Create App**
3. Connect your GitHub repository: `Artixcore/yourmindai-laravel`
4. Select branch: `main` (or your deployment branch)
5. Click **Next**

#### Step 2: Configure Service

**DigitalOcean will auto-detect the Dockerfile.** Verify the following:

- **Component Type**: Web Service (auto-detected)
- **HTTP Port**: `8000` (DigitalOcean will set `PORT=8000` automatically)
- **Dockerfile Path**: `Dockerfile` (root of repository)
- **Health Check Path**: `/api/health` (configured in Dockerfile)

**Important**: Do NOT create `app.yaml` or `project.yml` files. The Docker-only approach prevents runtime detection issues.

#### Step 3: Set Environment Variables

Go to **Settings** → **App-Level Environment Variables** and set:

**Required Variables (All Run Time):**

| Variable | Type | Description | How to Generate |
|----------|------|-------------|-----------------|
| `APP_KEY` | **Encrypted** | Laravel application key | Generate: `php artisan key:generate --show` |
| `APP_URL` | Plain | Your DO app URL | `https://your-app-name.ondigitalocean.app` |
| `APP_ENV` | Plain | Environment | `production` |
| `APP_DEBUG` | Plain | Debug mode | `false` |
| `MONGODB_URI` | **Encrypted** | MongoDB connection string | Your MongoDB URI |
| `MONGODB_DATABASE` | Plain | Database name | `yourmindai` |
| `JWT_SECRET` | **Encrypted** | JWT signing secret | Generate: `openssl rand -base64 64` |
| `JWT_TTL` | Plain | JWT expiration | `604800` (7 days) |
| `CORS_ORIGIN` | Plain | Allowed CORS origins | `https://yourmindaid.com` (or comma-separated) |
| `OPENAI_API_KEY` | **Encrypted** | OpenAI API key | Your OpenAI key |
| `OPENAI_MODEL` | Plain | OpenAI model | `gpt-4o-mini` (optional) |

**Note**: `PORT` is automatically set by DigitalOcean from the `http_port` value. Do not set it manually.

#### Step 4: Deploy

1. Click **Deploy** or push to your connected branch
2. Monitor deployment in **Activity** tab
3. Wait for build to complete (usually 5-10 minutes for first build)

#### Step 5: Run Database Seeders

After first successful deployment, connect to your app via DigitalOcean console:

1. Go to **Settings** → **Console**
2. Run:
   ```bash
   php artisan db:seed
   ```

This creates the admin user:
- **Email**: `admin@yourmindaid.com`
- **Password**: `Admin123456`

#### Step 6: Verify Deployment

1. **Health Check**: Visit `https://your-app-name.ondigitalocean.app/api/health`
   - Should return: `{"status":"ok","timestamp":"..."}`

2. **Check Logs**: Go to **Runtime Logs** tab
   - Should show: "Starting server on port 8000"
   - No errors about missing env vars

3. **Test API**: Try login endpoint:
   ```bash
   curl -X POST https://your-app-name.ondigitalocean.app/api/auth/login \
     -H "Content-Type: application/json" \
     -d '{"identifier":"admin@yourmindaid.com","password":"Admin123456"}'
   ```

### Dockerfile Configuration

The Dockerfile is configured for production:

- **Uses PORT env var**: `--port=${PORT:-8000}` (DO sets PORT automatically)
- **Health check**: Configured to check `/api/health` endpoint
- **Production optimizations**: Config cache, route cache, view cache
- **MongoDB extension**: Installed and enabled
- **Proper permissions**: Storage directories have correct permissions

### Troubleshooting DigitalOcean Issues

#### Build Fails

**Check:**
- Dockerfile syntax is correct
- All dependencies install successfully
- MongoDB extension installs (check build logs)

**Solution:**
- Review build logs in **Activity** tab
- Ensure MongoDB extension dependencies are installed (libmongoc-dev, libbson-dev)

#### Runtime Errors: "Missing Environment Variable"

**Check:**
- All required env vars are set in DO dashboard
- Variable names match exactly (case-sensitive)
- Secrets are marked as **Encrypted**

**Solution:**
- Verify all variables from the table above are set
- Check variable names match exactly
- Ensure APP_KEY is generated and set

#### Health Check Fails

**Check:**
- `/api/health` endpoint is accessible
- PORT env var is set correctly
- Application is running

**Solution:**
- Verify health check path in DO settings: `/api/health`
- Check runtime logs for errors
- Ensure server started successfully

#### "Runtime Detection" or "typescript:default" Errors

**This should NOT happen with Laravel**, but if it does:

**Check:**
- No `app.yaml` or `project.yml` files exist in repository
- Dockerfile is being used (not buildpack)
- No TypeScript files in repository

**Solution:**
- Ensure Docker-only deployment
- Delete any `app.yaml` or `project.yml` files
- Verify DO is using Dockerfile (check App Spec in DO dashboard)

#### PORT Not Working

**Check:**
- Dockerfile uses `${PORT:-8000}` syntax
- DO has set http_port to 8000
- CMD uses shell form to expand env var

**Solution:**
- Verify Dockerfile CMD uses: `sh -c "php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"`
- Check DO settings show http_port: 8000

### Important Notes

- **No Spec Files Needed**: This project uses Docker-only deployment. No `app.yaml` or `project.yml` files are required or recommended.
- **Auto-Detection**: DigitalOcean will auto-detect the Dockerfile and configure the service automatically.
- **PORT Variable**: DigitalOcean sets `PORT` automatically from `http_port`. The Dockerfile handles this correctly.
- **Health Checks**: Health check is configured in Dockerfile. DO will monitor `/api/health` endpoint.
- **MongoDB**: MongoDB doesn't require migrations, but seeders should be run manually after first deploy.
- **package.json**: The `package.json` file is for frontend assets only. It will NOT trigger Node.js detection because we're using Docker.

## Database Seeding

### Run All Seeders

```bash
php artisan db:seed
```

### Run Specific Seeder

```bash
php artisan db:seed --class=AdminUserSeeder
```

## Testing

Run the test suite:

```bash
php artisan test
```

## Project Structure

```
yourmindai-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # API controllers
│   │   ├── Middleware/      # JWT auth, role checks
│   │   └── Requests/        # Form validation requests
│   ├── Models/              # MongoDB Eloquent models
│   └── Services/             # OpenAI service
├── config/                   # Configuration files
├── database/
│   └── seeders/              # Database seeders
├── routes/
│   └── api.php               # API routes
└── Dockerfile                # Docker configuration
```

## Troubleshooting

### MongoDB Connection Issues

- Verify `MONGODB_URI` is correct
- Check MongoDB is running and accessible
- Ensure network/firewall allows connections

### JWT Token Issues

- Verify `JWT_SECRET` is set
- Check token expiration time
- Ensure token is sent in `Authorization: Bearer <token>` format

### OpenAI API Issues

- Verify `OPENAI_API_KEY` is set correctly
- Check API rate limits
- Ensure sufficient API credits

## License

Proprietary - YourMindAI
