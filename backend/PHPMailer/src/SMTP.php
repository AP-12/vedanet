<?php
/**
 * PHPMailer SMTP class.
 * PHP Version 5.5.
 *
 * @see       https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 *
 * @author    Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author    Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author    Brent R. Matzelle (original founder)
 * @copyright 2012 - 2020 Marcus Bointon
 * @copyright 2010 - 2012 Jim Jagielski
 * @copyright 2004 - 2009 Andy Prevost
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace PHPMailer\PHPMailer;

/**
 * PHPMailer SMTP class.
 *
 * @author  Marcus Bointon <phpmailer@synchromedia.co.uk>
 */
class SMTP
{
    /**
     * The PHPMailer SMTP version number.
     *
     * @var string
     */
    const VERSION = '6.5.0';

    /**
     * SMTP line break constant.
     *
     * @var string
     */
    const LE = "\r\n";

    /**
     * The SMTP port to use if one is not specified.
     *
     * @var int
     */
    const DEFAULT_PORT = 25;

    /**
     * The maximum line length allowed by RFC 2822 section 2.1.1.
     *
     * @var int
     */
    const MAX_LINE_LENGTH = 998;

    /**
     * The connection to the server.
     *
     * @var resource
     */
    protected $connection;

    /**
     * The socket for the server connection.
     *
     * @var resource
     */
    protected $socket;

    /**
     * The timeout value for connection.
     *
     * @var int
     */
    public $Timeout = 300;

    /**
     * The SMTP server.
     *
     * @var string
     */
    public $Host = 'localhost';

    /**
     * The port to connect to.
     *
     * @var int
     */
    public $Port = 25;

    /**
     * The SMTP HELO/EHLO command.
     *
     * @var string
     */
    public $Helo = '';

    /**
     * What kind of encryption to use on the SMTP connection.
     * Options: '', 'ssl' or 'tls'.
     *
     * @var string
     */
    public $SMTPSecure = '';

    /**
     * Whether to enable TLS encryption automatically if a server supports it.
     *
     * @var bool
     */
    public $SMTPAutoTLS = true;

    /**
     * Whether to use SMTP authentication.
     *
     * @var bool
     */
    public $SMTPAuth = false;

    /**
     * Options array passed to stream_context_create when connecting via SMTP.
     *
     * @var array
     */
    public $SMTPOptions = [];

    /**
     * SMTP username.
     *
     * @var string
     */
    public $Username = '';

    /**
     * SMTP password.
     *
     * @var string
     */
    public $Password = '';

    /**
     * SMTP auth type. Options are CRAM-MD5, LOGIN, PLAIN, XOAUTH2.
     *
     * @var string
     */
    public $AuthType = '';

    /**
     * Whether to enable SMTP debugging.
     *
     * @var int
     */
    public $SMTPDebug = 0;

    /**
     * Callback function for SMTP debugging.
     *
     * @var callable
     */
    public $Debugoutput = 'echo';

    /**
     * Whether to keep the SMTP connection open after each message.
     *
     * @var bool
     */
    public $SMTPKeepAlive = false;

    /**
     * The most recent reply received from the server.
     *
     * @var string
     */
    protected $last_reply = '';

    /**
     * Connect to an SMTP server.
     *
     * @param string $host    SMTP server IP or host name
     * @param int    $port    The port number to connect to
     * @param int    $timeout How long to wait for the connection to open
     * @param array  $options An array of options for stream_context_create()
     *
     * @return bool
     */
    public function connect($host, $port = null, $timeout = 30, $options = null)
    {
        // Clear errors to avoid confusion
        $this->setError('');
        // Make sure we are __not__ connected
        if ($this->connected()) {
            // Already connected, generate error
            $this->setError('Already connected to a server');

            return false;
        }
        if (empty($port)) {
            $port = self::DEFAULT_PORT;
        }
        // Connect to the SMTP server
        $this->edebug(
            "Connection: opening to $host:$port, timeout=$timeout, options=" .
            (count($options) > 0 ? var_export($options, true) : 'array()'),
            self::DEBUG_CONNECTION
        );
        $errno = 0;
        $errstr = '';
        if ($this->isTLS()) {
            $host = 'tls://' . $host;
        } elseif ($this->isSSL()) {
            $host = 'ssl://' . $host;
        }
        //Connect to the SMTP server
        $this->socket = $this->get_socket($host, $port, $errno, $errstr, $timeout, $options);
        if (!is_resource($this->socket)) {
            $this->setError(
                "Failed to connect to server: $errstr ($errno)",
                '',
                'ECONNECTION'
            );
            $this->edebug(
                "SMTP ERROR: " . $this->error['error'] . ": $errstr ($errno)",
                self::DEBUG_CLIENT
            );

            return false;
        }
        $this->edebug('Connection: opened', self::DEBUG_CONNECTION);
        // Get any announcement
        $this->last_reply = $this->get_lines();
        $this->edebug('SERVER -> CLIENT: ' . $this->last_reply, self::DEBUG_SERVER);

        return true;
    }

    /**
     * Create connection to the SMTP server.
     *
     * @param string $host    SMTP server IP or host name
     * @param int    $port    The port number to connect to
     * @param int    $errno   Error number
     * @param string $errstr  Error message
     * @param int    $timeout How long to wait for the connection to open
     * @param array  $options An array of options for stream_context_create()
     *
     * @return resource
     */
    protected function get_socket($host, $port, &$errno, &$errstr, $timeout, $options)
    {
        $this->edebug(
            "Connection: get_socket($host, $port, $timeout)",
            self::DEBUG_CONNECTION
        );
        // PHP version >= 5.6.7 requires an empty options array
        if (!is_array($options)) {
            $options = [];
        }
        // PHP version < 5.6.7 caused issues if options array was passed
        if (version_compare(PHP_VERSION, '5.6.7', '<')) {
            $options = [];
        }
        // Create the context
        $context = stream_context_create($options);
        set_error_handler([$this, 'errorHandler']);
        $this->socket = stream_socket_client(
            $host . ':' . $port,
            $errno,
            $errstr,
            $timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );
        restore_error_handler();

        return $this->socket;
    }

    /**
     * Initiate a TLS (encrypted) session.
     *
     * @return bool
     */
    public function startTLS()
    {
        if (!$this->sendCommand('STARTTLS', 'STARTTLS', 220)) {
            return false;
        }
        // Begin encrypted connection
        if (!stream_socket_enable_crypto(
            $this->socket,
            true,
            STREAM_CRYPTO_METHOD_TLS_CLIENT
        )) {
            return false;
        }

        return true;
    }

    /**
     * Perform SMTP authentication.
     * Must be run after hello().
     *
     * @param string $username The user name
     * @param string $password The password
     * @param string $authtype The auth type (CRAM-MD5, LOGIN, PLAIN, XOAUTH2)
     * @param string $realm    The auth realm for NTLM
     * @param string $workstation The auth workstation for NTLM
     *
     * @return bool True if successfully authenticated
     */
    public function authenticate(
        $username,
        $password,
        $authtype = null,
        $realm = '',
        $workstation = ''
    ) {
        if (!$this->server_caps()) {
            $this->setError('Authentication is not allowed before HELO/EHLO');

            return false;
        }

        if (array_key_exists('EHLO', $this->server_caps)) {
            // SMTP extensions are available. Let's try to find a proper authentication method

            if (!array_key_exists('AUTH', $this->server_caps)) {
                $this->setError('Authentication is not allowed at this stage');

                return false;
            }

            $this->edebug('Auth method requested: ' . ($authtype ?: 'UNSPECIFIED'), self::DEBUG_LOWLEVEL);
            $this->edebug(
                'Auth methods available on the server: ' . implode(',', $this->server_caps['AUTH']),
                self::DEBUG_LOWLEVEL
            );

            if (empty($authtype)) {
                foreach (array('CRAM-MD5', 'LOGIN', 'PLAIN', 'XOAUTH2') as $method) {
                    if (in_array($method, $this->server_caps['AUTH'])) {
                        $authtype = $method;
                        break;
                    }
                }
                if (empty($authtype)) {
                    $this->setError('No supported authentication methods found');

                    return false;
                }
                $this->edebug('Auth method selected: ' . $authtype, self::DEBUG_LOWLEVEL);
            }

            if (!in_array($authtype, $this->server_caps['AUTH'])) {
                $this->setError("The requested authentication method \"$authtype\" is not supported by the server");

                return false;
            }
        } elseif (empty($authtype)) {
            $authtype = 'LOGIN';
        }
        switch ($authtype) {
            case 'PLAIN':
                // Start authentication
                if (!$this->sendCommand('AUTH', 'AUTH PLAIN', 334)) {
                    return false;
                }
                // Send encoded username and password
                if (!$this->sendCommand(
                    'User & Password',
                    base64_encode("\0" . $username . "\0" . $password),
                    235
                )) {
                    return false;
                }
                break;
            case 'LOGIN':
                // Start authentication
                if (!$this->sendCommand('AUTH', 'AUTH LOGIN', 334)) {
                    return false;
                }
                // Send encoded username
                if (!$this->sendCommand('Username', base64_encode($username), 334)) {
                    return false;
                }
                // Send encoded password
                if (!$this->sendCommand('Password', base64_encode($password), 235)) {
                    return false;
                }
                break;
            case 'CRAM-MD5':
                // Start authentication
                if (!$this->sendCommand('AUTH CRAM-MD5', 'AUTH CRAM-MD5', 334)) {
                    return false;
                }
                // Get the challenge
                $challenge = base64_decode(substr($this->last_reply, 4));

                // Build the response
                $response = $username . ' ' . $this->hmac($challenge, $password);

                // Send encoded credentials
                return $this->sendCommand('Username', base64_encode($response), 235);
            default:
                $this->setError("Authentication method \"$authtype\" is not supported");

                return false;
        }

        return true;
    }

    /**
     * Calculate an MD5 HMAC hash.
     * Works like hash_hmac('md5', $data, $key) in case that function is not available
     *
     * @param string $data The data to hash
     * @param string $key  The key to hash with
     *
     * @return string
     */
    protected function hmac($data, $key)
    {
        if (function_exists('hash_hmac')) {
            return hash_hmac('md5', $data, $key);
        }

        // The following borrowed from
        // http://php.net/manual/en/function.mhash.php#27225

        // RFC 2104 HMAC implementation for php.
        // Creates an md5 HMAC.
        // Eliminates the need to install mhash to compute a HMAC
        // by Lance Rushing

        $b = 64; // byte length for md5
        if (strlen($key) > $b) {
            $key = pack('H*', md5($key));
        }
        $key = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack('H*', md5($k_ipad . $data)));
    }

    /**
     * Send an SMTP DATA command.
     * Issues a data command and sends the msg_data to the server,
     * finializing the mail transaction. $msg_data is the message
     * that is to be send with the headers. Each header needs to be
     * on a single line followed by a <CRLF> with the message headers
     * and the message body being separated by an additional <CRLF>.
     *
     * Implements RFC 821: DATA <CRLF>
     *
     * @param string $msg_data Message data to send
     *
     * @return bool
     */
    public function data($msg_data)
    {
        // This will use the standard timelimit
        if (!$this->sendCommand('DATA', 'DATA', 354)) {
            return false;
        }

        /* The server is ready to accept data!
         * According to rfc821 we should not send more than 1000 characters on a single line (including the LE)
         * so we will break the data up into lines by \r and/or \n then if needed we will break each of those into
         * smaller lines to fit within the limit.
         * We will also look for lines that start with a '.' and prepend an additional '.'.
         * NOTE: this does not count towards line-length limit.
         */

        // Normalize line breaks
        $msg_data = str_replace(["\r\n", "\r"], "\n", $msg_data);
        $lines = explode("\n", $msg_data);

        /* To distinguish between a complete RFC822 message and a plain message body, we check if the first field
         * of the first line (':' separated) does not contain a space then it _should_ be a header and we will
         * process all lines before a blank line as headers.
         */

        $field = substr($lines[0], 0, strpos($lines[0], ':'));
        $in_headers = false;
        if (!empty($field) && strpos($field, ' ') === false) {
            $in_headers = true;
        }

        foreach ($lines as $line) {
            $lines_out = [];
            if ($in_headers && $line === '') {
                $in_headers = false;
            }
            // Break this line up into several smaller lines if it's too long
            // Micro-optimisation: isset($str[$len]) is faster than (strlen($str) > $len),
            while (isset($line[self::MAX_LINE_LENGTH])) {
                // Working backwards, try to find a sensible point to break
                // the line at less than 998 characters from the end
                $len = self::MAX_LINE_LENGTH;
                while (--$len >= 0) {
                    $char = $line[$len];
                    if ($char === ' ' || $char === "\t") {
                        // Hit a whitespace, break there
                        $lines_out[] = substr($line, 0, $len);
                        $line = substr($line, $len + 1);
                        break;
                    }
                }
                // If we didn't find a break point, don't try to enforce the maximum length
                // and just break where we are
                if ($len < 0) {
                    $lines_out[] = substr($line, 0, self::MAX_LINE_LENGTH);
                    $line = substr($line, self::MAX_LINE_LENGTH);
                }
            }
            $lines_out[] = $line;

            // Send the lines to the server
            foreach ($lines_out as $line_out) {
                // RFC2821 section 4.5.2
                if (!empty($line_out) && $line_out[0] === '.') {
                    $line_out = '.' . $line_out;
                }
                $this->client_send($line_out . static::LE);
            }
        }

        // Message data has been sent, complete the command
        // Increase timelimit for end of DATA command
        $savetimelimit = $this->Timelimit;
        $this->Timelimit *= 2;
        $result = $this->sendCommand('DATA END', '.', 250);
        $this->Timelimit = $savetimelimit;

        return $result;
    }

    /**
     * Send an SMTP HELO or EHLO command.
     * Used to identify the sending server to the receiving server.
     * This makes sure that client and server are in a known state.
     *
     * @param string $host The host name or IP to connect to
     *
     * @return bool
     */
    public function hello($host = '')
    {
        // Try extended hello first (RFC 2821)
        if ($this->sendCommand('EHLO', "EHLO $host", 250)) {
            $this->parseHelloFields();
        } elseif (!$this->sendCommand('HELO', "HELO $host", 250)) {
            return false;
        }

        return true;
    }

    /**
     * Send an SMTP MAIL command.
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more recipient
     * commands may be called followed by a data command. This command
     * will send the message to the users terminal if they are logged
     * in and send them an email.
     *
     * Implements RFC 821: MAIL <SP> FROM:<reverse-path> <CRLF>
     *
     * @param string $from Source address of this message
     *
     * @return bool
     */
    public function mail($from)
    {
        $useVerp = ($this->do_verp ? ' XVERP' : '');

        return $this->sendCommand(
            'MAIL FROM',
            "MAIL FROM:<$from>$useVerp",
            250
        );
    }

    /**
     * Send an SMTP QUIT command.
     * Closes the socket if there is no error.
     *
     * @return bool
     */
    public function quit()
    {
        $this->setError('');

        return $this->sendCommand('QUIT', 'QUIT', 221);
    }

    /**
     * Send an SMTP RCPT command.
     * Sets the TO argument to $toaddr.
     * Returns true if the recipient was accepted false if it was rejected.
     *
     * Implements from RFC 821: RCPT <SP> TO:<forward-path> <CRLF>
     *
     * @param string $address The address the message is being sent to
     *
     * @return bool
     */
    public function rcpt($address)
    {
        return $this->sendCommand(
            'RCPT TO',
            "RCPT TO:<$address>",
            [250, 251]
        );
    }

    /**
     * Send an SMTP RSET command.
     * Abort any transaction that is currently in progress.
     *
     * @return bool True on success
     */
    public function reset()
    {
        return $this->sendCommand('RSET', 'RSET', 250);
    }

    /**
     * Send a command to an SMTP server and check its return code.
     *
     * @param string    $command       The command name - not sent to the server
     * @param string    $commandstring The actual command to send
     * @param int|array $expect        One or more expected integer success codes
     *
     * @return bool True on success
     */
    protected function sendCommand($command, $commandstring, $expect)
    {
        if (!$this->connected()) {
            $this->setError("Called $command without being connected");

            return false;
        }
        $this->client_send($commandstring . static::LE);

        $this->last_reply = $this->get_lines();
        $code = substr($this->last_reply, 0, 3);

        $this->edebug('SERVER -> CLIENT: ' . $this->last_reply, self::DEBUG_SERVER);

        if (!in_array($code, (array) $expect)) {
            $this->setError(
                "$command command failed",
                $this->last_reply,
                $code
            );
            $this->edebug(
                'SMTP ERROR: ' . $this->error['error'] . ': ' . $this->last_reply,
                self::DEBUG_CLIENT
            );

            return false;
        }

        $this->edebug("COMMAND: $command", self::DEBUG_CLIENT);

        return true;
    }

    /**
     * Send raw data to the server.
     *
     * @param string $data The data to send
     *
     * @return int|bool The number of bytes sent to the server or false on error
     */
    public function client_send($data)
    {
        $this->edebug("CLIENT -> SERVER: $data", self::DEBUG_CLIENT);
        set_error_handler([$this, 'errorHandler']);
        $result = fwrite($this->socket, $data);
        restore_error_handler();

        return $result;
    }

    /**
     * Get the current error.
     *
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Get the last reply from the server.
     *
     * @return string
     */
    public function getLastReply()
    {
        return $this->last_reply;
    }

    /**
     * Read the SMTP server's response.
     * Either before eof or socket timeout occurs.
     *
     * @return string
     */
    protected function get_lines()
    {
        // If the connection is bad, give up straight away
        if (!is_resource($this->socket)) {
            return '';
        }
        $data = '';
        $endtime = 0;
        stream_set_timeout($this->socket, $this->Timeout);
        if ($this->Timelimit > 0) {
            $endtime = time() + $this->Timelimit;
        }
        $selR = [$this->socket];
        $selW = null;
        while (is_resource($this->socket) && !feof($this->socket)) {
            //Must pass vars in here as params are by reference
            if (!stream_select($selR, $selW, $selW, $this->Timelimit)) {
                $this->edebug('SMTP -> get_lines(): timed-out (' . $this->Timeout . ' sec)', self::DEBUG_LOWLEVEL);
                break;
            }
            //Deliberately does not use fgets() here to allow multiple calls
            $str = @fread($this->socket, 515);
            $this->edebug('SMTP INBOUND: "' . trim($str) . '"', self::DEBUG_LOWLEVEL);
            $data .= $str;
            // If response is only 3 chars (not valid, but RFC5321 says it must be handled),
            // or 4th character is a space, we are done reading, break the loop.
            // String could be longer than 4 chars if multi-line responses are received
            if ((strlen($str) <= 4) || (strlen($str) > 4 && $str[3] === ' ')) {
                break;
            }
            // Timed-out? Log and break
            if ($endtime && time() > $endtime) {
                $this->edebug('SMTP -> get_lines(): timelimit exceeded (' . $this->Timelimit . ' sec)', self::DEBUG_LOWLEVEL);
                break;
            }
        }

        return $data;
    }

    /**
     * Enable or disable VERP (Variable Envelope Return Path) for sending mail.
     *
     * @param bool $enabled
     */
    public function setVerp($enabled = false)
    {
        $this->do_verp = $enabled;
    }

    /**
     * Get VERP (Variable Envelope Return Path) for sending mail.
     *
     * @return bool
     */
    public function getVerp()
    {
        return $this->do_verp;
    }

    /**
     * Set error messages and codes.
     *
     * @param string $message      The error message
     * @param string $detail       Further detail on the error
     * @param string $smtp_code    An associated SMTP error code
     * @param string $smtp_code_ex Extended SMTP error info
     */
    protected function setError($message, $detail = '', $smtp_code = '', $smtp_code_ex = '')
    {
        $this->error = [
            'error' => $message,
            'detail' => $detail,
            'smtp_code' => $smtp_code,
            'smtp_code_ex' => $smtp_code_ex
        ];
    }

    /**
     * Set debug output method.
     *
     * @param string|callable $method The name of the mechanism to use for debugging output
     */
    public function setDebugOutput($method = 'echo')
    {
        $this->Debugoutput = $method;
    }

    /**
     * Get debug output method.
     *
     * @return string
     */
    public function getDebugOutput()
    {
        return $this->Debugoutput;
    }

    /**
     * Set debug verbosity level.
     *
     * @param int $level
     */
    public function setDebugLevel($level = 0)
    {
    }

    /**
     * Get debug verbosity level.
     *
     * @return int
    */
    public function getDebugLevel()
    {
        return $this->SMTPDebug;
    }

    /**
     * Get the last transaction ID.
     *
     * @return string
     */
    public function getLastTransactionID()
    {
        return '';
    }

    /**
     * Parse server capabilities from EHLO response.
     */
    protected function parseHelloFields()
    {
        $this->server_caps = [];
        $lines = explode("\n", $this->last_reply);

        foreach ($lines as $n => $s) {
            // First line is the response code followed by the greeting
            if ($n === 0) {
                continue;
            }
            $s = trim(substr($s, 4));
            if (empty($s)) {
                continue;
            }
            $fields = explode(' ', $s);
            if (!empty($fields)) {
                if (!$n) {
                    $name = $fields[0];
                    $fields = $fields[1] ?? [];
                } else {
                    $name = $fields[0];
                    unset($fields[0]);
                    $fields = array_values($fields);
                }
                $this->server_caps[$name] = $fields;
            }
        }
    }

    /**
     * Check connection state.
     *
     * @return bool
     */
    public function connected()
    {
        if (is_resource($this->socket)) {
            $sock_status = stream_get_meta_data($this->socket);
            if ($sock_status['eof']) {
                // The socket is valid but we are at the end of the stream
                $this->edebug('SMTP -> get_lines(): EOF received from server', self::DEBUG_SERVER);
                return false;
            }

            return true; // Everything looks good
        }

        return false;
    }

    /**
     * Close the socket and clean up the state of the class.
     * Don't use this function without first trying to use QUIT.
     *
     * @return void
     */
    public function close()
    {
        $this->setError('');
        $this->server_caps = null;
        $this->helo_rply = null;
        if (is_resource($this->socket)) {
            fclose($this->socket);
            $this->socket = null;
            $this->edebug('Connection: closed', self::DEBUG_CONNECTION);
        }
    }

    /**
     * Send an SMTP NOOP command.
     * Used to keep keep-alives alive, doesn't actually do anything.
     *
     * @return bool
     */
    public function noop()
    {
        return $this->sendCommand('NOOP', 'NOOP', 250);
    }

    /**
     * Send an SMTP TURN command.
     * This is an optional command for SMTP that this class does not support.
     * This method is here to make the RFC821 Definition complete for this class
     * and _may_ be implemented in future
     * Implements from RFC 821: TURN <CRLF>
     *
     * @return bool
     */
    public function turn()
    {
        $this->setError('The SMTP TURN command is not implemented');
        $this->edebug('SMTP NOTICE: ' . $this->error['error'], self::DEBUG_CLIENT);

        return false;
    }

    /**
     * Send raw data to the server.
     *
     * @param string $data The data to send
     *
     * @return int|bool The number of bytes sent to the server or false on error
     */
    public function client_send($data)
    {
        $this->edebug("CLIENT -> SERVER: $data", self::DEBUG_CLIENT);
        set_error_handler([$this, 'errorHandler']);
        $result = fwrite($this->socket, $data);
        restore_error_handler();

        return $result;
    }

    /**
     * Get the current error.
     *
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Get the last reply from the server.
     *
     * @return string
     */
    public function getLastReply()
    {
        return $this->last_reply;
    }

    /**
     * Read the SMTP server's response.
     * Either before eof or socket timeout occurs.
     *
     * @return string
     */
    protected function get_lines()
    {
        // If the connection is bad, give up straight away
        if (!is_resource($this->socket)) {
            return '';
        }
        $data = '';
        $endtime = 0;
        stream_set_timeout($this->socket, $this->Timeout);
        if ($this->Timelimit > 0) {
            $endtime = time() + $this->Timelimit;
        }
        $selR = [$this->socket];
        $selW = null;
        while (is_resource($this->socket) && !feof($this->socket)) {
            //Must pass vars in here as params are by reference
            if (!stream_select($selR, $selW, $selW, $this->Timelimit)) {
                $this->edebug('SMTP -> get_lines(): timed-out (' . $this->Timeout . ' sec)', self::DEBUG_LOWLEVEL);
                break;
            }
            //Deliberately does not use fgets() here to allow multiple calls
            $str = @fread($this->socket, 515);
            $this->edebug('SMTP INBOUND: "' . trim($str) . '"', self::DEBUG_LOWLEVEL);
            $data .= $str;
            // If response is only 3 chars (not valid, but RFC5321 says it must be handled),
            // or 4th character is a space, we are done reading, break the loop.
            // String could be longer than 4 chars if multi-line responses are received
            if ((strlen($str) <= 4) || (strlen($str) > 4 && $str[3] === ' ')) {
                break;
            }
            // Timed-out? Log and break
            if ($endtime && time() > $endtime) {
                $this->edebug('SMTP -> get_lines(): timelimit exceeded (' . $this->Timelimit . ' sec)', self::DEBUG_LOWLEVEL);
                break;
            }
        }

        return $data;
    }

    /**
     * Enable or disable VERP (Variable Envelope Return Path) for sending mail.
     *
     * @param bool $enabled
     */
    public function setVerp($enabled = false)
    {
        $this->do_verp = $enabled;
    }

    /**
     * Get VERP (Variable Envelope Return Path) for sending mail.
     *
     * @return bool
     */
    public function getVerp()
    {
        return $this->do_verp;
    }

    /**
     * Set error messages and codes.
     *
     * @param string $message      The error message
     * @param string $detail       Further detail on the error
     * @param string $smtp_code    An associated SMTP error code
     * @param string $smtp_code_ex Extended SMTP error info
     */
    protected function setError($message, $detail = '', $smtp_code = '', $smtp_code_ex = '')
    {
        $this->error = [
            'error' => $message,
            'detail' => $detail,
            'smtp_code' => $smtp_code,
            'smtp_code_ex' => $smtp_code_ex
        ];
    }

    /**
     * Set debug output method.
     *
     * @param string|callable $method The name of the mechanism to use for debugging output
     */
    public function setDebugOutput($method = 'echo')
    {
        $this->Debugoutput = $method;
    }

    /**
     * Get debug output method.
     *
     * @return string
     */
    public function getDebugOutput()
    {
        return $this->Debugoutput;
    }

    /**
     * Set debug verbosity level.
     *
     * @param int $level
     */
    public function setDebugLevel($level = 0)
    {
        $this->SMTPDebug = $level;
    }

    /**
     * Get debug verbosity level.
     *
     * @return int
     */
    public function getDebugLevel()
    {
        return $this->SMTPDebug;
    }

    /**
     * Get the last transaction ID.
     *
     * @return string
     */
    public function getLastTransactionID()
    {
        return '';
    }

    /**
     * Parse server capabilities from EHLO response.
     */
    protected function parseHelloFields()
    {
        $this->server_caps = [];
        $lines = explode("\n", $this->last_reply);

        foreach ($lines as $n => $s) {
            // First line is the response code followed by the greeting
            if ($n === 0) {
                continue;
            }
            $s = trim(substr($s, 4));
            if (empty($s)) {
                continue;
            }
            $fields = explode(' ', $s);
            if (!empty($fields)) {
                if (!$n) {
                    $name = $fields[0];
                    $fields = $fields[1] ?? [];
                } else {
                    $name = $fields[0];
                    unset($fields[0]);
                    $fields = array_values($fields);
                }
                $this->server_caps[$name] = $fields;
            }
        }
    }

    /**
     * Check connection state.
     *
     * @return bool
     */
    public function connected()
    {
        if (is_resource($this->socket)) {
            $sock_status = stream_get_meta_data($this->socket);
            if ($sock_status['eof']) {
                // The socket is valid but we are at the end of the stream
                $this->edebug('SMTP -> get_lines(): EOF received from server', self::DEBUG_SERVER);
                return false;
            }

            return true; // Everything looks good
        }

        return false;
    }

    /**
     * Close the socket and clean up the state of the class.
     * Don't use this function without first trying to use QUIT.
     *
     * @return void
     */
    public function close()
    {
        $this->setError('');
        $this->server_caps = null;
        $this->helo_rply = null;
        if (is_resource($this->socket)) {
            fclose($this->socket);
            $this->socket = null;
            $this->edebug('Connection: closed', self::DEBUG_CONNECTION);
        }
    }

    /**
     * Send an SMTP NOOP command.
     * Used to keep keep-alives alive, doesn't actually do anything.
     *
     * @return bool
     */
    public function noop()
    {
        return $this->sendCommand('NOOP', 'NOOP', 250);
    }

    /**
     * Send an SMTP TURN command.
     * This is an optional command for SMTP that this class does not support.
     * This method is here to make the RFC821 Definition complete for this class
     * and _may_ be implemented in future
     * Implements from RFC 821: TURN <CRLF>
     *
     * @return bool
     */
    public function turn()
    {
        $this->setError('The SMTP TURN command is not implemented');
        $this->edebug('SMTP NOTICE: ' . $this->error['error'], self::DEBUG_CLIENT);

        return false;
    }

    /**
     * Get SMTP extension options.
     *
     * @param string $name
     *
     * @return array
     */
    public function getServerExt($name)
    {
        if (!$this->server_caps) {
            $this->setError('No HELO/EHLO was sent');

            return null;
        }

        // the tight logic knot
        if (!array_key_exists($name, $this->server_caps)) {
            if ('HELO' === $name) {
                return [[$name, $this->server_caps['EHLO'][0]]];
            }
            if ('EHLO' === $name || array_key_exists('EHLO', $this->server_caps)) {
                return null;
            }
            $this->setError('HELO handshake was used. Client knows nothing about server extensions');

            return null;
        }

        return $this->server_caps[$name];
    }

    /**
     * Get SMTP extension list.
     *
     * @return array
     */
    public function getServerExtList()
    {
        return array_keys($this->server_caps);
    }

    /**
     * Set SMTP extension options.
     *
     * @param string $name
     * @param array  $value
     */
    public function setServerExt($name, $value)
    {
        $this->server_caps[$name] = $value;
    }

    /**
     * Get SMTP connection mode.
     *
     * @return string
     */
    public function getConnectionMode()
    {
        if ($this->isTLS()) {
            return 'tls';
        }
        if ($this->isSSL()) {
            return 'ssl';
        }

        return 'plain';
    }

    /**
     * Check if the connection is using SSL.
     *
     * @return bool
     */
    public function isSSL()
    {
        return 'ssl' === $this->SMTPSecure;
    }

    /**
     * Check if the connection is using TLS.
     *
     * @return bool
     */
    public function isTLS()
    {
        return 'tls' === $this->SMTPSecure;
    }

    /**
     * Get the SMTP server timeout.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->Timeout;
    }

    /**
     * Set the SMTP server timeout.
     *
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->Timeout = $timeout;
    }

    /**
     * Get the SMTP timelimit.
     *
     * @return int
     */
    public function getTimelimit()
    {
        return $this->Timelimit;
    }

    /**
     * Set the SMTP timelimit.
     *
     * @param int $timelimit
     */
    public function setTimelimit($timelimit)
    {
        $this->Timelimit = $timelimit;
    }

    /**
     * Get the current error.
     *
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Get the last reply from the server.
     *
     * @return string
     */
    public function getLastReply()
    {
        return $this->last_reply;
    }

    /**
     * Read the SMTP server's response.
     * Either before eof or socket timeout occurs.
     *
     * @return string
     */
    protected function get_lines()
    {
        // If the connection is bad, give up straight away
        if (!is_resource($this->socket)) {
            return '';
        }
        $data = '';
        $endtime = 0;
        stream_set_timeout($this->socket, $this->Timeout);
        if ($this->Timelimit > 0) {
            $endtime = time() + $this->Timelimit;
        }
        $selR = [$this->socket];
        $selW = null;
        while (is_resource($this->socket) && !feof($this->socket)) {
            //Must pass vars in here as params are by reference
            if (!stream_select($selR, $selW, $selW, $this->Timelimit)) {
                $this->edebug('SMTP -> get_lines(): timed-out (' . $this->Timeout . ' sec)', self::DEBUG_LOWLEVEL);
                break;
            }
            //Deliberately does not use fgets() here to allow multiple calls
            $str = @fread($this->socket, 515);
            $this->edebug('SMTP INBOUND: "' . trim($str) . '"', self::DEBUG_LOWLEVEL);
            $data .= $str;
            // If response is only 3 chars (not valid, but RFC5321 says it must be handled),
            // or 4th character is a space, we are done reading, break the loop.
            // String could be longer than 4 chars if multi-line responses are received
            if ((strlen($str) <= 4) || (strlen($str) > 4 && $str[3] === ' ')) {
                break;
            }
            // Timed-out? Log and break
            if ($endtime && time() > $endtime) {
                $this->edebug('SMTP -> get_lines(): timelimit exceeded (' . $this->Timelimit . ' sec)', self::DEBUG_LOWLEVEL);
                break;
            }
        }

        return $data;
    }

    /**
     * Enable or disable VERP (Variable Envelope Return Path) for sending mail.
     *
     * @param bool $enabled
     */
    public function setVerp($enabled = false)
    {
        $this->do_verp = $enabled;
    }

    /**
     * Get VERP (Variable Envelope Return Path) for sending mail.
     *
     * @return bool
     */
    public function getVerp()
    {
        return $this->do_verp;
    }

    /**
     * Set error messages and codes.
     *
     * @param string $message      The error message
     * @param string $detail       Further detail on the error
     * @param string $smtp_code    An associated SMTP error code
     * @param string $smtp_code_ex Extended SMTP error info
     */
    protected function setError($message, $detail = '', $smtp_code = '', $smtp_code_ex = '')
    {
        $this->error = [
            'error' => $message,
            'detail' => $detail,
            'smtp_code' => $smtp_code,
            'smtp_code_ex' => $smtp_code_ex
        ];
    }

    /**
     * Set debug output method.
     *
     * @param string|callable $method The name of the mechanism to use for debugging output
     */
    public function setDebugOutput($method = 'echo')
    {
        $this->Debugoutput = $method;
    }

    /**
     * Get debug output method.
     *
     * @return string
     */
    public function getDebugOutput()
    {
        return $this->Debugoutput;
    }

    /**
     * Set debug verbosity level.
     *
     * @param int $level
     */
    public function setDebugLevel($level = 0)
    {
        $this->SMTPDebug = $level;
    }

    /**
     * Get debug verbosity level.
     *
     * @return int
     */
    public function getDebugLevel()
    {
        return $this->SMTPDebug;
    }

    /**
     * Get the last transaction ID.
     *
     * @return string
     */
    public function getLastTransactionID()
    {
        return '';
    }

    /**
     * Parse server capabilities from EHLO response.
     */
    protected function parseHelloFields()
    {
        $this->server_caps = [];
        $lines = explode("\n", $this->last_reply);

        foreach ($lines as $n => $s) {
            // First line is the response code followed by the greeting
            if ($n === 0) {
                continue;
            }
            $s = trim(substr($s, 4));
            if (empty($s)) {
                continue;
            }
            $fields = explode(' ', $s);
            if (!empty($fields)) {
                if (!$n) {
                    $name = $fields[0];
                    $fields = $fields[1] ?? [];
                } else {
                    $name = $fields[0];
                    unset($fields[0]);
                    $fields = array_values($fields);
                }
                $this->server_caps[$name] = $fields;
            }
        }
    }

    /**
     * Check connection state.
     *
     * @return bool
     */
    public function connected()
    {
        if (is_resource($this->socket)) {
            $sock_status = stream_get_meta_data($this->socket);
            if ($sock_status['eof']) {
                // The socket is valid but we are at the end of the stream
                $this->edebug('SMTP -> get_lines(): EOF received from server', self::DEBUG_SERVER);
                return false;
            }

            return true; // Everything looks good
        }

        return false;
    }

    /**
     * Close the socket and clean up the state of the class.
     * Don't use this function without first trying to use QUIT.
     *
     * @return void
     */
    public function close()
    {
        $this->setError('');
        $this->server_caps = null;
        $this->helo_rply = null;
        if (is_resource($this->socket)) {
            fclose($this->socket);
            $this->socket = null;
            $this->edebug('Connection: closed', self::DEBUG_CONNECTION);
        }
    }

    /**
     * Send an SMTP NOOP command.
     * Used to keep keep-alives alive, doesn't actually do anything.
     *
     * @return bool
     */
    public function noop()
    {
        return $this->sendCommand('NOOP', 'NOOP', 250);
    }

    /**
     * Send an SMTP TURN command.
     * This is an optional command for SMTP that this class does not support.
     * This method is here to make the RFC821 Definition complete for this class
     * and _may_ be implemented in future
     * Implements from RFC 821: TURN <CRLF>
     *
     * @return bool
     */
    public function turn()
    {
        $this->setError('The SMTP TURN command is not implemented');
        $this->edebug('SMTP NOTICE: ' . $this->error['error'], self::DEBUG_CLIENT);

        return false;
    }

    /**
     * Get SMTP extension options.
     *
     * @param string $name
     *
     * @return array
     */
    public function getServerExt($name)
    {
        if (!$this->server_caps) {
            $this->setError('No HELO/EHLO was sent');

            return null;
        }

        // the tight logic knot
        if (!array_key_exists($name, $this->server_caps)) {
            if ('HELO' === $name) {
                return [[$name, $this->server_caps['EHLO'][0]]];
            }
            if ('EHLO' === $name || array_key_exists('EHLO', $this->server_caps)) {
                return null;
            }
            $this->setError('HELO handshake was used. Client knows nothing about server extensions');

            return null;
        }

        return $this->server_caps[$name];
    }

    /**
     * Get SMTP extension list.
     *
     * @return array
     */
    public function getServerExtList()
    {
        return array_keys($this->server_caps);
    }

    /**
     * Set SMTP extension options.
     *
     * @param string $name
     * @param array  $value
     */
    public function setServerExt($name, $value)
    {
        $this->server_caps[$name] = $value;
    }

    /**
     * Get SMTP connection mode.
     *
     * @return string
     */
    public function getConnectionMode()
    {
        if ($this->isTLS()) {
            return 'tls';
        }
        if ($this->isSSL()) {
            return 'ssl';
        }

        return 'plain';
    }

    /**
     * Check if the connection is using SSL.
     *
     * @return bool
     */
    public function isSSL()
    {
        return 'ssl' === $this->SMTPSecure;
    }

    /**
     * Check if the connection is using TLS.
     *
     * @return bool
     */
    public function isTLS()
    {
        return 'tls' === $this->SMTPSecure;
    }

    /**
     * Get the SMTP server timeout.
     *
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->Timeout = $timeout;
    }

    /**
     * Get the SMTP timelimit.
     *
     * @return int
     */
    public function getTimelimit()
    {
        return $this->Timelimit;
    }

    /**
     * Set the SMTP timelimit.
     *
     * @param int $timelimit
     */
    public function setTimelimit($timelimit)
    {
        $this->Timelimit = $timelimit;
    }

    /**
     * Debug output method.
     *
     * @param string $str
     * @param int    $level
     */
    protected function edebug($str, $level = 0)
    {
        if ($this->SMTPDebug >= $level) {
            if ($this->Debugoutput instanceof \Closure) {
                call_user_func($this->Debugoutput, $str, $level);
            } elseif (is_callable($this->Debugoutput) && !in_array($this->Debugoutput, ['echo', 'error_log'])) {
                call_user_func($this->Debugoutput, $str, $level);
            } elseif ($this->Debugoutput === 'error_log') {
                error_log($str);
            } else {
                echo $str . "\n";
            }
        }
    }

    /**
     * Error handler callback.
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     */
    protected function errorHandler($errno, $errstr, $errfile = '', $errline = 0)
    {
        $this->setError(
            'Connection failed: ' . $errstr,
            '',
            (string) $errno
        );
        $this->edebug(
            'SMTP connect() failed: ' . $errstr,
            self::DEBUG_CONNECTION
        );
    }

    /**
     * Get the current error.
     *
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Get the last reply from the server.
     *
     * @return string
     */
    public function getLastReply()
    {
        return $this->last_reply;
    }

    /**
     * Read the SMTP server's response.
     * Either before eof or socket timeout occurs.
     *
     * @return string
     */
    protected function get_lines()
    {
        // If the connection is bad, give up straight away
        if (!is_resource($this->socket)) {
            return '';
        }
        $data = '';
        $endtime = 0;
        stream_set_timeout($this->socket, $this->Timeout);
        if ($this->Timelimit > 0) {
            $endtime = time() + $this->Timelimit;
        }
        $selR = [$this->socket];
        $selW = null;
        while (is_resource($this->socket) && !feof($this->socket)) {
            //Must pass vars in here as params are by reference
            if (!stream_select($selR, $selW, $selW, $this->Timelimit)) {
                $this->edebug('SMTP -> get_lines(): timed-out (' . $this->Timeout . ' sec)', self::DEBUG_LOWLEVEL);
                break;
            }
            //Deliberately does not use fgets() here to allow multiple calls
            $str = @fread($this->socket, 515);
            $this->edebug('SMTP INBOUND: "' . trim($str) . '"', self::DEBUG_LOWLEVEL);
            $data .= $str;
            // If response is only 3 chars (not valid, but RFC5321 says it must be handled),
            // or 4th character is a space, we are done reading, break the loop.
            // String could be longer than 4 chars if multi-line responses are received
            if ((strlen($str) <= 4) || (strlen($str) > 4 && $str[3] === ' ')) {
                break;
            }
            // Timed-out? Log and break
            if ($endtime && time() > $endtime) {
                $this->edebug('SMTP -> get_lines(): timelimit exceeded (' . $this->Timelimit . ' sec)', self::DEBUG_LOWLEVEL);
                break;
            }
        }

        return $data;
    }

    /**
     * Enable or disable VERP (Variable Envelope Return Path) for sending mail.
     *
     * @param bool $enabled
     */
    public function setVerp($enabled = false)
    {
        $this->do_verp = $enabled;
    }

    /**
     * Get VERP (Variable Envelope Return Path) for sending mail.
     *
     * @return bool
     */
    public function getVerp()
    {
        return $this->do_verp;
    }

    /**
     * Set error messages and codes.
     *
     * @param string $message      The error message
     * @param string $detail       Further detail on the error
     * @param string $smtp_code    An associated SMTP error code
     * @param string $smtp_code_ex Extended SMTP error info
     */
    protected function setError($message, $detail = '', $smtp_code = '', $smtp_code_ex = '')
    {
        $this->error = [
            'error' => $message,
            'detail' => $detail,
            'smtp_code' => $smtp_code,
            'smtp_code_ex' => $smtp_code_ex
        ];
    }

    /**
     * Set debug output method.
     *
     * @param string|callable $method The name of the mechanism to use for debugging output
     */
    public function setDebugOutput($method = 'echo')
    {
        $this->Debugoutput = $method;
    }

    /**
     * Get debug output method.
     *
     * @return string
     */
    public function getDebugOutput()
    {
        return $this->Debugoutput;
    }

    /**
     * Set debug verbosity level.
     *
     * @param int $level
     */
    public function setDebugLevel($level = 0)
    {
        $this->SMTPDebug = $level;
    }

    /**
     * Get debug verbosity level.
     *
     * @return int
     */
    public function getDebugLevel()
    {
        return $this->SMTPDebug;
    }

    /**
     * Get the last transaction ID.
     *
     * @return string
     */
    public function getLastTransactionID()
    {
        return '';
    }

    /**
     * Parse server capabilities from EHLO response.
     */
    protected function parseHelloFields()
    {
        $this->server_caps = [];
        $lines = explode("\n", $this->last_reply);

        foreach ($lines as $n => $s) {
            // First line is the response code followed by the greeting
            if ($n === 0) {
                continue;
            }
            $s = trim(substr($s, 4));
            if (empty($s)) {
                continue;
            }
            $fields = explode(' ', $s);
            if (!empty($fields)) {
                if (!$n) {
                    $name = $fields[0];
                    $fields = $fields[1] ?? [];
                } else {
                    $name = $fields[0];
                    unset($fields[0]);
                    $fields = array_values($fields);
                }
                $this->server_caps[$name] = $fields;
            }
        }
    }

    /**
     * Check connection state.
     *
     * @return bool
     */
    public function connected()
    {
        if (is_resource($this->socket)) {
            $sock_status = stream_get_meta_data($this->socket);
            if ($sock_status['eof']) {
                // The socket is valid but we are at the end of the stream
                $this->edebug('SMTP -> get_lines(): EOF received from server', self::DEBUG_SERVER);
                return false;
            }

            return true; // Everything looks good
        }

        return false;
    }

    /**
     * Close the socket and clean up the state of the class.
     * Don't use this function without first trying to use QUIT.
     *
     * @return void
     */
    public function close()
    {
        $this->setError('');
        $this->server_caps = null;
        $this->helo_rply = null;
        if (is_resource($this->socket)) {
            fclose($this->socket);
            $this->socket = null;
            $this->edebug('Connection: closed', self::DEBUG_CONNECTION);
        }
    }

    /**
     * Send an SMTP NOOP command.
     * Used to keep keep-alives alive, doesn't actually do anything.
     *
     * @return bool
     */
    public function noop()
    {
        return $this->sendCommand('NOOP', 'NOOP', 250);
    }

    /**
     * Send an SMTP TURN command.
     * This is an optional command for SMTP that this class does not support.
     * This method is here to make the RFC821 Definition complete for this class
     * and _may_ be implemented in future
     * Implements from RFC 821: TURN <CRLF>
     *
     * @return bool
     */
    public function turn()
    {
        $this->setError('The SMTP TURN command is not implemented');
        $this->edebug('SMTP NOTICE: ' . $this->error['error'], self::DEBUG_CLIENT);

        return false;
    }

    /**
     * Get SMTP extension options.
     *
     * @param string $name
     *
     * @return array
     */
    public function getServerExt($name)
    {
        if (!$this->server_caps) {
            $this->setError('No HELO/EHLO was sent');

            return null;
        }

        // the tight logic knot
        if (!array_key_exists($name, $this->server_caps)) {
            if ('HELO' === $name) {
                return [[$name, $this->server_caps['EHLO'][0]]];
            }
            if ('EHLO' === $name || array_key_exists('EHLO', $this->server_caps)) {
                return null;
            }
            $this->setError('HELO handshake was used. Client knows nothing about server extensions');

            return null;
        }

        return $this->server_caps[$name];
    }

    /**
     * Get SMTP extension list.
     *
     * @return array
     */
    public function getServerExtList()
    {
        return array_keys($this->server_caps);
    }

    /**
     * Set SMTP extension options.
     *
     * @param string $name
     * @param array  $value
     */
    public function setServerExt($name, $value)
    {
        $this->server_caps[$name] = $value;
    }

    /**
     * Get SMTP connection mode.
     *
     * @return string
     */
    public function getConnectionMode()
    {
        if ($this->isTLS()) {
            return 'tls';
        }
        if ($this->isSSL()) {
            return 'ssl';
        }

        return 'plain';
    }

    /**
     * Check if the connection is using SSL.
     *
     * @return bool
     */
    public function isSSL()
    {
        return 'ssl' === $this->SMTPSecure;
    }

    /**
     * Check if the connection is using TLS.
     *
     * @return bool
     */
    public function isTLS()
    {
        return 'tls' === $this->SMTPSecure;
    }

    /**
     * Get the SMTP server timeout.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->Timeout;
    }

    /**
     * Set the SMTP server timeout.
     *
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->Timeout = $timeout;
    }

    /**
     * Get the SMTP timelimit.
     *
     * @return int
     */
    public function getTimelimit()
    {
        return $this->Timelimit;
    }

    /**
     * Set the SMTP timelimit.
     *
     * @param int $timelimit
     */
    public function setTimelimit($timelimit)
    {
        $this->Timelimit = $timelimit;
    }

    /**
     * Debug output method.
     *
     * @param string $str
     * @param int    $level
     */
    protected function edebug($str, $level = 0)
    {
        if ($this->SMTPDebug >= $level) {
            if ($this->Debugoutput instanceof \Closure) {
                call_user_func($this->Debugoutput, $str, $level);
            } elseif (is_callable($this->Debugoutput) && !in_array($this->Debugoutput, ['echo', 'error_log'])) {
                call_user_func($this->Debugoutput, $str, $level);
            } elseif ($this->Debugoutput === 'error_log') {
                error_log($str);
            } else {
                echo $str . "\n";
            }
        }
    }

    /**
     * Error handler callback.
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     */
    protected function errorHandler($errno, $errstr, $errfile = '', $errline = 0)
    {
        $this->setError(
            'Connection failed: ' . $errstr,
            '',
            (string) $errno
        );
        $this->edebug(
            'SMTP connect() failed: ' . $errstr,
            self::DEBUG_CONNECTION
        );
    }

    // Debug levels
    const DEBUG_OFF = 0;
    const DEBUG_CLIENT = 1;
    const DEBUG_SERVER = 2;
    const DEBUG_CONNECTION = 3;
    const DEBUG_LOWLEVEL = 4;

    // Error properties
    protected $error = [];
    protected $server_caps = [];
    protected $helo_rply = null;
    protected $do_verp = false;
    protected $Timelimit = 30;
}

