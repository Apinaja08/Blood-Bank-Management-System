-- Member 3 (V10, E3): Create Least-Privilege Database User
-- This user is restricted to DML operations only and cannot alter schemas, drop databases, or grant privileges.
CREATE USER IF NOT EXISTS 'bloodbank_user'@'%' IDENTIFIED BY 'SecurePassword123!';
GRANT SELECT, INSERT, UPDATE, DELETE ON `bloodbank`.* TO 'bloodbank_user'@'%';
FLUSH PRIVILEGES;
