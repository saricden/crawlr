<?php
/*

So here's my backendy code.

Judge all you want, and please, if my code is criminally poor in it's structure do suggest changes.
This code currently feels kinda dirty.

... There isn't much structure at the moment.
I'm going to get the core functionality working, then at some point come back and structure nicer

*/

error_reporting(E_ALL);
ini_set('display_errors', '1');

if (isset($_GET['action'])) echo dispatch($_GET['action']);
else echo "Specify an action please";

/*
Returns:
An array of mime types / file extensions.
*/
function getMimeTypes()
{
  return file_get_contents("mimetypes.json");
}

/*
Get the content type of a given URL, if it exists.

Accepts:
url (string): URL to check content type of

Returns:
(string) Content type of page or false on bad URL
*/
function getContentType($url = 'http://jsforcats.com/')
{
  $ret = false;
  // This should follow any 300 redirections.. Needs testing though.
  if (getLastResponseCode($url) && getLastResponseCode($url) < 300)
  {
    $headers = get_headers($url, true);
    if ($headers && is_array($headers) && isset($headers['Content-Type']))
    {
      $ret = $headers['Content-Type'];
    }
  }
  return $ret;
}

/*
Follows redirects until we get a response code to return.
Credit to: http://stackoverflow.com/questions/2280394/how-can-i-check-if-a-url-exists-via-php#12628971

Accepts:
url (string): URL to start at

Returns:
An (int) of the final response code, or false if the URL could not be reached
*/
function getLastResponseCode($url = 'http://jsforcats.com/')
{
  if(!$url || !is_string($url)) return false;

  $headers = @get_headers($url); // Use @ here to prevent warning on failure
  if($headers && is_array($headers))
  {
      $headers = array_reverse($headers);
      foreach($headers as $hline)
      {
          if(preg_match('/^HTTP\/\S+\s+([1-9][0-9][0-9])\s+.*/', $hline, $matches))
          {
              $code = (int) $matches[1];
              return $code;
          }
      }
  }
  return false;
}

/*
Scan URL for more links if it's an HTML document, otherwise return content type.
~ I might refactor this, kinda feels like it's doing too many things.

Accepts:
url (string): URL to scan

Returns:
Either an (array) of URLs, a (string) content type, or false on failure
*/
function getLinksOrContentType($url = 'http://jsforcats.com/')
{
  $ret = false;
  $contentType = getContentType($url);
  if ($contentType && is_string($contentType) && strpos($contentType, 'html') !== false)
  {
    $content = file_get_contents($url);
    if ($content)
    {
      $urlMatches = array();
      preg_match_all('/(?<=href\=")(.*?)(?=")|(?<=src\=")(.*?)(?=")/', $content, $urlMatches);
      $urlMatches = $urlMatches[0];
      $ret = $urlMatches;
    }
  }
  else
  {
    $ret = $contentType;
  }
  return $ret;
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

?>