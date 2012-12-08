<?php

//$arr = new ArrayObject(array('foo'));

//$arr[] = 'bar';

//echo($arr[0]);
//print_r($arr);

//die;

$arr = array(
  'one' => array(
      'two'   => null,
  ),
);
$arr = new ArrayObject($arr);
//$arr = array('one' => 'one');

//$arr = 1;
Debug::dump(Arr::has($arr, 'one.two'));
//Arr::set($arr, 'one.two.three', 'wut');

//Debug::dump($arr);

/**
 * - use tooltip tp show full path
 *
 * Request::getSingleton()->getQuery('sort');
 * $this->getRequest()->getQuery('sort');
 * $this->getRequest()->hasGet('sort');
 */
