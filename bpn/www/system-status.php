<?php
require_once("system/shared.php");
$db = Database::getInstance();

$stmt = $db->prepareAbe("SELECT b.block_height
FROM block b
JOIN chain c ON c.chain_last_block_id = b.block_id
WHERE c.chain_id = 1");

$db->select($stmt);
$stmt->bind_result($id);

if ($stmt->fetch()) {
    $localblock = $id;
} else {
    $localblock = "?";
}

function getCacheValue($key) {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT `value` FROM `cache` WHERE `key`=?");
    $stmt->bind_param('s', $key);
    $db->select($stmt);
    $stmt->bind_result($id);

    if ($stmt->fetch()) {
        return $id;
    } else {
        return null;
    }
}

$bbeblock = getCacheValue("BBE");
if ($bbeblock == null) $bbeblock = "?";
$bciblock = getCacheValue("BCI");
if ($bciblock == null) $bciblock = "?";

?>
<div class="span4">
    <h3>System status</h3>
    Latest local block : <?=$localblock;?><br>
    Latest <a href="http://blockexplorer.com/" target="_blank">BBE</a> block : <?php echo $bbeblock;?><br>
    Latest <a href="http://blockchain.info/" target="_blank">BCI</a> block : <?php echo $bciblock;?><br>
    <br>
    <small>The system does not use BBE/BCI for data collection, the information is simply to see if our database is updated.</small>
<h3>Exchange rates</h3>
<table>
<?php
function showExchangeRate($pair)
{
	$db = Database::getInstance();
	$stmt = $db->prepare("SELECT `key`,`value`,`last_update` FROM exchange_rate WHERE `key` = ?");
	$stmt->bind_param('s', $pair);
	$db->select($stmt);
	$stmt->bind_result($key, $value, $update);

	if ($stmt->fetch())
	{
		$value = number_format($value, 4);
	        echo "<tr><td>".$key."</td><td>".$value."</td></tr>";
	}
}


showExchangeRate('USDBTC');
showExchangeRate('EURBTC');
showExchangeRate('GBPBTC');
showExchangeRate('JPYBTC');
showExchangeRate('AUDBTC');
showExchangeRate('DKKBTC');
?>
</table>
<small>This data is collected from various providers and provided free of charge for informational purposes only, with no guarantee whatsoever of accuracy, validity, availability or fitness for any purpose; use at your own risk</small>
</div>
</div>

