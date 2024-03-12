<?php

function uploadImage($folder,$image)
{
    $image->store('/',$folder);
    $filename=$image->hashName();
    $path='images/'.$folder.'/'.$filename;
    return $path;
}

function uploadVideo($folder,$video)
{
    $video->store('/',$folder);
    $filename=$video->hashName();
    $path='images/'.$folder.'/'.$filename;
    return $path;
}






