<?php    
    //Require
    require("../../index_files/sessionstart.php");
    require("../../index_files/sessioncheck.php");
    require("../../index_files/connect.php");

    //Session
    $currentUser = $_SESSION["username"]; //Current User

    //POST
    $vitals = array_filter( [ isset($_POST["vital1"]) ? $_POST["vital1"] : '', isset($_POST["vital2"]) ? $_POST["vital2"] : '', isset($_POST["vital3"]) ? $_POST["vital3"] : '', isset($_POST["vital4"]) ? $_POST["vital4"] : '', isset($_POST["vital5"]) ? $_POST["vital5"] : '' ] );
    $dateStart = new DateTime($_POST["date_start"], new DateTimeZone("America/Chicago"));
    $dateEnd = new DateTime($_POST["date_end"], new DateTimeZone("America/Chicago"));
    $timeStart = $_POST["time_start"];
    $timeEnd = $_POST["time_end"];
    $timeInterval = $_POST["time_interval"];
    $rpi = $_POST["rpi_select"];

    //Get the correct vital name
    $arrayVitals = array();
    foreach ($vitals as $var) {
        switch ($var) {
            case 'battery':
                $arrayVitals[] = [ "BatteryVoltage" ];
                $arrayVitals[] = [ "BatteryCurrent" ];
                break;
            case 'solar':
                $arrayVitals[] = [ "SolarPanelVoltage" ];
                $arrayVitals[] = [ "SolarPanelCurrent" ];
                break;
            case 'temperature':
                $arrayVitals[] = [ "TemperatureInner" ];
                $arrayVitals[] = [ "TemperatureOuter" ];
                break;
            case 'humidity':
                $arrayVitals[] = [ "HumidityInner" ];
                $arrayVitals[] = [ "HumidityOuter" ];
                break;
            case 'clarity':
                $arrayVitals[] = [ "Clarity" ];
                break;
            case 'exhaust':
                $arrayVitals[] = [ "Exhaust" ];
                break;
            default:
                break;
        }
    }

    //Database Queries
    $arrayLogVitalName = array(); //This array will contain all the vital names
    $arrayLogVital = array(); //This array will contain all the vital values during the given timestamps as before
    $arrayLogTS = array(); //This array will contain all the timestamps according to the dateStart/End, timeStart/End, and timeInterval
    foreach ($arrayVitals as $rowidx => $columnidx) {
        $sqlLog = "SELECT V.VN, V1, TS FROM log AS l NATURAL JOIN vitals AS V WHERE l.USR='{$currentUser}' AND l.RPID='{$rpi}' AND TYP='ST' AND V.VN='{$arrayVitals[$rowidx][0]}' ORDER BY l.TS ASC;"; //Select all logs related to the current user & 
        $resultLog = mysqli_query($conn, $sqlLog); 

        $tempLogVital = array(); //This array will contain all the vital values during the given timestamps as before
        $tempLogTS = array(); //This array will contain all the timestamps according to the dateStart/End, timeStart/End, and timeInterval
        if(mysqli_num_rows($resultLog) > 0) { 
            while($row = mysqli_fetch_assoc($resultLog)) {
                $arrayLogVitalName[] = $row['VN'];
                $tempLogVital[] = $row['V1'];
                $vitalTS = new DateTime($row['TS'], new DateTimeZone("America/Chicago"));
                $tempLogTS[] = $vitalTS->format('Y-m-d H:i');
            }
        }

        $arrayLogVital[] = $tempLogVital;
        $arrayLogTS[] = $tempLogTS;
    }

    //Output
    $arrayLogTS = array_values($arrayLogTS);
    $arrayLogVitalName = array_values($arrayLogVitalName);
    $arrayLogVital = array_values($arrayLogVital);
    
    //Debug
    print_r("<br/><br/><br/>");
    print_r($arrayLogTS);
    print_r("<br/><br/><br/>");
    print_r($arrayLogVitalName);
    print_r("<br/><br/><br/>");
    print_r($arrayLogVital);

    //Create CSV
    //$statsCSV = fopen("logs.csv", "w");
    //fputcsv($file, explode(',', $line));
    //fclose($file);

    //jQuery will then grab the file using its header function
    //echo "logs.csv";
?>