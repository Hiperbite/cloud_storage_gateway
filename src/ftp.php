<?php

class Ftp
{
    # Run the limonade app

    // Define FTP configuration
    protected $config = [
        'host' => '89.117.188.24',
        'username' => 'u763002564.dev',
        'password' => 'U763002564.dev'
    ];
    public $connection = null;

    public function __construct($config = [])
    {
        $this->connect();
    }
    // Function to establish FTP connection
    public function connect()
    {

        $this->connection = ftp_connect($this->config['host'], 21, 1000000);

        if ($this->connection) {
            $login = ftp_login($this->connection, $this->config['username'], $this->config['password']);
        }

        if (!$this->connection || !$login) {
            halt("Failed to connect or login to FTP server");
        }

        return $this->connection;
    }

    public function close()
    {
        return ftp_close($this->connection);
    }
}