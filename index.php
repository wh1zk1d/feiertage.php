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

  // Checken ob der Feiertag ein Werktag ist
  function checkWorkingDay($date) {
    // In Unix Timestamp umwandeln
    $unix_timestamp = strtotime($date);

    // Wochentag ziehen
    $dayOfWeek = date("l", $unix_timestamp);

    // Checken ob Werktag oder nicht
    $isWorkingDay = false;

    if ($dayOfWeek != 'Sunday' && $dayOfWeek != 'Saturday') {
      $isWorkingDay = true;
    }

    // Ergebnis zurückgeben
    return $isWorkingDay;
  }

  // Feiertage für Monat
  /*
    $month: Number, 1-12
  */
  function getHolidaysForMonth ($month, $holidays) {
    $holidays_in_month = array_filter($holidays, function($holiday) use ($month) {
      $unix_timestamp = strtotime($holiday->date);
      $holiday_month = date("m", $unix_timestamp);

      $trimmed_month = ltrim($holiday_month, '0');
      
      return $trimmed_month == $month ? true : false;
    });

    return $holidays_in_month;
  }

  // Neues, leeres Array generieren
  $holidays = array();

  // Über Titel loopen, Feiertag Objekt erstellen und in das $holidays Array packen
  foreach($titles as $title) {
    $holiday = new stdClass();
    $holiday->name = $title;
    $holiday->date = $arr[$title]->datum;

    $isWorkingDay = checkWorkingDay($holiday->date);

    if ($isWorkingDay) {
      $holidays[] = $holiday;
    }
  }

  $holidays_in_month = getHolidaysForMonth(4, $holidays);
  $num_of_holidays_in_month = count($holidays_in_month);
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
    <h1>🗓 Feiertage (<?php echo $num_of_holidays_in_month; ?>)</h1>

  <ul>
    <?php
      foreach ($holidays_in_month as $holiday) {
        echo '<li>' . $holiday->name . ' (' . $holiday->date . ')' . '</li>';
      }
    ?>
  </ul>
  </div>
</body>
</html>