<?php
/**
 * Plugin Name: SoFA Time
 * Description: Uses a shortcode to identify time and date strings and change them to the client's local timezone.
 * Author: SociocracyForAll, Vernon Coffey
 * Author: Vernon Coffey
 * Version: 0.813
 */

$sofatime_id_incrementer = 0;


add_action('wp_enqueue_scripts', 'sofatime_script_enqueue');
wp_register_script('dayjs', plugin_dir_url(__FILE__)."dayjs-1.9.5/dayjs.min.js", array(), '1.9.5');
wp_register_script('dayjs-utc', plugin_dir_url(__FILE__)."dayjs-1.9.5/plugin/utc.min.js", array('dayjs'), '1.9.5');
wp_register_script('dayjs-tz', plugin_dir_url(__FILE__)."dayjs-1.9.5/plugin/timezone.min.js", array('dayjs'), '1.9.5');
wp_register_script('dayjs-localizedFormat', plugin_dir_url(__FILE__)."dayjs-1.9.5/plugin/localizedFormat.js", array('dayjs'), '1.9.5');

add_action( 'init', 'sofatime_register_shortcodes');

function sofatime_script_enqueue() {
  wp_enqueue_script('jquery');
  $dir_url = plugin_dir_url(__FILE__);
  wp_enqueue_script('sofatime', $dir_url."sofatime.js", array('dayjs-utc','dayjs-tz','dayjs-localizedFormat'), filemtime($dir_url."sofatime.js"));
  wp_enqueue_style('sofatime-css', $dir_url."sofatime.css", array(), filemtime($dir_url."sofatime.css"));
}

function sofatime_register_shortcodes(){
  add_shortcode('sofatime', 'sofatime_shortcode_function');
}

function sofatime_shortcode_function($atts, $content = null) {
    $GLOBALS['sofatime_id_incrementer']++;
    $sofatimezone_select = '
    <select class="sofatimezone-select">
        <optgroup label = "UTC">
          <option value="Etc/UTC">UTC</option>
        </optgroup>
        <optgroup label = "Americas">
            <option value="Pacific/Honolulu">Hawaii Time</option>
            <option value="America/Adak">Alaska - Aleutian Islands - Adak</option>
            <option value="America/Juneau">Alaska Time</option>
            <option value="America/Los_Angeles">Pacific Time - US, Canada, Mexico</option>
            <option value="America/Phoenix">US - Arizona; Canada - Yukon; Mexico - Sonora</option>
            <option value="America/Mazatlan">Mexico - Mazatlan; Chihuahua</option>
            <option value="America/Denver">Mountain Time - US & Canada</option>
            <option value="America/Costa_Rica">Central Standard Time (Central America)</option>
            <option value="America/Regina">Canada - Saskatchewan - Regina</option>
            <option value="America/Mexico_City">Mexico City</option>
            <option value="America/Chicago">Central Time - US & Canada; Mexico - Matamoros</option>
            <option value="America/Bogota">Panama; Colombia; Ecuador; Peru; Jamaica; Mexico - Cancun</option>
            <option value="America/Rio_Branco">Brazil - Acre (Rio Branco)</option>
            <option value="America/New_York">Eastern Time - US & Canada</option>
            <option value="America/Havana">Cuba - Havana</option>
            <option value="America/Manaus">Amazon Time (Brazil)</option>
            <option value="America/Port_of_Spain">Atlantic Standard Time (Caribbean)</option>
            <option value="America/Caracas">Bolivia; Venezuela</option>
            <option value="America/Santiago">Chile - Santiago</option>
            <option value="America/Halifax">Canada - Halifax, Moncton; Bermuda</option>
            <option value="America/Asuncion">Paraguay - Asunción</option>
            <option value="America/St_Johns">Canada - Newfoundland - St Johns</option>
            <option value="America/Argentina/Buenos_Aires">Argentina - Buenos Aires</option>
            <option value="America/Sao_Paulo">Brazil - Brasilia; São Paulo</option>
            <option value="America/Nuuk">Greenland - Nuuk</option>
            <option value="America/Miquelon">Saint Pierre and Miquelon</option>
            <option value="America/Noronha">Atlantic Islands - Noronha</option>
        </optgroup>
        <optgroup label = "Atlantic">
            <option value="Atlantic/Cape_Verde">Cape Verde Time</option>
            <option value="Atlantic/Azores">Azores Time</option>
        </optgroup>
        <optgroup label = "Europe">
            <option value="Europe/Moscow">Russia - Moscow; Belarus - Minsk</option>
            <option value="Europe/Athens">Eastern European Time</option>
            <option value="Europe/Chisinau">Moldova - Chisinau</option>
            <option value="Europe/Berlin">Central European Time</option>
            <option value="Europe/Lisbon">Western European Time - UK, Ireland, Portugal</option>
        </optgroup>
        <optgroup label = "Africa">
            <option value="Africa/Nairobi">East Africa Time</option>
            <option value="Africa/Maputo">Central Africa Time</option>
            <option value="Africa/Lagos">West Africa Time</option>
            <option value="Africa/Casablanca">Morocco, Western Sahara</option>
            <option value="Africa/Abidjan">GMT - Abidjan; Accra; Bissau</option>
        </optgroup>
        <optgroup label = "Asia">
            <option value="Asia/Vladivostok">Russia - Vladivostok</option>
            <option value="Asia/Tokyo">Japan; Korea; Russia - Yakutsk</option>
            <option value="Asia/Shanghai">China; Singapore; Russia - Irkutsk</option>
            <option value="Asia/Bangkok">Indochina; Russia - Krasnoyarsk</option>
            <option value="Asia/Yangon">Myanmar Standard Time</option>
            <option value="Asia/Dhaka">Bangladesh; Russia - Omsk</option>
            <option value="Asia/Kathmandu">Nepal - Kathmandu</option>
            <option value="Asia/Kolkata">India; Sri Lanka</option>
            <option value="Asia/Karachi">Pakistan; Maldives; Russia - Yekaterinburg</option>
            <option value="Asia/Kabul">Afghanistan - Kabul</option>
            <option value="Asia/Baku">Armenia; Azerbaijan; UAE</option>
            <option value="Asia/Tehran">Iran - Tehran</option>
            <option value="Asia/Baghdad">Arabia Standard Time</option>
            <option value="Asia/Gaza">Palestine - Gaza; West Bank</option>
            <option value="Asia/Jerusalem">Israel - Jerusalem</option>
            <option value="Asia/Beirut">Lebanon - Beirut</option>
            <option value="Asia/Damascus">Syria - Damascus</option>
            <option value="Asia/Amman">Jordan - Amman</option>
        </optgroup>
        <optgroup label = "Australia">
            <option value="Australia/Lord_Howe">Australia - Lord_Howe</option>
            <option value="Australia/Sydney">Australia - Sydney; Melbourne</option>
            <option value="Australia/Brisbane">Australia - Brisbane</option>
            <option value="Australia/Adelaide">Australia - Adelaide</option>
            <option value="Australia/Darwin">Australia - Darwin</option>
            <option value="Australia/Eucla">Australia - Eucla</option>
            <option value="Australia/Perth">Australia - Perth</option>
        </optgroup>
        <optgroup label = "Pacific">
            <option value="Pacific/Pago_Pago">Pago Pago; Midway Island</option>
            <option value="Pacific/Marquesas">Marquesas</option>
            <option value="Pacific/Gambier">Gambier</option>
            <option value="Pacific/Pitcairn">Pitcairn Island</option>
            <option value="Pacific/Easter">Easter Island</option>
            <option value="Pacific/Kiritimati">Kiritimati</option>
            <option value="Pacific/Apia">Samoa - Apia</option>
            <option value="Pacific/Tongatapu">Tonga - Tongatapu</option>
            <option value="Pacific/Chatham">New Zealand - Chatham</option>
            <option value="Pacific/Auckland">New Zealand - Aukland</option>
            <option value="Pacific/Fiji">Fiji</option>
            <option value="Pacific/Majuro">Marshall Islands; Gilbert Islands</option>
            <option value="Pacific/Norfolk">Norfolk Island</option>
            <option value="Pacific/Noumea">New Caledonia - Noumea</option>
        </optgroup>
    </select>
  ';



  $out = '<div class="sofatime"';
  foreach($atts as $key => $value)
  {
    if(preg_match("/^[a-zA-Z0-9_\-]+$/",$key))
    {
      $out .= ' data-'.strtolower($key).'="'.preg_replace("/[^a-zA-Z0-9_\-]/","",$value).'"';
    }
  }

  $out .= ">\n";

  if(!isset($atts['display-time']) || ($atts['display-time'] != 'no' && $atts['display-time'] != 'false'))
  {
    $out .= "<span class = 'raw-user-input'>".htmlspecialchars($content)."</span>\n";
  }
  // if(!isset($atts['display-24h-toggle']) || ($atts['display-24h-toggle'] != 'no' && $atts['display-24h-toggle'] != 'false'))
  // {
  //   $out .= '<div class="sofatime-24h-wrapper">
  //     <input type="checkbox" class="sofatime-24h-checkbox" id="sofatime-24h-'.$GLOBALS['sofatime_id_incrementer'].'">
  //     <label class="sofatime-24h-label" for="sofatime-24h-'.$GLOBALS['sofatime_id_incrementer'].'">24h</label>
  //     <p>24h</p>
  //   </div>';
  // }
  if(!isset($atts['display-select']) || ($atts['display-select'] != 'no' && $atts['display-select'] != 'false'))
  {
    $out .= "<div class=\"sofatime-select-wrapper\">".$sofatimezone_select."</div>\n";
  }

  $out .= "</div>\n";

  return $out;
}
