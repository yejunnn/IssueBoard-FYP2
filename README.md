# IssueBoard - Laravel Issue Tracking System

A modern, real-time issue tracking and management system built with Laravel, featuring real-time notifications, department-based ticket management, and an intuitive user interface.

## 🚀 Features

- **Real-time Notifications**: Instant updates using WebSockets
- **Department-based Management**: Organize tickets by departments
- **Role-based Access Control**: Admin, Department Head, and User roles
- **Ticket Lifecycle Management**: Create, assign, track, and resolve tickets
- **Category System**: Organize tickets by categories
- **Real-time Dashboard**: Live updates on ticket status and assignments
- **Mobile Responsive**: Works seamlessly on all devices
- **Email Notifications**: Automated email alerts for ticket updates

## 🛠️ Technology Stack

- **Backend**: Laravel 11 (PHP)
- **Frontend**: Blade Templates with Tailwind CSS
- **Real-time**: WebSockets for live updates
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Breeze
- **Styling**: Tailwind CSS

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and NPM
- MySQL/PostgreSQL database
- Web server (Apache/Nginx)

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/IssueBoard.git
   cd IssueBoard
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   Edit `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=issueboard
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

## 👥 Default Users

After running the seeders, you'll have these default users:

- **Admin**: admin@issueboard.com / password
- **Department Head**: depthead@issueboard.com / password
- **Regular User**: user@issueboard.com / password

## 📁 Project Structure

```
IssueBoard/
├── app/
│   ├── Console/Commands/     # Custom Artisan commands
│   ├── Events/              # Event classes for notifications
│   ├── Http/Controllers/    # Application controllers
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic services
│   └── View/Components/     # Blade components
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   └── views/              # Blade templates
├── routes/                 # Application routes
└── public/                 # Public assets
```

## 🔧 Configuration

### Real-time Notifications
The system uses WebSockets for real-time notifications. Configure your broadcasting driver in `.env`:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1
```

### Email Configuration
Configure your email settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email
MAIL_FROM_NAME="${APP_NAME}"
```

## 🎯 Usage

### For Users
1. Register or login to your account
2. Create new tickets with detailed descriptions
3. Track ticket status and updates
4. Receive real-time notifications

### For Department Heads
1. View tickets assigned to your department
2. Assign tickets to team members
3. Update ticket status and priority
4. Manage department-specific categories

### For Admins
1. Manage all users and departments
2. Create and manage categories
3. Monitor system-wide ticket statistics
4. Assign tickets across departments

## 🧪 Testing

Run the test suite:

```bash
php artisan test
```

## 📝 API Documentation

The application provides RESTful APIs for:
- Ticket management (CRUD operations)
- User management
- Department management
- Notification handling

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

If you encounter any issues or have questions:
1. Check the [Issues](https://github.com/yourusername/IssueBoard/issues) page
2. Create a new issue with detailed information
3. Contact the development team

## 🔄 Changelog

### Version 1.0.0
- Initial release
- Real-time notifications
- Department-based ticket management
- Role-based access control
- Mobile responsive design

---

**Built with ❤️ using Laravel**
