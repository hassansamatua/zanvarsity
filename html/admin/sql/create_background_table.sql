CREATE TABLE IF NOT EXISTS background_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    backgroundinfo LONGTEXT,
    ownership_accredition LONGTEXT,
    establishment_faculties LONGTEXT,
    university_membership LONGTEXT,
    memoranda_understanding LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default content from Background_info.php
INSERT INTO background_info (
    backgroundinfo, 
    ownership_accredition, 
    establishment_faculties, 
    university_membership, 
    memoranda_understanding,
    is_active
) VALUES (
    'The Zanzibar University, the first University on the Isles, is a private institution sponsored by Darul Iman Charitable Association (DICA). The main campus is situated at Tunguu area, in the Central District, some 19 kilometers from Zanzibar Town. 

The University campus, with a total area of 69 hectares of land, is located among pleasant and quiet countryside surroundings overlooking vast expanses of deep blue waters of Indian Ocean. It is an ideal place for serious academic work and research. Public transport from Zanzibar Town will bring you to the University campus gates. Private cars are also common.',
    
    'The Zanzibar University was founded and is owned and governed by Darul-Iman Charitable Association. It was established on the basis of the following:

• The Constitution of Darul-Iman registered under the Society\'s Act No. 6, 1995 given at Zanzibar on 2nd August, 1996.
• A letter of Interim Authority issued by the then Higher Education Accreditation Council bearing Ref. No. HEAC/SU of 1st May, 1998.
• The Certificate of Provisional Registration No. 007 of 22nd December, 1999.
• The Certificate of Full Registration No. 003 of 4th May, 2000.
• The provisions of the Universities Act, 2005.
• The Zanzibar University Charter, 2010 issued on 24th March, 2010 by the President of the United Republic of Tanzania, H.E. Dr. Jakaya Mrisho Kikwete.',
    
    '1998

The proliferation of business enterprises, Hotels, Beach resorts, and the gradual expansion of the tourism industry in the country, had convinced the development partners to begin first with a Faculty of Business Administration, with the view to satisfy the immediate needs of the business community. Five more faculties have been established as per the market demand for other professions.

1999

The Faculty of Law and Shariah was established.

2002

The Faculty of Arts and Social Sciences was established. Within seven or so years that followed however more but quite modern structures with larger classrooms were erected to accommodate bigger student\'s intakes.

2008/2009

The Institute of Continuing Education and the Institute of Postgraduate Studies and Research were established.

2012/2013

The Faculty of Engineering was established.

2015/2016

The Faculty of Science was established.',
    
    'Zanzibar University is a member of various national and international academic associations and professional bodies. The university maintains active memberships in organizations that promote higher education, research, and academic excellence in the region and beyond.',
    
    'Zanzibar University has established Memoranda of Understanding with various national and international institutions to enhance academic collaboration, research, and student exchange programs. These partnerships help in sharing knowledge, resources, and best practices in higher education.',
    
    1
)
ON DUPLICATE KEY UPDATE 
    backgroundinfo = VALUES(backgroundinfo),
    ownership_accredition = VALUES(ownership_accredition),
    establishment_faculties = VALUES(establishment_faculties),
    university_membership = VALUES(university_membership),
    memoranda_understanding = VALUES(memoranda_understanding);
