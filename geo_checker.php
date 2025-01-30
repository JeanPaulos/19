<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

ini_set('max_execution_time', 300); // Max 5 minuten uitvoeren

// API-sleutel voor ipinfo.io (vervang met jouw sleutel)
define("API_KEY", "8c6bbca27806cb");

// Databaseverbinding instellen
$host = "localhost"; // Je MySQL host (meestal localhost)
$dbname = "u29676p38096_ipadressen1"; // Je database naam
$username = "u29676p38096_ipjp"; // Je MySQL gebruikersnaam
$password = "_s1JsobO%Jf/)MQK3Rgl++]Y"; // Je MySQL wachtwoord

// Maak verbinding met de database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Fout bij verbinden met database: " . $e->getMessage());
}

// Haal IP-adressen op uit de database (uit "TABLE 1")
function load_ips_from_db() {
    global $pdo;
    $stmt = $pdo->query("SELECT `COL 1` FROM `TABLE 1` LIMIT 10"); // Max 10 IP’s per keer
    return $stmt->fetchAll(PDO::FETCH_COLUMN); // Retourneer alleen de IP’s
}

// Geolocatie opzoeken via ipinfo.io
function get_ip_location($ip) {
    $token = API_KEY; // Gebruik de gedefinieerde API-sleutel
    $url = "https://ipinfo.io/{$ip}/json?token={$token}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        return "$ip: Fout bij ophalen van gegevens.";
    }

    $data = json_decode($response, true);

    if (isset($data["city"])) {
        if ($data["city"] === "Bemmel") {
            return "<span style='color: green;'>$ip komt uit Bemmel! ✅</span>";
        } else {
            return "$ip komt uit " . ($data["city"] ?? "onbekend") . ", " . ($data["region"] ?? "onbekend");
        }
    }

    return "$ip: Locatie onbekend.";
}

// Laad de IP-adressen uit de database
$ip_addresses = load_ips_from_db();

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta http-equiv="refresh" content="60">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<title>IP Geolocatie Checker</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .result { margin: 5px 0; padding: 8px; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>IP Geolocatie Checker</h1>
    <p>Deze pagina controleert of IP-adressen afkomstig zijn uit Bemmel.</p>
    
    <?php
    // Verwerk de IP-adressen en geef hun locatie weer
    foreach ($ip_addresses as $ip) {
        echo "<div class='result'>" . get_ip_location($ip) . "</div>";
        sleep(2); // Verhoog van 1 naar 2 seconden om te voorkomen dat je IP-info overbelast
    }
    ?>

</body>
</html>
