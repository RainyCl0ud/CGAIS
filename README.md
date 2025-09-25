# CGS - Campus Guidance System


1. git clone <repo-url>
2. cd project
3. composer install
4. npm install
6. php artisan key:generate

7. php artisan migrate
8. php artisan db:seed
9. npm run build
10. php artisan serve


A comprehensive, industrial-level counseling appointment management system built with Laravel, featuring advanced analytics, session history, audit trails, and robust user management.


## 🚀 Features

### Core Functionality
- **Multi-Role User Management** (Students, Faculty, Counselors, Assistants)
- **Appointment Booking & Management**
- **Schedule Management for Counselors**
- **Personal Data Sheet Management**
- **Feedback System**
- **Notification System**

### Advanced Features (Industrial Level)

#### 📊 Session History & Analytics
- **Comprehensive Session History** for counselors with advanced filtering
- **Real-time Analytics Dashboard** with charts and statistics
- **Export Functionality** (CSV) for all reports
- **Monthly Trends Analysis**
- **Client Engagement Metrics**
- **Feedback Analytics**

#### 🔍 Advanced Search & Filtering
- **Multi-criteria Search** across appointments, clients, and sessions
- **Date Range Filtering**
- **Status-based Filtering**
- **Category-based Filtering**
- **Sorting Options** (Date, Time, Status, Type)

#### 📈 Reporting System
- **Appointment Reports** with detailed statistics
- **Client Reports** showing engagement patterns
- **Feedback Reports** with satisfaction metrics
- **Custom Date Range Reports**
- **Exportable Reports** in CSV format

#### 🔐 Audit Trail & Security
- **Complete Activity Logging** for all user actions
- **IP Address Tracking**
- **User Agent Logging**
- **Change History** with before/after values
- **Security Event Monitoring**

#### 🎯 Role-Based Access Control
- **Granular Permissions** for each user role
- **Middleware Protection** for sensitive routes
- **Data Isolation** between user types
- **Secure Authentication** with rate limiting

#### 📱 Modern UI/UX
- **Responsive Design** for all devices
- **Real-time Updates** and notifications
- **Intuitive Navigation** with sidebar
- **Professional Dashboard** with statistics cards
- **Modern Color Scheme** and typography

## 🛠 Technology Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade Templates with Tailwind CSS
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Breeze
- **Icons**: Heroicons
- **Charts**: Custom CSS-based progress bars
- **Export**: Native PHP CSV generation

## 📋 User Roles & Permissions

### 👨‍🎓 Students
- Book appointments with counselors
- View personal appointment history
- Manage personal data sheet
- Submit feedback for completed sessions
- View notifications

### 👨‍🏫 Faculty/Staff
- Book consultation appointments
- View appointment history
- Manage profile information
- Submit feedback

### 👨‍⚕️ Counselors
- View assigned appointments
- Manage personal schedule
- Update appointment status
- Add counselor notes
- View session history with advanced filtering
- Access comprehensive analytics and reports
- Export session data
- View activity logs

### 👨‍💼 Assistants
- View assigned appointments
- Update appointment status
- Access session history
- View basic reports
- Export data

## 🚀 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL/PostgreSQL
- Node.js & NPM

### Setup Instructions

1. **Clone the repository**
```bash
git clone <repository-url>
cd CGS
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

4. **Database configuration**
```bash
# Update .env with your database credentials
php artisan migrate
php artisan db:seed
```

5. **Build assets**
```bash
npm run build
```

6. **Start the server**
```bash
php artisan serve
```

## 📊 Key Features in Detail

### Session History System
- **Advanced Filtering**: Filter by status, type, category, date range
- **Search Functionality**: Search by client name, email, or notes
- **Sorting Options**: Sort by date, time, status, or type
- **Statistics Cards**: Real-time statistics for filtered results
- **Export Capability**: Export filtered results to CSV

### Analytics Dashboard
- **Appointment Statistics**: Total, completed, cancelled, no-show counts
- **Client Analytics**: New vs returning clients, engagement metrics
- **Feedback Analytics**: Average ratings, recommendation rates
- **Category Distribution**: Counseling service type breakdown
- **Monthly Trends**: 6-month appointment trends

### Activity Logging
- **Comprehensive Tracking**: All user actions logged
- **Change History**: Before/after values for updates
- **Security Monitoring**: IP addresses and user agents logged
- **Filterable Logs**: Filter by action, model type, date range
- **Export Functionality**: Export activity logs to CSV

### Advanced Search & Filtering
- **Multi-field Search**: Search across multiple fields simultaneously
- **Date Range Filtering**: Filter by custom date ranges
- **Status-based Filtering**: Filter by appointment status
- **Type-based Filtering**: Filter by appointment type
- **Category Filtering**: Filter by counseling category

## 🔧 Configuration

### Rate Limiting
- Login attempts: 5 per minute
- Password reset: 1 per minute
- Email verification: 6 per minute

### File Uploads
- Personal data sheet documents
- Profile pictures
- Export files

### Notifications
- Email notifications for appointments
- In-app notifications
- Real-time updates

## 📈 Performance Features

- **Database Indexing**: Optimized queries with proper indexing
- **Pagination**: Efficient data loading with pagination
- **Caching**: Strategic caching for frequently accessed data
- **Lazy Loading**: Efficient relationship loading
- **Query Optimization**: Optimized database queries

## 🔒 Security Features

- **CSRF Protection**: All forms protected against CSRF attacks
- **SQL Injection Prevention**: Parameterized queries
- **XSS Protection**: Input sanitization and output escaping
- **Rate Limiting**: Protection against brute force attacks
- **Role-based Access**: Granular permission system
- **Audit Trail**: Complete activity logging

## 📱 Mobile Responsiveness

- **Responsive Design**: Works on all device sizes
- **Touch-friendly**: Optimized for touch interfaces
- **Progressive Enhancement**: Core functionality works without JavaScript
- **Accessibility**: WCAG compliant design

## 🧪 Testing

### Available Test Commands
```bash
# Test counselor features
php artisan test:counselor

# Run all tests
php artisan test

# Test with data creation
php artisan test:counselor --create-data

# Test with cleanup
php artisan test:counselor --cleanup
```

### Test Data
The system includes comprehensive test data:
- Sample users for all roles
- Sample appointments with various statuses
- Sample schedules
- Sample feedback forms
- Sample activity logs

## 📚 API Documentation

### Key Endpoints

#### Appointments
- `GET /appointments` - List appointments
- `POST /appointments` - Create appointment
- `GET /appointments/{id}` - View appointment
- `PUT /appointments/{id}` - Update appointment
- `DELETE /appointments/{id}` - Cancel appointment

#### Session History
- `GET /appointments/session-history` - View session history
- `GET /appointments/export-history` - Export session history

#### Reports
- `GET /reports` - Analytics dashboard
- `GET /reports/appointments` - Appointment reports
- `GET /reports/clients` - Client reports
- `GET /reports/feedback` - Feedback reports
- `GET /reports/export` - Export reports

#### Activity Logs
- `GET /activity-logs` - View activity logs
- `GET /activity-logs/{id}` - View specific log
- `GET /activity-logs/export` - Export activity logs

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new features
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Contact the development team
- Check the documentation

## 🔄 Version History

### v2.0.0 (Current)
- Added comprehensive session history system
- Implemented advanced analytics and reporting
- Added activity logging and audit trail
- Enhanced search and filtering capabilities
- Improved UI/UX with modern design
- Added export functionality
- Implemented role-based access control

### v1.0.0
- Basic appointment booking system
- User management
- Schedule management
- Basic feedback system

---

**CGS** - Empowering educational institutions with comprehensive counseling management solutions.
