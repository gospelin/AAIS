import re
import os

def convert_sql_to_php_array(input_file, output_file):
    """
    Convert SQL INSERT statements for the 'result' table to PHP array format.
    
    Args:
        input_file (str): Path to the input file containing SQL INSERT statements
        output_file (str): Path to the output file for PHP array
    """
    # Regular expression to match the SQL INSERT statement for the result table
    pattern = r"INSERT INTO `result` \(`id`, `student_id`, `subject_id`, `term`, `class_assessment`, `summative_test`, `exam`, `total`, `grade`, `remark`, `created_at`, `class_id`, `session_id`\) VALUES \((?P<id>\d+), (?P<student_id>\d+), (?P<subject_id>\d+), '(?P<term>[^']+)', (?P<class_assessment>\d+), (?P<summative_test>\d+), (?P<exam>\d+), (?P<total>\d+), '(?P<grade>[^']+)', '(?P<remark>[^']+)', '(?P<created_at>[^']+)', (?P<class_id>\d+), (?P<session_id>\d+)\);"

    results = []
    
    try:
        # Read the SQL file
        with open(input_file, 'r') as f:
            sql_content = f.read()
        
        # Find all matches in the SQL content
        matches = re.finditer(pattern, sql_content)
        
        # Convert each match to PHP array format
        for match in matches:
            result = {
                'id': int(match.group('id')),
                'student_id': int(match.group('student_id')),
                'subject_id': int(match.group('subject_id')),
                'term': match.group('term'),
                'class_assessment': int(match.group('class_assessment')),
                'summative_test': int(match.group('summative_test')),
                'exam': int(match.group('exam')),
                'total': int(match.group('total')),
                'grade': match.group('grade'),
                'remark': match.group('remark'),
                'created_at': match.group('created_at'),
                'class_id': int(match.group('class_id')),
                'session_id': int(match.group('session_id')),
            }
            results.append(result)
        
        # Write to output file in PHP array format
        with open(output_file, 'w') as f:
            f.write("$results = [\n")
            for result in results:
                f.write(f"    ['id' => {result['id']}, 'student_id' => {result['student_id']}, 'subject_id' => {result['subject_id']}, 'term' => '{result['term']}', 'class_assessment' => {result['class_assessment']}, 'summative_test' => {result['summative_test']}, 'exam' => {result['exam']}, 'total' => {result['total']}, 'grade' => '{result['grade']}', 'remark' => '{result['remark']}', 'created_at' => '{result['created_at']}', 'class_id' => {result['class_id']}, 'session_id' => {result['session_id']}],\n")
            f.write("];\n")
        
        print(f"Successfully converted {len(results)} records to PHP array format. Output written to {output_file}")
    
    except FileNotFoundError:
        print(f"Error: Input file '{input_file}' not found.")
    except Exception as e:
        print(f"Error during conversion: {str(e)}")

if __name__ == "__main__":
    input_file = "results.sql"  # Replace with your SQL file path
    output_file = "results_array.php"  # Replace with desired output file path
    convert_sql_to_php_array(input_file, output_file)