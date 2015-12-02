<?php

global $scaip_period, $scaip_repetitions, $scaip_minimum_paragraphs;

// This is the number of paragraphs after which SCAIP should insert a shortcode, counted in paragraphs since wither the beginning or the last time SCAIP inserted a shortcode
$scaip_period = 3;

// This is the number of times that SCAIP should insert a shortcode in a post
$scaip_repetitions = 2;

// This is the minimum number of paragraphs in a post required for SCAIP to insert a shortcode in a post.
$scaip_minimum_paragraphs = 6;
