SELECT 
    s.id AS SID,
    s.reg_no AS reg,
    s.first_name AS FN,
    s.last_name AS LN,
    sch.session_id AS sch_id,
    a.year AS s_year,
    sch.start_term,
    sch.end_term,
    sch.leave_date,
    sch.is_active,
    sch.inactive_session_id AS in_SID,
    sch.inactive_term AS in_term
FROM 
    students s
    INNER JOIN student_class_history sch ON s.id = sch.student_id
    LEFT JOIN academic_sessions a ON sch.session_id = a.id
WHERE 
    sch.leave_date IS NOT NULL
    AND sch.is_active = FALSE
    AND (sch.inactive_session_id IS NULL OR sch.inactive_term IS NULL)
ORDER BY 
    s.id;




SELECT 
    s.id AS student_id,
    s.reg_no,
    CONCAT(s.first_name, ' ', s.last_name) AS full_name,
    sch.session_id,
    a.year AS session_year,
    sch.start_term,
    sch.end_term,
    sch.leave_date,
    sch.is_active,
    sch.inactive_session_id,
    sch.inactive_term,
    COUNT(*) OVER (PARTITION BY s.id) AS record_count
FROM 
    students s
    INNER JOIN student_class_history sch ON s.id = sch.student_id
    LEFT JOIN academic_sessions a ON sch.session_id = a.id
WHERE 
    a.year = '2023/2024'
ORDER BY 
    s.id;



SELECT 
    s.id AS student_id,
    s.reg_no,
    CONCAT(s.first_name, ' ', s.last_name) AS full_name,
    sch.session_id,
    a.year AS session_year,
    sch.start_term,
    sch.end_term,
    sch.is_active,
    COUNT(*) OVER (PARTITION BY s.id) AS record_count
FROM 
    students s
    INNER JOIN student_class_history sch ON s.id = sch.student_id
    LEFT JOIN academic_sessions a ON sch.session_id = a.id
ORDER BY 
    s.id;



UPDATE student_class_history sch
INNER JOIN academic_sessions a ON sch.session_id = a.id
SET sch.start_term = 'First'
WHERE sch.student_id = 107 AND a.year = '2024/2025';






mysql> SELECT
    ->     s.id AS student_id,
    ->     s.reg_no,
    ->     CONCAT(s.first_name, ' ', s.last_name) AS full_name,
    ->     sch.session_id,
    ->     a.year AS session_year,
    ->     sch.start_term,
    ->     sch.end_term,
    ->     sch.is_active,
    ->     COUNT(*) OVER (PARTITION BY s.id) AS record_count
    -> FROM
    ->     students s
    ->     INNER JOIN student_class_history sch ON s.id = sch.student_id
    ->     LEFT JOIN academic_sessions a ON sch.session_id = a.id
    -> ORDER BY
    ->     s.id;
+------------+---------------+------------------------+------------+--------------+------------+----------+-----------+--------------+
| student_id | reg_no        | full_name              | session_id | session_year | start_term | end_term | is_active | record_count |
+------------+---------------+------------------------+------------+--------------+------------+----------+-----------+--------------+
|          7 | AAIS/0559/007 | Ogochukwu Okorie       |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|          8 | AAIS/0559/008 | David Ndubueze         |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|          9 | AAIS/0559/009 | Taylor Imeole          |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         10 | AAIS/0559/010 | Temple Williams        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         11 | AAIS/0559/011 | Jadon Nwoji            |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         12 | AAIS/0559/012 | Joshua Nwoji           |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         13 | AAIS/0559/013 | Ezinne Chinenye        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         14 | AAIS/0559/014 | John Miracle           |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         15 | AAIS/0559/015 | Adaeze Okechukwu       |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         16 | AAIS/0559/016 | Ikechukwu Chukwubuikem |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         17 | AAIS/0559/017 | Goodluck Nwankpa       |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         18 | AAIS/0559/018 | Wisdom Ndubueze        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         19 | AAIS/0559/019 | Wisdom Chinyeaka       |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         20 | AAIS/0559/020 | Oluchukwu Chinedu      |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         21 | AAIS/0559/021 | Miracle Ezereibe       |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         22 | AAIS/0559/022 | Ifunanya Charles       |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         23 | AAIS/0559/023 | Elder Udochukwu        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         24 | AAIS/0559/024 | Special Chidi          |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         25 | AAIS/0559/025 | Ijeoma Moses           |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         26 | AAIS/0559/026 | Genevieve Joseph       |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         27 | AAIS/0559/027 | David Asoegwu          |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         28 | AAIS/0559/028 | DivineFavour Israel    |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         29 | AAIS/0559/029 | Emmanuel Divine        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         30 | AAIS/0559/030 | MaryJane Joseph        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         31 | AAIS/0559/031 | Gift John              |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         32 | AAIS/0559/032 | Excel Maduabuchi       |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         33 | AAIS/0559/033 | Prince Tochukwu        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         34 | AAIS/0559/034 | Mary John              |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         35 | AAIS/0559/035 | Great Chinyeaka        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         36 | AAIS/0559/036 | Adaeze Moses           |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         37 | AAIS/0559/037 | Amarachi Chinedu       |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         38 | AAIS/0559/038 | Nneomannaya Nwankpa    |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         39 | AAIS/0559/039 | Ugoeze Chidi           |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         40 | AAIS/0559/040 | Chikamso Odimegwu      |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         41 | AAIS/0559/041 | Divine Ndubueze        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         42 | AAIS/0559/042 | Future Anosike         |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         43 | AAIS/0559/043 | Daniel Asoegwu         |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         44 | AAIS/0559/044 | Flourish Maduabuchi    |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         46 | AAIS/0559/046 | Nice Freedom           |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         47 | AAIS/0559/047 | David Chinenye         |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         48 | AAIS/0559/048 | Victory Gideon         |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         49 | AAIS/0559/049 | Callistus Moses        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         50 | AAIS/0559/050 | Samuel Ime             |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         51 | AAIS/0559/051 | Winner Chidi           |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         52 | AAIS/0559/052 | Miracle Ndubueze       |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         53 | AAIS/0559/053 | Esther Nwankpa         |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         54 | AAIS/0559/054 | Chizaram Prince        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         55 | AAIS/0559/055 | Chinyere Emmanuel      |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         56 | AAIS/0559/056 | Marvellous Maduabuchi  |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         57 | AAIS/0559/057 | Treasure Chidi         |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         58 | AAIS/0559/058 | Believe Kanu           |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         59 | AAIS/0559/059 | Amazing Kanu           |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         60 | AAIS/0559/060 | Chukwuemeka Chibuzo    |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         61 | AAIS/0559/061 | Light Chinyeaka        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         62 | AAIS/0559/062 | Chisom Chinedu         |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         63 | AAIS/0559/063 | Esther Asoegwu         |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         64 | AAIS/0559/064 | Somotochukwu Eze       |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         65 | AAIS/0559/065 | Victoria Uwadiegwu     |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         66 | AAIS/0559/066 | Prevailer Ekene        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         67 | AAIS/0559/067 | Beauty Ime             |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         68 | AAIS/0559/068 | Chidiebube Chibuzo     |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         70 | AAIS/0559/070 | Oluchukwu Okoye        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         71 | AAIS/0559/071 | Olivia Gideon          |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         72 | AAIS/0559/072 | Prince Charles Asoegwu |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         73 | AAIS/0559/073 | Delight Ezereibe       |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         74 | AAIS/0559/074 | Chimaobi Uwadiegwu     |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         76 | AAIS/0559/076 | Ime Goodluck           |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         78 | AAIS/0559/078 | Precious Ekene         |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         81 | AAIS/0559/081 | Destiny Chiemela       |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         82 | AAIS/0559/082 | Chijindu Williams      |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         83 | AAIS/0559/083 | Chimamanda Chibuzo     |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         84 | AAIS/0559/084 | Glory Uwadiegwu        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         85 | AAIS/0559/085 | Success Israel         |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         86 | AAIS/0559/086 | Sochima Samuel         |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         87 | AAIS/0559/087 | Joshua Iheanyi         |          2 | 2024/2025    | First      | NULL     |         1 |            1 |
|         88 | AAIS/0559/088 | Stanley Alexander      |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         89 | AAIS/0559/089 | Excel Joseph           |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         90 | AAIS/0559/090 | Royalty Anosike        |          2 | 2024/2025    | First      | NULL     |         1 |            1 |
|         91 | AAIS/0559/091 | Wisdom Augustine       |          2 | 2024/2025    | First      | NULL     |         1 |            1 |
|         92 | AAIS/0559/092 | Miracle John           |          2 | 2024/2025    | First      | NULL     |         1 |            1 |
|         93 | AAIS/0559/093 | Onyinyechi Augustine   |          2 | 2024/2025    | First      | NULL     |         1 |            1 |
|         94 | AAIS/0559/094 | Emmanuel Joseph        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         95 | AAIS/0559/095 | Chijindu Sunday        |          1 | 2023/2024    | Third      | NULL     |         1 |            1 |
|         96 | AAIS/0559/096 | Great Augustine        |          2 | 2024/2025    | First      | NULL     |         1 |            1 |
|         99 | AAIS/0559/099 | Munachimso David       |          2 | 2024/2025    | First      | NULL     |         1 |            1 |
|        104 | AAIS/0559/104 | Mkpuruoma Chigozie     |          2 | 2024/2025    | First      | NULL     |         1 |            1 |
|        107 | AAIS/0559/107 | Joyful Praise          |          2 | 2024/2025    | First      | NULL     |         1 |            1 |
|        108 | AAIS/0559/108 | David Onyenwe          |          2 | 2024/2025    | First      | NULL     |         1 |            1 |
|        118 | AAIS/0559/118 | Wealth Christopher     |          2 | 2024/2025    | Second     | NULL     |         1 |            1 |
|        119 | AAIS/0559/119 | Covenant Izuchukwu     |          2 | 2024/2025    | Second     | NULL     |         1 |            1 |
|        120 | AAIS/0559/120 | Miracle Emmanuel       |          2 | 2024/2025    | Second     | NULL     |         1 |            1 |
|        121 | AAIS/0559/121 | David Udo              |          2 | 2024/2025    | Second     | NULL     |         1 |            1 |
|        122 | AAIS/0559/122 | Christable Nwokeocha   |          2 | 2024/2025    | Second     | NULL     |         1 |            1 |
|        123 | AAIS/0559/123 | Wisdom Christopher     |          2 | 2024/2025    | Second     | NULL     |         1 |            1 |
|        124 | AAIS/0559/124 | Miracle Udo            |          2 | 2024/2025    | Second     | NULL     |         1 |            1 |
|        126 | AAIS/0559/126 | Samuel Udo             |          2 | 2024/2025    | Second     | NULL     |         1 |            1 |
|        127 | AAIS/0559/127 | Goodnews Ukam          |          2 | 2024/2025    | Second     | NULL     |         1 |            1 |
|        128 | AAIS/0559/128 | Awesome Chigbu         |          2 | 2024/2025    | Second     | NULL     |         1 |            1 |
+------------+---------------+------------------------+------------+--------------+------------+----------+-----------+--------------+
98 rows in set (0.00 sec)




=SUMPRODUCT(IF(COUNTIFS(TextbookDetails[Textbook Title], TextbookDetails[Textbook Title], TextbookDetails[Purchase ID], "<="&TextbookDetails[Purchase ID])<=VLOOKUP(TextbookDetails[Textbook Title], BookInventory, 3, FALSE),0,TextbookDetails[Line Purchase Cost]))


=SUMPRODUCT(IF(SUMIFS(TextbookDetails[Quantity],TextbookDetails[Textbook Title],TextbookDetails[Textbook Title],TextbookDetails[Purchase ID],"<="&TextbookDetails[Purchase ID])<=IFERROR(VLOOKUP(TextbookDetails[Textbook Title],BookInventory,3,FALSE),0),0,TextbookDetails[Line Purchase Cost]))

