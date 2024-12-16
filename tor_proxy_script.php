<?php

// Tor control port and password
$controlPort = 9051;
$password = '16:872860B76453A77D60CA2BB8C1A7042072093276A3D701AD684053EC4C872860B76453A77D60CA2BB8C1A7042072093276A3D701AD684053EC4C'; // Replace with your actual password

// Function to clear screen based on OS
function clearScreen() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        system('cls');
    } else {
        system('clear');
    }
}

// Function to print colored text
function printColored($text, $color) {
    return "\033[" . $color . "m" . $text . "\033[0m";
}

// Color codes
$green = "32";
$red = "31";
$yellow = "33";

// Function to print banner
function printBanner() {
    global $green;
    $banner = "
░▀▀█░█▀█░▀█▀░█▀█
░▄▀░░█▀█░░█░░█░█
░▀▀▀░▀░▀░▀▀▀░▀░▀
╔══════════════════════════════════╗
║                                  ║
║  ZAIN ARAIN                      ║
║  AUTO SCRIPT MASTER              ║
║                                  ║
║  JOIN TELEGRAM CHANNEL NOW!      ║
║  https://t.me/AirdropScript6     ║
║  @AirdropScript6 - OFFICIAL      ║
║  CHANNEL                         ║
║                                  ║
║  FAST - RELIABLE - SECURE        ║
║  SCRIPTS EXPERT                  ║
║                                  ║
╚══════════════════════════════════╝
";
    echo printColored($banner, $green);
}

function startTor() {
    exec('sudo service tor start');
}

function getNewTorIdentity($controlPort, $password) {
    $fp = fsockopen("127.0.0.1", $controlPort, $errno, $errstr, 30);
    if (!$fp) {
        echo "Error: $errstr ($errno)\n";
        return false;
    } else {
        fwrite($fp, "AUTHENTICATE \"$password\"\r\n");
        $response = fread($fp, 1024);
        if (strpos($response, '250') !== false) {
            fwrite($fp, "SIGNAL NEWNYM\r\n");
            $response = fread($fp, 1024);
            fclose($fp);
            if (strpos($response, '250') !== false) {
                return true;
            }
        }
        fclose($fp);
        return false;
    }
}

function makeApiRequest($userId, $tgId, $headers) {
    $url = "https://api.adsgram.ai/adv?blockId=4853&tg_id=$tgId&tg_platform=android&platform=Linux%20aarch64&language=en&chat_type=sender&chat_instance=" . generateChatInstance() . "&top_domain=app.notpx.app";
    $torProxy = "socks5://127.0.0.1:9050";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_PROXY, $torProxy);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [$response, $httpCode];
}

function claimReward($rewardUrl, $headers) {
    $torProxy = "socks5://127.0.0.1:9050";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $rewardUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_PROXY, $torProxy);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode === 200);
}

function generateChatInstance() {
    return strval(rand(10000000000000, 99999999999999));
}

function extractReward($response) {
    $data = json_decode($response, true);
    if ($data && isset($data['banner']['trackings'])) {
        foreach ($data['banner']['trackings'] as $tracking) {
            if ($tracking['name'] === 'reward') {
                return $tracking['value'];
            }
        }
    }
    return null;
}

// Check for users.json file
$usersFile = 'users.json';
if (!file_exists($usersFile)) {
    echo printColored("Error: 'users.json' file not found! Please create the file with valid user data.\n", $red);
    exit;
}

// Load users from file
$users = json_decode(file_get_contents($usersFile), true);
if (!$users || !is_array($users)) {
    echo printColored("Error: 'users.json' contains invalid data or is empty.\n", $red);
    exit;
}

// Headers for API requests
$userAgent = "Mozilla/5.0 (Linux; Android 10; Samsung Galaxy) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Mobile Safari/537.36";
$headers = [
    'Host: api.adsgram.ai',
    'Connection: keep-alive', 
    'Cache-Control: max-age=0',
    'sec-ch-ua-platform: "Android"',
    "User-Agent: $userAgent",
    'Accept: */*',
    'Origin: https://app.notpx.app',
    'X-Requested-With: org.telegram.messenger',
    'Sec-Fetch-Site: cross-site',
    'Sec-Fetch-Mode: cors',
    'Sec-Fetch-Dest: empty',
    'Accept-Encoding: gzip, deflate, br, zstd',
    'Accept-Language: en,en-US;q=0.9'
];

// Main logic
$totalPoints = 0;
$startTime = microtime(true);

while (true) {
    clearScreen();
    printBanner();
    echo "[ INFO ] Starting Ads Watching...\n";

    foreach ($users as $userId => $userData) {
        $tgId = $userData['tg_id'];

        // Rotate Tor identity for a new IP
        if (getNewTorIdentity($controlPort, $password)) {
            usleep(200000); // Sleep for 0.2 seconds to let the IP change
            echo "[ INFO ] Injecting TG ID | $tgId...\n";

            // Make API request
            list($response, $httpCode) = makeApiRequest($userId, $tgId, $headers);

            if ($httpCode === 200) {
                $reward = extractReward($response);
                if ($reward) {
                    // Claim reward
                    if (claimReward($reward, $headers)) {
                        $totalPoints += 16;
                        echo printColored("BYPASS WOLF XD | BYPASSING ADS LIMIT\n", $yellow);
                        echo printColored("TOR IP\n", $yellow);
                        echo printColored("SUCCESS | ADS WATCHED 16+PX EARNED\n", $green);
                        echo printColored("PROGRESS | TRANSFERRED TO CLIENT\n", $green);
                        echo printColored("TOTAL PX: $totalPoints\n", $green);
                    } else {
                        echo printColored("[ ERROR ] Failed to claim reward for TG ID $tgId.\n", $red);
                    }
                } else {
                    echo printColored("[ ERROR ] No reward found in API response.\n", $red);
                }
            } else {
                echo printColored("[ ERROR ] HTTP Error: $httpCode\n", $red);
            }
        } else {
            echo printColored("[ ERROR ] Failed to get a new Tor identity.\n", $red);
        }

        // Calculate the elapsed time and ensure the script runs 2-3 times per second
        $elapsedTime = microtime(true) - $startTime;
        if ($elapsedTime < 0.5) {
            usleep((0.5 - $elapsedTime) * 1000000); // Sleep to ensure the execution rate
        }
        $startTime = microtime(true);
    }

    echo printColored("[ INFO ] Total Points Earned: $totalPoints PX\n", $green);
    echo "[ INFO ] Taking cooldown to avoid detection...\n";
    usleep(rand(20000000, 30000000)); // Batch cooldown in microseconds
}
?>
