<?php
  include_once 'shared.php';
  include_once '../configs/config.php';

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
