<?php

namespace App;

trait StatusCodes {
    protected $SUCCESS = 200;
    protected $CREATED = 201;
    protected $ACCEPTED = 202;
    protected $NO_CONTENT = 204;
    protected $ALREADY_REPORTED = 208; // Already Subscribed
    protected $BAD_REQUEST = 400; // Unknown Error
    protected $MISSING_REQUIRED_INPUTS = 401;
    protected $NOT_FOUND = 404;
    protected $UNPROCESSABLE = 422;
    protected $EXPECTATION_FAILED = 417;
    protected $TOOMANYREQUESTS = 429;
}