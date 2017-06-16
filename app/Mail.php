<?php

class Mail
{
    public function perform()
    {
        sleep(10);
        //echo '执行成功！'.PHP_EOL;
        print_r($this->args);
    }
}