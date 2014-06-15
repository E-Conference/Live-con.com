<?php

namespace fibe\Bundle\WWWConfBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class fibeWWWConfBundle
 * @package fibe\Bundle\WWWConfBundle
 */
class fibeWWWConfBundle extends Bundle
{
  /**
   * Returns the bundle parent name.
   *
   * @return string The Bundle parent name it overrides or null if no parent
   *
   * @api
   */
  public function getParent()
  {
    return null;
  }
}

?>
