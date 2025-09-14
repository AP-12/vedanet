<?php
/**
 * PHPMailer - PHP email creation and transport class.
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
 * PHPMailer - PHP email creation and transport class.
 *
 * @author  Marcus Bointon <phpmailer@synchromedia.co.uk>
 * @author  Jim Jagielski <jimjag@gmail.com>
 * @author  Andy Prevost <codeworxtech@users.sourceforge.net>
 * @author  Brent R. Matzelle <original.author@mvpi.net>
 */
class PHPMailer
{
    /**
     * Email priority.
     * Options: null (default), 1 = High, 3 = Normal, 5 = low.
     * When null, the header is not set at all.
     *
     * @var int
     */
    public $Priority;

    /**
     * The character set of the message.
     *
     * @var string
     */
    public $CharSet = 'iso-8859-1';

    /**
     * The MIME Content-type of the message.
     *
     * @var string
     */
    public $ContentType = 'text/plain';

    /**
     * The message encoding.
     * Options: "8bit", "7bit", "binary", "base64", and "quoted-printable".
     *
     * @var string
     */
    public $Encoding = '8bit';

    /**
     * Holds the most recent mailer error message.
     *
     * @var string
     */
    public $ErrorInfo = '';

    /**
     * The From email address for the message.
     *
     * @var string
     */
    public $From = 'root@localhost';

    /**
     * The From name of the message.
     *
     * @var string
     */
    public $FromName = 'Root User';

    /**
     * The envelope sender of the message.
     * This will usually be turned into a Return-Path header by the receiver,
     * and is the address that bounces will be sent to.
     * If not empty, will be passed via `-f` to sendmail or as the 'MAIL FROM' value over SMTP.
     *
     * @var string
     */
    public $Sender = '';

    /**
     * The Subject of the message.
     *
     * @var string
     */
    public $Subject = '';

    /**
     * An HTML or plain text message body.
     * If HTML then call isHTML(true).
     *
     * @var string
     */
    public $Body = '';

    /**
     * The plain-text message body.
     * This body can be read by mail clients that do not have HTML email
     * capability such as mutt & Eudora.
     * Clients that can read HTML will view the normal Body.
     *
     * @var string
     */
    public $AltBody = '';

    /**
     * Stores the complete compiled MIME message body.
     *
     * @var string
     */
    protected $MIMEBody = '';

    /**
     * Stores the complete compiled MIME message headers.
     *
     * @var string
     */
    protected $MIMEHeader = '';

    /**
     * Stores the complete compiled MIME message headers and body.
     *
     * @var string
     */
    protected $mailHeader = '';

    /**
     * An instance of the SMTP sender class.
     *
     * @var SMTP
     */
    protected $smtp;

    /**
     * The path to a log file the transport should use.
     *
     * @var string
     */
    protected $logFile = '';

    /**
     * The log level the transport should use.
     *
     * @var int
     */
    protected $logLevel = 1;

    /**
     * Constructor.
     *
     * @param bool $exceptions Should we throw external exceptions?
     */
    public function __construct($exceptions = null)
    {
        $this->smtp = new SMTP();
        if ($exceptions !== null) {
            $this->exceptions = (bool) $exceptions;
        }
    }

    /**
     * Send an email using the $Sendmail or $Mailer mailer.
     *
     * @return bool
     */
    public function send()
    {
        try {
            if (!$this->preSend()) {
                return false;
            }
            return $this->postSend();
        } catch (Exception $exc) {
            $this->mailHeader = '';
            $this->setError($exc->getMessage());
            if ($this->exceptions) {
                throw $exc;
            }

            return false;
        }
    }

    /**
     * Prepare and send an email via SMTP.
     *
     * @param string $header The message headers
     * @param string $body   The message body
     *
     * @return bool
     */
    public function smtpSend($header, $body)
    {
        if (!$this->smtp->connect($this->Host, $this->Port, $this->Timeout, $this->SMTPOptions)) {
            throw new Exception($this->lang('smtp_connect_failed'), self::STOP_CRITICAL);
        }

        if (!$this->smtp->hello($this->getLocalHostname())) {
            throw new Exception($this->lang('smtp_connect_failed'), self::STOP_CRITICAL);
        }

        $hello = $this->smtp->getServerExtList();
        $noAuth = $this->smtpAuth($hello);

        $this->smtp->mail($this->From);
        if (!$this->smtp->recipient($this->to[0][0])) {
            throw new Exception($this->lang('smtp_recipient_failed'), self::STOP_CRITICAL);
        }
        if (!$this->smtp->data($header . $body)) {
            throw new Exception($this->lang('smtp_data_not_accepted'), self::STOP_CRITICAL);
        }

        if ($this->SMTPKeepAlive) {
            $this->smtp->reset();
        } else {
            $this->smtp->quit();
            $this->smtp->close();
        }

        return true;
    }

    /**
     * Perform SMTP authentication.
     *
     * @param array $capabilities An array of SMTP capabilities
     *
     * @return bool
     */
    protected function smtpAuth($capabilities)
    {
        if (!$this->SMTPAuth) {
            return true;
        }

        if (!$this->smtp->authenticate($this->Username, $this->Password, $this->AuthType)) {
            throw new Exception($this->lang('smtp_auth_failed'), self::STOP_CRITICAL);
        }

        return true;
    }

    /**
     * Set the From and FromName properties.
     *
     * @param string $address
     * @param string $name
     * @param bool   $auto    Whether to also set the Sender address, defaults to true
     *
     * @return bool
     */
    public function setFrom($address, $name = '', $auto = true)
    {
        $address = trim($address);
        $name = trim(preg_replace('/[\r\n]+/', '', $name)); // Strip breaks and trim

        if (!self::validateAddress($address)) {
            $error_message = $this->lang('invalid_address') . " (setFrom) $address";
            $this->setError($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }

            return false;
        }

        $this->From = $address;
        $this->FromName = $name;

        if ($auto) {
            if (empty($this->Sender)) {
                $this->Sender = $address;
            }
        }

        return true;
    }

    /**
     * Add a "To" address.
     *
     * @param string $address The email address to send to
     * @param string $name
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addAddress($address, $name = '')
    {
        return $this->addAnAddress('to', $address, $name);
    }

    /**
     * Add an address to one of the recipient arrays.
     * Addresses that have been added already return false, but do not throw exceptions.
     *
     * @param string $kind    One of 'to', 'cc', 'bcc', 'ReplyTo'
     * @param string $address The email address to send, resp. to reply to
     * @param string $name
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    protected function addAnAddress($kind, $address, $name = '')
    {
        if (!in_array($kind, ['to', 'cc', 'bcc', 'ReplyTo'])) {
            $error_message = $this->lang('invalid_address_kind') . ': ' . $kind;
            $this->setError($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }

            return false;
        }
        $address = trim($address);
        $name = trim(preg_replace('/[\r\n]+/', '', $name)); // Strip breaks and trim

        if (!self::validateAddress($address)) {
            $error_message = $this->lang('invalid_address') . " (addAnAddress $kind): $address";
            $this->setError($error_message);
            if ($this->exceptions) {
                throw new Exception($error_message);
            }

            return false;
        }

        if ($kind !== 'ReplyTo') {
            if (!isset($this->$kind)) {
                $this->$kind = [];
            }
            if (!array_key_exists($address, $this->$kind)) {
                $this->{$kind}[$address] = $name;
            }
        } else {
            if (!array_key_exists($address, $this->ReplyTo)) {
                $this->ReplyTo[$address] = $name;
            }
        }

        return true;
    }

    /**
     * Check that a string looks like an email address.
     *
     * @param string $address The email address to check
     * @param string $patternselect A selector for the validation pattern to use :
     *                              'auto' - pick best one automatically;
     *                              'pcre8' - use the squiloople.com pattern, requires PCRE > 8.0;
     *                              'pcre' - use old PCRE implementation;
     *                              'php' - use PHP built-in FILTER_VALIDATE_EMAIL;
     *                              'noregex' - don't use a regex: super fast, really dumb.
     *
     * @return bool
     * @static
     */
    public static function validateAddress($address, $patternselect = null)
    {
        if (null === $patternselect) {
            $patternselect = 'auto';
        }

        if (!$address) {
            return false;
        }

        // Check that $address is a valid address. Read the following RFCs to understand the mail address syntax:
        // http://tools.ietf.org/html/rfc3696 section 3
        // http://tools.ietf.org/html/rfc5322#section-3.2.3
        // http://tools.ietf.org/html/rfc5322#section-3.4.1
        // http://tools.ietf.org/html/rfc3696 section 2
        // One way to validate whether an email address is as valid as possible is to actually send a message.
        // That's the only way to really validate an email address.

        switch ($patternselect) {
            case 'pcre8':
                /**
                 * Uses the same RFC5322 regex on which FILTER_VALIDATE_EMAIL is based, but allows dotless domains.
                 * @see       http://squiloople.com/2009/12/20/email-validation/
                 * @copyright 2009-2010 Michael Rushton
                 * Feel free to use and redistribute this code. But please keep this copyright notice.
                 */
                return (bool) preg_match(
                    '/^(?!(?>(?1)"?(?>\\\[ -~]|[^"])"?(?1))*$(?<-1>))' .
                    '((?>[^()<>@,;:\\".\[\] ]+(?:\.[^()<>@,;:\\".\[\] ]+)*)|(?0))' .
                    '@((?>[^()<>@,;:\\".\[\] ]+(?:\.[^()<>@,;:\\".\[\] ]+)*)|(?0))$/',
                    $address
                );
            case 'pcre':
                // An older regex that doesn't need a recent PCRE
                return (bool) preg_match(
                    '/^(?!(?>"?(?>\\\[ -~]|[^"])"?(?1))*$(?<-1>))' .
                    '((?>[^()<>@,;:\\".\[\] ]+(?:\.[^()<>@,;:\\".\[\] ]+)*)|(?0))' .
                    '@((?>[^()<>@,;:\\".\[\] ]+(?:\.[^()<>@,;:\\".\[\] ]+)*)|(?0))$/',
                    $address
                );
            case 'php':
            default:
                return (bool) filter_var($address, FILTER_VALIDATE_EMAIL);
        }
    }

    /**
     * Set the subject of the message.
     *
     * @param string $subject
     *
     * @return bool
     */
    public function setSubject($subject)
    {
        $this->Subject = $subject;

        return true;
    }

    /**
     * Set the body of the message.
     *
     * @param string $body
     *
     * @return bool
     */
    public function setBody($body)
    {
        $this->Body = $body;

        return true;
    }

    /**
     * Set the alternative body of the message.
     *
     * @param string $body
     *
     * @return bool
     */
    public function setAltBody($body)
    {
        $this->AltBody = $body;

        return true;
    }

    /**
     * Set the message type to HTML.
     *
     * @param bool $ishtml
     *
     * @return void
     */
    public function isHTML($ishtml = true)
    {
        if ($ishtml) {
            $this->ContentType = 'text/html';
        } else {
            $this->ContentType = 'text/plain';
        }
    }

    /**
     * Prepare and send the email.
     *
     * @return bool
     */
    protected function preSend()
    {
        $this->mailHeader = '';

        // Set the From address if not already set
        if (empty($this->From)) {
            $this->From = 'root@localhost';
        }

        // Set the From name if not already set
        if (empty($this->FromName)) {
            $this->FromName = 'Root User';
        }

        // Set the Sender if not already set
        if (empty($this->Sender)) {
            $this->Sender = $this->From;
        }

        // Create the message
        $this->createHeader();
        $this->createBody();

        return true;
    }

    /**
     * Create the message header.
     *
     * @return void
     */
    protected function createHeader()
    {
        $result = '';

        // Set the boundaries
        $uniq_id = md5(uniqid(time()));
        $this->boundary[1] = 'b1_' . $uniq_id;
        $this->boundary[2] = 'b2_' . $uniq_id;

        $result .= $this->headerLine('Date', self::rfcDate());
        if ($this->Sender !== '') {
            $result .= $this->headerLine('Return-Path', '<' . $this->Sender . '>');
        }

        // To be created
        if ($this->SingleTo) {
            foreach ($this->to as $toaddr) {
                $result .= $this->addrAppend('To', $toaddr);
            }
        } else {
            $result .= $this->addrAppend('To', $this->to);
        }

        $result .= $this->headerLine('From', $this->addrFormat($this->From, $this->FromName));

        if (count($this->cc) > 0) {
            $result .= $this->addrAppend('Cc', $this->cc);
        }

        if (count($this->bcc) > 0) {
            $result .= $this->addrAppend('Bcc', $this->bcc);
        }

        if (count($this->ReplyTo) > 0) {
            $result .= $this->addrAppend('Reply-To', $this->ReplyTo);
        }

        if ($this->Priority !== null) {
            $result .= $this->headerLine('X-Priority', $this->Priority);
        }

        $result .= $this->headerLine('Message-ID', '<' . $uniq_id . '@' . $this->getServerHostname() . '>');

        $result .= $this->headerLine('X-Mailer', 'PHPMailer ' . self::VERSION);

        if ($this->ConfirmReadingTo !== '') {
            $result .= $this->headerLine('Disposition-Notification-To', '<' . $this->ConfirmReadingTo . '>');
        }

        // Add custom headers
        foreach ($this->CustomHeader as $header) {
            $result .= $this->headerLine($header[0], $this->encodeHeader($header[1]));
        }

        if (!$this->sign_key_file) {
            $result .= $this->headerLine('MIME-Version', '1.0');
            $result .= $this->getMailMIME();
        }

        $this->mailHeader = $result;
    }

    /**
     * Create the message body.
     *
     * @return void
     */
    protected function createBody()
    {
        $body = '';

        if ($this->sign_key_file) {
            $body .= $this->getMailMIME() . static::LE;
        }

        $this->setWordWrap();

        $bodyEncoding = $this->Encoding;
        $bodyCharSet = $this->CharSet;

        if ($this->ContentType === 'text/html' && empty($this->AltBody)) {
            $body .= $this->getBoundary($this->boundary[1], $bodyCharSet, '', $bodyEncoding);
            $body .= $this->encodeString($this->Body, $bodyEncoding);
            $body .= static::LE . static::LE;
        } elseif ($this->ContentType === 'text/html' && !empty($this->AltBody)) {
            $body .= $this->getBoundary($this->boundary[1], $bodyCharSet, 'text/html', $bodyEncoding);
            $body .= $this->encodeString($this->Body, $bodyEncoding);
            $body .= static::LE . static::LE;
            $body .= $this->getBoundary($this->boundary[1], $bodyCharSet, 'text/plain', $bodyEncoding);
            $body .= $this->encodeString($this->AltBody, $bodyEncoding);
            $body .= static::LE . static::LE;
        } else {
            $body .= $this->encodeString($this->Body, $bodyEncoding);
        }

        if (!empty($this->attachment)) {
            $body .= static::LE;
            $body .= $this->getBoundary($this->boundary[2], $bodyCharSet, '', $bodyEncoding);
            $body .= $this->encodeString($this->Body, $bodyEncoding);
            $body .= static::LE . static::LE;
        }

        $body .= $this->endBoundary($this->boundary[1]);

        if (!empty($this->attachment)) {
            $body .= $this->endBoundary($this->boundary[2]);
        }

        $this->MIMEBody = $body;
    }

    /**
     * Send the email via SMTP.
     *
     * @return bool
     */
    protected function postSend()
    {
        // Choose the mailer and send through it
        try {
            // Set the mailer
            $this->Mailer = 'smtp';

            // Send via SMTP
            return $this->smtpSend($this->mailHeader, $this->MIMEBody);
        } catch (Exception $exc) {
            $this->setError($exc->getMessage());
            if ($this->exceptions) {
                throw $exc;
            }

            return false;
        }
    }

    /**
     * Set the error message.
     *
     * @param string $msg
     */
    protected function setError($msg)
    {
        $this->ErrorInfo = $msg;
    }

    /**
     * Get the local machine hostname.
     *
     * @return string
     */
    protected function getLocalHostname()
    {
        if (!empty($this->Hostname)) {
            $result = $this->Hostname;
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $result = $_SERVER['SERVER_NAME'];
        } else {
            $result = 'localhost.localdomain';
        }

        return $result;
    }

    /**
     * Get the server hostname.
     *
     * @return string
     */
    protected function getServerHostname()
    {
        $result = 'localhost.localdomain';
        if (!empty($this->Hostname)) {
            $result = $this->Hostname;
        } elseif (isset($_SERVER) && array_key_exists('SERVER_NAME', $_SERVER) && !empty($_SERVER['SERVER_NAME'])) {
            $result = $_SERVER['SERVER_NAME'];
        } elseif (function_exists('gethostname') && gethostname() !== false) {
            $result = gethostname();
        } elseif (php_uname('n') !== false) {
            $result = php_uname('n');
        }

        return $result;
    }

    /**
     * Create a message header line.
     *
     * @param string $name
     * @param string $value
     *
     * @return string
     */
    protected function headerLine($name, $value)
    {
        return $name . ': ' . $value . static::LE;
    }

    /**
     * Format an address for use in a message header.
     *
     * @param string $addr
     * @param string $name
     *
     * @return string
     */
    protected function addrFormat($addr, $name = '')
    {
        if (empty($name)) {
            return $addr;
        }

        return $this->encodeHeader($name) . ' <' . $addr . '>';
    }

    /**
     * Encode a header string to best of Q, B, quoted or none.
     *
     * @param string $str
     * @param string $position
     *
     * @return string
     */
    protected function encodeHeader($str, $position = 'text')
    {
        $matchcount = 0;
        switch (strtolower($position)) {
            case 'phrase':
                if (!preg_match('/[\200-\377]/', $str)) {
                    $encoded = addcslashes($str, "\0..\37\177\\\"");
                    if (($str === $encoded) && !preg_match('/[^A-Za-z0-9!#$%&\'*+\/=?^_`{|}~ -]/', $str)) {
                        return ($encoded);
                    } else {
                        return ("\"$encoded\"");
                    }
                }
                $matchcount = preg_match_all('/[^\040\041\043-\133\135-\176]/', $str, $matches);
                break;
            case 'comment':
                $matchcount = preg_match_all('/[()"]/', $str, $matches);
            case 'text':
            default:
                $matchcount += preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);
                break;
        }

        if ($matchcount === 0) {
            return ($str);
        }

        $maxlen = 75 - 7 - strlen($this->CharSet);
        if ($matchcount > strlen($str) / 3) {
            $encoding = 'B';
            if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                $encoded = $this->encodeB(mb_substr($str, 0, $maxlen - 10, $this->CharSet));
            } else {
                $encoded = $this->encodeB(substr($str, 0, $maxlen - 10));
            }
            $maxlen -= $maxlen % 4;
            $encoded = trim(chunk_split($encoded, $maxlen, "\n"));
        } else {
            $encoding = 'Q';
            $encoded = $this->encodeQ($str, $position);
            $encoded = $this->wrapText($encoded, $maxlen, true);
            $encoded = str_replace('=' . static::LE, "\n", trim($encoded));
        }

        $encoded = preg_replace('/^(.*)$/m', ' =?' . $this->CharSet . "?$encoding?\\1?=", $encoded);
        $encoded = trim(str_replace("\n", static::LE, $encoded));

        return $encoded;
    }

    /**
     * Encode string to RFC2047 quoted-printable.
     *
     * @param string $str
     * @param string $position
     *
     * @return string
     */
    protected function encodeQ($str, $position = 'text')
    {
        $pattern = '';
        $rep = '';
        $encoded = str_replace(["\r", "\n"], '', $str);
        switch (strtolower($position)) {
            case 'phrase':
                $pattern = '^A-Za-z0-9!*+\/ -';
                $rep = '';
                break;
            case 'comment':
                $pattern = '\(\)"';
            case 'text':
            default:
                $pattern = '\000-\011\013\014\016-\037\075\077\137\177-\377' . $pattern;
                $rep = '=';
                break;
        }
        if (preg_match_all("/[{$pattern}]/", $encoded, $matches)) {
            $eqkey = array_search('=', $matches[0]);
            if (false !== $eqkey) {
                unset($matches[0][$eqkey]);
                array_unshift($matches[0], '=');
            }
            foreach (array_unique($matches[0]) as $char) {
                $encoded = str_replace($char, '=' . sprintf('%02X', ord($char)), $encoded);
            }
        }

        return str_replace(' ', '_', $encoded);
    }

    /**
     * Encode string to base64.
     *
     * @param string $str
     *
     * @return string
     */
    protected function encodeB($str)
    {
        $encoded = base64_encode($str);

        return $encoded;
    }

    /**
     * Wraps message for use with mailers that do not automatically perform wrapping
     * and for quoted-printable.
     *
     * @param string $message The message to wrap
     * @param int    $length  The line length to wrap to
     * @param bool   $qp_mode Whether to run in Quoted-Printable mode
     *
     * @return string
     */
    protected function wrapText($message, $length, $qp_mode = false)
    {
        if ($qp_mode) {
            $soft_break = sprintf(' =%s', static::LE);
        } else {
            $soft_break = static::LE;
        }
        $is_utf8 = (strtolower($this->CharSet) === 'utf-8');
        $lelen = strlen(static::LE);
        $crlflen = strlen(static::LE);

        $message = $this->fixEOL($message);
        if (substr($message, -$lelen) === static::LE) {
            $message = substr($message, 0, -$lelen);
        }

        $lines = explode(static::LE, $message);
        $message = '';
        foreach ($lines as $line) {
            $words = explode(' ', $line);
            $buf = '';
            $firstword = true;
            foreach ($words as $word) {
                if ($qp_mode && (strlen($word) > $length)) {
                    $space_left = $length - strlen($buf) - $crlflen;
                    if (!$firstword) {
                        if ($space_left > 20) {
                            $len = $space_left;
                            if ($is_utf8) {
                                $len = $this->utf8CharBoundary($word, $len);
                            } elseif (substr($word, $len - 1, 1) === '=') {
                                --$len;
                            } elseif (substr($word, $len - 2, 1) === '=') {
                                $len -= 2;
                            }
                            $part = substr($word, 0, $len);
                            $word = substr($word, $len);
                            $buf .= ' ' . $part;
                            $message .= $buf . sprintf('=%s', static::LE);
                        } else {
                            $message .= $buf . $soft_break;
                        }
                        $buf = '';
                    }
                    while (strlen($word) > 0) {
                        if ($length <= 0) {
                            break;
                        }
                        $len = $length;
                        if ($is_utf8) {
                            $len = $this->utf8CharBoundary($word, $len);
                        } elseif (substr($word, $len - 1, 1) === '=') {
                            --$len;
                        } elseif (substr($word, $len - 2, 1) === '=') {
                            $len -= 2;
                        }
                        $part = substr($word, 0, $len);
                        $word = substr($word, $len);

                        if (strlen($word) > 0) {
                            $message .= $part . sprintf('=%s', static::LE);
                        } else {
                            $buf = $part;
                        }
                    }
                } else {
                    $buf_o = $buf;
                    if (!$firstword) {
                        $buf .= ' ';
                    }
                    $buf .= $word;

                    if (strlen($buf) > $length && $buf_o !== '') {
                        $message .= $buf_o . $soft_break;
                        $buf = $word;
                    }
                }
                $firstword = false;
            }
            $message .= $buf . static::LE;
        }

        return $message;
    }

    /**
     * Finds last character boundary prior to maxLength in a utf-8 quoted-printable encoded string.
     *
     * @param string $encodedText utf-8 QP encoded string
     * @param int    $maxLength   Find the last character boundary prior to this length
     *
     * @return int
     */
    protected function utf8CharBoundary($encodedText, $maxLength)
    {
        $foundSplitPos = false;
        $lookBack = 3;
        while (!$foundSplitPos) {
            $lastChunk = substr($encodedText, $maxLength - $lookBack, $lookBack);
            $encodedCharPos = strpos($lastChunk, '=');
            if (false !== $encodedCharPos) {
                $hex = substr($encodedText, $maxLength - $lookBack + $encodedCharPos + 1, 2);
                $dec = hexdec($hex);
                if ($dec < 128) {
                    if ($encodedCharPos > 0) {
                        $maxLength -= $lookBack - $encodedCharPos;
                    }
                    $foundSplitPos = true;
                } elseif ($dec >= 192) {
                    $maxLength -= $lookBack - $encodedCharPos;
                    $foundSplitPos = true;
                } elseif ($dec < 224) {
                    $lookBack += 1;
                } else {
                    $lookBack += 2;
                }
            } else {
                $foundSplitPos = true;
            }
        }

        return $maxLength;
    }

    /**
     * Set the word wrapping on the message.
     *
     * @param int $length
     */
    public function setWordWrap($length = 0)
    {
        $this->WordWrap = $length;
    }

    /**
     * Get the word wrapping on the message.
     *
     * @return int
     */
    public function getWordWrap()
    {
        return $this->WordWrap;
    }

    /**
     * Set the SMTP server.
     *
     * @param string $host
     */
    public function setHost($host)
    {
        $this->Host = $host;
    }

    /**
     * Get the SMTP server.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->Host;
    }

    /**
     * Set the SMTP port.
     *
     * @param int $port
     */
    public function setPort($port)
    {
        $this->Port = $port;
    }

    /**
     * Get the SMTP port.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->Port;
    }

    /**
     * Set the SMTP username.
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->Username = $username;
    }

    /**
     * Get the SMTP username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->Username;
    }

    /**
     * Set the SMTP password.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->Password = $password;
    }

    /**
     * Get the SMTP password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->Password;
    }

    /**
     * Set SMTP authentication.
     *
     * @param bool $auth
     */
    public function setSMTPAuth($auth)
    {
        $this->SMTPAuth = $auth;
    }

    /**
     * Get SMTP authentication.
     *
     * @return bool
     */
    public function getSMTPAuth()
    {
        return $this->SMTPAuth;
    }

    /**
     * Set the SMTP secure type.
     *
     * @param string $secure
     */
    public function setSMTPSecure($secure)
    {
        $this->SMTPSecure = $secure;
    }

    /**
     * Get the SMTP secure type.
     *
     * @return string
     */
    public function getSMTPSecure()
    {
        return $this->SMTPSecure;
    }

    /**
     * Set the SMTP timeout.
     *
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->Timeout = $timeout;
    }

    /**
     * Get the SMTP timeout.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->Timeout;
    }

    /**
     * Set the SMTP debug level.
     *
     * @param int $level
     */
    public function setSMTPDebug($level)
    {
        $this->SMTPDebug = $level;
    }

    /**
     * Get the SMTP debug level.
     *
     * @return int
     */
    public function getSMTPDebug()
    {
        return $this->SMTPDebug;
    }

    /**
     * Get the SMTP instance.
     *
     * @return SMTP
     */
    public function getSMTPInstance()
    {
        return $this->smtp;
    }

    /**
     * Set the SMTP instance.
     *
     * @param SMTP $smtp
     */
    public function setSMTPInstance(SMTP $smtp)
    {
        $this->smtp = $smtp;
    }

    /**
     * Fix line endings for different operating systems.
     *
     * @param string $str
     *
     * @return string
     */
    protected function fixEOL($str)
    {
        $lastchar = substr($str, -1);
        if (!in_array($lastchar, ["\n", "\r"])) {
            $str .= static::LE;
        }

        return str_replace(["\n\n", "\r\n\r\n"], "\n\n", str_replace(["\r\n", "\n"], static::LE, $str));
    }

    /**
     * Get the boundary for multipart messages.
     *
     * @param string $boundary
     * @param string $charSet
     * @param string $contentType
     * @param string $encoding
     *
     * @return string
     */
    protected function getBoundary($boundary, $charSet, $contentType, $encoding)
    {
        $result = '';
        if ($charSet === '') {
            $charSet = $this->CharSet;
        }
        if ($contentType === '') {
            $contentType = $this->ContentType;
        }
        if ($encoding === '') {
            $encoding = $this->Encoding;
        }
        $result .= $this->textLine('--' . $boundary);
        $result .= sprintf("Content-Type: %s; charset=%s", $contentType, $charSet);
        $result .= static::LE;
        if ($encoding !== '7bit') {
            $result .= $this->headerLine('Content-Transfer-Encoding', $encoding);
        }
        $result .= static::LE;

        return $result;
    }

    /**
     * Get the end boundary for multipart messages.
     *
     * @param string $boundary
     *
     * @return string
     */
    protected function endBoundary($boundary)
    {
        return $this->LE . '--' . $boundary . '--' . $this->LE;
    }

    /**
     * Set the message type.
     *
     * @param string $type
     */
    public function setMessageType()
    {
        $type = [];
        if ($this->alternativeExists()) {
            $type[] = 'alt';
        }
        if ($this->inlineImageExists()) {
            $type[] = 'inline';
        }
        if ($this->attachmentExists()) {
            $type[] = 'attach';
        }
        $this->message_type = implode('_', $type);
        if ($this->message_type === '') {
            $this->message_type = 'plain';
        }
    }

    /**
     * Format an address list for use in a message header.
     *
     * @param array $addr
     *
     * @return string
     */
    protected function addrAppend($type, $addr)
    {
        $addresses = [];
        foreach ($addr as $address => $name) {
            $addresses[] = $this->addrFormat($address, $name);
        }

        return $type . ': ' . implode(', ', $addresses) . static::LE;
    }

    /**
     * Word-wrap the message body to the number of chars set.
     *
     * @param string $message The message to wrap
     * @param int    $length  The line length to wrap to
     * @param bool   $qp_mode Whether to run in Quoted-Printable mode
     *
     * @return string
     */
    protected function wrapText($message, $length, $qp_mode = false)
    {
        if ($qp_mode) {
            $soft_break = sprintf(' =%s', static::LE);
        } else {
            $soft_break = static::LE;
        }
        $is_utf8 = (strtolower($this->CharSet) === 'utf-8');
        $lelen = strlen(static::LE);
        $crlflen = strlen(static::LE);

        $message = $this->fixEOL($message);
        if (substr($message, -$lelen) === static::LE) {
            $message = substr($message, 0, -$lelen);
        }

        $lines = explode(static::LE, $message);
        $message = '';
        foreach ($lines as $line) {
            $words = explode(' ', $line);
            $buf = '';
            $firstword = true;
            foreach ($words as $word) {
                if ($qp_mode && (strlen($word) > $length)) {
                    $space_left = $length - strlen($buf) - $crlflen;
                    if (!$firstword) {
                        if ($space_left > 20) {
                            $len = $space_left;
                            if ($is_utf8) {
                                $len = $this->utf8CharBoundary($word, $len);
                            } elseif (substr($word, $len - 1, 1) === '=') {
                                --$len;
                            } elseif (substr($word, $len - 2, 1) === '=') {
                                $len -= 2;
                            }
                            $part = substr($word, 0, $len);
                            $word = substr($word, $len);
                            $buf .= ' ' . $part;
                            $message .= $buf . sprintf('=%s', static::LE);
                        } else {
                            $message .= $buf . $soft_break;
                        }
                        $buf = '';
                    }
                    while (strlen($word) > 0) {
                        if ($length <= 0) {
                            break;
                        }
                        $len = $length;
                        if ($is_utf8) {
                            $len = $this->utf8CharBoundary($word, $len);
                        } elseif (substr($word, $len - 1, 1) === '=') {
                            --$len;
                        } elseif (substr($word, $len - 2, 1) === '=') {
                                $len -= 2;
                            }
                        $part = substr($word, 0, $len);
                        $word = substr($word, $len);

                        if (strlen($word) > 0) {
                            $message .= $part . sprintf('=%s', static::LE);
                        } else {
                            $buf = $part;
                        }
                    }
                } else {
                    $buf_o = $buf;
                    if (!$firstword) {
                        $buf .= ' ';
                    }
                    $buf .= $word;

                    if (strlen($buf) > $length && $buf_o !== '') {
                        $message .= $buf_o . $soft_break;
                        $buf = $word;
                    }
                }
                $firstword = false;
            }
            $message .= $buf . static::LE;
        }

        return $message;
    }

    /**
     * Return the current line ending string.
     *
     * @return string
     */
    public static function getLE()
    {
        return static::LE;
    }

    /**
     * Set the line ending string.
     *
     * @param string $le
     */
    public static function setLE($le)
    {
        static::LE = $le;
    }

    /**
     * Set the public and private keys and password for S/MIME signing.
     *
     * @param string $cert_filename
     * @param string $key_filename
     * @param string $key_pass
     * @param string $extracerts_filename
     */
    public function sign($cert_filename, $key_filename, $key_pass, $extracerts_filename = '')
    {
        $this->sign_cert_file = $cert_filename;
        $this->sign_key_file = $key_filename;
        $this->sign_key_pass = $key_pass;
        $this->sign_extracerts_file = $extracerts_filename;
    }

    /**
     * Quoted-Printable-encode a DKIM header.
     *
     * @param string $txt
     *
     * @return string
     */
    public function DKIM_QP($txt)
    {
        $line = '';
        $len = strlen($txt);
        for ($i = 0; $i < $len; ++$i) {
            $ord = ord($txt[$i]);
            if (((0x21 <= $ord) && ($ord <= 0x3A)) || $ord === 0x3C || ((0x3E <= $ord) && ($ord <= 0x7E))) {
                $line .= $txt[$i];
            } else {
                $line .= '=' . sprintf('%02X', $ord);
            }
        }

        return $line;
    }

    /**
     * Generate a DKIM signature.
     *
     * @param string $signHeader
     *
     * @return string The DKIM signature value
     */
    public function DKIM_Sign($signHeader)
    {
        if (!defined('PKCS7_TEXT')) {
            if ($this->exceptions) {
                throw new Exception($this->lang('extension_missing') . 'openssl');
            }

            return '';
        }
        $privKeyStr = !empty($this->DKIM_private_string) ? $this->DKIM_private_string : file_get_contents($this->DKIM_private);
        if ('' !== $this->DKIM_passphrase) {
            $privKey = openssl_pkey_get_private($privKeyStr, $this->DKIM_passphrase);
        } else {
            $privKey = openssl_pkey_get_private($privKeyStr);
        }
        if (openssl_sign($signHeader, $signature, $privKey, 'sha256WithRSAEncryption')) {
            openssl_pkey_free($privKey);

            return base64_encode($signature);
        }
        openssl_pkey_free($privKey);

        return '';
    }

    /**
     * Generate a DKIM canonicalization header.
     *
     * @param string $signHeader
     *
     * @return string
     */
    public function DKIM_HeaderC($signHeader)
    {
        $signHeader = preg_replace('/\r\n\s+/', ' ', $signHeader);
        $lines = explode("\r\n", $signHeader);
        foreach ($lines as $key => $line) {
            list($heading, $value) = explode(':', $line, 2);
            $heading = strtolower(trim($heading));
            $value = str_replace("\r\n", "\r\n\t", $value);
            $lines[$key] = $heading . ':' . trim($value);
        }
        $signHeader = implode("\r\n", $lines);

        return $signHeader;
    }

    /**
     * Generate a DKIM canonicalization body.
     *
     * @param string $body
     *
     * @return string
     */
    public function DKIM_BodyC($body)
    {
        if (empty($body)) {
            return "\r\n";
        }
        $body = static::normalizeBreaks($body, "\r\n");
        if (!$this->DKIM_bodyCanonicalize) {
            $body = static::normalizeBreaks($body, "\r\n");
        }

        return $body;
    }

    /**
     * Create the DKIM header and body in a new message header.
     *
     * @param string $headers_line
     * @param string $subject
     * @param string $body
     *
     * @return string
     */
    public function DKIM_Add($headers_line, $subject, $body)
    {
        $DKIMsignatureType = 'rsa-sha256';
        $DKIMcanonicalization = 'relaxed/simple';
        $DKIMquery = 'dns/txt';
        $DKIMtime = time();
        $this->DKIM_domain = $this->DKIM_domain ?: $this->extractDomain($this->From);
        $this->DKIM_selector = $this->DKIM_selector ?: 'phpmailer';
        $subject_header = "Subject: $subject";
        $headers = explode(static::LE, $headers_line);
        $from_header = '';
        $to_header = '';
        $date_header = '';
        $subject_header_to_sign = '';
        foreach ($headers as $header) {
            if (strpos($header, 'From:') === 0) {
                $from_header = $header;
                $from = trim(str_replace('|', '=7C', $this->DKIM_QP(substr($header, 5))));
            } elseif (strpos($header, 'To:') === 0) {
                $to_header = $header;
                $to = trim(str_replace('|', '=7C', $this->DKIM_QP(substr($header, 3))));
            } elseif (strpos($header, 'Date:') === 0) {
                $date_header = $header;
            } elseif (strpos($header, 'Subject:') === 0) {
                $subject_header_to_sign = $header;
            }
        }
        $from = str_replace('|', '=7C', $this->DKIM_QP($from));
        $to = str_replace('|', '=7C', $this->DKIM_QP($to));
        $from_header = str_replace('|', '=7C', $this->DKIM_QP($from_header));
        $to_header = str_replace('|', '=7C', $this->DKIM_QP($to_header));
        $date_header = str_replace('|', '=7C', $this->DKIM_QP($date_header));
        $subject_header = str_replace('|', '=7C', $this->DKIM_QP($subject_header_to_sign));
        $body = $this->DKIM_BodyC($body);
        $DKIMlen = strlen($body);
        $DKIMb64 = base64_encode(pack('H*', hash('sha256', $body)));
        if ('' === $this->DKIM_identity) {
            $this->DKIM_identity = $this->From;
        }
        $dkimSignatureHeader = 'DKIM-Signature: v=1; a=' . $DKIMsignatureType . '; q=' . $DKIMquery . '; l=' . $DKIMlen . '; s=' . $this->DKIM_selector . '; t=' . $DKIMtime . '; c=' . $DKIMcanonicalization . '; h=from:to:date:subject; d=' . $this->DKIM_domain . '; i=' . $this->DKIM_identity . '; z=' . $from_header . '|' . $to_header . '|' . $date_header . '|' . $subject_header . '; bh=' . $DKIMb64 . '; b=';
        $DKIMsignature = $this->DKIM_Sign($dkimSignatureHeader);
        $dkimSignatureHeader .= $DKIMsignature;

        return $dkimSignatureHeader;
    }

    /**
     * Normalize line breaks.
     *
     * @param string $text
     * @param string $breaktype
     *
     * @return string
     */
    public static function normalizeBreaks($text, $breaktype = "\r\n")
    {
        return preg_replace('/(\r\n|\r|\n)/ms', $breaktype, $text);
    }

    /**
     * Extract domain from email address.
     *
     * @param string $email
     *
     * @return string
     */
    protected function extractDomain($email)
    {
        if (preg_match('/^[^@]*@([^@]*)$/', $email, $matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * Get the OAuthTokenProvider instance.
     *
     * @return object
     */
    protected function getOAuth()
    {
        return $this->oauth;
    }

    /**
     * Set an OAuthTokenProvider instance.
     *
     * @param object $oauth
     */
    public function setOAuth($oauth)
    {
        $this->oauth = $oauth;
    }

    /**
     * Check if there are alternative bodies.
     *
     * @return bool
     */
    protected function alternativeExists()
    {
        return !empty($this->AltBody);
    }

    /**
     * Check if there are inline images.
     *
     * @return bool
     */
    protected function inlineImageExists()
    {
        foreach ($this->attachment as $attachment) {
            if ('inline' === $attachment[6]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if there are attachments.
     *
     * @return bool
     */
    protected function attachmentExists()
    {
        foreach ($this->attachment as $attachment) {
            if ('attachment' === $attachment[6]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create a unique ID to use for boundaries.
     *
     * @return string
     */
    protected function generateId()
    {
        return md5(uniqid(time()));
    }

    /**
     * Get the message MIME type headers.
     *
     * @return string
     */
    protected function getMailMIME()
    {
        $result = '';
        $hasMultipart = $this->alternativeExists() || $this->attachmentExists() || $this->inlineImageExists();
        $charset = $this->CharSet;
        if ($hasMultipart) {
            if ('7bit' === $this->Encoding) {
                $this->Encoding = '8bit';
            }
            $boundary = $this->generateId();
            $result .= $this->headerLine('Content-Type', 'multipart/mixed; boundary="' . $boundary . '"');
            $body = $this->createBody();
            $result .= $this->headerLine('Content-Type', 'multipart/alternative; boundary="' . $this->boundary[1] . '"');
        } else {
            $result .= $this->headerLine('Content-Type', $this->ContentType . '; charset=' . $charset);
            if ('7bit' !== $this->Encoding) {
                $result .= $this->headerLine('Content-Transfer-Encoding', $this->Encoding);
            }
        }

        return $result;
    }

    /**
     * Create a message body.
     *
     * @return string
     */
    protected function createBody()
    {
        $body = '';

        if ($this->sign_key_file) {
            $body .= $this->getMailMIME() . static::LE;
        }

        $this->setWordWrap();

        $bodyEncoding = $this->Encoding;
        $bodyCharSet = $this->CharSet;

        if ($this->ContentType === 'text/html' && empty($this->AltBody)) {
            $body .= $this->getBoundary($this->boundary[1], $bodyCharSet, '', $bodyEncoding);
            $body .= $this->encodeString($this->Body, $bodyEncoding);
            $body .= static::LE . static::LE;
        } elseif ($this->ContentType === 'text/html' && !empty($this->AltBody)) {
            $body .= $this->getBoundary($this->boundary[1], $bodyCharSet, 'text/html', $bodyEncoding);
            $body .= $this->encodeString($this->Body, $bodyEncoding);
            $body .= static::LE . static::LE;
            $body .= $this->getBoundary($this->boundary[1], $bodyCharSet, 'text/plain', $bodyEncoding);
            $body .= $this->encodeString($this->AltBody, $bodyEncoding);
            $body .= static::LE . static::LE;
        } else {
            $body .= $this->encodeString($this->Body, $bodyEncoding);
        }

        if (!empty($this->attachment)) {
            $body .= static::LE;
            $body .= $this->getBoundary($this->boundary[2], $bodyCharSet, '', $bodyEncoding);
            $body .= $this->encodeString($this->Body, $bodyEncoding);
            $body .= static::LE . static::LE;
        }

        $body .= $this->endBoundary($this->boundary[1]);

        if (!empty($this->attachment)) {
            $body .= $this->endBoundary($this->boundary[2]);
        }

        $this->MIMEBody = $body;

        return $body;
    }

    /**
     * Encode a string.
     *
     * @param string $str
     * @param string $encoding
     *
     * @return string
     */
    protected function encodeString($str, $encoding = 'base64')
    {
        $encoded = '';
        switch (strtolower($encoding)) {
            case 'base64':
                $encoded = chunk_split(base64_encode($str), 76, static::LE);
                break;
            case '7bit':
            case '8bit':
                $encoded = $this->fixEOL($str);
                if (substr($encoded, -(strlen(static::LE))) !== static::LE) {
                    $encoded .= static::LE;
                }
                break;
            case 'binary':
                $encoded = $str;
                break;
            case 'quoted-printable':
                $encoded = $this->encodeQP($str);
                break;
            default:
                $this->setError($this->lang('encoding') . $encoding);
                break;
        }

        return $encoded;
    }

    /**
     * Encode a string in quoted-printable format.
     *
     * @param string $string The text to encode
     *
     * @return string
     */
    protected function encodeQP($string)
    {
        return static::normalizeBreaks(quoted_printable_encode($string), static::LE);
    }

    /**
     * Get the RFC 822 date format.
     *
     * @return string
     */
    protected static function rfcDate()
    {
        date_default_timezone_set(@date_default_timezone_get());
        return date('D, j M Y H:i:s O');
    }

    /**
     * Get the language string.
     *
     * @param string $key
     *
     * @return string
     */
    protected function lang($key)
    {
        $lang = [
            'authenticate' => 'SMTP Error: Could not authenticate.',
            'connect_host' => 'SMTP Error: Could not connect to SMTP host.',
            'data_not_accepted' => 'SMTP Error: Data not accepted.',
            'empty_message' => 'Message body empty',
            'encoding' => 'Unknown encoding: ',
            'execute' => 'Could not execute: ',
            'file_access' => 'Could not access file: ',
            'file_open' => 'File Error: Could not open file: ',
            'from_failed' => 'The following From address failed: ',
            'instantiate' => 'Could not instantiate mail function.',
            'invalid_address' => 'Invalid address: ',
            'invalid_address_kind' => 'Invalid address kind: ',
            'invalid_hostentry' => 'Invalid hostentry: ',
            'invalid_host' => 'Invalid host: ',
            'mailer_not_supported' => ' mailer is not supported.',
            'provide_address' => 'You must provide at least one recipient email address.',
            'recipients_failed' => 'SMTP Error: The following recipients failed: ',
            'signing' => 'Signing Error: ',
            'smtp_connect_failed' => 'SMTP connect() failed.',
            'smtp_error' => 'SMTP server error: ',
            'variable_set' => 'Cannot set or reset variable: ',
            'extension_missing' => 'Extension missing: ',
        ];

        return isset($lang[$key]) ? $lang[$key] : 'Language string failed to load: ' . $key;
    }

    /**
     * Create a message from line.
     *
     * @param string $value
     *
     * @return string
     */
    protected function textLine($value)
    {
        return $value . static::LE;
    }

    /**
     * Return the number of attachments.
     *
     * @return int
     */
    public function getAttachmentsCount()
    {
        return count($this->attachment);
    }

    /**
     * Return the number of inline images.
     *
     * @return int
     */
    public function getInlineImagesCount()
    {
        $count = 0;
        foreach ($this->attachment as $attachment) {
            if ('inline' === $attachment[6]) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * Return the number of To recipients.
     *
     * @return int
     */
    public function getToAddressesCount()
    {
        return count($this->to);
    }

    /**
     * Return the number of Cc recipients.
     *
     * @return int
     */
    public function getCcAddressesCount()
    {
        return count($this->cc);
    }

    /**
     * Return the number of Bcc recipients.
     *
     * @return int
     */
    public function getBccAddressesCount()
    {
        return count($this->bcc);
    }

    /**
     * Return the number of ReplyTo recipients.
     *
     * @return int
     */
    public function getReplyToAddressesCount()
    {
        return count($this->ReplyTo);
    }

    /**
     * Return the number of all recipients.
     *
     * @return int
     */
    public function getAllRecipientsCount()
    {
        return $this->getToAddressesCount() + $this->getCcAddressesCount() + $this->getBccAddressesCount();
    }

    /**
     * Get the message type.
     *
     * @return string
     */
    public function getMessageType()
    {
        return $this->message_type;
    }

    /**
     * Set the message type.
     *
     * @param string $type
     */
    public function setMessageType($type)
    {
        $this->message_type = $type;
    }

    /**
     * Get the content type.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->ContentType;
    }

    /**
     * Set the content type.
     *
     * @param string $type
     */
    public function setContentType($type)
    {
        $this->ContentType = $type;
    }

    /**
     * Get the encoding.
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->Encoding;
    }

    /**
     * Set the encoding.
     *
     * @param string $encoding
     */
    public function setEncoding($encoding)
    {
        $this->Encoding = $encoding;
    }

    /**
     * Get the charset.
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->CharSet;
    }

    /**
     * Set the charset.
     *
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->CharSet = $charset;
    }

    /**
     * Get the From address.
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->From;
    }

    /**
     * Get the From name.
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->FromName;
    }

    /**
     * Get the Sender address.
     *
     * @return string
     */
    public function getSender()
    {
        return $this->Sender;
    }

    /**
     * Set the Sender address.
     *
     * @param string $sender
     */
    public function setSender($sender)
    {
        $this->Sender = $sender;
    }

    /**
     * Get the subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->Subject;
    }

    /**
     * Get the body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->Body;
    }

    /**
     * Get the alt body.
     *
     * @return string
     */
    public function getAltBody()
    {
        return $this->AltBody;
    }

    /**
     * Get the error info.
     *
     * @return string
     */
    public function getErrorInfo()
    {
        return $this->ErrorInfo;
    }

    /**
     * Get the priority.
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->Priority;
    }

    /**
     * Set the priority.
     *
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->Priority = $priority;
    }

    /**
     * Get the word wrap.
     *
     * @return int
     */
    public function getWordWrap()
    {
        return $this->WordWrap;
    }

    /**
     * Set the word wrap.
     *
     * @param int $length
     */
    public function setWordWrap($length)
    {
        $this->WordWrap = $length;
    }

    /**
     * Get the mailer.
     *
     * @return string
     */
    public function getMailer()
    {
        return $this->Mailer;
    }

    /**
     * Set the mailer.
     *
     * @param string $mailer
     */
    public function setMailer($mailer)
    {
        $this->Mailer = $mailer;
    }

    /**
     * Get the sendmail path.
     *
     * @return string
     */
    public function getSendmail()
    {
        return $this->Sendmail;
    }

    /**
     * Set the sendmail path.
     *
     * @param string $path
     */
    public function setSendmail($path)
    {
        $this->Sendmail = $path;
    }

    /**
     * Get the use sendmail.
     *
     * @return bool
     */
    public function getUseSendmail()
    {
        return $this->UseSendmail;
    }

    /**
     * Set the use sendmail.
     *
     * @param bool $use
     */
    public function setUseSendmail($use)
    {
        $this->UseSendmail = $use;
    }

    /**
     * Get the confirm reading to.
     *
     * @return string
     */
    public function getConfirmReadingTo()
    {
        return $this->ConfirmReadingTo;
    }

    /**
     * Set the confirm reading to.
     *
     * @param string $address
     */
    public function setConfirmReadingTo($address)
    {
        $this->ConfirmReadingTo = $address;
    }

    /**
     * Get the hostname.
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->Hostname;
    }

    /**
     * Set the hostname.
     *
     * @param string $hostname
     */
    public function setHostname($hostname)
    {
        $this->Hostname = $hostname;
    }

    /**
     * Get the message ID.
     *
     * @return string
     */
    public function getMessageID()
    {
        return $this->MessageID;
    }

    /**
     * Set the message ID.
     *
     * @param string $id
     */
    public function setMessageID($id)
    {
        $this->MessageID = $id;
    }

    /**
     * Get the host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->Host;
    }

    /**
     * Set the host.
     *
     * @param string $host
     */
    public function setHost($host)
    {
        $this->Host = $host;
    }

    /**
     * Get the port.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->Port;
    }

    /**
     * Set the port.
     *
     * @param int $port
     */
    public function setPort($port)
    {
        $this->Port = $port;
    }

    /**
     * Get the username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->Username;
    }

    /**
     * Set the username.
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->Username = $username;
    }

    /**
     * Get the password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->Password;
    }

    /**
     * Set the password.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->Password = $password;
    }

    /**
     * Get the SMTP auth.
     *
     * @return bool
     */
    public function getSMTPAuth()
    {
        return $this->SMTPAuth;
    }

    /**
     * Set the SMTP auth.
     *
     * @param bool $auth
     */
    public function setSMTPAuth($auth)
    {
        $this->SMTPAuth = $auth;
    }

    /**
     * Get the SMTP secure.
     *
     * @return string
     */
    public function getSMTPSecure()
    {
        return $this->SMTPSecure;
    }

    /**
     * Set the SMTP secure.
     *
     * @param string $secure
     */
    public function setSMTPSecure($secure)
    {
        $this->SMTPSecure = $secure;
    }

    /**
     * Get the timeout.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->Timeout;
    }

    /**
     * Set the timeout.
     *
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->Timeout = $timeout;
    }

    /**
     * Get the SMTP debug.
     *
     * @return int
     */
    public function getSMTPDebug()
    {
        return $this->SMTPDebug;
    }

    /**
     * Set the SMTP debug.
     *
     * @param int $level
     */
    public function setSMTPDebug($level)
    {
        $this->SMTPDebug = $level;
    }

    /**
     * Get the SMTP instance.
     *
     * @return SMTP
     */
    public function getSMTPInstance()
    {
        return $this->smtp;
    }

    /**
     * Set the SMTP instance.
     *
     * @param SMTP $smtp
     */
    public function setSMTPInstance(SMTP $smtp)
    {
        $this->smtp = $smtp;
    }

    /**
     * Get the LE.
     *
     * @return string
     */
    public static function getLE()
    {
        return static::LE;
    }

    /**
     * Set the LE.
     *
     * @param string $le
     */
    public static function setLE($le)
    {
        static::LE = $le;
    }

    /**
     * Get the version.
     *
     * @return string
     */
    public static function getVersion()
    {
        return self::VERSION;
    }

    // PHPMailer properties and constants
    const VERSION = '6.5.0';
    const LE = "\r\n";
    const STOP_MESSAGE = 0;
    const STOP_CONTINUE = 1;
    const STOP_CRITICAL = 2;

    protected $exceptions = false;
    protected $to = [];
    protected $cc = [];
    protected $bcc = [];
    protected $ReplyTo = [];
    protected $all_recipients = [];
    protected $attachment = [];
    protected $CustomHeader = [];
    protected $lastMessageID = '';
    protected $message_type = '';
    protected $boundary = [];
    protected $language = [];
    protected $error_count = 0;
    protected $sign_cert_file = '';
    protected $sign_key_file = '';
    protected $sign_extracerts_file = '';
    protected $sign_key_pass = '';
    protected $DKIM_domain = '';
    protected $DKIM_private = '';
    protected $DKIM_private_string = '';
    protected $DKIM_selector = '';
    protected $DKIM_passphrase = '';
    protected $DKIM_identity = '';
    protected $DKIM_domainSigned = false;
    protected $DKIM_bodyCanonicalize = true;
    protected $DKIM_headerCanonicalize = true;
    protected $action_function = '';
    protected $XMailer = '';
    protected $smtp = null;
    protected $WordWrap = 0;
    protected $Mailer = 'mail';
    protected $Sendmail = '/usr/sbin/sendmail';
    protected $UseSendmailOptions = true;
    protected $PluginDir = '';
    protected $ConfirmReadingTo = '';
    protected $Hostname = '';
    protected $MessageID = '';
    protected $MessageDate = '';
    protected $Host = 'localhost';
    protected $Port = 25;
    protected $Helo = '';
    protected $SMTPSecure = '';
    protected $SMTPAutoTLS = true;
    protected $SMTPAuth = false;
    protected $SMTPOptions = [];
    protected $Username = '';
    protected $Password = '';
    protected $AuthType = '';
    protected $Realm = '';
    protected $Workstation = '';
    protected $Timeout = 300;
    protected $SMTPDebug = 0;
    protected $Debugoutput = 'echo';
    protected $SMTPKeepAlive = false;
    protected $SingleTo = false;
    protected $do_verp = false;
    protected $AllowEmpty = false;
    protected $LE = "\r\n";
    protected $DKIM_header = '';
    protected $DKIM_body = '';
    protected $DKIM_private_key = '';
    protected $autoSignKey = '';
    protected $autoSignPass = '';
    protected $oauth = null;
}

