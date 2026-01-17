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

## DigitalOcean Deployment

### Prerequisites

1. DigitalOcean App Platform account
2. MongoDB database (DigitalOcean Managed Database or external)
3. OpenAI API key

### Deployment Steps

1. **Connect Repository**: Connect your Git repository to DigitalOcean App Platform

2. **Set Environment Variables** in DigitalOcean App Platform:

   - `APP_KEY` - Generate with `php artisan key:generate`
   - `APP_URL` - Your DigitalOcean app URL
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `MONGODB_URI` - Your MongoDB connection string
   - `MONGODB_DATABASE` - Database name
   - `JWT_SECRET` - Generate a secure random string
   - `JWT_TTL=604800`
   - `CORS_ORIGIN` - Your web frontend URL
   - `OPENAI_API_KEY` - Your OpenAI API key
   - `OPENAI_MODEL=gpt-4o-mini`

3. **Build Configuration**: DigitalOcean will auto-detect PHP from the Dockerfile

4. **Run Migrations and Seeders** (on first deploy):

   Connect to your app via DigitalOcean console and run:

   ```bash
   php artisan db:seed
   ```

5. **Deploy**: Push to your connected branch to trigger deployment

### Important Notes

- The Dockerfile uses PHP 8.3 with MongoDB extension
- No `app.yaml` or `project.yml` files are needed - DigitalOcean auto-detects from Dockerfile
- Migrations are optional (MongoDB doesn't require migrations, but seeders should be run)

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
