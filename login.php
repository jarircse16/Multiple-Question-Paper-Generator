<?php

// Initialize session
session_start();

// Check if the user is already logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Redirect to the main page
    header("Location: Multiple_Question_Paper_Generator.php");
    exit;
}

// Function to validate the login credentials
function validateLogin($username, $password)
{
    // Read the user credentials from the file
    $users = [];
    $file = fopen("user_credentials.txt", "r");
    if (!$file) {
        return false;
    } else {
        while (($line = fgets($file)) !== false) {
            $line = trim($line);
            if (!empty($line)) {
                list($storedUsername, $storedPassword) = explode(":", $line);
                $users[$storedUsername] = $storedPassword;
            }
        }
        fclose($file);
    }

    // Check if the username exists and the password matches the stored hash
    if (array_key_exists($username, $users) && md5($password) === $users[$username]) {
        return true;
    }

    return false;
}

// Handle the login form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";

    // Validate the login credentials
    if (validateLogin($username, $password)) {
        // Store user information in session variables
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;

        // Set a cookie to remember the user
        setcookie("remember_me", $username, time() + (86400 * 30), "/"); // Cookie expires in 30 days

        // Redirect to the main page
        header("Location: Multiple_Question_Paper_Generator.php");
        exit;
    } else {
        $loginError = "Invalid username or password.";
    }
}

// Handle the logout request
if (isset($_GET["logout"]) && $_GET["logout"] === "true") {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Delete the remember me cookie
    setcookie("remember_me", "", time() - 3600, "/");

    // Redirect to the login page
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <input type="submit" value="Login">
    </form>
    <?php if (isset($loginError)) { ?>
        <p><?php echo $loginError; ?></p>
    <?php } ?>
    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) { ?>
        <p>Welcome, <?php echo $_SESSION["username"]; ?>!</p>
        <p><a href="login.php?logout=true">Logout</a></p>
    <?php } ?>
</body>
</html>
