<?php

function array_peek($array)
{
  $value = end($array);

  reset($array);

  return $value;
}
