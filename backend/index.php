<?php
/*

So here's my backendy code. I'm trying to write it as functional not OO.
I think this makes sense because it will always be operating on URLs and responses.
The things won't change.

Judge all you want, and please, if my code is criminally poor in it's structure do suggest changes.
This code currently feels kinda dirty.

*/

error_reporting(E_ALL);
ini_set('display_errors', '1');

// Gets mime type list for dropdown
function getMimeTypes()
{
  return file_get_contents("mimetypes.json");
}

// From https://subinsb.com/php-check-if-string-is-json
// This kind of bugs me though... We decode it anyways to check?
function isJSON($string) {
  return is_string($string) && is_object(json_decode($string)) ? true : false;
}

// Dispatches action string to function if it exists, and returns our JSON
function dispatch($action)
{
  header('Content-Type: application/json');
  http_response_code(400);
  $response = json_encode("No dice homeslice.");

  if ($action && function_exists($action))
  {
    http_response_code(200);
    $response = call_user_func($action);
    if (!isJSON($response))
    {
      $response = json_encode($response);
    }
  }

  return $response;
}

echo dispatch($_GET['action']);

?>