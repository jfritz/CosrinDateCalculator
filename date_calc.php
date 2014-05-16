<?php
/**
 * Cosrin Date Calculator
 * 
 * Displays a simple interface for calculating
 * the time-distance to a future Cosrin date and the
 * projected time at which that Cosrin date will arrive
 * in real life time.
 * 
 * Morning - 96-72m left (up to 24m have elapseed)
 * Noon - 72m-48m (24 - 48m have elapsed)
 * Evening -  48-24m (48 - 72m have elapsed)
 * Night - 24-0m (72 -95m have elapsed
 * 
 * Awakenings - The start of the end to Winter
 * Frost Fall - The last gasp of Winter
 * First Growth - The start of Spring
 * Weeping Skies - Rainy season
 * Cherrin - The start of Summer
 * Deori's Pleasure - The middle of Summer
 * Longing - The waiting month when you long to harvest
 * First Harvest - Harvest time
 * Hallow Month - A month of reflection the coming of Winter
 * Winter Cloak - Winter over the land
 * --- 
 * 20 OOC days = 10 Cosrin months = 1 Cosrin year
 * 2 OOC days = 30 Cosrin days = 1 Cosrin month
 * 1 OOC day = 15 Cosrin days
 * 96 OOC minutes = 1 Cosrin day
 * 24 OOC minutes = Cosrin morning, noon, evening or night
 * 
 * @author Thau <jefffritz@gmail.com>
 * @copyright 2011
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$DATE_FORMAT = 'l, F d Y g:i A T';
$DAY_CHUNKS[0] = array(0,0,null); 
$DAY_CHUNKS[1] = array(72,96,"Morning"); 
$DAY_CHUNKS[2] = array(48,72,"Noon");
$DAY_CHUNKS[3] = array(24,48,"Evening");
$DAY_CHUNKS[4] = array(0,24,"Night");

$MONTHS = array(
    null,
    "Awakenings",
    "Frost Fall",
    "First Growth",
    "Weeping Skies",
    "Cherrin",
    "Deori's Pleasure",
    "Longing",
    "First Harvest",
    "Hallow Month",
    "Winter Cloak"
);

$input = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);
if (isset($input['submit_date'])) {

    // Clean input some more.
    foreach ($input as $k => $v) {
        $input[$k] = trim($v);
    } 
    
    // Calculate distance of date 2 from date 1.
    $y_dist = $input['ic_date_2_year'] - $input['ic_date_1_year'];
    $m_dist = $input['ic_date_2_month'] - $input['ic_date_1_month'];
    $d_dist = $input['ic_date_2_day'] - $input['ic_date_1_day'];

    // Calculdate total distance (in minutes)
    $total_dist = 0;
    //              year (in minutes) = 10 months * 30 day/mo * 96 min/day
    $total_dist += ($y_dist * 10 * 30 * 96);
    //              month (in minutes) = 30 day/mo * 96 min/day
    $total_dist += ($m_dist * 30 * 96);
    //              day (in minutes) = 96 min/day
    $total_dist += ($d_dist * 96);


    // calculate minimum/maximum distances taking into
    // account the current day chunk given to us.
    $total_dist_min = $total_dist - 96 + $DAY_CHUNKS[$input['ic_date_1_daychunk']][0]; 
    $total_dist_max = $total_dist - 96 + $DAY_CHUNKS[$input['ic_date_1_daychunk']][1];


    // ERROR HANDLING
    if ($total_dist <= 0) {
        // Ensure date 2 is not before or equal to date 1
        $error_string = "The target date is earlier or the same as the current date!";
    } 
    else if ($input['ic_date_1_day'] > 30 || $input['ic_date_2_day'] > 30) {
        // Ensure user does not give us a garbage date
        $error_string = "There are only 30 days in a month.";
    }
    else {
        // Calculate date in future
        // Convert the "distance" between the two dates from minutes to seconds.
        $total_dist_min *= 60;
        $total_dist_max *= 60;

        $now_time = date($DATE_FORMAT);
        $future_time1 = date($DATE_FORMAT, $total_dist_min + time());
        $future_time2 = date($DATE_FORMAT, $total_dist_max + time());

        $result_string = "It is now $now_time <br />The future date will occur sometime between <br />$future_time1 and <br />$future_time2";
    }


    // Format Output
    if ($error_string) {
        $output = "<h3>Error: The target date is earlier or equal to the current date.</h3>";
    }
    else {
        $output = "<table>
        <tr><th>Current Date</th><th>Target Date</th></tr>
        <tr><td>" . $DAY_CHUNKS[$input['ic_date_1_daychunk']][2] . " of " . $input['ic_date_1_day'] . " " . $MONTHS[$input['ic_date_1_month']]  . " " . $input['ic_date_1_year'] . "</td><td>" . $input['ic_date_2_day'] . " " . $MONTHS[$input['ic_date_2_month']] . " " . $input['ic_date_2_year'] . "</td></tr>
        <tr><td>$now_time</td><td>Between:<br /><strong>$future_time1</strong> and <br /><strong>$future_time2</strong></td></tr>
        </table>
        ";
    }
    

}

?>


<html>
    <head>
        <title>Cosrin Date Calculator</title>
    </head>

    <style type="text/css">
    body {
        font-family: Calibri, Verdana, Helvetica, sans-serif;
        font-size:11px;
    }
    table {
        color:#333333;
        border-width: 1px;
        border-color: #666666;
        border-collapse: collapse;
    }
    th {
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #666666;
        background-color: #dedede;
    }
    td {
        border-width: 1px;
        padding: 8px;
        border-style: solid;
        border-color: #666666;
        background-color: #ffffff;
    }
    </style>

<body>
    <h1>Cosrin Date Calculator</h1>

    <?php echo $output;?>

    <p>&nbsp;</p>

    <form action="?" method="post" id="dateform">
    <fieldset>
        <legend>Date Calculator</legend>
        
        <p><strong>Current</strong> In Character Date:</p>
        <select name="ic_date_1_daychunk" id="ic_date_1_daychunk">
        <?php
            foreach ($DAY_CHUNKS as $k => $v) {
                if ($k == 0) { continue; }
                if (isset($input['ic_date_1_daychunk']) && $k == $input['ic_date_1_daychunk']) 
                { 
                    $selected = "selected";
                }
                echo "<option value=\"$k\" $selected >$v</option>";
                $selected = "";
            }
        ?>
        </select>

        <?php 
            if (isset($input['ic_date_1_day'])) { 
                $v = $input['ic_date_1_day']; 
            } else { 
                $v = "1"; 
            } 
        ?>
        <input type="text" name="ic_date_1_day" value="<?php echo $v; ?>" id="ic_date_1_day" />

        <select name="ic_date_1_month" id="ic_date_1_month" />
        <?php
            foreach ($MONTHS as $k => $v) {
                if ($k == 0) { continue; }
                if (isset($input['ic_date_1_month']) && $k == $input['ic_date_1_month']) 
                { 
                    $selected = "selected";
                }
                echo "<option value=\"$k\" $selected >$v</option>";
                $selected = "";
            }
        ?>
        </select>

        <?php 
            if (isset($input['ic_date_1_year'])) { 
                $v = $input['ic_date_1_year']; 
            } else { 
                $v = "100"; 
            } 
        ?>
        <input type="text" name="ic_date_1_year" value="<?php echo $v; ?>" id="ic_date_1_year" />

        <p><strong>Future</strong> In Character Date:</p>
        <?php 
            if (isset($input['ic_date_2_day'])) { 
                $v = $input['ic_date_2_day']; 
            } else { 
                $v = "1"; 
            } 
        ?>
        <input type="text" name="ic_date_2_day" value="<?php echo $v; ?>" id="ic_date_2_day" />

        <select name="ic_date_2_month" id="ic_date_2_month" />
        <?php
            foreach ($MONTHS as $k => $v) {
                if ($k == 0) { continue; }
                if (isset($input['ic_date_2_month']) && $k == $input['ic_date_2_month']) 
                { 
                    $selected = "selected";
                }
                echo "<option value=\"$k\" $selected >$v</option>";
                $selected = "";
            }
        ?>
        </select>

        <?php 
            if (isset($input['ic_date_2_year'])) { 
                $v = $input['ic_date_2_year']; 
            } else { 
                $v = "100"; 
            } 
        ?>
        <input type="text" name="ic_date_2_year" value="<?php echo $v; ?>" id="ic_date_2_year" />
        <p><input type="submit" name="submit_date" value="Calculate! &raquo;" /></p>
    </fieldset>                
    </form>

    <h3>Handy Resources</h3>
    <p><a href="http://www.timeanddate.com/worldclock/converter.html">Time Zone Conversion</a> | <a href="http://cosrin.net/wiki/index.php5?title=Dates_and_Times">Cosrin Dates and Times</a></p>

    <p><strong>Note: </strong>This tool does not take into account dramatic changes or interruptions in game time. If the game server(s) go down or if game time is manipulated in any way, the Cosrin Date Calculator's previous results will be inaccurate. You will <strong>need to re-calculate any dates using a recent, updated in-game date.</strong></p>
    <p><strong>Source Code: </strong>See <a href="https://github.com/jfritz/CosrinDateCalculator">https://github.com/jfritz/CosrinDateCalculator</a>.</p>
</body>

</html>
