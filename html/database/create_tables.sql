-- Create carousel_slides table
CREATE TABLE IF NOT EXISTS `carousel_slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_url` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create announcements table
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create news table
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `publish_date` datetime NOT NULL,
  `is_published` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create events table (if not already exists)
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample carousel slides
INSERT INTO `carousel_slides` (`title`, `description`, `image_url`, `button_text`, `button_url`, `display_order`, `is_active`) VALUES
('Welcome to Zanvarsity', 'Your gateway to quality education and academic excellence', 'assets/img/slide-1.jpg', 'Learn More', 'about.php', 1, 1),
('Join Our Community', 'Become part of our growing academic family', 'assets/img/slide-2.jpg', 'Apply Now', 'admissions.php', 2, 1),
('Excellence in Education', 'Committed to providing world-class education', 'assets/img/slide-3.jpg', 'Our Programs', 'programs.php', 3, 1);

-- Insert sample announcements
INSERT INTO `announcements` (`title`, `content`, `image_url`, `is_published`) VALUES
('Campus Reopening', 'The campus will reopen on June 1st with new safety protocols in place.', 'assets/img/announcement-1.jpg', 1),
('Scholarship Applications', 'Applications for the 2025-2026 academic year are now open.', 'assets/img/announcement-2.jpg', 1),
('New Program Launch', 'We are excited to announce our new Computer Science program starting Fall 2025.', 'assets/img/announcement-3.jpg', 1);

-- Insert sample news
INSERT INTO `news` (`title`, `content`, `image_url`, `publish_date`, `is_published`) VALUES
('University Ranks Among Top 10', 'Zanvarsity has been ranked among the top 10 universities in the region for academic excellence.', 'assets/img/news-1.jpg', '2025-09-20 10:00:00', 1),
('Research Breakthrough', 'Our research team has made a significant breakthrough in renewable energy technology.', 'assets/img/news-2.jpg', '2025-09-15 14:30:00', 1),
('Alumni Reunion 2025', 'Join us for the annual alumni reunion event on December 15th, 2025.', 'assets/img/news-3.jpg', '2025-09-10 09:15:00', 1);

-- Insert sample events
INSERT INTO `events` (`title`, `description`, `start_date`, `end_date`, `location`, `image_url`, `is_published`) VALUES
('Open House Day', 'Visit our campus and learn about our programs and facilities.', '2025-10-15 09:00:00', '2025-10-15 17:00:00', 'Main Campus', 'assets/img/event-1.jpg', 1),
('Career Fair', 'Connect with top employers and explore job opportunities.', '2025-11-05 10:00:00', '2025-11-06 16:00:00', 'University Auditorium', 'assets/img/event-2.jpg', 1),
('Science & Tech Expo', 'Showcasing innovative projects by our students and faculty.', '2025-11-20 08:00:00', '2025-11-22 18:00:00', 'Science Building', 'assets/img/event-3.jpg', 1);
