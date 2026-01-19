# 🚀 Kitabasan LMS - Professional Project Recreation Prompt

## 📋 Project Overview

Create a comprehensive, production-ready **Learning Management System (LMS)** called **"Kitabasan LMS"** using Laravel 12, featuring multi-role access control, course management, payment integration, video hosting, device binding, and AI chatbot support.

---

## 🎯 Core Requirements

### Technology Stack
- **Backend**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL 5.7+
- **Authentication**: Laravel Sanctum
- **Authorization**: Spatie Laravel Permission (v6.24+)
- **Frontend**: Blade Templates + Tailwind CSS 4.0 + Alpine.js 3.15+ + AOS 2.3+
- **Build Tool**: Vite 7.0+
- **Video Hosting**: YouTube API, Bunny Stream API, Direct File Upload
- **Payment Gateways**: JazzCash, EasyPaisa
- **Package Manager**: Composer (PHP), NPM (Node.js)

---

## 👥 User Roles & Permissions

### 1. Admin Role
**Full System Access:**
- User management (CRUD operations for all users)
- Teacher & Student management
- Course approval/rejection workflow
- Payment transaction management & reports
- Device binding management & reset approval
- System settings configuration (languages, payment methods, theme)
- Analytics dashboard with key metrics
- Bulk operations (email/SMS to students)

**Default Credentials:**
- Email: `admin@kitabasan.com`
- Password: `password`

### 2. Teacher Role
**Course Creation & Management:**
- Create and manage courses (Books)
- Hierarchical content structure: Modules → Chapters → Lessons → Topics
- Upload videos via YouTube, Bunny Stream, or direct upload
- Mark content as FREE or PAID at chapter/lesson/topic level
- View enrolled students and their progress
- Use AI chatbot for support
- Manage course content (edit, delete, reorder)
- Course analytics (enrollments, views, completion rates)

**Default Credentials:**
- Email: `teacher@kitabasan.com`
- Password: `password`

### 3. Student Role
**Learning & Enrollment:**
- Browse course catalog (public and authenticated views)
- Enroll in free courses instantly
- Purchase paid courses via payment gateways
- Watch videos with progress tracking
- Track learning progress and completion
- Use AI chatbot for support
- View enrolled courses and learning dashboard
- Request device reset (requires admin approval)

**Default Credentials:**
- Email: `student@kitabasan.com`
- Password: `password`

---

## 🗄️ Database Schema

### Core Tables

#### Users & Authentication
```sql
users
- id, name, first_name, last_name, email, mobile, password
- profile_image, bio, status (active/inactive)
- address, city, state, country, postal_code
- date_of_birth, last_login_at
- email_verified_at, remember_token
- timestamps

permissions (Spatie)
- id, name, guard_name, timestamps

roles (Spatie)
- id, name, guard_name, timestamps

model_has_permissions, model_has_roles (Spatie pivot tables)
```

#### Course Structure (Hierarchical)
```sql
grades
- id, name, description, order, is_active, timestamps

subjects
- id, grade_id, name, description, order, is_active, timestamps

books (Courses)
- id, subject_id, grade_name, subject_name, teacher_id
- title, slug, description, short_description
- thumbnail, cover_image
- price (decimal), is_free (boolean)
- duration_months, access_duration_months
- status (draft/pending/approved/rejected/published)
- order, total_lessons, total_duration
- enrollment_count, rating, rating_count
- language, difficulty_level, course_level
- learning_objectives (JSON), prerequisites, tags (JSON)
- max_enrollments, start_date, end_date
- certificate_enabled, reviews_enabled, comments_enabled
- intro_video_url, intro_video_provider
- what_you_will_learn, course_requirements, target_audience
- meta_title, meta_description, meta_keywords (JSON)
- is_featured, is_popular, priority_order
- duration_hours, lectures_count, resources_count
- timestamps

modules
- id, book_id, title, description
- order_index, is_active
- release_type (immediate/scheduled), release_date
- timestamps

chapters
- id, book_id, module_id, title, description
- chapter_type, order, is_preview, is_free, is_active
- timestamps

lessons
- id, chapter_id, title, description
- video_id, video_host (youtube/bunny/direct)
- video_file, video_size, video_mime_type
- duration, order, status
- is_preview, is_free
- timestamps

topics
- id, lesson_id, title, description
- video_id, video_host, video_file
- video_size, video_mime_type
- duration, order, is_preview, is_free
- timestamps

content_items
- id, lesson_id, topic_id (nullable)
- title, description, content_type (video/audio/document/image/text)
- content_url, file_path, file_size, mime_type
- order_index, is_preview, is_free
- timestamps
```

#### Enrollment & Payment
```sql
course_enrollments
- id, user_id, book_id, payment_id (nullable)
- status (active/expired/cancelled)
- payment_status (free/paid/pending)
- enrolled_at, expires_at
- timestamps

payments
- id, user_id, book_id, payment_method_id
- amount, currency, status (pending/completed/failed/cancelled)
- transaction_id, gateway_response (JSON)
- paid_at, timestamps

transactions
- id, payment_id, gateway (jazzcash/easypaisa)
- transaction_id, status, amount
- response_data (JSON), timestamps

payment_methods
- id, name (JazzCash/EasyPaisa), type
- credentials (JSON), config (JSON)
- is_active, timestamps
```

#### Progress & Learning
```sql
lesson_progress
- id, user_id, lesson_id
- watch_percentage, completed_at
- last_watched_at, duration_watched
- timestamps

quizzes
- id, lesson_id, title, description
- passing_score, order, timestamps

mcqs
- id, quiz_id, question, options (JSON)
- correct_answer, order, timestamps

quiz_submissions
- id, user_id, quiz_id, score
- submitted_at, timestamps
```

#### Device Binding & Security
```sql
device_bindings
- id, user_id, device_fingerprint (unique)
- device_name, ip_address, user_agent
- status (active/blocked/pending_reset)
- last_used_at, reset_requested_at
- reset_request_reason, timestamps
```

#### Additional Features
```sql
chatbot_conversations
- id, user_id, role (student/teacher/public)
- message, response, timestamps

teacher_profiles
- id, user_id, bio, expertise, experience
- education, certifications, timestamps

course_teachers
- id, book_id, teacher_id, role (creator/co-teacher)
- timestamps

system_settings
- id, key, value, type, description, timestamps

theme_settings
- id, key, value, type, timestamps

languages
- id, name, code, is_default, is_active, timestamps
```

---

## 🔄 System Flows

### 1. Authentication & Device Binding Flow
- User registers/logs in
- System generates device fingerprint (SHA256 hash of user agent + IP + headers)
- **Students**: First device auto-binds; subsequent devices blocked until admin approval
- **Admin/Teacher**: Device binding bypassed
- Role-based redirect to appropriate dashboard

### 2. Course Creation Flow (Teacher)
1. Create Course (Book) → Select Grade & Subject → Enter details → Set price/free
2. Create Modules (optional grouping) → Set release type
3. Create Chapters within Modules → Mark as preview/free (optional)
4. Create Lessons within Chapters → Add video (YouTube/Bunny/Direct) → Mark preview/free
5. Create Topics within Lessons (optional) → Add content items
6. Submit for Admin Approval
7. Admin reviews → Approves/Rejects → Course published

### 3. Student Enrollment Flow
1. Browse Courses → View Details → See Preview Content
2. Click "Enroll":
   - **Free Course**: Instant enrollment → Redirect to learning dashboard
   - **Paid Course**: Redirect to payment page
3. Select Payment Method → Review Order → Initiate Payment
4. Redirect to Payment Gateway → Complete Payment
5. Gateway Callback/Webhook → Verify Payment → Activate Enrollment
6. Redirect to Learning Dashboard

### 4. Learning Flow (Student)
1. Access Learning Dashboard → View Course Structure
2. Click Lesson → Check Enrollment & Access Rights
3. Load Video Player (YouTube/Bunny/Direct) → Track Progress
4. Update Watch Percentage → Mark Complete (if 100%)
5. Access Topics & Content Items → Take Quizzes (if available)

### 5. Payment Processing Flow
1. Student selects payment method → Create Payment record (pending)
2. Generate Transaction ID → Prepare Gateway Request
3. Redirect to Payment Gateway → User completes payment
4. Gateway Callback → Verify Transaction → Update Payment Status
5. Activate Enrollment → Create/Update CourseEnrollment (active, paid)
6. Send Confirmation Email → Redirect to Learning Dashboard

### 6. Device Reset Flow
1. Student/Teacher requests device reset → Provide reason
2. Device status → `pending_reset`
3. Admin reviews request → Views device info & reason
4. Admin approves → Delete all device bindings → Allow new device
5. Admin rejects → Clear reset request → Keep current device

---

## 🎨 Frontend Requirements

### Design System
- **Color Scheme**:
  - Primary: #2563EB (Blue)
  - Secondary: #10B981 (Green)
  - Accent: #F59E0B (Amber)
  - Danger: #EF4444 (Red)
  - Success: #10B981 (Green)
  - Warning: #F59E0B (Amber)
  - Info: #3B82F6 (Blue)
  - Dark: #1F2937 (Gray)
  - Light: #F9FAFB (Gray)

### Layout Structure
- **Base Layout** (`layouts/app.blade.php`): Public pages
- **Admin Layout** (`layouts/admin.blade.php`): Admin dashboard & pages
- **Teacher Layout** (`layouts/teacher.blade.php`): Teacher dashboard & pages
- **Student Layout** (`layouts/student.blade.php`): Student dashboard & pages

### Components
- **Notification Toast System**: Real-time success/error/info messages
- **Public Chatbot Widget**: AI chatbot on public pages
- **Video Source Selector**: Component for selecting video hosting method
- **Progress Bar**: Visual progress indicators
- **Course Card**: Reusable course display component

### Responsive Design
- Mobile-first approach
- Breakpoints: sm (640px), md (768px), lg (1024px), xl (1280px), 2xl (1536px)
- Touch-friendly interfaces
- Optimized for all screen sizes

---

## 🔐 Security Requirements

### Authentication & Authorization
- Laravel Sanctum for API authentication
- Spatie Permission for role-based access control
- CSRF protection on all forms
- Password hashing (bcrypt)
- Session management

### Device Security
- One device per student account
- Device fingerprinting (SHA256)
- IP address tracking
- Device reset approval system
- Admin/Teacher bypass device binding

### Content Security
- Enrollment verification middleware
- Payment status verification
- Expiration date checking
- Preview/Free content access rules
- Secure video URL generation

### Payment Security
- Unique transaction ID generation
- Payment gateway signature verification
- Webhook security (signature validation)
- Idempotency checks
- Secure credential storage (encrypted JSON)

---

## 📁 Project Structure

```
kitabasan-lms/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── TeacherController.php
│   │   │   │   ├── StudentController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── PaymentController.php
│   │   │   │   ├── DeviceController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   ├── SettingsController.php
│   │   │   │   ├── PaymentMethodController.php
│   │   │   │   ├── LanguageController.php
│   │   │   │   └── ProfileController.php
│   │   │   ├── Teacher/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── ModuleController.php
│   │   │   │   ├── ChapterController.php
│   │   │   │   ├── LessonController.php
│   │   │   │   ├── TopicController.php
│   │   │   │   ├── ContentItemController.php
│   │   │   │   ├── VideoUploadController.php
│   │   │   │   ├── StudentController.php
│   │   │   │   ├── ChatbotController.php
│   │   │   │   ├── DeviceController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   └── SettingsController.php
│   │   │   ├── Student/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── LearningController.php
│   │   │   │   ├── PaymentController.php
│   │   │   │   ├── QuizController.php
│   │   │   │   ├── ChatbotController.php
│   │   │   │   ├── DeviceController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   └── SettingsController.php
│   │   │   ├── Public/
│   │   │   │   ├── HomeController.php
│   │   │   │   ├── CourseController.php
│   │   │   │   ├── AboutController.php
│   │   │   │   ├── ContactController.php
│   │   │   │   ├── PublicChatbotController.php
│   │   │   │   └── SitemapController.php
│   │   │   └── Auth/
│   │   │       ├── LoginController.php
│   │   │       ├── RegisterController.php
│   │   │       └── PasswordController.php
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
│   │   ├── Module.php
│   │   ├── Chapter.php
│   │   ├── Lesson.php
│   │   ├── Topic.php
│   │   ├── ContentItem.php
│   │   ├── Quiz.php
│   │   ├── Mcq.php
│   │   ├── QuizSubmission.php
│   │   ├── CourseEnrollment.php
│   │   ├── Payment.php
│   │   ├── Transaction.php
│   │   ├── PaymentMethod.php
│   │   ├── LessonProgress.php
│   │   ├── DeviceBinding.php
│   │   ├── ChatbotConversation.php
│   │   ├── TeacherProfile.php
│   │   ├── CourseTeacher.php
│   │   ├── SystemSetting.php
│   │   ├── ThemeSetting.php
│   │   └── Language.php
│   ├── Services/
│   │   ├── PaymentService.php
│   │   ├── VideoService.php
│   │   ├── DeviceService.php
│   │   ├── ChatbotService.php
│   │   ├── PublicChatbotService.php
│   │   ├── QuizService.php
│   │   ├── SEOService.php
│   │   └── SMSService.php
│   └── Mail/
│       └── StudentOffer.php
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2025_12_30_190943_create_permission_tables.php
│   │   ├── 2025_12_30_190943_create_personal_access_tokens_table.php
│   │   ├── 2025_12_30_190952_create_grades_table.php
│   │   ├── 2025_12_30_190952_create_subjects_table.php
│   │   ├── 2025_12_30_190953_create_books_table.php
│   │   ├── 2025_12_30_190953_create_chapters_table.php
│   │   ├── 2025_12_30_190954_create_lessons_table.php
│   │   ├── 2025_12_30_190954_create_quizzes_table.php
│   │   ├── 2025_12_30_190954_create_topics_table.php
│   │   ├── 2025_12_30_190955_create_mcqs_table.php
│   │   ├── 2025_12_30_191000_create_payments_table.php
│   │   ├── 2025_12_30_191000_create_transactions_table.php
│   │   ├── 2025_12_30_191001_create_course_enrollments_table.php
│   │   ├── 2025_12_30_191001_create_device_bindings_table.php
│   │   ├── 2025_12_30_191002_create_chatbot_conversations_table.php
│   │   ├── 2025_12_30_191002_create_lesson_progress_table.php
│   │   ├── 2025_12_30_191002_create_teacher_profiles_table.php
│   │   ├── 2025_12_30_191003_create_course_teachers_table.php
│   │   └── [Additional migrations for advanced features]
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── RoleSeeder.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── admin.blade.php
│   │   │   ├── teacher.blade.php
│   │   │   └── student.blade.php
│   │   ├── components/
│   │   │   ├── notification-toast.blade.php
│   │   │   ├── public-chatbot.blade.php
│   │   │   └── video-source-selector.blade.php
│   │   ├── partials/
│   │   │   ├── navigation.blade.php
│   │   │   └── footer.blade.php
│   │   ├── admin/
│   │   │   ├── dashboard/
│   │   │   ├── users/
│   │   │   ├── teachers/
│   │   │   ├── students/
│   │   │   ├── courses/
│   │   │   ├── payments/
│   │   │   ├── devices/
│   │   │   ├── reports/
│   │   │   ├── settings/
│   │   │   └── profile/
│   │   ├── teacher/
│   │   │   ├── dashboard/
│   │   │   ├── courses/
│   │   │   ├── lessons/
│   │   │   ├── students/
│   │   │   ├── chatbot/
│   │   │   ├── devices/
│   │   │   ├── profile/
│   │   │   └── settings/
│   │   ├── student/
│   │   │   ├── dashboard/
│   │   │   ├── courses/
│   │   │   ├── learning/
│   │   │   ├── payments/
│   │   │   ├── chatbot/
│   │   │   ├── devices/
│   │   │   ├── profile/
│   │   │   └── settings/
│   │   ├── public/
│   │   │   ├── home.blade.php
│   │   │   ├── courses/
│   │   │   ├── about/
│   │   │   └── contact/
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   ├── forgot-password.blade.php
│   │   │   └── reset-password.blade.php
│   │   └── emails/
│   │       └── student-offer.blade.php
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
├── routes/
│   └── web.php
├── public/
│   └── [Public assets]
├── composer.json
├── package.json
├── vite.config.js
├── tailwind.config.js
├── .env.example
└── README.md
```

---

## 🚀 Implementation Steps

### Phase 1: Project Setup
1. Create Laravel 12 project: `composer create-project laravel/laravel kitabasan-lms`
2. Install dependencies:
   - `composer require spatie/laravel-permission`
   - `composer require laravel/sanctum`
3. Install frontend dependencies:
   - `npm install -D tailwindcss@^4.0.0 @tailwindcss/vite vite laravel-vite-plugin`
   - `npm install alpinejs@^3.15.3 aos@^2.3.4 axios`
4. Configure Tailwind CSS and Vite
5. Set up database connection

### Phase 2: Database & Models
1. Create all migrations (49 migrations)
2. Run migrations: `php artisan migrate`
3. Create all models (24 models) with relationships
4. Create seeders (RoleSeeder, DatabaseSeeder)
5. Run seeders: `php artisan db:seed`

### Phase 3: Authentication & Authorization
1. Configure Spatie Permission
2. Create middleware (CheckRole, DeviceBinding, CheckEnrollment)
3. Create Auth controllers (Login, Register, Password Reset)
4. Create Auth views
5. Implement device fingerprinting logic

### Phase 4: Admin Module
1. Create Admin controllers (12 controllers)
2. Create Admin views (dashboard, users, courses, payments, etc.)
3. Implement CRUD operations
4. Implement course approval workflow
5. Implement device reset approval system

### Phase 5: Teacher Module
1. Create Teacher controllers (13 controllers)
2. Create Teacher views
3. Implement course creation flow
4. Implement hierarchical content structure (Modules → Chapters → Lessons → Topics)
5. Implement video upload (YouTube, Bunny Stream, Direct)
6. Implement free/paid content marking

### Phase 6: Student Module
1. Create Student controllers (10 controllers)
2. Create Student views
3. Implement course browsing & enrollment
4. Implement payment integration (JazzCash, EasyPaisa)
5. Implement learning dashboard & video player
6. Implement progress tracking

### Phase 7: Public Pages
1. Create Public controllers (6 controllers)
2. Create Public views (home, courses, about, contact)
3. Implement public chatbot
4. Implement SEO features

### Phase 8: Services & Integrations
1. Implement PaymentService (JazzCash, EasyPaisa)
2. Implement VideoService (YouTube, Bunny Stream)
3. Implement DeviceService
4. Implement ChatbotService (AI integration)
5. Implement other services (Quiz, SEO, SMS)

### Phase 9: Frontend Enhancement
1. Implement notification toast system
2. Implement responsive design
3. Add animations (AOS)
4. Implement video player components
5. Implement progress tracking UI

### Phase 10: Testing & Optimization
1. Test all user flows
2. Test payment integration (sandbox)
3. Test device binding
4. Optimize database queries
5. Cache configuration
6. Performance optimization

---

## 📝 Key Features to Implement

### 1. Free/Paid Content System
- Teachers can mark chapters, lessons, and topics as FREE
- Students can access free content without purchasing
- Paid content requires course enrollment with `payment_status='paid'`
- Clear visual indicators (FREE/PAID badges)

### 2. Video Hosting Options
- **YouTube**: Embed using video ID
- **Bunny Stream**: Professional video hosting with CDN
- **Direct Upload**: Upload videos to server storage
- Video player component that handles all three sources

### 3. Payment Integration
- JazzCash integration (sandbox & production)
- EasyPaisa integration (sandbox & production)
- Payment method management (admin)
- Payment history tracking
- Transaction management

### 4. Device Binding
- One device per student account
- Automatic device binding on first login
- Device reset request system
- Admin approval for device reset
- Device fingerprinting (SHA256)

### 5. Progress Tracking
- Watch percentage per lesson
- Completion status
- Last watched timestamp
- Duration watched
- Course completion tracking

### 6. AI Chatbot
- Student chatbot (role-specific)
- Teacher chatbot (role-specific)
- Public chatbot (no authentication)
- Chat history storage
- Context-aware responses

### 7. Quiz System
- Multiple choice questions (MCQs)
- Passing score configuration
- Quiz submissions
- Score calculation
- Quiz results tracking

---

## 🔧 Configuration Requirements

### Environment Variables (.env)
```env
APP_NAME="Kitabasan LMS"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kitabasan_lms
DB_USERNAME=root
DB_PASSWORD=

# Payment Gateways
JAZZCASH_MERCHANT_ID=
JAZZCASH_PASSWORD=
JAZZCASH_INTEGRITY_SALT=

EASYPAISA_MERCHANT_ID=
EASYPAISA_PASSWORD=

# Video Services
YOUTUBE_API_KEY=
BUNNY_STREAM_API_KEY=
BUNNY_LIBRARY_ID=

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@kitabasan.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## ✅ Acceptance Criteria

### Functional Requirements
- [ ] All three user roles (Admin, Teacher, Student) can log in and access their dashboards
- [ ] Teachers can create courses with hierarchical content structure
- [ ] Teachers can mark content as FREE or PAID
- [ ] Students can browse courses and see preview content
- [ ] Students can enroll in free courses instantly
- [ ] Students can purchase paid courses via payment gateways
- [ ] Payment integration works with JazzCash and EasyPaisa
- [ ] Students can watch videos with progress tracking
- [ ] Device binding works correctly (one device per student)
- [ ] Device reset request and approval system works
- [ ] AI chatbot responds appropriately for each role
- [ ] Admin can approve/reject courses
- [ ] Admin can manage users, payments, and devices
- [ ] All CRUD operations work correctly
- [ ] Responsive design works on all devices

### Non-Functional Requirements
- [ ] Code follows Laravel best practices
- [ ] Database queries are optimized
- [ ] Security best practices implemented
- [ ] Error handling is comprehensive
- [ ] UI/UX is intuitive and modern
- [ ] Performance is optimized
- [ ] Code is well-documented

---

## 📚 Documentation Requirements

Create comprehensive documentation including:
1. **README.md**: Project overview, installation, setup instructions
2. **PROJECT_STRUCTURE.md**: Detailed project structure
3. **PROJECT_FLOW.md**: Complete system flow documentation
4. **API_DOCUMENTATION.md**: API endpoints documentation
5. **DEPLOYMENT_GUIDE.md**: Deployment instructions
6. **FEATURE_DOCUMENTATION.md**: Feature-specific documentation

---

## 🎯 Success Metrics

The project is considered complete when:
- All user roles can perform their designated tasks
- Payment integration works in sandbox mode
- Video hosting works for all three sources
- Device binding works correctly
- All CRUD operations function properly
- UI is responsive and modern
- Code is production-ready
- Documentation is comprehensive

---

## 📞 Support & Maintenance

### Default Login Credentials
- **Admin**: admin@kitabasan.com / password
- **Teacher**: teacher@kitabasan.com / password
- **Student**: student@kitabasan.com / password

### Important Notes
- Use sandbox credentials for payment testing
- Configure video service API keys before testing video features
- Run migrations and seeders before first use
- Create storage link: `php artisan storage:link`
- Clear cache after configuration changes

---

**This prompt provides a complete blueprint for recreating the Kitabasan LMS project from scratch. Follow the implementation steps systematically, and ensure all features are tested thoroughly before deployment.**

---

**Prompt Version**: 1.0  
**Created**: January 2025  
**For**: Professional Project Recreation
