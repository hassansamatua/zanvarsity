-- Create the announcements table if it doesn't exist
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `location` varchar(255) DEFAULT NULL,
  `announcement_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_announcement_date` (`announcement_date`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert some sample data
INSERT INTO `announcements` (`title`, `description`, `location`, `announcement_date`, `is_active`) VALUES
('Conservatory Exhibit', 'The garden of India: a country and culture revealed', 'Matthaei Botanical Gardens', '2025-01-18', 1),
('February Half-Term Activities', 'Big Stars and Little Secrets', 'Pitt Rivers and Natural History Museums', '2025-02-01', 1),
('Orchestra Performance', 'The Orchestra of the Age of Enlightenment perform with Music', 'Faculty of Music', '2025-03-23', 1);
