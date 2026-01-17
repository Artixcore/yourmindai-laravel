-- MySQL initialization script
-- This script runs automatically when MySQL container is first created

-- Create database if it doesn't exist (already created by MYSQL_DATABASE env var, but ensure it exists)
CREATE DATABASE IF NOT EXISTS yourmindai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Grant privileges (user is already created by MYSQL_USER env var)
-- This ensures the user has all necessary permissions
GRANT ALL PRIVILEGES ON yourmindai.* TO 'yourmindai'@'%';
FLUSH PRIVILEGES;

-- Set secure defaults
SET GLOBAL max_connections = 200;
SET GLOBAL innodb_buffer_pool_size = 1073741824; -- 1GB
SET GLOBAL innodb_log_file_size = 268435456; -- 256MB
