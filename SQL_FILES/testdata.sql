-- Example data to populate the website with. They are not intended to be accessible, 
-- but to demonstrate the functionality of the website and make it look alive.

-- Insert 10 example users (all searchable)
INSERT INTO `user` (`email`, `password`, `first_name`, `last_name`, `telephone`, `location`, `birthday`, `searchable`, `competence`)
VALUES
('eljefejeff@quickmail.com', 'inaccessible', 'Jeff', 'Jefferson', '+1234567890', 'Springfield', '1990-05-15', 1, 'Experienced software developer with a focus on web development. Proficient in JavaScript, React, and Node.js. Strong problem-solving skills and a proven track record in delivering high-quality applications.'),
('aliceinwonderland@cyberinbox.net', 'inaccessible', 'Alice', 'Johnson', '+1987654321', 'Rivertown', '1985-08-22', 1, 'IT consultant specializing in network infrastructure and security. Skilled in designing and implementing scalable IT solutions. Excellent communication and client relationship management.'),
('emma.s@virtualpost.com', 'inaccessible', 'Emma', 'Smith', '+1654321987', 'Oakville', '1992-02-10', 1, 'Quality assurance professional with expertise in software testing and troubleshooting. Detail-oriented and committed to delivering bug-free applications. Familiar with various testing methodologies.'),
('mwilliams@mailinator.com', 'inaccessible', 'Michael', 'Williams', '+1765432109', 'Harborview', '1988-11-30', 1, 'Seasoned project manager with a background in software development. Proven leadership skills and a track record of successfully delivering projects on time and within budget.'),
('soph95@techmail.org', 'inaccessible', 'Sophia', 'Miller', '+1432109876', 'Maplewood', '1995-04-18', 1, 'Customer service professional dedicated to providing exceptional support. Effective communication and problem-solving skills. Committed to ensuring positive customer experiences.'),
('dannybrown@galaxyemail.com', 'inaccessible', 'Daniel', 'Brown', '+1122334455', 'Sunnydale', '1983-09-25', 1, 'Experienced software developer with a focus on leadership. Skilled in managing development teams and driving projects to successful completion. Proficient in Java and Spring.'),
('msdavis@nextronmail.co', 'inaccessible', 'Olivia', 'Davis', '+1555666777', 'Meadowville', '1998-07-12', 1, 'Digital marketing specialist with expertise in developing and implementing successful marketing campaigns. Proficient in SEO, SEM, and social media marketing.'),
('nissanfan80@quickmail.com', 'inaccessible', 'Noah', 'Jones', '+1444333222', 'Pinecrest', '1980-12-05', 1, 'Sales professional with a focus on negotiation and relationship building. Proven track record in achieving and exceeding sales targets.'),
('at1993@cyberinbox.net', 'inaccessible', 'Ava', 'Taylor', '+1777888999', 'Brookhaven', '1993-06-28', 1, 'Project manager with expertise in construction project oversight. Skilled in coordinating with contractors and ensuring project adherence to specifications.'),
('liam@virtualpost.com', 'inaccessible', 'Liam', 'Moore', '+1999111222', 'Summitville', '1991-03-08', 1, 'Database administrator with experience in managing and optimizing database systems. Skilled in ensuring data security and integrity. Proficient in SQL and database performance tuning.');

-- Insert 10 fictional companies with descriptions
INSERT INTO `company` (`name`, `description`)
VALUES
('InnovateHub', 'A cutting-edge technology company specializing in innovative solutions for businesses. We bring creativity and efficiency to the forefront of every project, from software development to automation systems.'),
('QuantumCraft', 'Pioneers in quantum computing and advanced algorithms, QuantumCraft pushes the boundaries of what''s possible in the digital realm. Our team is dedicated to shaping the future of computing with revolutionary technologies.'),
('EcoHarmony', 'Committed to sustainability and eco-friendly practices, EcoHarmony is a leading environmental solutions company. We provide innovative green technologies to help businesses reduce their ecological footprint.'),
('VirtuosoVista', 'A dynamic entertainment company, VirtuosoVista is dedicated to creating immersive experiences through virtual reality and augmented reality. Step into a world of limitless possibilities with our cutting-edge entertainment solutions.'),
('NebulaTech', 'Exploring the cosmos of technology, NebulaTech is a space-themed company specializing in aerospace innovations and satellite technologies. We reach for the stars to bring groundbreaking solutions back to Earth.'),
('WellSpring Wellness', 'Dedicated to holistic well-being, WellSpring Wellness is a health and lifestyle company. We offer a range of products and services focused on promoting physical and mental health for a balanced life.'),
('SynthSound Solutions', 'At the intersection of music and technology, SynthSound Solutions is a leading audio engineering company. Our team of sound enthusiasts crafts premium audio experiences through state-of-the-art technologies.'),
('PinnaclePulse', 'Elevating the heartbeat of technology, PinnaclePulse is a leading electronics company. We specialize in creating cutting-edge gadgets and devices that seamlessly integrate into the fast-paced modern lifestyle.'),
('SerenityScape', 'Escape to tranquility with SerenityScape, a nature-inspired landscaping company. We design and create serene outdoor spaces that bring the beauty of nature to homes and businesses.'),
('CelestialSweets', 'Indulge in heavenly delights with CelestialSweets, a boutique confectionery company. Our handcrafted treats and unique flavor combinations promise a sweet journey for your taste buds.');

-- Get company_id for each company
SET @company_id1 = (SELECT id FROM `company` WHERE name = 'InnovateHub');
SET @company_id2 = (SELECT id FROM `company` WHERE name = 'QuantumCraft');
SET @company_id3 = (SELECT id FROM `company` WHERE name = 'EcoHarmony');
SET @company_id4 = (SELECT id FROM `company` WHERE name = 'VirtuosoVista');
SET @company_id5 = (SELECT id FROM `company` WHERE name = 'NebulaTech');
SET @company_id6 = (SELECT id FROM `company` WHERE name = 'WellSpring Wellness');
SET @company_id7 = (SELECT id FROM `company` WHERE name = 'SynthSound Solutions');
SET @company_id8 = (SELECT id FROM `company` WHERE name = 'PinnaclePulse');
SET @company_id9 = (SELECT id FROM `company` WHERE name = 'SerenityScape');
SET @company_id10 = (SELECT id FROM `company` WHERE name = 'CelestialSweets');

-- Create job categories
INSERT INTO `job_category` (`title`) VALUES
('Software Development'),
('IT Consulting'),
('Environmental Science'),
('Virtual Reality Development'),
('Aerospace Engineering'),
('Health and Wellness'),
('Audio Engineering'),
('Electronics Development'),
('Landscaping Architecture'),
('Confectionery');

-- Get job_category_id for each category
SET @category_id1 = (SELECT id FROM `job_category` WHERE title = 'Software Development');
SET @category_id2 = (SELECT id FROM `job_category` WHERE title = 'IT Consulting');
SET @category_id3 = (SELECT id FROM `job_category` WHERE title = 'Environmental Science');
SET @category_id4 = (SELECT id FROM `job_category` WHERE title = 'Virtual Reality Development');
SET @category_id5 = (SELECT id FROM `job_category` WHERE title = 'Aerospace Engineering');
SET @category_id6 = (SELECT id FROM `job_category` WHERE title = 'Health and Wellness');
SET @category_id7 = (SELECT id FROM `job_category` WHERE title = 'Audio Engineering');
SET @category_id8 = (SELECT id FROM `job_category` WHERE title = 'Electronics Development');
SET @category_id9 = (SELECT id FROM `job_category` WHERE title = 'Landscaping Architecture');
SET @category_id10 = (SELECT id FROM `job_category` WHERE title = 'Confectionery');

-- Insert job listings with assigned categories
INSERT INTO `job_listing` (`name`, `description`, `deadline`, `published`, `company_id`, `job_category_id`)
VALUES
('Software Developer', 'Develop and maintain software applications.', '2024-01-15', 1, @company_id1, @category_id1),
('IT Consultant', 'Provide IT consultation services to clients.', '2024-02-01', 1, @company_id2, @category_id2),
('Environmental Scientist', 'Research and analyze environmental data.', '2024-01-30', 1, @company_id3, @category_id3),
('Virtual Reality Developer', 'Create immersive experiences through VR development.', '2024-02-15', 1, @company_id4, @category_id4),
('Aerospace Engineer', 'Design and develop innovative aerospace technologies.', '2024-01-20', 1, @company_id5, @category_id5),
('Health and Wellness Coordinator', 'Promote holistic health and well-being programs.', '2024-02-10', 1, @company_id6, @category_id6),
('Audio Engineer', 'Craft premium audio experiences with cutting-edge technologies.', '2024-01-25', 1, @company_id7, @category_id7),
('Electronics Developer', 'Create cutting-edge gadgets and devices.', '2024-02-20', 1, @company_id8, @category_id8),
('Landscaping Architect', 'Design and create serene outdoor spaces.', '2024-02-08', 1, @company_id9, @category_id9),
('Confectionery Chef', 'Craft handcrafted treats with unique flavor combinations.', '2024-01-28', 1, @company_id10, @category_id10);
