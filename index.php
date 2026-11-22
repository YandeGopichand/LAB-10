
<?php
ob_start();
session_start();

date_default_timezone_set("Asia/Kolkata");

$ageMessage = "";
$logoutMessage = "";

// ================= LOGIN =================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {

    $name = trim($_POST["name"] ?? "");

    if (!empty($name)) {
        $_SESSION["name"] = $name;
        $_SESSION["start_time"] = time();
    }
}

// ================= AGE VERIFICATION =================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["check_age"])) {

    $name = htmlspecialchars(trim($_POST["age_name"] ?? ""));
    $age = intval($_POST["age"] ?? 0);

    if ($age < 18) {
        $ageMessage = "Sorry, $name! You are not authorized to visit this site.";
    } else {
        $ageMessage = "Welcome, $name! You are authorized to visit this site.";
    }
}

// ================= LOGOUT =================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logout"])) {

    $name = $_SESSION["name"] ?? "Guest";
    $startTime = $_SESSION["start_time"] ?? time();

    $duration = time() - $startTime;

    $minutes = floor($duration / 60);
    $seconds = $duration % 60;

    $logoutMessage = "Thank you, " . htmlspecialchars($name) .
        "! You logged out successfully. Session Duration: " .
        $minutes . " minutes and " . $seconds . " seconds.";

    session_unset();
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
        }

    </style>

</head>

<body>

<div class="container">

    <h1>Lab 10 - PHP Applications</h1>

    <!-- DATE AND TIME -->
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
                <?php echo $ageMessage; ?>
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

    <!-- WELCOME AND SESSION -->
    <div class="section">

        <h2>4. Welcome Page and Session Management</h2>

        <?php if (isset($_SESSION["name"])): ?>

            <div class="welcome">

                <h3>
                    Welcome,
                    <?php echo htmlspecialchars($_SESSION["name"]); ?>!
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

                <form method="POST" action="index.php">

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

        <?php if (!empty($logoutMessage)): ?>

            <div class="result">
                <?php echo $logoutMessage; ?>
            </div>

        <?php endif; ?>

    </div>

</div>

<!-- JAVASCRIPT DATE AND TIME -->
<script>

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

</script>

</body>
</html>
