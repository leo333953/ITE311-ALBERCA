<?php
echo "🔧 Setting up LMS Database...\n";

// Connect to MySQL server
$mysqli = new mysqli('localhost', 'root', '');
if ($mysqli->connect_error) {
    die("❌ Connection failed: " . $mysqli->connect_error);
}
echo "✅ Connected to MySQL server\n";

// Create database
if ($mysqli->query("CREATE DATABASE IF NOT EXISTS lms_system")) {
    echo "✅ Database 'lms_system' created successfully\n";
} else {
    echo "❌ Error creating database: " . $mysqli->error . "\n";
}

// Select the database
$mysqli->select_db('lms_system');

// Create migrations table
$migrations_sql = "
CREATE TABLE IF NOT EXISTS migrations (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  version varchar(255) NOT NULL,
  class varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  namespace varchar(255) NOT NULL,
  time int(11) NOT NULL,
  batch int(11) unsigned NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if ($mysqli->query($migrations_sql)) {
    echo "✅ Migrations table created\n";
} else {
    echo "❌ Error creating migrations table: " . $mysqli->error . "\n";
}

// Create users table
$users_sql = "
CREATE TABLE IF NOT EXISTS users (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  role enum('admin','teacher','student') NOT NULL DEFAULT 'student',
  created_at datetime DEFAULT NULL,
  updated_at datetime DEFAULT NULL,
  deleted_at datetime DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if ($mysqli->query($users_sql)) {
    echo "✅ Users table created\n";
} else {
    echo "❌ Error creating users table: " . $mysqli->error . "\n";
}

// Create courses table
$courses_sql = "
CREATE TABLE IF NOT EXISTS courses (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(255) NOT NULL,
  description text,
  instructor_id int(11) unsigned DEFAULT NULL,
  created_at datetime DEFAULT NULL,
  updated_at datetime DEFAULT NULL,
  deleted_at datetime DEFAULT NULL,
  PRIMARY KEY (id),
  KEY instructor_id (instructor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if ($mysqli->query($courses_sql)) {
    echo "✅ Courses table created\n";
} else {
    echo "❌ Error creating courses table: " . $mysqli->error . "\n";
}

// Create enrollments table
$enrollments_sql = "
CREATE TABLE IF NOT EXISTS enrollments (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(11) unsigned NOT NULL,
  course_id int(11) unsigned NOT NULL,
  enrollment_date datetime DEFAULT NULL,
  status enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  student_info text,
  student_details_id int(11) DEFAULT NULL,
  approved_by int(11) unsigned DEFAULT NULL,
  approved_at datetime DEFAULT NULL,
  rejection_reason text,
  student_number varchar(50) DEFAULT NULL,
  full_name varchar(255) DEFAULT NULL,
  course_year varchar(255) DEFAULT NULL,
  email varchar(255) DEFAULT NULL,
  phone_number varchar(20) DEFAULT NULL,
  address text,
  date_of_birth date DEFAULT NULL,
  guardian_name varchar(255) DEFAULT NULL,
  guardian_phone varchar(20) DEFAULT NULL,
  guardian_email varchar(255) DEFAULT NULL,
  submitted_at datetime DEFAULT NULL,
  processed_at datetime DEFAULT NULL,
  remarks text,
  deleted_at datetime DEFAULT NULL,
  PRIMARY KEY (id),
  KEY user_id (user_id),
  KEY course_id (course_id),
  KEY approved_by (approved_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if ($mysqli->query($enrollments_sql)) {
    echo "✅ Enrollments table created\n";
} else {
    echo "❌ Error creating enrollments table: " . $mysqli->error . "\n";
}

// Create notifications table
$notifications_sql = "
CREATE TABLE IF NOT EXISTS notifications (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(11) unsigned NOT NULL,
  message text NOT NULL,
  is_read tinyint(1) NOT NULL DEFAULT 0,
  created_at datetime DEFAULT NULL,
  PRIMARY KEY (id),
  KEY user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if ($mysqli->query($notifications_sql)) {
    echo "✅ Notifications table created\n";
} else {
    echo "❌ Error creating notifications table: " . $mysqli->error . "\n";
}

// Create materials table
$materials_sql = "
CREATE TABLE IF NOT EXISTS materials (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  course_id int(11) unsigned NOT NULL,
  file_name varchar(255) NOT NULL,
  file_path varchar(500) NOT NULL,
  created_at datetime DEFAULT NULL,
  updated_at datetime DEFAULT NULL,
  deleted_at datetime DEFAULT NULL,
  PRIMARY KEY (id),
  KEY course_id (course_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if ($mysqli->query($materials_sql)) {
    echo "✅ Materials table created\n";
} else {
    echo "❌ Error creating materials table: " . $mysqli->error . "\n";
}

// Insert default admin user
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
$admin_sql = "
INSERT IGNORE INTO users (id, name, email, password, role, created_at, updated_at) VALUES
(1, 'Admin User', 'admin@lms.com', '$admin_password', 'admin', NOW(), NOW());
";

if ($mysqli->query($admin_sql)) {
    echo "✅ Default admin user created\n";
    echo "   📧 Email: admin@lms.com\n";
    echo "   🔑 Password: admin123\n";
} else {
    echo "❌ Error creating admin user: " . $mysqli->error . "\n";
}

// Insert migration records
$migration_records = "
INSERT IGNORE INTO migrations (version, class, `group`, namespace, time, batch) VALUES
('2025-09-05-201314', 'App\\\\Database\\\\Migrations\\\\CreateUsersTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
('2025-09-05-201712', 'App\\\\Database\\\\Migrations\\\\CreateCoursesTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
('2025-09-05-202240', 'App\\\\Database\\\\Migrations\\\\CreateEnrollmentsTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
('2025-12-03-055323', 'App\\\\Database\\\\Migrations\\\\CreateNotificationsTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
('2025-10-27-051022', 'App\\\\Database\\\\Migrations\\\\CreateMaterialsTable', 'default', 'App', UNIX_TIMESTAMP(), 1);
";

if ($mysqli->query($migration_records)) {
    echo "✅ Migration records inserted\n";
} else {
    echo "❌ Error inserting migration records: " . $mysqli->error . "\n";
}

$mysqli->close();

echo "\n🎉 Database setup complete!\n";
echo "🌐 You can now access: http://localhost/ITE311-ALBERCA/login\n";
echo "👤 Login with: admin@lms.com / admin123\n";
?>