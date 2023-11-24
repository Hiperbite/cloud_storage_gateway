<?php


require __DIR__ . '/vendor/autoload.php';

# Defining our route
# RESTFul mapping:
# HTTP Method | Url path               | Controller function
# ------------+------------------------+---------------------
#   GET       | /                      | hello_im_using_limonade

# matches GET /hellolimusinglimonade

dispatch('/', 'hello_im_using_limonade',
function(){

header('Content-type: application/json');
    return "{'hellogreeting': 'Hello Im Using Limonade!'}";
});

# Run the limonade app

// Define FTP configuration
$ftpConfig = [
    'host' => 'ftp://nestangola.co.in',
    'username' => 'u763002564.dev',
    'password' => 'U763002564.dev'
];

// Function to establish FTP connection
function connectFTP() {
    global $ftpConfig;
    
    $ftpConnection = ftp_connect('ftp://nestangola.co.in:21');
    
    if($ftpConnection){
        $login = ftp_login($ftpConnection, $ftpConfig['username'], $ftpConfig['password']);
    }
    
    if (!$ftpConnection || !$login) {
        halt("Failed to connect or login to FTP server");
    }
    
    return $ftpConnection;
}

// Define route to list FTP folder contents
dispatch('/ftp-list/:folder', function () {
    $folder = params('folder');
    $ftpConnection = connectFTP();
    
    $files = ftp_nlist($ftpConnection, $folder);
    
    ftp_close($ftpConnection);
    
    return $files;
});

// Define route to upload file to FTP server
dispatch_post('/upload', function () {
    $uploadedFile = $_FILES['file'];
    $ftpConnection = connectFTP();
    
    if (ftp_put($ftpConnection, $uploadedFile['name'], $uploadedFile['tmp_name'], FTP_BINARY)) {
        return "File uploaded successfully";
    } else {
        return "Error uploading file";
    }
    
    ftp_close($ftpConnection);
});

// Define route to download file from FTP server
dispatch('/download/:file', function () {
    $file = params('file');
    $ftpConnection = connectFTP();
    
    $fileContent = ftp_get($ftpConnection, 'php://output', $file, FTP_BINARY);
    
    ftp_close($ftpConnection);
    
    if ($fileContent) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        echo $fileContent;
        exit();
    } else {
        halt("Error downloading file");
    }
});

// Define route to create a file on FTP server
dispatch_post('/create-file', function () {
    $fileName = $_POST['file_name'];
    $fileContent = $_POST['file_content'];
    $ftpConnection = connectFTP();
    
    $tempHandle = tmpfile();
    fwrite($tempHandle, $fileContent);
    fseek($tempHandle, 0);
    
    if (ftp_fput($ftpConnection, $fileName, $tempHandle, FTP_BINARY)) {
        fclose($tempHandle);
        return "File created successfully";
    } else {
        fclose($tempHandle);
        return "Error creating file";
    }
    
    ftp_close($ftpConnection);
});

dispatch('/documentation', function () {
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
});

run();

?>