<?php

require './src/ftp.php';

# Defining our route
# RESTFul mapping:
# HTTP Method | Url path               | Controller function
# ------------+------------------------+---------------------
#   GET       | /                      | hello_im_using_limonade

# matches GET /hellolimusinglimonade
$to_json = fn() => header('Content-type: application/json');
$home = fn() => "{'hellogreeting': 'Hello Im Using Limonade!'}";

# Run the limonade app



// Define route to list FTP folder contents
$listFolderContent = function () {
    $folder = $_GET['folderName'];
    $ftp = new Ftp();

    $files = ftp_nlist($ftp->connection, $folder);

    $ftp->close();

    return $files;
};

// Define route to upload file to FTP server
$uploadFile = function () {
    $uploadedFile = $_FILES['file'];
    $uploadedPath = $_POST['path'];

    $ftp = new Ftp();

    ftp_chdir($ftp->connection, $uploadedPath);
    $final = (
        ftp_put(
            $ftp->connection,
            $uploadedFile['name'],
            $uploadedFile['tmp_name'],
            FTP_BINARY
        )
    )
        ? "File uploaded successfully"
        : "Error uploading file";

    $ftp->close();

    return $final;
};

// Define route to download file from FTP server
$downloadFile = function () {

    header('Content-Type: application/octet-stream');

    $file = $_GET['file'];

    $ftp = new Ftp();

    $fileContent = ftp_get($ftp->connection, 'php://output', $file, FTP_BINARY);

    $ftp->close();

    if ($fileContent) {
        header('Content-Disposition: attachment; filename="' . $file . '"');
        echo $fileContent;
        exit();
    } else {
        halt("Error downloading file");
    }

};


$createFile = function () {
    header('Content-Type: application/octet-stream');

    $fileName = $_POST['file_name'];
    $fileContent = $_POST['file_content'];

    $ftp = new Ftp();

    $tempHandle = tmpfile();
    fwrite($tempHandle, $fileContent);
    fseek($tempHandle, 0);

    if (ftp_fput($ftp->connection, $fileName, $tempHandle, FTP_BINARY)) {
        fclose($tempHandle);
        $final = "File created successfully";
    } else {
        fclose($tempHandle);
        $final = "Error creating file";
    }

    $ftp->close();

    return $final;
};



$createFolder = function () {
    $folderNames = explode('/', $_POST['folderName']);
    $ftp = new Ftp();

    foreach ($folderNames as $part) {
        if (!@ftp_chdir($ftp->connection, $part)) {
            ftp_mkdir($ftp->connection, $part);
            ftp_chdir($ftp->connection, $part);
            //ftp_chmod($ftpcon, 0777, $part);
        }
    }

    $ftp->close();

    return $folderNames;
};


$documentation = function () {
    ob_start(); ?>

    <!DOCTYPE html>
    <html>

    <head>
        <title>FTP Explorer API Documentation</title>
    </head>

    <body>
        <h1>FTP Explorer API Documentation</h1>
        <h2>List FTP Folder Contents:</h2>
        <p>Endpoint: GET /ftp-list/:folder</p>
        <p>Description: Retrieves a list of files and folders in the specified FTP folder.</p>

        <h2>Upload File:</h2>
        <p>Endpoint: POST /upload</p>
        <p>Description: Uploads a file to the FTP server.</p>

        <!-- Add documentation for other endpoints -->

        <h2>Download File:</h2>
        <p>Endpoint: GET /download/:file</p>
        <p>Description: Downloads a file from the FTP server.</p>

        <h2>Create File:</h2>
        <p>Endpoint: POST /create-file</p>
        <p>Description: Creates a new file on the FTP server.</p>
    </body>

    </html>

    <?php
    $content = ob_get_clean();
    return $content;
};

?>