# Ticket Management System

A real-time ticket management system built with PHP, MySQL, and Server-Sent Events (SSE) that enables dynamic ticket tracking and updates without page refreshes.

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

## Installation

### 1. Clone the Repository

```sh
git clone https://github.com/yourusername/ticket-management-system.git
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

1. Create `config/db.php` with your database credentials:

```php
<?php
$pdo = new PDO(
    "mysql:host=localhost;dbname=your_database",
    "username",
    "password",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
```

2. Ensure your PHP environment has:
   - `max_execution_time` set appropriately for SSE
   - Required PHP extensions: `pdo_mysql`

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

## Development

The system consists of several key components:

- `index.php`: Main interface
- `fetch_tickets.php`: Handles ticket retrieval and search
- `update_tickets.php`: Processes ticket updates
- `realtime_updates.php`: Manages SSE connections
- `js/tickets.js`: Client-side functionality

## Author

Liou Jun Yi

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request
