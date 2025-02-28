# Ticket Management System

A real-time ticket management system built with PHP, MySQL, and Server-Sent Events (SSE) that enables dynamic ticket tracking and updates without page refreshes.

## Disclaimer

This project was originally developed as part of my professional work and has been sanitized and generalized for portfolio purposes. It demonstrates my software development capabilities in building real-time web applications. Please note:

- This is a sanitized version of a work project, modified for educational purposes
- All proprietary code, business logic, and company-specific implementations have been removed
- The code is provided "as-is" without any warranties or guarantees
- This is not an officially supported product
- The developer and the original employer are not responsible for any misuse
- This implementation is generic and contains no proprietary information
- The project should be used for learning and educational purposes only
- Always obtain proper authorization before deploying in a production environment
- Users should thoroughly test in a non-production environment first
- All sensitive information, credentials, and internal logic have been removed
- This represents common ticketing system patterns and is intended for demonstration only

## Features

- **Real-Time Updates**: Automatic table updates using Server-Sent Events (SSE)
- **Dynamic Search**: Instant search across multiple fields (Contact, Ticket No, Description, Category, Status)
- **Ticket Management**:
  - View ticket status, assignment, and resolution times
  - Update ticket status (Ongoing/Resolved)
  - Assign tickets to personnel
  - Automatic timestamp recording for resolved tickets
- **Responsive Design**: Bootstrap-based mobile-friendly interface
- **Pagination**: Efficient handling of large ticket volumes
- **Secure Database Operations**: MySQL with PDO for SQL injection prevention

## Technical Stack

- **Frontend**: HTML5, JavaScript (jQuery), Bootstrap 5
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Real-time**: Server-Sent Events (SSE)
- **Dependencies**:
  - jQuery 3.6.0
  - Bootstrap 5.3.0
  - Bootstrap Icons 1.11.1

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (optional)

## Installation

### 1. Clone the Repository

```sh
git clone https://github.com/allenliou12/Ticket-Management-System
cd ticket-management-system
```

### 2. Database Setup

1. Create a MySQL database
2. Import the schema:

```sql
CREATE TABLE tickets (
    ticket_no INT PRIMARY KEY AUTO_INCREMENT,
    contact_details VARCHAR(255),
    issue_category VARCHAR(100),
    description TEXT,
    status ENUM('Ongoing', 'Resolved'),
    date_created DATETIME,
    date_resolved DATETIME NULL,
    assigned_to VARCHAR(100)
);
```

### 3. Configuration

1. Copy the example configuration file:

```sh
cp config/db.config.example.php config/db.config.php
```

2. Edit `config/db.config.php` with your database credentials:

```php
<?php
return [
    'DB_HOST' => 'your_database_host',
    'DB_NAME' => 'your_database_name',
    'DB_USER' => 'your_database_username',
    'DB_PASS' => 'your_database_password',
];
```

3. Server Requirements:
   - PHP extensions: `pdo_mysql`
   - PHP configuration:
     - `max_execution_time`: Set appropriately for SSE (recommended: 0)
     - `output_buffering`: off
     - `zlib.output_compression`: 0
     - `implicit_flush`: 1

### 4. Web Server Configuration

#### Apache

Ensure `.htaccess` allows SSE connections:

```apache
Header set Cache-Control "no-cache"
Header set Connection "keep-alive"
```

#### Nginx

Add to your server configuration:

```nginx
location / {
    proxy_set_header Connection '';
    proxy_http_version 1.1;
    proxy_buffering off;
    proxy_cache off;
    chunked_transfer_encoding off;
}
```

## Usage

1. Start your PHP server:

```sh
php -S localhost:8000
```

2. Access the application at `http://localhost:8000`

3. Features available:
   - Search tickets using the search bar
   - Update ticket status and assignments
   - View real-time updates
   - Navigate through paginated results
   - Apply changes in bulk

## Security Considerations

- Ensure proper access controls are implemented
- Keep database credentials secure
- Regularly update dependencies
- Monitor server resources for SSE connections
- Implement rate limiting if needed

## Troubleshooting

Common issues and solutions:

- If SSE updates aren't working, check server timeout settings
- For database connection issues, verify credentials and network access
- If changes aren't saving, check database permissions

## License

[MIT License](LICENSE)

## Author

Liou Jun Yi

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request
