<?php
  include_once '../config/config.php';
?>

<?php
  function file_key() {
    return $_GET['filekey'];
  }

  function short_date() {
    return date("Ymd");
  }

  function long_date() {
    return date("Ymd\T000000\Z");
  }

  function hashed_content_body($content_body) {
    return bin2hex(hash('sha256',$content_body,true));
  }

  function canonical_request($access_key_id,$file_key,$bucket,$short_date,$long_date,$expiration_time_limit='86400',$service='s3',$region='us-east-1') {
    $canonical_request =
      "GET"."\n".
      "/$file_key"."\n".
      "X-Amz-Algorithm="."AWS4-HMAC-SHA256"."&".
        "X-Amz-Credential="."$access_key_id%2F$short_date%2F$region%2F$service%2Faws4_request"."&".
        "X-Amz-Date="."$long_date"."&".
        "X-Amz-Expires="."$expiration_time_limit"."&".
        "X-Amz-SignedHeaders="."host"."\n".
      "host:$bucket.s3.amazonaws.com"."\n".
      "\n".
      "host"."\n".
      "UNSIGNED-PAYLOAD";
    return $canonical_request;
  }

  function string_to_sign($short_date,$long_date,$canonical_request,$service='s3',$region='us-east-1') {
    $hashed_canonical_request = bin2hex(hash('sha256',$canonical_request,true));
    $string_to_sign =
      "AWS4-HMAC-SHA256"."\n".
      "$long_date"."\n".
      "$short_date/$region/$service/aws4_request"."\n".
      "$hashed_canonical_request";
    return $string_to_sign;
  }

  function signing_key($secret_access_key, $short_date, $service='s3',$region='us-east-1') {
    $start_key = 'AWS4'.$secret_access_key;
    $date_key = hash_hmac('sha256',$short_date,$start_key, true);
    $region_key = hash_hmac('sha256', $region, $date_key, true);
    $service_key = hash_hmac('sha256', $service, $region_key, true);
    $signing_key = hash_hmac('sha256', 'aws4_request', $service_key, true);
    return $signing_key;
  }

  function signature($string_to_sign, $signing_key) {
    return bin2hex(hash_hmac('sha256', $string_to_sign, $signing_key, true));
  }

  function signed_get_url($bucket,$file_key,$access_key_id,$short_date,$long_date,$expiration_time_limit,$signature,$service='s3',$region='us-east-1') {
    $signed_get_url =
      "https://$bucket.s3.amazonaws.com".
      "/$file_key"."?".
      "X-Amz-Algorithm="."AWS4-HMAC-SHA256"."&".
      "X-Amz-Credential="."$access_key_id%2F$short_date%2F$region%2F$service%2Faws4_request"."&".
      "X-Amz-Date="."$long_date"."&".
      "X-Amz-Expires="."$expiration_time_limit"."&".
      "X-Amz-SignedHeaders="."host"."&".
      "X-Amz-Signature="."$signature";

    return json_encode(array('signed_get_url' => $signed_get_url), JSON_UNESCAPED_SLASHES);
  }
?>


<?php
  $GET_file_key       = file_key();
  $short_date         = short_date();
  $long_date          = long_date();

  $canonical_request  = canonical_request(  $config_access_key_id,
                                            $GET_file_key,
                                            $config_GET_bucket,
                                            $short_date,
                                            $long_date);

  $string_to_sign     = string_to_sign(     $short_date,
                                            $long_date,
                                            $canonical_request);

  $signing_key        = signing_key(        $config_secret_access_key,
                                            $short_date);

  $signature          = signature(          $string_to_sign,
                                            $signing_key);

  $signed_GET_url     = signed_GET_url(     $config_GET_bucket,
                                            $GET_file_key,
                                            $config_access_key_id,
                                            $short_date,
                                            $long_date,
                                            $config_GET_expiration_time_limit,
                                            $signature);
  echo $signed_GET_url;
?>
