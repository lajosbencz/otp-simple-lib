<?php

namespace OtpSimple;


interface ContainerAwareInterface
{
    public function setContainer(Container $container): void;

    public function getContainer(): Container;
}
