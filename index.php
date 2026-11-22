<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
date_default_timezone_set("Asia/Kolkata");

$ageMessage = "";
$logoutMessage = "";
$loggedOut = false;

// Unique token for application usage tracking
if (!isset($_SESSION["usage_token"])) {
    $_SESSION["usage_token"] = bin2hex(random_bytes(16));
}

$usageToken = $_SESSION["usage_token"];

// ================= LOGIN =================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {

    $name = trim($_POST["name"] ?? "");

    if ($name !== "") {

        session_regenerate_id(true);

        $_SESSION["name"] = $name;
        $_SESSION["start_time"] = time();

        // Preserve the application usage token
        if (!isset($_SESSION["usage_token"])) {
            $_SESSION["usage_token"] = $usageToken;
        }
    }
}

// ================= AGE VERIFICATION =================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["check_age"])) {

    $name = htmlspecialchars(
        trim($_POST["age_name"] ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );

    $age = intval($_POST["age"] ?? 0);

    if ($age < 18) {

        $ageMessage = "Hello $name, you are not authorized to visit this site.";

    } else {

        $ageMessage = "Welcome $name! You are authorized to visit this site.";
    }
}

// ================= LOGOUT =================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["logout"])) {

    $name = $_SESSION["name"] ?? "Guest";
    $startTime = $_SESSION["start_time"] ?? time();

    // Total session duration
    $sessionDuration = max(0, time() - $startTime);

    // Application usage received from JavaScript
    $activeUsage = max(
        0,
        intval($_POST["active_usage"] ?? 0)
    );

    // Session time
    $sessionHours = floor($sessionDuration / 3600);
    $sessionMinutes = floor(($sessionDuration % 3600) / 60);
    $sessionSeconds = $sessionDuration % 60;

    // Application usage time
    $usageHours = floor($activeUsage / 3600);
    $usageMinutes = floor(($activeUsage % 3600) / 60);
    $usageSeconds = $activeUsage % 60;

    $logoutMessage = "
        <h3>Thank you, " .
        htmlspecialchars($name, ENT_QUOTES, "UTF-8") .
        "!</h3>

        <p>You logged out successfully.</p>

        <p>
            <strong>Total Session Duration:</strong>
            $sessionHours hours,
            $sessionMinutes minutes,
            $sessionSeconds seconds
        </p>

        <p>
            <strong>Active Application Usage:</strong>
            $usageHours hours,
            $usageMinutes minutes,
            $usageSeconds seconds
        </p>
    ";

    $loggedOut = true;

    session_unset();
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Lab 10 - PHP Applications</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #2563eb;
            margin-bottom: 30px;
        }

        h2 {
            color: #333;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }

        .section {
            margin: 25px 0;
            padding: 25px;
            background: #f8fafc;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
        }

        button:hover {
            background: #1d4ed8;
        }

        .result {
            background: #e0f2fe;
            color: #075985;
            padding: 15px;
            margin-top: 15px;
            border-radius: 6px;
        }

        .welcome {
            background: #dcfce7;
            color: #166534;
            padding: 20px;
            border-radius: 8px;
        }

        .logout-button {
            background: #dc2626;
        }

        .logout-button:hover {
            background: #b91c1c;
        }

        .datetime {
            font-size: 18px;
            line-height: 2;
        }

        .timer-box {
            background: #dbeafe;
            color: #1e40af;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            text-align: center;
        }

        .usage-box {
            background: #f3e8ff;
            color: #6b21a8;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            text-align: center;
        }

        .timer {
            font-size: 32px;
            font-weight: bold;
            margin: 15px 0;
            font-family: monospace;
        }

        .usage-timer {
            font-size: 32px;
            font-weight: bold;
            margin: 15px 0;
            font-family: monospace;
        }

        .timer-label {
            font-size: 16px;
            font-weight: bold;
        }

        @media (max-width: 600px) {

            body {
                padding: 10px;
            }

            .container {
                padding: 20px;
            }

            .section {
                padding: 15px;
            }

            .timer,
            .usage-timer {
                font-size: 24px;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <h1>Lab 10 - PHP Applications</h1>

    <!-- DATE, TIME AND DAY -->
    <div class="section">

        <h2>1. Current Date, Time and Day</h2>

        <div class="datetime">

            <p>
                <strong>Current Date:</strong>
                <span id="date"></span>
            </p>

            <p>
                <strong>Current Time:</strong>
                <span id="time"></span>
            </p>

            <p>
                <strong>Current Day:</strong>
                <span id="day"></span>
            </p>

        </div>

    </div>

    <!-- AGE VERIFICATION -->
    <div class="section">

        <h2>2. Age Verification</h2>

        <form method="POST" action="index.php">

            <label>Name:</label>

            <input
                type="text"
                name="age_name"
                placeholder="Enter your name"
                required
            >

            <label>Age:</label>

            <input
                type="number"
                name="age"
                placeholder="Enter your age"
                min="1"
                required
            >

            <button type="submit" name="check_age">
                Verify Age
            </button>

        </form>

        <?php if (!empty($ageMessage)): ?>

            <div class="result">
                <?php echo htmlspecialchars(
                    $ageMessage,
                    ENT_QUOTES,
                    "UTF-8"
                ); ?>
            </div>

        <?php endif; ?>

    </div>

    <!-- LOGIN -->
    <div class="section">

        <h2>3. Login Application</h2>

        <form method="POST" action="index.php">

            <label>Enter Your Name:</label>

            <input
                type="text"
                name="name"
                placeholder="Enter your name"
                required
            >

            <button type="submit" name="login">
                Login
            </button>

        </form>

    </div>

    <!-- SESSION MANAGEMENT -->
    <div class="section">

        <h2>4. Welcome Page and Session Management</h2>

        <?php if (isset($_SESSION["name"])): ?>

            <div class="welcome">

                <h3>
                    Welcome,
                    <?php echo htmlspecialchars(
                        $_SESSION["name"],
                        ENT_QUOTES,
                        "UTF-8"
                    ); ?>!
                </h3>

                <p>You have successfully logged in.</p>

                <p>
                    <strong>Session Start Time:</strong>

                    <?php
                    echo date(
                        "h:i:s A",
                        $_SESSION["start_time"]
                    );
                    ?>
                </p>

                <!-- SESSION TIMER -->
                <div class="timer-box">

                    <div class="timer-label">
                        Total Session Duration
                    </div>

                    <div class="timer" id="sessionTimer">
                        00:00:00
                    </div>

                    <p>
                        Total elapsed time since login.
                    </p>

                </div>

                <!-- APPLICATION USAGE TIMER -->
                <div class="usage-box">

                    <div class="timer-label">
                        Active Application Usage
                    </div>

                    <div class="usage-timer" id="usageTimer">
                        00:00:00
                    </div>

                    <p>
                        Counts time while this application
                        is visible and you are active.
                    </p>

                </div>

                <!-- LOGOUT -->
                <form
                    method="POST"
                    action="index.php"
                    id="logoutForm"
                >

                    <input
                        type="hidden"
                        name="active_usage"
                        id="activeUsageInput"
                        value="0"
                    >

                    <button
                        type="submit"
                        name="logout"
                        class="logout-button"
                    >
                        Logout
                    </button>

                </form>

            </div>

        <?php else: ?>

            <p>
                Login above to display your welcome message
                and session information.
            </p>

        <?php endif; ?>

        <!-- LOGOUT SUMMARY -->
        <?php if (!empty($logoutMessage)): ?>

            <div class="result">
                <?php echo $logoutMessage; ?>
            </div>

        <?php endif; ?>

    </div>

    <!-- APPLICATION USAGE BEFORE LOGIN -->
    <?php if (!isset($_SESSION["name"]) && !$loggedOut): ?>

        <div class="section">

            <h2>Application Usage</h2>

            <div class="usage-box">

                <div class="timer-label">
                    Application Usage Since Opening
                </div>

                <div class="usage-timer" id="usageTimer">
                    00:00:00
                </div>

                <p>
                    The timer starts when you open this application.
                    It counts while the page is visible and active.
                </p>

            </div>

        </div>

    <?php endif; ?>

</div>

<script>

// ================= DATE, TIME AND DAY =================

function updateDateTime() {

    const now = new Date();

    document.getElementById("date").textContent =
        now.toLocaleDateString("en-IN");

    document.getElementById("time").textContent =
        now.toLocaleTimeString("en-IN");

    document.getElementById("day").textContent =
        now.toLocaleDateString("en-IN", {
            weekday: "long"
        });
}

updateDateTime();

setInterval(updateDateTime, 1000);


// ================= TIMER FORMAT =================

function formatTime(totalSeconds) {

    const hours = Math.floor(totalSeconds / 3600);

    const minutes = Math.floor(
        (totalSeconds % 3600) / 60
    );

    const seconds = totalSeconds % 60;

    return (
        String(hours).padStart(2, "0") + ":" +
        String(minutes).padStart(2, "0") + ":" +
        String(seconds).padStart(2, "0")
    );
}


// ================= APPLICATION USAGE TIMER =================

// This timer starts when the application page opens.

const usageToken =
    "<?php echo htmlspecialchars(
        $usageToken,
        ENT_QUOTES,
        "UTF-8"
    ); ?>";

const storageKey = "applicationUsage_" + usageToken;

let activeSeconds = parseInt(
    sessionStorage.getItem(storageKey) || "0",
    10
);

if (!Number.isFinite(activeSeconds) || activeSeconds < 0) {
    activeSeconds = 0;
}

let lastActivityTime = Date.now();

const inactivityLimit = 60000; // 60 seconds

const usageStopped = <?php echo $loggedOut ? "true" : "false"; ?>;


// User activity detection

function resetActivityTimer() {
    lastActivityTime = Date.now();
}

document.addEventListener("mousemove", resetActivityTimer);
document.addEventListener("keydown", resetActivityTimer);
document.addEventListener("click", resetActivityTimer);
document.addEventListener("scroll", resetActivityTimer);
document.addEventListener("touchstart", resetActivityTimer);


// Application usage timer

function updateUsageTimer() {

    if (usageStopped) {
        return;
    }

    const currentTime = Date.now();

    const isVisible = !document.hidden;

    const isRecentlyActive =
        currentTime - lastActivityTime < inactivityLimit;

    // Count only when visible and recently active

    if (isVisible && isRecentlyActive) {
        activeSeconds++;
    }

    // Save usage for this browser tab

    sessionStorage.setItem(
        storageKey,
        String(activeSeconds)
    );

    // Update display

    const usageDisplay =
        document.getElementById("usageTimer");

    if (usageDisplay) {
        usageDisplay.textContent =
            formatTime(activeSeconds);
    }

    // Send usage to PHP when logout is clicked

    const usageInput =
        document.getElementById("activeUsageInput");

    if (usageInput) {
        usageInput.value = activeSeconds;
    }
}


// Display usage immediately

const initialUsageDisplay =
    document.getElementById("usageTimer");

if (initialUsageDisplay) {
    initialUsageDisplay.textContent =
        formatTime(activeSeconds);
}


// Start application usage timer

if (!usageStopped) {
    setInterval(updateUsageTimer, 1000);
}


// ================= SESSION TIMER =================

<?php if (isset($_SESSION["start_time"])): ?>

const sessionStartTime =
    <?php echo (int) $_SESSION["start_time"]; ?>;


function updateSessionTimer() {

    const currentTime =
        Math.floor(Date.now() / 1000);

    const elapsed =
        Math.max(0, currentTime - sessionStartTime);

    document.getElementById("sessionTimer").textContent =
        formatTime(elapsed);
}

updateSessionTimer();

setInterval(updateSessionTimer, 1000);


// ================= LOGOUT =================

const logoutForm =
    document.getElementById("logoutForm");

if (logoutForm) {

    logoutForm.addEventListener("submit", function() {

        document.getElementById("activeUsageInput").value =
            activeSeconds;

        sessionStorage.setItem(
            storageKey,
            String(activeSeconds)
        );

    });

}

<?php endif; ?>

</script>

</body>
</html>