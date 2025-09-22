# Employee Attendance System

A production-ready web attendance application with GPS verification, photo capture, and role-based access control built with Laravel 11, Tailwind CSS, and MySQL.

## Features

### 🎯 Core Functionality
- **GPS-based Attendance**: Employees can only check in/out within predefined geofence radius
- **Live Photo Capture**: Mandatory selfie verification using device camera
- **Role-based Access**: Admin, Leader, and Employee roles with specific permissions
- **Geofencing**: Configurable location radius with Haversine distance calculation
- **Real-time Validation**: Server-side GPS and photo validation

### 👥 User Roles

#### Employee Features
- Dashboard with attendance overview and statistics
- Check-in/Check-out with camera and GPS verification
- Attendance history with filtering and search
- Correction request system for attendance modifications
- Personal attendance statistics and trends

#### Admin Features
- Comprehensive dashboard with system-wide statistics
- Employee management (create, edit, deactivate)
- Location management with geofence configuration
- Shift management and assignment
- Attendance reports and data export (CSV/Excel)
- Audit log for all system activities
- Correction request approval workflow

#### Leader/HR Features
- Team attendance monitoring
- Correction request approval
- Department-specific reports
- Employee attendance compliance tracking

### 🔐 Security Features
- **Authentication**: Secure login with session management
- **Authorization**: Role-based permissions using Spatie Laravel Permission
- **CSRF Protection**: All forms protected against CSRF attacks
- **Input Validation**: Server-side validation for all inputs
- **GPS Validation**: Server-side coordinate validation
- **Photo Security**: Base64 image processing with size/type validation
- **Audit Trail**: Complete activity logging for compliance

### 📱 Technical Features
- **Responsive Design**: Mobile-first design works on all devices
- **PWA Ready**: Can be installed as a mobile app
- **Real-time GPS**: High-accuracy location services
- **Camera Integration**: Direct camera access without file uploads
- **Offline Handling**: Graceful handling of network issues
- **Performance Optimized**: Efficient queries with eager loading

## Installation

### Prerequisites
- PHP 8.2+
- MySQL 8.0+
- Node.js 18+
- Composer
- Git

### Setup Instructions

1. **Clone the repository**
```bash
git clone <repository-url>
cd attendance-app
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node.js dependencies**
```bash
npm install
```

4. **Environment Configuration**
```bash
cp .env.example .env
```

5. **Configure your `.env` file**
```env
APP_NAME="Attendance App"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=attendance_app
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Add any additional configuration
```

6. **Generate application key**
```bash
php artisan key:generate
```

7. **Run database migrations and seeders**
```bash
php artisan migrate --seed
```

8. **Create storage symlink**
```bash
php artisan storage:link
```

9. **Build assets**
```bash
npm run build
```

10. **Set permissions (Linux/macOS)**
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Development Setup

For development environment:

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate --seed
php artisan storage:link

# Start development servers
php artisan serve
npm run dev
```

## Demo Accounts

The system comes with pre-configured demo accounts:

- **Admin**: admin@example.com / password
- **Leader**: leader@example.com / password  
- **Employee**: employee@example.com / password

## Database Schema

### Core Tables
- `users` - Employee information and authentication
- `locations` - Work locations with GPS coordinates
- `shifts` - Work shift definitions
- `user_locations` - Employee location/shift assignments
- `attendance_records` - Check-in/out records with GPS and photos
- `correction_requests` - Attendance correction workflow
- `audit_logs` - System activity tracking

### Key Relationships
- Users have many attendance records
- Locations have geofence radius and GPS coordinates
- Users are assigned to specific location/shift combinations
- Correction requests link to attendance records

## API Endpoints

### Employee Endpoints
```
GET  /employee/dashboard              - Dashboard overview
GET  /employee/check-in               - Check-in interface
POST /employee/attendance/check-in    - Process check-in
GET  /employee/check-out              - Check-out interface  
POST /employee/attendance/check-out   - Process check-out
POST /employee/attendance/validate-location - GPS validation
GET  /employee/attendance/history     - Attendance history
POST /employee/attendance/correction-request - Submit correction
```

### Admin Endpoints
```
GET  /admin/dashboard     - Admin dashboard
GET  /admin/employees     - Employee management
GET  /admin/locations     - Location management
GET  /admin/reports       - Attendance reports
GET  /admin/attendance    - All attendance records
```

## Configuration

### Geofencing
Configure location radius in the locations table:
```sql
UPDATE locations SET geofence_radius = 100 WHERE id = 1; -- 100 meters
```

### Photo Storage
Photos are stored in `storage/app/public/attendance_photos/` organized by date:
```
attendance_photos/
├── 2024/01/15/
│   ├── checkin_1_2024_01_15_09_15_30_abc123.jpg
│   └── checkout_1_2024_01_15_17_30_45_def456.jpg
```

### Shifts Configuration
Manage work shifts in the database:
```sql
INSERT INTO shifts (name, start_time, end_time, is_active) 
VALUES ('Morning Shift', '09:00:00', '17:00:00', 1);
```

## Security Considerations

### Production Deployment
1. **SSL/TLS**: Always use HTTPS in production
2. **Environment**: Set `APP_ENV=production` and `APP_DEBUG=false`
3. **Database**: Use strong passwords and restrict access
4. **File Permissions**: Proper file permissions on storage directories
5. **Updates**: Keep Laravel and dependencies updated

### GPS Security
- Server-side coordinate validation
- Distance calculations use Haversine formula
- IP address and user agent logging
- GPS accuracy requirements

### Photo Security
- Base64 image processing
- File type validation (JPEG only)
- Size limitations (max 2MB)
- Watermarking with timestamp
- Secure storage with organized structure

## Testing

Run the test suite:
```bash
# Run all tests
php artisan test

# Run specific test types
php artisan test --filter=AttendanceTest
php artisan test --filter=GeofenceTest
```

### Test Coverage
- Unit tests for Haversine distance calculations
- Feature tests for attendance workflows  
- Form validation tests
- Authentication and authorization tests
- GPS validation tests

## Performance Optimization

### Database Optimization
- Indexed columns for frequent queries
- Eager loading to prevent N+1 queries
- Pagination for large datasets
- Connection pooling for high load

### Caching Strategy
```bash
# Enable caching
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear caches when needed
php artisan optimize:clear
```

### Asset Optimization
```bash
# Production build with minification
npm run build

# Enable Gzip compression (server config)
# Enable browser caching for static assets
```

## Monitoring & Logging

### Log Files
- `storage/logs/laravel.log` - Application logs
- Attendance activities logged to `audit_logs` table
- GPS validation logs
- Photo processing logs

### Health Checks
Monitor these metrics:
- Database connection status
- Storage disk space
- Average response times
- Failed login attempts
- GPS validation success rate

## Troubleshooting

### Common Issues

1. **GPS not working**
   - Ensure HTTPS is enabled
   - Check browser permissions
   - Verify location services are enabled

2. **Camera access denied**
   - Check browser camera permissions
   - Ensure HTTPS is enabled
   - Test on different devices/browsers

3. **Storage errors**
   - Verify storage symlink: `php artisan storage:link`
   - Check file permissions
   - Ensure disk space available

4. **Database connection errors**
   - Verify database credentials in `.env`
   - Check database server status
   - Test connection: `php artisan tinker` → `DB::connection()->getPdo()`

### Performance Issues
- Enable query logging to identify slow queries
- Use database query optimization
- Implement Redis for session/cache storage
- Enable APCu for PHP opcode caching

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## License

This project is licensed under the MIT License. See LICENSE file for details.

## Support

For technical support or questions:
- Create an issue in the repository
- Check the troubleshooting guide
- Review the Laravel documentation

## Roadmap

### Upcoming Features
- Mobile app (React Native/Flutter)
- Facial recognition integration
- Advanced reporting dashboard
- Multi-tenant support
- Shift scheduling system
- Integration with payroll systems
- Notification system (SMS/Email)
- Offline mode with sync capability

### Version History
- **v1.0.0** - Initial release with core functionality
- **v1.1.0** - Added correction request system
- **v1.2.0** - Enhanced admin dashboard and reports
- **v1.3.0** - Mobile app integration ready