<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

$dsn = "mysql:host=localhost;dbname=systems;charset=utf8mb4";
$uname = "root";
$pass = "";
$validate = null;
$validate2 = null;
$validate3 = null;
$validate4 = null;
$validate5 = null;
$validate6 = null;
$validate7 = null;
$validate8 = null;
$validate9 = null;
$mysql_reserved = [
    'ADD',
    'ALL',
    'ALTER',
    'ANALYZE',
    'AND',
    'AS',
    'ASC',
    'ASENSITIVE',
    'BEFORE',
    'BETWEEN',
    'BIGINT',
    'BINARY',
    'BLOB',
    'BOTH',
    'BY',
    'CALL',
    'CASCADE',
    'CASE',
    'CHANGE',
    'CHAR',
    'CHARACTER',
    'CHECK',
    'COLLATE',
    'COLUMN',
    'CONDITION',
    'CONSTRAINT',
    'CONTINUE',
    'CONVERT',
    'CREATE',
    'CROSS',
    'CURRENT_DATE',
    'CURRENT_TIME',
    'CURRENT_TIMESTAMP',
    'CURRENT_USER',
    'CURSOR',
    'DATABASE',
    'DATABASES',
    'DAY_HOUR',
    'DAY_MICROSECOND',
    'DAY_MINUTE',
    'DAY_SECOND',
    'DEC',
    'DECIMAL',
    'DECLARE',
    'DEFAULT',
    'DELAYED',
    'DELETE',
    'DESC',
    'DESCRIBE',
    'DETERMINISTIC',
    'DISTINCT',
    'DISTINCTROW',
    'DIV',
    'DOUBLE',
    'DROP',
    'DUAL',
    'EACH',
    'ELSE',
    'ELSEIF',
    'ENCLOSED',
    'ESCAPED',
    'EXISTS',
    'EXIT',
    'EXPLAIN',
    'FALSE',
    'FETCH',
    'FLOAT',
    'FLOAT4',
    'FLOAT8',
    'FOR',
    'FOREIGN',
    'FROM',
    'FULLTEXT',
    'GENERAL',
    'GRANT',
    'GROUP',
    'HAVING',
    'HIGH_PRIORITY',
    'HOUR_MICROSECOND',
    'HOUR_MINUTE',
    'HOUR_SECOND',
    'IF',
    'IGNORE',
    'IN',
    'INDEX',
    'INFILE',
    'INNER',
    'INOUT',
    'INSENSITIVE',
    'INSERT',
    'INT',
    'INT1',
    'INT2',
    'INT3',
    'INT4',
    'INT8',
    'INTEGER',
    'INTERVAL',
    'INTO',
    'IS',
    'ITERATE',
    'JOIN',
    'KEY',
    'KEYS',
    'KILL',
    'LABEL',
    'LEADING',
    'LEAVE',
    'LEFT',
    'LIKE',
    'LIMIT',
    'LINEAR',
    'LINES',
    'LOAD',
    'LOCALTIME',
    'LOCALTIMESTAMP',
    'LOCK',
    'LONG',
    'LONGBLOB',
    'LONGTEXT',
    'LOOP',
    'LOW_PRIORITY',
    'MASTER_SSL_VERIFY_SERVER_CERT',
    'MATCH',
    'MAXVALUE',
    'MEDIUMBLOB',
    'MEDIUMINT',
    'MEDIUMTEXT',
    'MIDDLEINT',
    'MINUTE_MICROSECOND',
    'MINUTE_SECOND',
    'MOD',
    'MODIFIES',
    'NATURAL',
    'NOT',
    'NO_WRITE_TO_BINLOG',
    'NULL',
    'NUMERIC',
    'ON',
    'OPTIMIZE',
    'OPTION',
    'OPTIONALLY',
    'OR',
    'ORDER',
    'OUT',
    'OUTER',
    'OUTFILE',
    'PRECISION',
    'PRIMARY',
    'PROCEDURE',
    'PURGE',
    'RANGE',
    'READ',
    'READS',
    'REAL',
    'REFERENCES',
    'REGEXP',
    'RELEASE',
    'RENAME',
    'REPEAT',
    'REPLACE',
    'REQUIRE',
    'RESIGNAL',
    'RESTRICT',
    'RETURN',
    'REVOKE',
    'RIGHT',
    'RLIKE',
    'SCHEMA',
    'SCHEMAS',
    'SECOND_MICROSECOND',
    'SELECT',
    'SENSITIVE',
    'SEPARATOR',
    'SET',
    'SHOW',
    'SIGNAL',
    'SMALLINT',
    'SPATIAL',
    'SPECIFIC',
    'SQL',
    'SQLEXCEPTION',
    'SQLSTATE',
    'SQLWARNING',
    'SQL_BIG_RESULT',
    'SQL_CALC_FOUND_ROWS',
    'SQL_SMALL_RESULT',
    'SSL',
    'STARTING',
    'STRAIGHT_JOIN',
    'TABLE',
    'TERMINATED',
    'THEN',
    'TINYBLOB',
    'TINYINT',
    'TINYTEXT',
    'TO',
    'TRAILING',
    'TRIGGER',
    'TRUE',
    'UNDO',
    'UNION',
    'UNIQUE',
    'UNLOCK',
    'UNSIGNED',
    'UPDATE',
    'USAGE',
    'USE',
    'USING',
    'UTC_DATE',
    'UTC_TIME',
    'UTC_TIMESTAMP',
    'VALUES',
    'VARBINARY',
    'VARCHAR',
    'VARCHARACTER',
    'VARYING',
    'WHEN',
    'WHERE',
    'WHILE',
    'WITH',
    'WRITE',
    'XOR',
    'YEAR_MONTH',
    'ZEROFILL'
];
$options = [
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_general_ci"
];

try {
    $db = new PDO($dsn, $uname, $pass, $options);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "use systems";
    $db->query($sql);
    session_start();
    $table = substr_replace($_COOKIE["table"], "", 0, 7);
    $sql = "SELECT * FROM $table;";
    $stmt = $db->query($sql);
    // Fetch all results as associative array
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["add"])) {
            $name = $_POST["studentName"];
            $sql = "SELECT student_name from $table";
            $names = $db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
            if (strlen($name) > 0 && !in_array($name, $mysql_reserved) && !in_array($name, $names)) {
                $db->query($sql);
                $stmt = $db->prepare("INSERT INTO $table (student_name) VALUES (:name)");
                $stmt->execute(['name' => $name]);
                $sql = "SELECT student_id FROM $table WHERE student_name = '$name';";
                $studentId = $db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
                $sql = "CREATE TABLE {$studentId[0]}_$table (
    `lesson_num` INT(3),
    `student_results` DECIMAL(3,1) DEFAULT NULL,
    `max_quiz_result` DECIMAL(3,1) DEFAULT NULL,
    `student_exams` DECIMAL(4,1) DEFAULT NULL,
    `max_exam_result` DECIMAL(4,1) DEFAULT NULL,
    `student_attends` BOOLEAN,
    `student_homeworks` BOOLEAN,
    `attend_date` DATE DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
                $db->query($sql);
                header("Location: system.php");
                exit;
            } else {
                $validate = false;
            }
        }
        if (isset($_POST["remove"])) {
            $sql = "SELECT student_id FROM $table";
            $IDs = $db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
            $id = (int)$_POST["studentId"];
            if (in_array($id, $IDs)) {
                $sql = "DELETE FROM $table WHERE student_id = $id;";
                $db->query($sql);
                $sql = "DROP TABLE {$id}_$table;";
                $db->query($sql);
                header("Location: system.php");
                exit;
            } else {
                $validate2 = false;
            }
        }
        $sql = "SELECT student_id FROM $table";
        $IDs = $db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
        foreach ($IDs as $ID) {
            if (isset($_POST["select-$ID-$table"])) {
                setcookie("student", "select-" . $ID . "-" . $table, 0, "/");
                header("Location: student.php");
                exit;
            }
        }
        if (isset($_POST["add_status"])) {
            if (!in_array($_POST["student_id"], $IDs)) {
                $validate3 = false;
            }
            if ((int)$_POST["student_attend"] !== 1 && (int)$_POST["student_attend"] !== 0) {
                $validate4 = false;
            }
            if ((int)$_POST["homework"] !== 1 && (int)$_POST["homework"] !== 0) {
                $validate5 = false;
            }
            if ($_POST["exam"]) {
                if (filter_var($_POST["exam"], FILTER_VALIDATE_FLOAT) === false || $_POST["exam"] >= 1000) {
                    $validate7 = false;
                }
            }
            if ($_POST["max_exam"]) {
                if (filter_var($_POST["max_exam"], FILTER_VALIDATE_FLOAT) === false || $_POST["max_exam"] >= 1000) {
                    $validate9 = false;
                }
            }
            if (filter_var($_POST["quiz_result"], FILTER_VALIDATE_FLOAT) === false || $_POST["quiz_result"] >= 100) {
                $validate6 = false;
            }
            if (filter_var($_POST["max_quiz"], FILTER_VALIDATE_FLOAT) === false || $_POST["max_quiz"] >= 100) {
                $validate8 = false;
            }
            if ($validate3 === null && $validate4 === null && $validate5 === null && $validate6 === null && $validate7 === null && $validate8 === null && $validate9 === null) {
                $exam_result = null;
                if ($_POST["exam"]) {
                    $exam_result = $_POST["exam"];
                }
                if ($_POST["max_exam"]) {
                    $max_exam = $_POST["max_exam"];
                }
                if ((int)$_POST["student_attend"] === 1) {
                    $attend = true;
                } else {
                    $attend = false;
                }
                if ((int)$_POST["homework"] === 1) {
                    $homework = true;
                } else {
                    $homework = false;
                }
                $quiz_result = number_format((float) $_POST["quiz_result"], 1);
                $max_quiz = number_format((float) $_POST["max_quiz"], 1);
                $sql = "SELECT attend_date
                FROM {$_POST['student_id']}_{$table}
                ORDER BY attend_date DESC
                LIMIT 1";
                $pre_month = date("n", strtotime($db->query($sql)->fetchColumn()));
                $current_month = date("n");
                $date = date("Y-m-d");
                $sql = "SELECT MAX(lesson_num) FROM {$_POST['student_id']}_{$table}";
                $result = $db->query($sql)->fetchColumn();
                $day_of_lessons = ($result !== null ? (int)$result + 1 : 1);
                if ($pre_month !== $current_month) {
                    $day_of_lessons = 1;
                }
                $sql = "INSERT INTO {$_POST['student_id']}_{$table} (
                student_results,max_quiz_result, student_attends, student_homeworks, attend_date, lesson_num, student_exams, max_exam_result
                ) VALUES (:quiz, :max_quiz, :attend, :homework, :date, :num, :exam , :max_exam)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'quiz' => $quiz_result,
                    'max_quiz' => $max_quiz,
                    'attend' => $attend,        // 0 or 1
                    'homework' => $homework,    // 0 or 1
                    'date' => $date,
                    'num' => $day_of_lessons,
                    'exam' => $exam_result,
                    'max_exam' => $max_exam
                ]);


                header("Location: system.php");
                exit;
            }
        }
        // Helper: Clean text for Word (removes invalid XML chars)
        // Inside the try block, where your current report logic is located
        // ...
        // ... (Your loop to process all IDs and build the Word Document)

        // Define cleanWordText function earlier in your script if you haven't already
        function cleanWordText($text)
        {
            $text = (string)$text;
            $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $text);
            return $text;
        }

        if (isset($_POST["report"])) {
            // CRITICAL: Start buffering early to catch any potential errors
            ob_start();

            // --- 1. Setup Variables and Spreadsheet ---
            $month = $_POST["month"];
            $year = date("Y");
            $monthName = DateTime::createFromFormat('!m', $month)->format('F');

            // Define the full path for the file save
            $saveDir = "D:/reports/";
            $fileName = "All_Students_{$monthName}_report.xlsx";
            $savePath = $saveDir . $fileName;

            // Use PhpSpreadsheet for Excel
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Summary');

            $rowNum = 1; // Starting row for data insertion

            // NOTE: $IDs and $table are assumed to be defined earlier in your script.

            // --- 2. Loop through all students to aggregate content ---
            foreach ($IDs as $ID) {
                // Fetch lessons for the month
                $sql = "SELECT * FROM {$ID}_{$table} WHERE MONTH(attend_date) = :month AND YEAR(attend_date) = :year";
                $stmt = $db->prepare($sql);
                $stmt->execute(['month' => $month, 'year' => $year]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // If no data for this student, skip them.
                if (empty($results)) {
                    continue;
                }

                // Fetch student name
                $sql = "SELECT student_name FROM `$table` WHERE student_id = :id";
                $stmt = $db->prepare($sql);
                $stmt->execute(['id' => $ID]);
                $name = $stmt->fetchColumn() ?: 'Unknown';
                $nameSafe = cleanWordText($name);

                // --- Insert Content into Spreadsheet (Display Data) ---
                $sheet->setCellValue('A' . $rowNum, "P/I: Report for $monthName, $year");
                $sheet->getStyle('A' . $rowNum)->getFont()->setBold(true)->setSize(14);
                $rowNum++;
                $sheet->setCellValue('A' . $rowNum, "Student: $nameSafe (ID: $ID)");
                $rowNum += 2;

                // Headers
                $sheet->setCellValue('A' . $rowNum, "Lesson #")->getStyle('A' . $rowNum)->getFont()->setBold(true);
                $sheet->setCellValue('B' . $rowNum, "Quiz Score")->getStyle('B' . $rowNum)->getFont()->setBold(true);
                $sheet->setCellValue('C' . $rowNum, "Attendance")->getStyle('C' . $rowNum)->getFont()->setBold(true);
                $sheet->setCellValue('D' . $rowNum, "Homework")->getStyle('D' . $rowNum)->getFont()->setBold(true);
                $rowNum++;

                // Variables for calculating totals
                $exams = [];
                $totalScore = 0.0;
                $totalItems = 0;
                $maxPossibleScore = 0.0; // Tracks the actual maximum points (10 per quiz, 100 per exam)

                // Lessons Data & Calculation
                foreach ($results as $lesson) {
                    $quizResult = (float)($lesson['student_results'] ?? 0.0);
                    $attend = (int)($lesson['student_attends'] ?? 0);
                    $homework = (int)($lesson['student_homeworks'] ?? 0);

                    $attendMark = $attend === 1 ? "Present" : "Absent (-10 points)";
                    $homeworkMark = $homework === 1 ? "Done" : "Missed (-10 points)";

                    $sheet->setCellValue('A' . $rowNum, $lesson['lesson_num'] ?? '-');
                    $sheet->setCellValue('B' . $rowNum, $quizResult);
                    $sheet->setCellValue('C' . $rowNum, $attendMark);
                    $sheet->setCellValue('D' . $rowNum, $homeworkMark);
                    $rowNum++;

                    // Calculation Logic (Quizzes out of 10)
                    if (is_numeric($lesson['student_results'])) {
                        // 1. Add raw quiz score to total.
                        $totalScore += $quizResult;

                        // 2. Max Possible Score increases by 10 (the quiz maximum).
                        $maxPossibleScore += 10;

                        $totalItems++;

                        // 3. Apply Penalties (deducted from the total score)
                        if ($attend !== 1) {
                            $totalScore -= 10;
                        }
                        if ($homework !== 1) {
                            $totalScore -= 10;
                        }
                    }

                    // Check for exams
                    if (!is_null($lesson['student_exams'])) {
                        $examName = "Lesson " . ($lesson['lesson_num'] ?? '-');
                        $exams[] = [
                            'exam_name' => $examName,
                            'score' => (float)($lesson['student_exams'] ?? 0.0)
                        ];
                    }
                }
                $rowNum++;

                // Exams Data & Calculation
                if (!empty($exams)) {
                    $sheet->setCellValue('A' . $rowNum, "Exams Scores:");
                    $rowNum++;

                    $sheet->setCellValue('A' . $rowNum, "Exam")->getStyle('A' . $rowNum)->getFont()->setBold(true);
                    $sheet->setCellValue('B' . $rowNum, "Score")->getStyle('B' . $rowNum)->getFont()->setBold(true);
                    $rowNum++;

                    foreach ($exams as $exam) {
                        $sheet->setCellValue('A' . $rowNum, $exam['exam_name']);
                        $sheet->setCellValue('B' . $rowNum, $exam['score']);

                        $totalScore += (float)$exam['score'];
                        $totalItems++;
                        $maxPossibleScore += 100; // Exams are assumed out of 100
                        $rowNum++;
                    }
                    $rowNum++;
                }

                // --- Final Summary ---
                $percent = $maxPossibleScore > 0 ? ($totalScore / $maxPossibleScore) * 100 : 0;

                $sheet->setCellValue('A' . $rowNum, "Total Score/Items:")->getStyle('A' . $rowNum)->getFont()->setBold(true);
                $sheet->setCellValue('B' . $rowNum, round($totalScore, 1) . " / " . $maxPossibleScore . " points");
                $rowNum++;

                $sheet->setCellValue('A' . $rowNum, "Overall Percent:")->getStyle('A' . $rowNum)->getFont()->setBold(true)->setSize(12);
                $sheet->setCellValue('B' . $rowNum, round($percent, 2) . "%")->getStyle('B' . $rowNum)->getFont()->setBold(true)->setSize(12);

                $rowNum += 3; // Space before the next student report
            }
            // --- End of Loop ---

            // Check if any data was added
            if ($rowNum <= 2) {
                ob_end_clean();
                header("Location: system.php?report_error=no_data_excel");
                exit;
            }

            // Auto-size columns
            foreach (range('A', 'D') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // --- 3. Save the file locally to D:/reports/ ---
            try {
                // Create the directory if it doesn't exist
                if (!is_dir($saveDir)) {
                    mkdir($saveDir, 0777, true);
                }

                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($savePath);

                // Clear output buffer and redirect the user
                ob_end_clean();
                header("Location: system.php?report_success=saved_to_disk&path=" . urlencode($savePath));
                exit;
            } catch (\Exception $e) {
                $errorMsg = "File Save Error: " . $e->getMessage();
                error_log($errorMsg);

                ob_end_clean();
                header("Location: system.php?report_error=save_failed&detail=" . urlencode($errorMsg));
                exit;
            }
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="main.css">
    <title>System</title>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container mt-5">
        <h1><?php echo htmlspecialchars($table); ?></h1>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="mb-5">Student Management</h1>
                <div class="row g-4 mb-5">
                    <!-- Add Student Form -->
                    <div class="col-lg-6">
                        <div class="card shadow-lg border-0">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">➕ Add Student</h5>
                            </div>
                            <div class="card-body">
                                <form method="post">
                                    <div class="mb-3">
                                        <label for="adduser" class="form-label">Student Name</label>
                                        <input type="text" class="form-control" placeholder="Enter student name" name="studentName" id="adduser" required>
                                        <small class="form-text text-muted">Enter a unique student name</small>
                                    </div>
                                    <?php
                                    if ($validate === false) {
                                        echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                                            <strong>Error!</strong> This name is already taken or invalid.
                                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                        </div>";
                                    }
                                    ?>
                                    <button type="submit" class="btn btn-success w-100" name="add">Add Student</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Remove Student Form -->
                    <div class="col-lg-6">
                        <div class="card shadow-lg border-0">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">🗑️ Remove Student</h5>
                            </div>
                            <div class="card-body">
                                <form method="post">
                                    <div class="mb-3">
                                        <label for="removeuser" class="form-label">Student ID</label>
                                        <input type="number" class="form-control" placeholder="Enter student ID" name="studentId" id="removeuser" required>
                                        <small class="form-text text-muted">Enter the student ID to remove</small>
                                    </div>
                                    <?php
                                    if ($validate2 === false) {
                                        echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                                            <strong>Error!</strong> Student ID not found in the database.
                                            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                        </div>";
                                    }
                                    ?>
                                    <button type="submit" class="btn btn-danger w-100" name="remove">Remove Student</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card shadow-lg border-0 create-system">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">➕ Add Lesson / Status</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" class="row g-3">
                                    <div class="col-md-4">
                                        <label for="student_id" class="form-label">Student ID</label>
                                        <input type="text" name="student_id" id="student_id" class="form-control" placeholder="Enter student id" required>
                                        <?php
                                        if ($validate3 === false) {
                                            echo "<div class='form-text text-danger'>This ID isn't found.</div>";
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="student_attend" class="form-label">Attend (0 or 1)</label>
                                        <input type="text" id="student_attend" name="student_attend" class="form-control" min="0" max="1" placeholder="0 or 1" required>
                                        <?php
                                        if ($validate4 === false) {
                                            echo "<div class='form-text text-danger'>Attend must be 0 or 1.</div>";
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="homework" class="form-label">Homework (0 or 1)</label>
                                        <input type="text" id="homework" name="homework" class="form-control" min="0" max="1" placeholder="0 or 1" required>
                                        <?php
                                        if ($validate5 === false) {
                                            echo "<div class='form-text text-danger'>Homework must be 0 or 1.</div>";
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="quiz" class="form-label">Quiz Result (0 - 99)</label>
                                        <input type="text" step="0.1" id="quiz" name="quiz_result" class="form-control" placeholder="Enter quiz result" required>
                                        <?php
                                        if ($validate6 === false) {
                                            echo "<div class='form-text text-danger'>Quiz result must be a number between 0 and 99.</div>";
                                        }
                                        ?>
                                        <label for="max_quiz" class="form-label">Max Quiz Result (0 - 99)</label>
                                        <input type="text" step="0.1" id="max_quiz" name="max_quiz" class="form-control" placeholder="Enter max quiz result" required>
                                        <?php
                                        if ($validate8 === false) {
                                            echo "<div class='form-text text-danger'>Quiz result must be a number between 0 and 99.</div>";
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="exam" class="form-label">Exam Result (optional)</label>
                                        <input type="text" step="0.1" id="exam" name="exam" class="form-control exam-input" placeholder="Enter exam result if exists">
                                        <?php
                                        if ($validate7 === false) {
                                            echo "<div class='form-text text-danger'>Exam result must be a number between 0 and 999.</div>";
                                        }
                                        ?>
                                        <label for="max_exam" class="form-label">Max Exam Result</label>
                                        <input type="text" step="0.1" id="max_exam" name="max_exam" class="form-control exam-input" placeholder="Enter max exam result if exists">
                                        <?php
                                        if ($validate9 === false) {
                                            echo "<div class='form-text text-danger'>Exam result must be a number between 0 and 999.</div>";
                                        }
                                        ?>
                                    </div>
                                    <div class="col-12 d-flex align-items-end justify-content-end">
                                        <button type="submit" name="add_status" class="btn btn-success">Add Lesson</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card shadow-lg border-0 mt-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">📊 Monthly Report</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label for="month" class="form-label">Select Month</label>
                                        <select id="month" name="month" class="form-select" required>
                                            <option value="1">January</option>
                                            <option value="2">February</option>
                                            <option value="3">March</option>
                                            <option value="4">April</option>
                                            <option value="5">May</option>
                                            <option value="6">June</option>
                                            <option value="7">July</option>
                                            <option value="8">August</option>
                                            <option value="9">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-md-end mt-3 mt-md-0">
                                        <button type="submit" name="report" class="btn btn-warning w-100 w-md-auto">
                                            <i class="bi bi-file-earmark-spreadsheet"></i> Generate Report
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students List -->
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">📋 All Students</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_POST["studentId"])) {
                            echo "<div class='alert alert-info alert-dismissible fade show mb-3' role='alert'>
                                <strong>Searched ID:</strong> " . htmlspecialchars($_POST["studentId"]) . "
                                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                            </div>";
                        }
                        ?>
                        <?php
                        if (count($result) > 0) {
                            echo "<div class='table-responsive'>
                                <table class='table table-striped table-hover'>
                                    <thead class='table-light'>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>";
                            foreach ($result as $row) {
                                echo "<tr>
                                    <td><span class='badge bg-primary'>" . htmlspecialchars($row['student_id']) . "</span></td>
                                    <td>" . htmlspecialchars($row['student_name']) . "</td>
                                    <td><form method='post' class='d-grid gap-2'>
                                        <button type='submit' name='select-{$row['student_id']}-$table' class='btn btn-primary btn-sm'>Select Student</button></form></td>
                                </tr>";
                            }
                            echo "  </tbody>
                                </table>
                            </div>";
                        } else {
                            echo "<div class='alert alert-info' role='alert'>
                                <strong>No students found.</strong> Add your first student above.
                            </div>";
                        }
                        ?>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <a href="new.php" class="btn btn-outline-primary">← Back to Systems</a>
                </div>
            </div>
        </div>
    </div>
    <script src="main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>