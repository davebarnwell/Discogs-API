<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 30/04/2017
 * Time: 17:58
 */

namespace davebarnwell\Discogs;


use Throwable;

class NoNextPageException extends \Exception
{

    public function __construct($message = "No next page for query", $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}