<?php
/**
 * Send messages using a local or remote SMTP server.
 * It supports TLS and SSL crypto.
 * @class Smtp
 * @author wooptoo, http://wooptoo.com
 * @license BSD
 */
class Smtp 
{
    public $server;
    public $port;
    public $crypto;
    public $user;
    public $pass;
    private $timeout = '45';
    private $localhost = 'localhost';
    private $nl = "\r\n";
    private $conn;
	
	public $showOutput = 0;

    /**
     * Connect and Auth to server.
     *
     * @param string $server - remote server address or 'localhost'
     * @param int $port
     * @param string $crypto - can be null, ssl, tls
     * @param string $user - optional for localhost server
     * @param string $pass - optional for localhost server
     */
    function __construct($server, $port, $crypto=null, $user=null, $pass=null) 
	{
        $this->server = $server;
        $this->port = $port;
        $this->crypto = $crypto;
        $this->user = $user;
        $this->pass = $pass;

        $this->connect();
        $this->auth();
    }

    /**
     * Connect to server.
     */
    function connect() {
        $this->crypto = strtolower(trim($this->crypto));
        $this->server = strtolower(trim($this->server));

        if($this->crypto == 'ssl')
            $this->server = 'ssl://' . $this->server;
        $this->conn = fsockopen(
            $this->server, $this->port, $errno, $errstr, $this->timeout
        );
        $this->getResult($this->conn);
        return;
    }
	
	function getResult($conn)
	{
		
		$res = fgets($conn);
		if($this->showOutput == 1)
			echo $res;
		return $res;
	}
	
	function sendCommand($conn, $command)
	{
		fputs($conn,$command);	
		if($this->showOutput == 1)
			echo $command;
	}

    /**
     * Auth.
     */
    function auth() 
	{
        $this->sendCommand($this->conn, 'HELO ' . $this->localhost . $this->nl);
        $this->getResult($this->conn);
        if($this->crypto == 'tls') {
            $this->sendCommand($this->conn, 'STARTTLS' . $this->nl);
            $this->getResult($this->conn);
            stream_socket_enable_crypto(
                $this->conn, true, STREAM_CRYPTO_METHOD_TLS_CLIENT
            );
            $this->sendCommand($this->conn, 'HELO ' . $this->localhost . $this->nl);
            $this->getResult($this->conn);
        }
        if($this->server != 'localhost') {
            $this->sendCommand($this->conn, 'AUTH LOGIN' . $this->nl);
            $this->getResult($this->conn);
			
            $this->sendCommand($this->conn, base64_encode($this->user) . $this->nl);
            $this->getResult($this->conn);
            $this->sendCommand($this->conn, base64_encode($this->pass) . $this->nl);
            $this->getResult($this->conn);
			
        }
        return;
    }

    /**
     * Send an email.
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string $headers - optional
     */
    function send($from, $to, $subject, $message, $headers=null) 
	{
        $this->sendCommand($this->conn, 'MAIL FROM: <'. $from .'>'. $this->nl);
        $this->getResult($this->conn);
        $this->sendCommand($this->conn, 'RCPT TO: <'. $to .'>'. $this->nl);
        $this->getResult($this->conn);
        $this->sendCommand($this->conn, 'DATA'. $this->nl);
        $this->getResult($this->conn);
        $this->sendCommand($this->conn,
            'To: '. $to .$this->nl.
            'Subject: '. $subject .$this->nl.
            $headers .$this->nl.
            $this->nl.
            $message . $this->nl.
            '.' .$this->nl
        );
        if(substr($this->getResult($this->conn),0,3) == "250")
			return true;
		else
			return false;
    }

    /**
     * Quit and disconnect.
     */
    function __destruct() 
	{
        $this->sendCommand($this->conn, 'QUIT' . $this->nl);
        $this->getResult($this->conn);
        fclose($this->conn);
    }
}

?>