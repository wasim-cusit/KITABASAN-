# KITABASAN LEARNING PLATFORM

A comprehensive Laravel-based Learning Management System (LMS) with video streaming, payment integration, and role-based access control.

## Features

- ✅ Multi-role system (Admin, Teacher, Student)
- ✅ Course structure: Grade → Subject → Book → Chapter → Lesson
- ✅ Video hosting (YouTube API / Bunny Stream)
- ✅ Payment gateways (JazzCash, EasyPaisa)
- ✅ Device binding & anti-sharing
- ✅ Progress tracking
- ✅ AI Chatbot support
- ✅ Mobile-ready REST API
- ✅ Professional UI/UX

## Installation

1. **Clone the repository**
   ```bash
   cd kitabasan-lms
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   Update `.env` with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=kitabasan_lms
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Publish vendor assets**
   ```bash
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
   ```

7. **Start development server**
   ```bash
   php artisan serve
   npm run dev
   ```

## Default Credentials

After seeding:
- **Admin**: admin@kitabasan.com / password
- **Teacher**: teacher@kitabasan.com / password
- **Student**: student@kitabasan.com / password

## Project Structure

- `app/Http/Controllers/Admin/` - Admin controllers
- `app/Http/Controllers/Teacher/` - Teacher controllers
- `app/Http/Controllers/Student/` - Student controllers
- `app/Http/Controllers/Public/` - Public pages
- `resources/views/admin/` - Admin views
- `resources/views/teacher/` - Teacher views
- `resources/views/student/` - Student views
- `resources/views/public/` - Public views

## Configuration

### Payment Gateways

Configure in `.env`:
```env
JAZZCASH_MERCHANT_ID=your_merchant_id
JAZZCASH_PASSWORD=your_password
EASYPAISA_MERCHANT_ID=your_merchant_id
EASYPAISA_PASSWORD=your_password
```

### Video Hosting

Configure in `.env`:
```env
YOUTUBE_API_KEY=your_api_key
BUNNY_STREAM_API_KEY=your_api_key
BUNNY_STREAM_LIBRARY_ID=your_library_id
```

## API Documentation

API endpoints are available at `/api/documentation` (to be implemented)

## License

Proprietary - All rights reserved
