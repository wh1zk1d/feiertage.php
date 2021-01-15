<?php
  // Request Parameter setzen
  $year = date("Y"); // Nimmt automatisch das aktuelle Jahr
  $state = 'HE'; // HE = Hessen
  
  // URL die abgefragt werden soll
  $url = 'https://feiertage-api.de/api/?jahr='.$year.'&nur_land='.$state;

  // cURL vorbereiten
  $req = curl_init($url);
  curl_setopt($req, CURLOPT_RETURNTRANSFER, true);

  // Anfrage ausführen
  $result = curl_exec($req);

  // Anfrage abschließen
  curl_close($req);

  // JSON in PHP Objekt umwandeln
  $obj = json_decode($result);

  // Keys (Titel) und Daten splitten
  $arr = get_object_vars($obj);
  $titles = array_keys($arr);

  // Neues, leeres Array generieren
  $holidays = array();

  // Über Titel loopen, Feiertag Objekt erstellen und in das $holidays Array packen
  foreach($titles as $title) {
    $holiday = new stdClass();
    $holiday->name = $title;
    $holiday->date = $arr[$title]->datum;

    $holidays[] = $holiday;
  }
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feiertage <?php echo $year; ?></title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🗓</text></svg>"/>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>🗓 Feiertage <?php echo $year; ?> (<?php echo $state; ?>)</h1>

  <ul>
    <?php
      foreach ($holidays as $holiday) {
        echo '<li>' . $holiday->name . ' (' . $holiday->date . ')' . '</li>';
      }
    ?>
  </ul>
  </div>
</body>
</html>