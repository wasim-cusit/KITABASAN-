# KITABASAN LEARNING PLATFORM - Project Structure

## Directory Structure

```
kitabasan-lms/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin-specific controllers
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── PaymentController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   └── DeviceController.php
│   │   │   ├── Teacher/        # Teacher-specific controllers
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── LessonController.php
│   │   │   │   ├── StudentController.php
│   │   │   │   └── ChatbotController.php
│   │   │   ├── Student/        # Student-specific controllers
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── LearningController.php
│   │   │   │   ├── PaymentController.php
│   │   │   │   └── ChatbotController.php
│   │   │   └── Public/          # Public pages
│   │   │       ├── HomeController.php
│   │   │       ├── CourseController.php
│   │   │       ├── AboutController.php
│   │   │       └── ContactController.php
│   │   ├── Middleware/
│   │   │   ├── CheckRole.php
│   │   │   ├── DeviceBinding.php
│   │   │   └── CheckEnrollment.php
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Grade.php
│   │   ├── Subject.php
│   │   ├── Book.php
│   │   ├── Chapter.php
│   │   ├── Lesson.php
│   │   ├── Topic.php
│   │   ├── Quiz.php
│   │   ├── Mcq.php
│   │   ├── Payment.php
│   │   ├── Transaction.php
│   │   ├── DeviceBinding.php
│   │   ├── CourseEnrollment.php
│   │   ├── LessonProgress.php
│   │   ├── TeacherProfile.php
│   │   ├── ChatbotConversation.php
│   │   └── CourseTeacher.php
│   └── Services/
│       ├── PaymentService.php
│       ├── VideoService.php
│       ├── DeviceService.php
│       └── ChatbotService.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── admin.blade.php
│   │   │   ├── teacher.blade.php
│   │   │   └── student.blade.php
│   │   ├── admin/               # Admin views
│   │   │   ├── dashboard/
│   │   │   ├── users/
│   │   │   ├── courses/
│   │   │   ├── payments/
│   │   │   └── reports/
│   │   ├── teacher/             # Teacher views
│   │   │   ├── dashboard/
│   │   │   ├── courses/
│   │   │   ├── lessons/
│   │   │   └── students/
│   │   ├── student/             # Student views
│   │   │   ├── dashboard/
│   │   │   ├── courses/
│   │   │   ├── learning/
│   │   │   └── payments/
│   │   └── public/              # Public views
│   │       ├── home.blade.php
│   │       ├── courses/
│   │       ├── about.blade.php
│   │       └── contact.blade.php
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
├── routes/
│   ├── web.php
│   ├── admin.php
│   ├── teacher.php
│   ├── student.php
│   └── api.php
└── database/
    ├── migrations/
    └── seeders/
```

## Color Scheme

- **Primary Color**: #2563EB (Blue)
- **Secondary Color**: #10B981 (Green)
- **Accent Color**: #F59E0B (Amber)
- **Danger Color**: #EF4444 (Red)
- **Success Color**: #10B981 (Green)
- **Warning Color**: #F59E0B (Amber)
- **Info Color**: #3B82F6 (Blue)
- **Dark Color**: #1F2937 (Gray)
- **Light Color**: #F9FAFB (Gray)

## Technology Stack

- **Backend**: Laravel 12
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **Role Management**: Spatie Laravel Permission
- **Frontend**: Blade Templates with Tailwind CSS
- **Video Hosting**: YouTube API / Bunny Stream
- **Payment**: JazzCash, EasyPaisa

## Key Features

1. **Role-Based Access Control**: Admin, Teacher, Student
2. **Course Structure**: Grade → Subject → Book → Chapter → Lesson
3. **Video Streaming**: Secure video embedding
4. **Payment Integration**: JazzCash & EasyPaisa
5. **Device Binding**: One device per account
6. **Progress Tracking**: Watch percentage, timestamps
7. **AI Chatbot**: Support for students and teachers
8. **Mobile Ready**: REST API for mobile apps

