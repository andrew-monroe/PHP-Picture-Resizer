<?php
  include_once '../config/my_config.php';
?>

<?php
  function file_key($config_key_starts_with="") {
    $filename       = preg_replace("/[^A-Za-z0-9.]/", '', $_POST['filename']);
    $random_string  = generate_random_string(10);
    $file_key       = "$config_key_starts_with"."$random_string/"."$filename";
    return $file_key;
  }

  function short_date() {
    return date("Ymd");
  }

  function long_date() {
    return date("Ymd\T000000\Z");
  }

  function expiration_date($config_POST_expiration_time_limit) {
    return date("Y-m-d\TH:i:s.000\Z", strtotime("+$config_POST_expiration_time_limit seconds"));
  }

  function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  function string_to_sign($access_key_id,$expiration_date,$short_date,$long_date,$bucket,$key_starts_with,$acl,$service='s3',$region='us-east-1') {
    $policy_str =
"{ \"expiration\": \"$expiration_date\",\r
  \"conditions\": [\r
    {\"bucket\": \"$bucket\"},\r
    [\"starts-with\", \"\$key\", \"$key_starts_with\"],\r
    {\"acl\": \"$acl\"},\r
    {\"success_action_redirect\": \"http://$bucket.s3.amazonaws.com/\"},\r
    [\"starts-with\", \"\$Content-Type\", \"\"],\r
    {\"x-amz-meta-uuid\": \"14365123651274\"},\r
    {\"x-amz-server-side-encryption\": \"AES256\"},\r
    [\"starts-with\", \"\$x-amz-meta-tag\", \"\"],\r
\r
    {\"x-amz-credential\": \"$access_key_id/$short_date/$region/$service/aws4_request\"},\r
    {\"x-amz-algorithm\": \"AWS4-HMAC-SHA256\"},\r
    {\"x-amz-date\": \"$long_date\" }\r
  ]\r
}";

    $string_to_sign = base64_encode($policy_str);

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

  function json($string_to_sign,$signature,$access_key_id,$key,$bucket,$short_date,$long_date,$acl,$service='s3',$region='us-east-1') {
    $json_array = array('bucket' => $bucket,
                        'key' => $key,
                        'Policy' => $string_to_sign,
                        'XAmzSignature' => $signature,
                        'AWSAccessKeyId' => $access_key_id,
                        'acl' => $acl,
                        'success_action_status' => '201',
                        'success_action_redirect' => "http://$bucket.s3.amazonaws.com/",
                        'XAmzMetaTag' => '',
                        'ContentType' => 'image',
                        'XAmzMetaUuid' => '14365123651274',
                        'XAmzServerSideEncryption' => 'AES256',
                        'XAmzCredential' => "$access_key_id/$short_date/$region/$service/aws4_request",
                        'XAmzAlgorithm' => 'AWS4-HMAC-SHA256',
                        'XAmzDate' => $long_date);
    return json_encode($json_array, JSON_UNESCAPED_SLASHES);
  }
?>

<?php
  $POST_file_key        = file_key(       $config_key_starts_with);
  $short_date           = short_date();
  $long_date            = long_date();
  $POST_expiration_date = expiration_date($config_POST_expiration_time_limit);

  $string_to_sign       = string_to_sign( $config_access_key_id,
                                          $POST_expiration_date,
                                          $short_date,
                                          $long_date,
                                          $config_POST_bucket,
                                          $config_key_starts_with,
                                          $config_acl);

  $signing_key          = signing_key(    $config_secret_access_key,
                                          $short_date);

  $signature            = signature(      $string_to_sign,
                                          $signing_key);

  echo json(                              $string_to_sign,
                                          $signature,
                                          $config_access_key_id,
                                          $POST_file_key,
                                          $config_POST_bucket,
                                          $short_date,
                                          $long_date,
                                          $config_acl);
?>
