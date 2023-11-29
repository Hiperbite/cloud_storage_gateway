<?php

require __DIR__ . '/api.php';
require __DIR__ . '/helper.php';
# Defining our route
# RESTFul mapping:
# HTTP Method | Url path               | Controller function
# ------------+------------------------+---------------------
#   GET       | /                      | hello_im_using_limonade

# matches GET /hellolimusinglimonade
$to_json = fn() => header('Content-type: application/json');


$render = fn($cb) => function () use ($cb) {
    header('Content-type: application/json');
    return json_encode($cb());
};

dispatch('/', $home);

before(function(){
    $headers = getallheaders();
    $token = null;
    
    if (isset($headers['Authorization'])) {
        $authorizationHeader = $headers['Authorization'];
        $matches = array();
        if (preg_match('/Bearer (.+)/', $authorizationHeader, $matches)) {
            if (isset($matches[1])) {
                $token = $matches[1];
            }
        }
    }
    
    if ($token) {
        // El token está presente en la cabecera de autorización
        echo json_encode("Token recibido: " . $token);
    } else {
        header('HTTP/1.0 401 Unauthorized');
        exit;
    }
})

// Define route to list FTP folder contents
dispatch('/api/list/', $render($listFolderContent));

// Define route to upload file to FTP server
dispatch_post('/api/upload', $render($uploadFile));


// Define route to download file from FTP server
dispatch('/api/download', $render($downloadFile));

// Define route to create a file on FTP server
dispatch_post('/api/create-file', 'createFile');
$render($createFile);

dispatch_post('/api/create-folder', $render($createFolder));

dispatch('/api/documentation', $render($documentation));

run();

?>
