<?php

if (isset($_POST['submit'])) {
    if (!empty($_FILES['fileToUpload'])) {
        print_r($_FILES['fileToUpload']);
        echo "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
        <div>
            <input type="file" name="fileToUpload" id="fileToUpload">
        </div>
        <div>
            <input type="submit" value="Upload File" name="submit">
        </div>
    </form>
</body>
<?php
$error_message = "<p style='color: red;'>Error: Please select a file to upload.</p>";
?>

</html>