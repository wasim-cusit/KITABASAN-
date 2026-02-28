# 📚 Kitabasan Learning Management System (LMS)

A comprehensive, secure, and scalable video-based Learning Management System built with Laravel 12. This platform enables teachers to create and manage courses with free/paid content, and students to learn at their own pace.

## 🌟 Features

### Core Features
- ✅ **Multi-Role System**: Admin, Teacher, and Student roles with proper permissions
- ✅ **Course Management**: Grade → Subject → Course → Chapter → Lesson → Topic structure
- ✅ **Free/Paid Content**: Teachers can mark chapters, lessons, and topics as FREE or PAID
- ✅ **Video Hosting**: Support for YouTube, Bunny Stream, and direct video uploads
- ✅ **Payment Integration**: JazzCash and EasyPaisa payment gateways
- ✅ **Device Binding**: One device per user with admin-controlled reset
- ✅ **Progress Tracking**: Watch percentage, completion status, and learning analytics
- ✅ **AI Chatbot**: Integrated chatbot for student and teacher support
- ✅ **Mobile Responsive**: Beautiful, modern UI that works on all devices

### Free/Paid Content System
- Teachers can mark individual chapters, lessons, and topics as FREE
- Students can access free content without purchasing the course
- Paid content requires course enrollment
- Clear visual indicators (FREE/PAID badges) throughout the platform
- Free preview helps students make informed purchase decisions

## 🚀 Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 5.7 or higher
- Node.js and NPM (for frontend assets)

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/wasim-cusit/KITABASAN-.git
cd KITABASAN-
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Update `.env` file**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kitabasan_lms
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

6. **Create storage link** (required for uploaded images to show for admin, teacher, and student)
```bash
php artisan storage:link
```
If the link already exists, that's fine. Without it, images are still served via a fallback route.

7. **Start the development server**
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## 👥 Default Login Credentials

### Admin
- **Email**: admin@kitabasan.com
- **Password**: password

### Teacher
- **Email**: teacher@kitabasan.com
- **Password**: password

### Student
- **Email**: student@kitabasan.com
- **Password**: password

## 📁 Project Structure

```
kitabasan-lms/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/        # Admin controllers
│   │   │   ├── Teacher/      # Teacher controllers
│   │   │   ├── Student/     # Student controllers
│   │   │   ├── Public/      # Public controllers
│   │   │   └── Auth/         # Authentication controllers
│   │   └── Middleware/       # Custom middleware
│   ├── Models/              # Eloquent models
│   └── Services/             # Business logic services
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/
│   └── views/
│       ├── admin/            # Admin views
│       ├── teacher/           # Teacher views
│       ├── student/           # Student views
│       ├── public/            # Public views
│       └── auth/              # Authentication views
└── routes/
    └── web.php               # Web routes
```

## 🎯 Key Features Explained

### Free/Paid Content System
Teachers can mark content as FREE when creating:
- **Chapters**: Entire chapters can be free previews
- **Lessons**: Individual lessons can be free
- **Topics**: Specific topics within lessons can be free

Students can:
- Access all FREE content after login
- See FREE/PAID indicators on all content
- Purchase courses to unlock PAID content
- View free previews before purchasing

### Video Hosting Options
1. **YouTube**: Embed YouTube videos using video ID
2. **Bunny Stream**: Professional video hosting with CDN
3. **Direct Upload**: Upload videos directly to server storage

### Device Binding & Security
- First login binds to device fingerprint
- Only one active device per user
- Admin can reset device bindings
- IP and device tracking for security

### Payment Integration
- JazzCash integration
- EasyPaisa integration
- Payment history tracking
- Transaction management

## 🔧 Configuration

### Video Services
Configure in `.env`:
```env
YOUTUBE_API_KEY=your_youtube_api_key
BUNNY_STREAM_API_KEY=your_bunny_api_key
BUNNY_LIBRARY_ID=your_library_id
```

### Payment Gateways
Configure payment gateway credentials in `.env`:
```env
JAZZCASH_MERCHANT_ID=your_merchant_id
JAZZCASH_PASSWORD=your_password
EASYPAISA_MERCHANT_ID=your_merchant_id
EASYPAISA_PASSWORD=your_password
```

## 📝 API Endpoints

### Public Routes
- `GET /` - Home page
- `GET /courses` - Browse courses
- `GET /courses/{id}` - Course details
- `GET /about` - About page
- `GET /contact` - Contact page

### Student Routes
- `GET /student/dashboard` - Student dashboard
- `GET /student/courses` - Browse courses
- `GET /student/learning/{bookId}` - Learning dashboard
- `GET /student/learning/{bookId}/lesson/{lessonId}` - Watch lesson
- `POST /student/courses/{id}/enroll` - Enroll in course

### Teacher Routes
- `GET /teacher/dashboard` - Teacher dashboard
- `GET /teacher/courses` - My courses
- `POST /teacher/courses/{bookId}/chapters` - Create chapter
- `POST /teacher/courses/{bookId}/chapters/{chapterId}/lessons` - Create lesson
- `POST /teacher/courses/{bookId}/chapters/{chapterId}/lessons/{lessonId}/topics` - Create topic

## 🛠️ Technologies Used

- **Backend**: Laravel 12
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **Authorization**: Spatie Laravel Permission
- **Frontend**: Blade Templates + Tailwind CSS
- **Video**: YouTube API, Bunny Stream, Direct Upload
- **Payments**: JazzCash, EasyPaisa

## 📚 Documentation

- [Project Structure](PROJECT_STRUCTURE.md)
- [Implementation Guide](IMPLEMENTATION_GUIDE.md)
- [Free/Paid Content Feature](FREE_PAID_CONTENT_FEATURE.md)
- [Video Upload Guide](VIDEO_UPLOAD_COMPLETE.md)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is proprietary software. All rights reserved.

## 👨‍💻 Author

**Wasim CUSIT**
- GitHub: [@wasim-cusit](https://github.com/wasim-cusit)

## 🙏 Acknowledgments

- Laravel Framework
- Spatie Laravel Permission
- Tailwind CSS
- All contributors and testers

---

**Note**: Make sure to configure your `.env` file with proper database credentials and API keys before running the application.
