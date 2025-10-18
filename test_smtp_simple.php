<?php
echo "Testing SMTP connection...\n";
$fp = stream_socket_client('tcp://smtp.gmail.com:587', $errno, $errstr, 30);
if (!$fp) {
    echo "Connection failed: $errstr ($errno)\n";
    exit;
}
echo "Connected\n";
fwrite($fp, "EHLO localhost\r\n");
echo fgets($fp, 512);
fwrite($fp, "STARTTLS\r\n");
echo fgets($fp, 512);
if (stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
    echo "STARTTLS successful\n";
} else {
    echo "STARTTLS failed\n";
}
fclose($fp);
?>
