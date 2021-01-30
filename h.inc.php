<?php
function h($str)
{
    echo htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}