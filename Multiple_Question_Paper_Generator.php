<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Initialize variables
$messages = [];

// Read the questions from the file
$questions = [];
$file = fopen("question_bank.txt", "r");
if (!$file) {
    $messages[] = "Failed to open the file";
} else {
    while (($question = fgets($file)) !== false) {
        $question = trim($question);
        if (!empty($question)) {
            $questions[] = $question;
        }
    }
    fclose($file);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the user input
    $numQuestions = isset($_POST["numQuestions"]) ? intval($_POST["numQuestions"]) : 0;
    $numPapers = isset($_POST["numPapers"]) ? intval($_POST["numPapers"]) : 0;

    // Generate and save each question paper
    for ($i = 1; $i <= $numPapers; $i++) {
        // Shuffle the questions again for each paper
        shuffle($questions);

        // Create the filename for the current paper
        $filename = "generated_question_" . $i . ".txt";

        // Open the file for writing
        $outfile = fopen($filename, "w");
        if (!$outfile) {
            $messages[] = "Failed to open the file: " . $filename;
            break;
        }

        // Choose the questions for the current paper and write them to the file
        $messages[] = "Question paper " . $i . ":";
        for ($j = 0; $j < $numQuestions; $j++) {
            $index = $i * $j % count($questions);
            $currQuestion = "Question " . ($j + 1) . ": " . $questions[$index];
            fwrite($outfile, $currQuestion . PHP_EOL);
            $messages[] = $currQuestion;
        }

        // Close the file
        fclose($outfile);

        // Print a message indicating that the current paper has been generated and saved
        $messages[] = "The generated questions have been saved in " . $filename;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Multiple Question Paper Generator</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <a href="logout.php">Logout</a>
	<br><br>
	<h1><b>Multiple Question Paper Generator</h1></b>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="numQuestions">How many questions do you need in each question paper?</label>
		<br>
		<br>
        <input type="text" id="numQuestions" name="numQuestions">
        <br>
		<br>
        <label for="numPapers">How many question papers do you want to generate?</label>
		<br>
		<br>
        <input type="text" id="numPapers" name="numPapers">
        <br>
		<br>
        <input type="submit" value="Generate Question Papers">
    </form>

    <?php
    // Display the messages if any
    if (!empty($messages)) {
        echo "<h3>Output Messages:</h3>";
        foreach ($messages as $message) {
            echo $message . "<br>";
        }
    }
    ?>
</body>
</html>
