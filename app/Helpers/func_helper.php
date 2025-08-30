<?php

if (!function_exists('sendSignal')) {

    function fSendSignal(...$types)
    {
        $Signal = session()->getFlashdata('signal') ?? [];
        session()->setFlashdata('signal', array_merge($Signal, $types));
    }
}
