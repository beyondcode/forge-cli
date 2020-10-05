<?php

function exit_if($condition) {
    if ($condition) {
        exit();
    }
}

function exit_unless($condition) {
    if (! $condition) {
        exit();
    }
}
