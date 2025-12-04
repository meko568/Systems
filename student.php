<?php
date_default_timezone_set("Africa/Cairo");
$dsn = "mysql:host=localhost;dbname=systems;charset=utf8mb4";
$uname = "root";
$pass = "";
$options = [
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_general_ci"
];

try {
    $db = new PDO($dsn, $uname, $pass, $options);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    session_start();
    $student_id = explode("-", $_COOKIE["student"])[1];
    $table_name = explode("-", $_COOKIE["student"])[2];
    if (!isset($_COOKIE['student'])) {
        throw new Exception('Student cookie not set');
    }
    $parts = explode("-", $_COOKIE["student"]);
    if (count($parts) < 3) {
        throw new Exception('Invalid student cookie format');
    }
    $student_id = $parts[1];
    $table_name = $parts[2];

    $tableIdent = $student_id . '_' . $table_name;
    $sql = "SELECT student_name FROM $table_name WHERE student_id = $student_id;";
    $name = $db->query($sql)->fetchAll(PDO::FETCH_COLUMN)[0];
    // Fetch all rows as associative arrays (using your preferred table interpolation)
    $sql = "SELECT * FROM `{$student_id}_{$table_name}`";
    $stmt = $db->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // detect exam column name (support both student_exams or student_exam)
    $examCol = null;
    if (!empty($rows)) {
        $first = $rows[0];
        if (array_key_exists('student_exams', $first)) $examCol = 'student_exams';
        elseif (array_key_exists('student_exam', $first)) $examCol = 'student_exam';
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
    <title>Student Details - System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="main.css">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container mt-4 student-page">
        <h2 class="mb-3">Student: <?php echo htmlspecialchars($name); ?> | <?php echo htmlspecialchars($table_name); ?> | Student ID: <?php echo htmlspecialchars($student_id); ?> </h2>

        <?php if (empty($rows)) : ?>
            <div class="alert alert-info">No lesson rows found for this student.</div>
        <?php else : ?>
            <div class="card mb-4 student-table-card" style="transition: .3s;">
                <div class="card-header bg-secondary text-white">
                    <strong>All Lessons</strong>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <?php
                                    // Build headers but skip the exam column
                                    // Show lesson_num first if it exists
                                    if (isset($rows[0]['lesson_num'])) {
                                        echo '<th>Lesson #</th>';
                                    }
                                    $headers = array_keys($rows[0]);
                                    foreach ($headers as $h) {
                                        if ($h === $examCol || $h === 'lesson_num') continue;
                                        if ($h === 'student_results') {
                                            echo '<th>Quiz (value / %)</th>';
                                        } else {
                                            // Convert column names to friendly format: replace _ with space, capitalize
                                            $friendly = str_replace('_', ' ', $h);
                                            $friendly = ucwords($friendly);
                                            echo '<th>' . htmlspecialchars($friendly) . '</th>';
                                        }
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // configuration: max scores
                                $quizMax = 10;
                                $examMax = 100;

                                // First pass: compute averages
                                $quizSum = 0;
                                $quizCount = 0;
                                $examSum = 0;
                                $examCount = 0;
                                foreach ($rows as $r) {
                                    if (isset($r['student_results']) && $r['student_results'] !== '') {
                                        $quizSum += (float)$r['student_results'];
                                        $quizCount++;
                                    }
                                    if ($examCol !== null && isset($r[$examCol]) && $r[$examCol] !== '') {
                                        $examSum += (float)$r[$examCol];
                                        $examCount++;
                                    }
                                }

                                // Compute full mark (max possible) and obtained mark
                                $fullMark = ($quizMax * $quizCount) + ($examMax * $examCount);
                                $markObtained = $quizSum + $examSum;
                                $quizAvgPercent = ($quizCount > 0 && $quizMax > 0) ? round(($quizSum / $quizCount / $quizMax) * 100, 1) : null;
                                $examAvgPercent = ($examCount > 0 && $examMax > 0) ? round(($examSum / $examCount / $examMax) * 100, 1) : null;
                                if ($quizAvgPercent !== null && $examAvgPercent !== null) {
                                    $finalPercent = round((($markObtained) / $fullMark) * 100, 1);
                                } elseif ($quizAvgPercent !== null) {
                                    $finalPercent = $quizAvgPercent;
                                } else {
                                    $finalPercent = null;
                                }


                                // Compute penalties: count absences and missing homeworks
                                $absenceCount = 0;
                                $homeworkFailCount = 0;
                                foreach ($rows as $r) {
                                    $attend = isset($r['student_attends']) || isset($r['student_attend']) ?
                                        (isset($r['student_attends']) ? $r['student_attends'] : $r['student_attend']) : null;
                                    $homework = isset($r['student_homeworks']) || isset($r['student_homework']) ?
                                        (isset($r['student_homeworks']) ? $r['student_homeworks'] : $r['student_homework']) : null;
                                    if ($attend !== null && (int)$attend !== 1) $absenceCount++;
                                    if ($homework !== null && (int)$homework !== 1) $homeworkFailCount++;
                                }
                                $totalPenalty = ($absenceCount + $homeworkFailCount) * 10;
                                $finalPercentWithPenalty = round((($markObtained - $totalPenalty) / $fullMark) * 100, 1);

                                // Second pass: render rows
                                foreach ($rows as $r) : ?>
                                    <tr>
                                        <?php
                                        // Show lesson_num first if it exists
                                        if (isset($r['lesson_num'])) {
                                            echo '<td>' . htmlspecialchars((string)$r['lesson_num']) . '</td>';
                                        }
                                        foreach ($r as $colName => $val) :
                                            // skip the exam column and lesson_num (already shown first)
                                            if ($colName === $examCol || $colName === 'lesson_num') continue;
                                            // Custom rendering for attend and homework
                                            if ($colName === 'student_attends' || $colName === 'student_attend') {
                                                $out = ((int)$val === 1) ? 'حاضر' : 'غائب';
                                            } elseif ($colName === 'student_homeworks' || $colName === 'student_homework') {
                                                $out = ((int)$val === 1) ? '✓' : '✗';
                                            } elseif ($colName === 'student_results') {
                                                $quizVal = (float)$val;
                                                $quizPercent = ($quizMax > 0) ? round(($quizVal / $quizMax) * 100, 1) : null;
                                                $out = (string)$val . ' / ' . $quizPercent . '%';
                                            } else {
                                                $out = (string)$val;
                                            }
                                        ?>
                                            <td><?php echo htmlspecialchars($out); ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Overall Summary Card -->
            <div class="card mb-4 summary-card">
                <div class="card-header bg-success text-white">
                    <strong>Overall Performance Summary</strong>
                </div>
                <div class="card-body">
                    <!-- Row 1: Total Marks -->
                    <div class="row text-center mb-3">
                        <div class="col-md-6">
                            <h5>Full Mark</h5>
                            <p class="badge bg-warning rounded-pill" style="font-size: 1.2rem;"><?php echo htmlspecialchars((string)$fullMark); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Mark Obtained</h5>
                            <p class="badge bg-info rounded-pill" style="font-size: 1.2rem;"><?php echo htmlspecialchars((string)$markObtained); ?></p>
                        </div>
                    </div>

                    <!-- Row 2: Marks Decrease -->
                    <div class="row text-center mb-3">
                        <div class="col-md-4">
                            <h5>Absences</h5>
                            <p class="badge bg-danger rounded-pill" style="font-size: 1.1rem;">-<?php echo htmlspecialchars((string)($absenceCount * 10)); ?></p>
                        </div>
                        <div class="col-md-4">
                            <h5>Missing Homework</h5>
                            <p class="badge bg-danger rounded-pill" style="font-size: 1.1rem;">-<?php echo htmlspecialchars((string)($homeworkFailCount * 10)); ?></p>
                        </div>
                        <div class="col-md-4">
                            <h5>Total Penalty</h5>
                            <p class="badge bg-dark rounded-pill" style="font-size: 1.1rem;">-<?php echo htmlspecialchars((string)$totalPenalty); ?></p>
                        </div>
                    </div>

                    <!-- Row 3: Percentages -->
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h5>Average Quiz %</h5>
                            <p class="badge bg-primary rounded-pill" style="font-size: 1.2rem;"><?php echo htmlspecialchars((string)($quizAvgPercent ?? 'N/A')); ?>%</p>
                        </div>
                        <div class="col-md-4">
                            <h5>Average Exam %</h5>
                            <p class="badge bg-info rounded-pill" style="font-size: 1.2rem;"><?php echo htmlspecialchars((string)($examAvgPercent ?? 'N/A')); ?>%</p>
                        </div>
                        <div class="col-md-4">
                            <h5>Final % (with penalties)</h5>
                            <p class="badge bg-success rounded-pill" style="font-size: 1.2rem;"><?php echo htmlspecialchars((string)$finalPercentWithPenalty); ?>%</p>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($examCol !== null) : ?>
                <div class="card exam-results-card">
                    <div class="card-header bg-info text-white">
                        <strong>Exam Results (percent)</strong>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach ($rows as $r) : ?>
                                <?php if (isset($r[$examCol]) && ($r[$examCol] !== null && $r[$examCol] !== '')) :
                                    $examVal = (float)$r[$examCol];
                                    $examPercent = ($examMax > 0) ? round(($examVal / $examMax) * 100, 1) : null;
                                    $quizVal = isset($r['student_results']) ? (float)$r['student_results'] : 0.0;
                                    $quizPercent = ($quizMax > 0) ? round(($quizVal / $quizMax) * 100, 1) : null;
                                ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Lesson <?php echo htmlspecialchars((string)($r['lesson_num'] ?? '')); ?></span>
                                        <span class="d-flex align-items-center">
                                            <span class="me-3">Mark: <?php echo htmlspecialchars((string)$examVal); ?></span>
                                            <span class="me-3">Quiz percent: <?php echo htmlspecialchars((string)$quizPercent . '%'); ?></span>
                                            <span class="me-3">Exam percent: <?php echo htmlspecialchars((string)$examPercent . '%'); ?></span>
                                        </span>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>