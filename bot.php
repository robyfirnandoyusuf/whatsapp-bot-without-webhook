<?php
ini_set('date.timezone', 'Asia/Jakarta');


/////////////////////////////////
//////    BEGIN CONFIG    ///////
////////////////////////////////

$apiKey 	= "API Key Mu";
$myNumber 	= "No HP mu";
$staticText = "Halo saya MattBot, Si Bos saat ini masih tidur silahkan hubungi kembali ketika sudah bangun, Terima Kasih";

////////////////////////////////
//////    END CONFIG    ///////
//////////////////////////////



/* Don't touch below if u don't undertstand */ 
$no 	= 1;
while (true) {

	$url 	= "http://panel.apiwha.com/get_messages.php?apikey=".$apiKey;

	$getMessages = file_get_contents($url);
	$jsonDec 	 = json_decode($getMessages);
	$lastSender  = end($jsonDec)->from;
	$lastMsg 	 = end($jsonDec)->text;
	$allMessages = object_to_array($jsonDec);


	$filter = [];
	foreach ($allMessages as $key => $value) {
		if($value['from'] != $myNumber){
			$filter[] = $value['text'];
		}
	}
	$MyText 	 =  $staticText. end($filter)."\n Pesan dibalas pada :".date('Y-m-d H:i:s');

	if($lastSender != $myNumber || count($filter) != file_get_contents('db.txt')){
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://panel.apiwha.com/send_message.php?apikey=".$apiKey."&number=".$lastSender."&text=".urlencode($MyText));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

	$headers = array(
		"Cookie: _ga=GA1.2.488642940.1544323554; _gid=GA1.2.1202797080.1544323554; website_reference=APIWHA; PHPSESSID=pldvgmc94h7hlrm30bpkvjmi83; send_bulk_country={\"id\":\"96\",\"country_name\":\"Indonesia\",\"country_code\":\"62\",\"cellphone_code\":\"8\",\"digits\":\"11\",\"min_digits\":\"8\",\"must_start_with\":null,\"approve\":\"-1\"}",
		"Accept-Encoding: gzip, deflate, br",
		"Accept-Language: en-US,en;q=0.9",
		"User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36",
		"Accept: application/json, text/javascript, */*; q=0.01",
		"Referer: https://panel.apiwha.com/page_addons_send_bulk.php",
		"X-Requested-With: XMLHttpRequest",
		"Connection: keep-alive"
	);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$result = curl_exec($ch);
	if (curl_errno($ch)) {
	    echo 'Error:' . curl_error($ch);
	}
	curl_close ($ch);
	echo $no." - "."\033[32m ". $result."\033[0m"."\n";

		$saveLastCount = file_put_contents("db.txt", count($filter));
	}else{
			echo $no." - "."\033[33m-=[ Tidak Ada Pesan Baru ]=-\033[0m"."\n";
	}

	$no++;
}

function object_to_array($data)
{
    if (is_array($data) || is_object($data))
    {
        $result = array();
        foreach ($data as $key => $value)
        {
            $result[$key] = object_to_array($value);
        }
        return $result;
    }
    return $data;
}
