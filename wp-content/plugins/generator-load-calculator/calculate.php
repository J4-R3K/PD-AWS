<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /**
     * calculate_load():
     * Reflects your Excel logic exactly.
     * If LRA>0 => 9s at LRA, then FLA (override).
     * Otherwise, we do a SWITCH (mType) for DOL, SD, SS, VSD, STD.
     */
    function calculate_load($t, $start, $mType, $LRA, $SC, $FLA) {
        // If not running or not started => 0
        if ($start == 0) return 0;
        if ($t < $start) return 0;

        // DOL LRA override => 9s at LRA, then FLA
        if ($LRA > 0) {
            return ($t < $start + 9) ? $LRA : $FLA;
        }

        // Otherwise, rely on SC-based logic
        // Some constants
        $dolTime         = 9;
        $sdStarTime      = 3;
        $sdTransition    = 0.5;  // star–delta transition time
        $ssRampTime      = 5;
        $vsdRampTime     = 5;

        switch (strtoupper($mType)) {

            case 'DOL':
                // DOL: 9s at SC, then FLA
                return ($t < $start + $dolTime) ? $SC : $FLA;

            case 'SD':
                // Star–Delta advanced formula:
                //  0–3s => linear ramp 0.2*SC to 0.35*SC
                //  next 0.1s => 0.25*SC (brief dip)
                //  next 0.4s => 0.5*SC (transition spike)
                //  then => FLA
                if ($t < $start + $sdStarTime) {
                    // linear from 0.2*SC up to 0.35*SC
                    // fraction = (elapsed / sdStarTime)
                    $elapsed = $t - $start;
                    $fraction = $elapsed / $sdStarTime;
                    // start at 0.2, ramp 0.15 => final = 0.2 + fraction*0.15
                    return (0.2 + (0.15 * $fraction)) * $SC;
                } elseif ($t < $start + $sdStarTime + 0.1) {
                    // brief dip ~25% SC
                    return 0.25 * $SC;
                } elseif ($t < $start + $sdStarTime + $sdTransition) {
                    // partial spike ~50% SC
                    return 0.5 * $SC;
                } else {
                    // after that => FLA
                    return $FLA;
                }

            case 'SS':
                // Soft Starter:
                // At time=start => 1×FLA
                // ramp linearly up to 2.5×FLA over 5s, then hold FLA
                if ($t < $start + $ssRampTime) {
                    $elapsed = $t - $start;
                    $ratio   = $elapsed / $ssRampTime; // 0..1
                    // start=1×FLA => end=2.5×FLA => difference=1.5×FLA
                    // formula => FLA + ratio*(2.5*FLA - FLA) = (1 + 1.5*ratio)*FLA
                    return ($FLA + (2.5*$FLA - $FLA)*$ratio);
                } else {
                    return $FLA;
                }

            case 'VSD':
                // VSD:
                // Start ~0.6×SC, ramp to ~1.5×SC => Excel formula = (0.6 + fraction*0.9)*SC
                // Then => FLA
                if ($t < $start + $vsdRampTime) {
                    $elapsed = $t - $start;
                    $fraction = $elapsed / $vsdRampTime; // 0..1
                    // start=0.6 => end=1.5 => difference=0.9 => final= 0.6 + 0.9*fraction
                    return ($SC * (0.6 + 0.9*$fraction));
                } else {
                    return $FLA;
                }

            case 'STD':
                // Non-motor => constant FLA from start
                return $FLA;

            default:
                // if no match => 0
                return 0;
        }
    }

    // Collect POST data
    $equipments   = isset($_POST['details'])   ? $_POST['details']   : [];
    $load_types   = isset($_POST['load_type']) ? $_POST['load_type'] : [];
    $lra_values   = isset($_POST['lra'])       ? $_POST['lra']       : [];
    $sc_values    = isset($_POST['sc'])        ? $_POST['sc']        : [];
    $flc_values   = isset($_POST['flc'])       ? $_POST['flc']       : [];
    $start_times  = isset($_POST['start_time'])? $_POST['start_time']: [];

    // We calculate loads for t=1..30
    $total_load = array_fill(1, 30, 0.0);
    $equipment_loads = [];

    // For each piece of equipment
    foreach ($equipments as $index => $equipName) {
        $equipment_loads[$index] = [];
        $type  = $load_types[$index];
        $LRA   = floatval($lra_values[$index]);
        $SC    = floatval($sc_values[$index]);
        $FLA   = floatval($flc_values[$index]);
        $start = floatval($start_times[$index]);

        // Compute load from t=1..30
        for ($t = 1; $t <= 30; $t++) {
            $val = calculate_load($t, $start, $type, $LRA, $SC, $FLA);
            $equipment_loads[$index][$t] = $val;
            $total_load[$t] += $val;
        }
    }

    // Find maximum total load
    $max_total_amperage = max($total_load);

    // ---------- INPUT SUMMARY ----------
    echo '<h3 style="padding-left:10px;">Input Summary</h3>';
    echo "<table border='1' cellpadding='4' cellspacing='0' style='margin-bottom:20px; border-collapse: collapse; width:100%;'>";
    echo "<thead><tr style='background-color:#0E172D; color:#fff;'>
            <th>Equipment</th>
            <th>Load Type</th>
            <th>LRA</th>
            <th>SC</th>
            <th>FLC</th>
            <th>Start Time (s)</th>
          </tr></thead><tbody>";

    foreach ($equipments as $i => $name) {
        $eq  = htmlspecialchars($name);
        $lt  = htmlspecialchars($load_types[$i]);
        $lv  = htmlspecialchars($lra_values[$i]);
        $sv  = htmlspecialchars($sc_values[$i]);
        $fv  = htmlspecialchars($flc_values[$i]);
        $st  = htmlspecialchars($start_times[$i]);

        echo "<tr>
                <td>$eq</td>
                <td>$lt</td>
                <td>$lv</td>
                <td>$sv</td>
                <td>$fv</td>
                <td>$st</td>
              </tr>";
    }
    echo "</tbody></table>";

    /**
     * print_subtable($start,$end) => T1..T15 or T16..T30
     * We want 16 columns total: 1 for Equipment + 15 for T columns
     */
    function print_subtable($start, $end, $equipments, $equipment_loads, $total_load, $max_total_amperage) {
        echo "<table class='calc-subtable' border='1' cellpadding='4' cellspacing='0' style='margin-bottom:20px;'>";
        // colgroup => 1 for equipment, 15 for T columns
        echo "<colgroup>";
        echo "<col>";
        for ($col = $start; $col <= $end; $col++) {
            echo "<col>";
        }
        echo "</colgroup>";

        echo "<thead>
                <tr style='background-color:#0E172D; color:#fff;'>
                  <th>Equipment</th>";
        for ($t = $start; $t <= $end; $t++) {
            echo "<th>T$t</th>";
        }
        echo "</tr></thead><tbody>";

        // Equipment rows
        foreach ($equipments as $idx => $eqName) {
            $eqName = htmlspecialchars($eqName);
            echo "<tr>
                    <td style='font-weight:bold; text-align:left;'>$eqName</td>";
            for ($t = $start; $t <= $end; $t++) {
                $valStr = round($equipment_loads[$idx][$t], 2);
                echo "<td>$valStr</td>";
            }
            echo "</tr>";
        }

        // TOTAL LOAD (A)
        echo "<tr class='total-load-row'>
                <td style='font-weight:bold;'>Total Load (A)</td>";
        for ($t = $start; $t <= $end; $t++) {
            $amp = round($total_load[$t], 2);
            if ($amp == round($max_total_amperage, 2) && $max_total_amperage > 0) {
                echo "<td style='background:#FFC7CE; color:#9C0006; font-weight:bold;'>$amp</td>";
            } else {
                echo "<td>$amp</td>";
            }
        }
        echo "</tr>";

        // TOTAL kVA row
        echo "<tr class='total-kva-row'>
                <td style='font-weight:bold;'>Total kVA</td>";
        for ($t = $start; $t <= $end; $t++) {
            $kVA = sqrt(3) * 400 * $total_load[$t] / 1000;
            $kvaStr = round($kVA, 2);
            if (round($total_load[$t], 2) == round($max_total_amperage, 2) && $max_total_amperage > 0) {
                echo "<td style='background:#FFC7CE; color:#9C0006; font-weight:bold;'>$kvaStr</td>";
            } else {
                echo "<td>$kvaStr</td>";
            }
        }
        echo "</tr>";

        echo "</tbody></table>";
    }

    // ---------- LOAD CALCULATION TABLES ----------
    echo '<h3 style="padding-left:10px;">Load Calculation</h3>';
    print_subtable(1, 15, $equipments, $equipment_loads, $total_load, $max_total_amperage);
    print_subtable(16, 30, $equipments, $equipment_loads, $total_load, $max_total_amperage);

    // Some row background styling
    echo '<style>
        .total-load-row td { background: #f2f2f2; }
        .total-kva-row td { background: #dff0d8; }
    </style>';
}
?>
