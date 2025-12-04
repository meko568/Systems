<?php
$dsn = "mysql:host=localhost;dbname=systems;charset=utf8mb4";
$uname = "root";
$pass = "";
$validate = null;
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
    $sql = "USE systems;";
    $db->query($sql);
    $sql = "show tables;";
    $tables = $db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        foreach ($tables as $row) {
            if (isset($_POST["delete-" . $row])) {
                $pattern = "/^\d+_{$row}$/";
                $sql = "DROP table $row;";
                $db->query($sql);
                foreach ($tables as $table) {
                    if (preg_match($pattern, $table)) {
                        $sql = "DROP table $table;";
                        $db->query($sql);
                    }
                }
                header("Location: new.php");
                exit;
            }
        }
        if (isset($_POST["system-name"])) {
            $name = $_POST["system-name"];
            if (strlen($name) > 0 && !in_array($name, $tables) && preg_match('/^[a-zA-Z0-9_]+$/', $name) && !in_array(strtoupper($name), $mysql_reserved) && !preg_match('/^[0-9][0-9A-Za-z_]*$/', $name)) {
                $sql = "CREATE TABLE " . $_POST["system-name"] . " (
                    student_id INT AUTO_INCREMENT PRIMARY KEY,
                    student_name VARCHAR(50)
                )";
                $db->query($sql);
                header("Location: new.php");
                exit;
            } else {
                $validate = false;
            }
        }
        foreach ($tables as $row) {
            if (isset($_POST["select-" . $row])) {
                session_start();
                setcookie("table", "select-" . $row, 0, "/");
                header("Location: system.php");
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
    <title>New System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="mb-4">Systems Management</h1>

                <div class="systems-list mb-5">
                    <?php
                    if (count($tables) > 0) {
                        echo "<h3 class='mb-3'>Available Systems</h3>";
                        echo "<div class='row g-3'>";
                        foreach ($tables as $row) {
                            if (!preg_match('/^[0-9][0-9A-Za-z_]*$/', $row)) {
                                echo "<div class='col-md-6'>
                                <div class='card h-100 shadow-sm system-card'>
                                    <div class='card-body'>
                                        <h5 class='card-title'>$row</h5>
                                        <form method='post' class='d-grid gap-2'>
                                            <button type='submit' name='select-$row' class='btn btn-primary btn-sm'>Select System</button>
                                            <button type='submit' name='delete-$row' class='btn btn-danger btn-sm'>Delete System</button>
                                        </form>
                                    </div>
                                </div>
                            </div>";
                            }
                        }
                        echo "</div>";
                    } else {
                        echo "<div class='alert alert-info' role='alert'>
                            <strong>No systems found.</strong> Create your first system below.
                        </div>";
                    }
                    ?>
                </div>

                <div class="create-system card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Create New System</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" class="needs-validation">
                            <div class="mb-3">
                                <label for="name" class="form-label">System Name</label>
                                <input type="text" class="form-control" name="system-name" placeholder="Enter system name (alphanumeric and underscore only)" id="name" required>
                                <small class="form-text text-muted">Only letters, numbers, and underscores allowed</small>
                            </div>
                            <?php
                            if ($validate === false) {
                                echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                                    <strong>Invalid name!</strong> The name is invalid or already taken.
                                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                                </div>";
                            }
                            ?>
                            <button type="submit" class="btn btn-success btn-lg w-100">Create System</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>