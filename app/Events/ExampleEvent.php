<?php

namespace App\Events;

class ExampleEvent extends Event
{

    public $tid;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($tid)
    {
        $this->tid = $tid;
        //
    }
}
