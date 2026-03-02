<?php
if (isset($_POST['submit'])) {
    $permittedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $file_name = $_FILES['fileToUpload']['name'] ?? '';
    if (!empty($file_name)) {
        // print_r($_FILES['fileToUpload']);
        $_FILES_name = $_FILES['fileToUpload']['name'] ?? '';
        $_FILES_path = $_FILES['fileToUpload']['full_path'] ?? '';
        $_FILES_type = $_FILES['fileToUpload']['type'] ?? '';
        $_FILES_tmp_name = $_FILES['fileToUpload']['tmp_name'] ?? '';
        $_FILES_size = $_FILES['fileToUpload']['size'] ?? '';
        $files_extensions = strtolower($_FILES_type);
        if (in_array($files_extensions, $permittedTypes)) {
            if ($_FILES_size < 2000000) {
                move_uploaded_file($_FILES_tmp_name, "uploads/" . $_FILES_name);
                echo "The file " . htmlspecialchars(basename($_FILES_name)) . " has been uploaded.";
            } else {
                echo "Sorry, your file is too large.";
            }
        }
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